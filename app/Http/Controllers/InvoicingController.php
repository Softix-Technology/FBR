<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\SaleInvoiceFbr;
use App\Services\FbrApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoicingController extends Controller
{
    private $fbrApiService;

    public function __construct(FbrApiService $fbrApiService)
    {
        $this->middleware('auth');
        $this->fbrApiService = $fbrApiService;
    }

    /**
     * Get FBR API service with user context
     */
    private function getFbrApiService()
    {
        $user = Auth::user();
        return $this->fbrApiService->setUser($user);
    }

    /**
     * Display the invoicing form
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Please set your FBR Access Token in your profile to use the invoicing system.');
        }
        // dd("WORKING");
        // Load data from FBR API
        $provinces = [];
        $hsCodes = [];
        $uoMs = [];
        $transactionTypes = [];

        try {
            $fbrService = $this->getFbrApiService();

            // Load provinces
            $provincesResult = $fbrService->getProvinceCodes($user->fbr_access_token);
            if ($provincesResult['success']) {
                $provinces = $provincesResult['data'] ?? [];
            }

            // Load HS codes (Item Description Codes)
            $hsCodesResult = $fbrService->getItemDescriptionCodes($user->fbr_access_token);
            if ($hsCodesResult['success']) {
                $hsCodes = $hsCodesResult['data'] ?? [];
            }

            // Load Units of Measurement
            $uoMsResult = $fbrService->getUnitsOfMeasurement($user->fbr_access_token);
            if ($uoMsResult['success']) {
                $uoMs = $uoMsResult['data'] ?? [];
            }

            // Load Transaction Types
            $transactionTypesResult = $fbrService->getTransactionTypeCodes($user->fbr_access_token);
            if ($transactionTypesResult['success']) {
                $transactionTypes = $transactionTypesResult['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading FBR data', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        $existingProducts = [];
        try {
            $registeredProducts = \App\Models\ProductMaster::where('c_id', $user->c_id)->pluck('prod_name')->filter()->toArray();
            $previousInvoices = \App\Models\SaleInvoiceFbr::where('cid', $user->c_id)->pluck('items');
            $savedDescriptions = [];
            foreach ($previousInvoices as $itemsJson) {
                $itemsArr = is_string($itemsJson) ? json_decode($itemsJson, true) : ($itemsJson ?? []);
                if (is_array($itemsArr)) {
                    foreach ($itemsArr as $item) {
                        if (!empty($item['productDescription'])) {
                            $savedDescriptions[] = trim($item['productDescription']);
                        }
                    }
                }
            }
            $existingProducts = array_values(array_unique(array_filter(array_merge($registeredProducts, $savedDescriptions))));
        } catch (\Exception $e) {
            Log::error('Error fetching existing products', ['error' => $e->getMessage()]);
        }

        return view('invoicing.index', compact('provinces', 'hsCodes', 'uoMs', 'transactionTypes', 'user', 'existingProducts'));
    }

    /**
     * Submit invoice data to FBR
     */
     
    private function cleanAddress($address)
    {
        // Replace new lines with a space
        $address = str_replace(["\n", "\r"], ' ', $address);

        // Remove double commas
        $address = str_replace(",,", ",", $address);

        // Remove extra spaces
        $address = preg_replace('/\s+/', ' ', $address);

        // Trim spaces at start and end
        return trim($address);
    }
    public function submitInvoice(Request $request)
{
    $user = Auth::user();

    if (!$user->fbr_access_token) {
        return response()->json([
            'success' => false,
            'message' => 'FBR Access Token is required. Please update your profile.'
        ], 400);
    }

    try {
        // Get invoice data
     $invoiceData = $request->all();

        // Remove non-FBR fields that leak from the form
    unset($invoiceData['_token'], $invoiceData['furtherexpense'], $invoiceData['title'], $invoiceData['notes'], $invoiceData['status'], $invoiceData['cid'], $invoiceData['user_id']);

    // Clean sellerAddress if it exists
    if (!empty($invoiceData['sellerAddress'])) {
        $invoiceData['sellerAddress'] = $this->cleanAddress($invoiceData['sellerAddress']);
    }

    // Clean buyerAddress if needed
    if (!empty($invoiceData['buyerAddress'])) {
        $invoiceData['buyerAddress'] = $this->cleanAddress($invoiceData['buyerAddress']);
    }

foreach ($invoiceData['items'] as &$item) {

    // Convert numeric fields to float (FBR expects numbers, not strings)
    if (isset($item['furtherTax'])) {
        $item['furtherTax'] = (float) $item['furtherTax'];
    }
       if (isset($item['productDescription'])) {
           $item['productDescription'] = $this->cleanAddress($item['productDescription']);
    }


    if (isset($item['valueSalesExcludingST'])) {
        $item['valueSalesExcludingST'] = (float) $item['valueSalesExcludingST'];
    }

    if (isset($item['rateValues'])) {
        $item['rateValues'] = (float) $item['rateValues'];
    }

    if (isset($item['totalValues'])) {
        $item['totalValues'] = (float) $item['totalValues'];
    }

    if (isset($item['salesTaxApplicable'])) {
        $item['salesTaxApplicable'] = (float) $item['salesTaxApplicable'];
    }

    if (isset($item['quantity'])) {
        $item['quantity'] = (float) $item['quantity'];
    }
}

        // Handle unregistered supplier logic
        $invoiceData = $this->handleUnregisteredSupplier($invoiceData);

        // Remove scenarioId in production
        if (!$user->use_sandbox && isset($invoiceData['scenarioId'])) {
            unset($invoiceData['scenarioId']);
            Log::info('Removed scenarioId for production environment');
        }

        // Ensure top-level null-safe fields
        $invoiceData['invoiceRefNo'] = $invoiceData['invoiceRefNo'] ?? '';

        // Ensure items array exists
        $invoiceData['items'] = $invoiceData['items'] ?? [];
        foreach ($invoiceData['items'] as &$item) {
            // Convert null string fields to empty string
            foreach (['extraTax', 'sroScheduleNo', 'sroItemSerialNo'] as $field) {
                $item[$field] = $item[$field] ?? '';
            }
            // Convert null numeric fields to 0
            foreach (['fixedNotifiedValueOrRetailPrice', 'salesTaxWithheldAtSource', 'furtherTax', 'fedPayable', 'discount'] as $field) {
                $item[$field] = $item[$field] ?? 0;
            }
        }

        // Store buyer information if not unregistered
        if (!empty($invoiceData['buyerNTNCNIC']) && ($invoiceData['buyerRegistrationType'] ?? '') !== 'Unregistered') {
            try {
                Buyer::updateOrCreate(
                    ['ntn_cnic' => $invoiceData['buyerNTNCNIC']],
                    [
                        'cid' => $user->c_id,
                        'user_id' => $user->id,
                        'business_name' => $invoiceData['buyerBusinessName'] ?? '',
                        'address' => $invoiceData['buyerAddress'] ?? '',
                        'registration_type' => $invoiceData['buyerRegistrationType'] ?? '',
                        'province' => $invoiceData['buyerProvince'] ?? ''
                    ]
                );

                Log::info('Buyer information stored/updated', [
                    'user_id' => $user->id,
                    'buyer_ntn_cnic' => $invoiceData['buyerNTNCNIC'],
                    'province' => $invoiceData['buyerProvince'] ?? ''
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to store buyer information', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'buyer_data' => $invoiceData
                ]);
            }
        }
// Save original items (with rate) for local database
$originalItems = $invoiceData['items'];

// Create a deep copy for FBR API
$fbrItems = json_decode(json_encode($invoiceData['items']), true);

// Remove rate only from FBR items
foreach ($fbrItems as &$item) {
    unset($item['rateValues']);
    if (isset($item['rate'])) {
        $rateParsed = json_decode($item['rate'], true);
        if (json_last_error() === JSON_ERROR_NONE && isset($rateParsed['rate_desc'])) {
            $item['rate'] = $rateParsed['rate_desc'];
        }
    }
    // Fallback: If saleType is numeric ID, resolve to description text from reference API
    if (isset($item['saleType']) && (is_numeric($item['saleType']) || ctype_digit((string)$item['saleType']))) {
        try {
            $fbrService = $this->getFbrApiService();
            $ttRes = $fbrService->getTransactionTypeCodes($user->fbr_access_token);
            if ($ttRes['success'] && !empty($ttRes['data'])) {
                foreach ($ttRes['data'] as $tType) {
                    if (isset($tType['transactioN_TYPE_ID']) && (string)$tType['transactioN_TYPE_ID'] === (string)$item['saleType']) {
                        $item['saleType'] = $tType['transactioN_DESC'] ?? $item['saleType'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to map numeric saleType in submitInvoice: ' . $e->getMessage());
        }
    }
}

// Replace only for API submission
$invoiceData['items'] = $fbrItems;

// Submit to FBR API
$result = $this->getFbrApiService()->postInvoiceData($user->fbr_access_token, $invoiceData);

        Log::info('Invoice submission response', [
            'user_id' => $user->id,
            'fbr_response' => $result
        ]);

        if ($result['success'] ?? false) {
            $validationResponse = $result['data']['validationResponse'] ?? null;

            // Check FBR validation status
            if ($validationResponse && ($validationResponse['status'] ?? '') !== 'Valid') {
                $errorDetails = '';
                $statuses = $validationResponse['invoiceStatuses'] ?? [];
                foreach ($statuses as $i => $st) {
                    if (($st['status'] ?? '') !== 'Valid') {
                        $itemNum = $i + 1;
                        $errMsg = $st['error'] ?? 'Unknown error';
                        $errorDetails .= "Item {$itemNum}: {$errMsg}. ";
                    }
                }
                $message = $errorDetails ?: ($validationResponse['error'] ?? 'Validation failed');
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $result['status_code'] ?? 400);
            }

            // Prepare draft data for local DB storage
            $draftData = [
                'cid' => $user->c_id,
                'user_id' => $user->id,
                'title' => 'Sale Invoice',
                'notes' => null,
                'seller_ntn_cnic' => $request->input('sellerNTNCNIC'),
                'seller_business_name' => $request->input('sellerBusinessName'),
                'seller_province' => $request->input('sellerProvince'),
                'seller_address' => $request->input('sellerAddress'),
                'invoice_type' => $request->input('invoiceType'),
                'invoice_date' => $request->input('invoiceDate'),
                'invoice_ref_no' => $request->input('invoiceRefNo'),
                'buyer_ntn_cnic' => $request->input('buyerNTNCNIC'),
                'buyer_business_name' => $request->input('buyerBusinessName'),
                'buyer_province' => $request->input('buyerProvince'),
                'buyer_registration_type' => $request->input('buyerRegistrationType'),
                'buyer_address' => $request->input('buyerAddress'),
                'items' => json_encode($request->input('items', [])),
                'fbr_invoice_no' => $result['data']['invoiceNumber'] ?? null,
                'expense_col'=>$request->input('furtherexpense'),
                'cid'=>auth()->user()->c_id
            ];

            $invoice = SaleInvoiceFbr::create($draftData);

            Log::info('Invoice stored locally', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice submitted successfully to FBR',
                'data' => $result
            ]);
        } else {
            Log::warning('Invoice submission failed', [
                'user_id' => $user->id,
                'error' => $result['message'] ?? 'Unknown error',
                'invoice_data' => $invoiceData
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invoice submission failed',
                'errors' => $result['errors'] ?? null
            ], $result['status_code'] ?? 400);
        }

    } catch (\Exception $e) {
        Log::error('Invoice submission exception', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while submitting the invoice: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Validate invoice data with FBR
     */
    public function validateInvoice(Request $request)
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $invoiceData = $request->all();

            // Handle unregistered supplier logic
            $invoiceData = $this->handleUnregisteredSupplier($invoiceData);

            // Remove scenarioId when not using sandbox (production)
            if (!$user->use_sandbox && isset($invoiceData['scenarioId'])) {
                unset($invoiceData['scenarioId']);
                Log::info('Removed scenarioId for production environment during validation');
            }

            Log::info('=== INVOICE VALIDATION REQUEST ===', [
                'user_id' => $user->id,
                'invoice_data_keys' => array_keys($invoiceData),
                'items_count' => count($invoiceData['items'] ?? []),
                'use_sandbox' => $user->use_sandbox
            ]);

            $result = $this->getFbrApiService()->validateInvoiceData(
                $user->fbr_access_token,
                $invoiceData
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Invoice validation exception', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get province codes from FBR API
     */
    public function getProvinceCodes(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required. Please set your FBR Access Token in your profile to load provinces.'
            ], 400);
        }

        $result = $this->getFbrApiService()->getProvinceCodes($user->fbr_access_token);
        return response()->json($result);
    }

    /**
     * Get HS codes with UOM from FBR API
     */
    public function getHsCodes()
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $result = $this->getFbrApiService()->getItemDescriptionCodes($user->fbr_access_token);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching HS codes', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch HS codes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get item description codes from FBR API
     */
    public function getItemDescriptionCodes()
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $result = $this->getFbrApiService()->getItemDescriptionCodes($user->fbr_access_token);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching item description codes', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch item description codes: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
     * Search item description codes from FBR API with pagination and filtering
     */
    public function searchItemDescriptionCodes(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $fbrAccessToken = $user->fbr_access_token;

        $search = $request->query('search', '');
        $limit = min((int) $request->query('limit', 20), 50); // Max 50 items per page
        $page = (int) $request->query('page', 1);

        try {
            // Cache key for HS codes - cache for 24 hours since HS codes don't change frequently
            $cacheKey = 'fbr_hs_codes_' . md5($fbrAccessToken);

            // Get all item description codes from cache or FBR API
            $allItems = Cache::remember($cacheKey, 86400, function () use ($fbrAccessToken) {
                $result = $this->getFbrApiService()->getItemDescriptionCodes($fbrAccessToken);

                if (!$result['success'] || !isset($result['data'])) {
                    throw new \Exception('Failed to fetch item description codes from FBR API');
                }

                Log::info('HS codes fetched from FBR API and cached', [
                    'count' => count($result['data'])
                ]);

                return $result['data'];
            });

            // Filter by search term if provided
            if (!empty($search)) {
                $allItems = array_filter($allItems, function($item) use ($search) {
                    $code = $item['hS_CODE'] ?? $item['code'] ?? '';
                    $description = $item['description'] ?? $item['itemDescription'] ?? '';

                    return stripos($code, $search) !== false ||
                           stripos($description, $search) !== false;
                });
            }

            // Reset array keys after filtering
            $allItems = array_values($allItems);

            // Calculate pagination
            $total = count($allItems);
            $offset = ($page - 1) * $limit;
            $hasMore = $offset + $limit < $total;

            // Get items for current page
            $items = array_slice($allItems, $offset, $limit);

            return response()->json([
                'success' => true,
                'data' => $items,
                'has_more' => $hasMore,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'cached' => Cache::has($cacheKey) // Indicate if data was served from cache
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching item description codes', [
                'search' => $search,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search item description codes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get units of measurement from FBR API
     */
    public function getUnitsOfMeasurement(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $result = $this->getFbrApiService()->getUnitsOfMeasurement($user->fbr_access_token);
        return response()->json($result);
    }

    /**
     * Get UOM based on HS code from FBR API
     */
    public function getUomByHsCode(Request $request)
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'hs_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'HS Code is required.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->getFbrApiService()->getHsCodesWithUom($user->fbr_access_token, $request->hs_code);

            Log::info('=== UOM BY HS CODE REQUEST ===', [
                'user_id' => $user->id,
                'hs_code' => $request->hs_code,
                'api_result_success' => $result['success'],
                'api_result_count' => $result['success'] ? count($result['data'] ?? []) : 0,
                'annexure_id_used' => 3,
                'result' => $result,
                'data_structure' => $result['success'] ? array_keys($result['data'][0] ?? []) : []
            ]);

            if ($result['success'] && $result['data']) {
                // Since we're passing a specific HS code, the API should return UOM data for that code
                $hsCode = $request->hs_code;
                $uomData = [];

                // Process the API response - it should contain UOM data for the specific HS code
                if (is_array($result['data'])) {
                    foreach ($result['data'] as $item) {
                        $uomId = $item['uoM_ID'] ?? $item['uom_id'] ?? $item['id'] ?? $item['uoM_Code'] ?? null;
                        $uomDesc = $item['description'] ?? $item['uoM_DESC'] ?? $item['desc'] ?? $item['itemDescription'] ?? $item['uom_desc'] ?? null;

                        if ($uomId !== null && $uomDesc !== null) {
                            $uomData[] = [
                                'uoM_ID' => $uomId,
                                'description' => $uomDesc
                            ];
                        }

                        // Handle nested UOM structure if exists
                        if (isset($item['uoms']) && is_array($item['uoms'])) {
                            foreach ($item['uoms'] as $uom) {
                                $subId = $uom['uoM_ID'] ?? $uom['id'] ?? $uom['uom_id'] ?? null;
                                $subDesc = $uom['description'] ?? $uom['uoM_DESC'] ?? $uom['desc'] ?? null;
                                if ($subId !== null && $subDesc !== null) {
                                    $uomData[] = [
                                        'uoM_ID' => $subId,
                                        'description' => $subDesc
                                    ];
                                }
                            }
                        }
                    }
                }

                // Remove duplicates based on UOM ID
                $uniqueUomData = [];
                $seenIds = [];
                foreach ($uomData as $uom) {
                    if (!in_array($uom['uoM_ID'], $seenIds)) {
                        $uniqueUomData[] = $uom;
                        $seenIds[] = $uom['uoM_ID'];
                    }
                }

                if (!empty($uniqueUomData)) {
                    Log::info('UOM data found for HS code', [
                        'hs_code' => $hsCode,
                        'uom_count' => count($uniqueUomData),
                        'uom_data' => $uniqueUomData
                    ]);

                    return response()->json([
                        'success' => true,
                        'data' => $uniqueUomData,
                        'hs_code' => $hsCode,
                        'message' => 'UOM data found for HS Code: ' . $hsCode
                    ]);
                } else {
                    // If no UOM data found, fall back to general UOM
                    Log::info('No UOM data found for specific HS code, falling back to general UOM', [
                        'hs_code' => $hsCode,
                        'api_response' => $result,
                        'processed_data' => $result['data'] ?? [],
                        'uom_data_extracted' => $uomData
                    ]);

                    $generalUomResult = $this->getFbrApiService()->getUnitsOfMeasurement($user->fbr_access_token);
                    return response()->json([
                        'success' => true,
                        'data' => $generalUomResult['data'] ?? [],
                        'hs_code' => $hsCode,
                        'fallback' => true,
                        'message' => 'No specific UOM found for this HS code, showing general UOM options'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to fetch UOM for HS code.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching UOM for HS code', [
                'user_id' => $user->id,
                'hs_code' => $request->hs_code,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch UOM for HS code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction type codes from FBR API
     */
    public function getTransactionTypeCodes(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $result = $this->getFbrApiService()->getTransactionTypeCodes($user->fbr_access_token);
        return response()->json($result);
    }

    /**
     * Get tax rates from FBR API
     */
    public function getTaxRates()
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $result = $this->getFbrApiService()->getTaxRates($user->fbr_access_token);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching tax rates', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tax rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get document type codes from FBR API
     */
    public function getDocumentTypeCodes()
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $result = $this->getFbrApiService()->getDocumentTypeIds($user->fbr_access_token);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching document type codes', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch document type codes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration type for a given NTN/CNIC
     */
    public function getRegistrationType(Request $request)
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'registration_no' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration number.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->getFbrApiService()->getRegistrationType(
                $user->fbr_access_token,
                $request->registration_no
            );

            // Handle FBR specific response format
            if ($result['success'] && isset($result['data'])) {
                $data = $result['data'];

                // Check if the response has the expected structure
                if (isset($data['statuscode'])) {
                    if ($data['statuscode'] === '00' || $data['statuscode'] === '01') {
                        return response()->json([
                            'success' => true,
                            'data' => [
                                'registration_no' => $data['REGISTRATION_NO'] ?? $request->registration_no,
                                'registration_type' => $data['REGISTRATION_TYPE'] ?? 'unregistered'
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid registration number or API error.'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unexpected response format from FBR API.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to fetch registration type.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching registration type', [
                'user_id' => $user->id,
                'registration_no' => $request->registration_no,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch registration type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sale type to rate based on date, transaction type, and province
     */
    public function getSaleTypeToRate(Request $request)
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'trans_type_id' => 'required|integer',
            'origination_supplier' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Format date to DD-MmmYYYY format as expected by FBR API (e.g., 24-Feb2024)
            $date = \Carbon\Carbon::parse($request->date)->format('d-MY');

            $result = $this->getFbrApiService()->getSaleTypeToRate(
                $user->fbr_access_token,
                $date,
                $request->trans_type_id,
                $request->origination_supplier
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to fetch rate information.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching sale type to rate', [
                'user_id' => $user->id,
                'date' => $request->date,
                'trans_type_id' => $request->trans_type_id,
                'origination_supplier' => $request->origination_supplier,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rate information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SRO schedule based on rate ID, date, and origination supplier
     */
    public function getSroSchedule(Request $request)
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rate_id' => 'required|integer',
            'date' => 'required|date',
            'origination_supplier_csv' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Format date to DD-MmmYYYY format as expected by FBR API (e.g., 04-Feb2024)
            $date = \Carbon\Carbon::parse($request->date)->format('d-MY');

            $result = $this->getFbrApiService()->getSroSchedule(
                $user->fbr_access_token,
                $request->rate_id,
                $date,
                $request->origination_supplier_csv
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to fetch SRO schedule information.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching SRO schedule', [
                'user_id' => $user->id,
                'rate_id' => $request->rate_id,
                'date' => $request->date,
                'origination_supplier_csv' => $request->origination_supplier_csv,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch SRO schedule information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SRO items based on date and SRO ID
     */
    public function getSroItem(Request $request)
    {
        $user = Auth::user();

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'sro_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Format date to YYYY-MM-DD format as expected by FBR API (e.g., 2025-03-25)
            $date = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

            $result = $this->getFbrApiService()->getSroItem(
                $user->fbr_access_token,
                $date,
                $request->sro_id
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to fetch SRO item information.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching SRO item', [
                'user_id' => $user->id,
                'date' => $request->date,
                'sro_id' => $request->sro_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch SRO item information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare invoice data according to FBR API specifications
     */
    private function prepareInvoiceData(array $requestData, array $referenceData): array
    {
        // Helper function to find province description by code
        $getProvinceDesc = function($code) use ($referenceData) {
            $provincesData = $referenceData['provinces'] ?? [];

            Log::info('=== PROVINCE MAPPING DEBUG ===', [
                'searching_for_code' => $code,
                'code_type' => gettype($code),
                'provinces_count' => count($provincesData),
                'provinces_sample' => array_slice($provincesData, 0, 3)
            ]);

            foreach ($provincesData as $province) {
                $provinceCode = $province['stateProvinceCode'] ?? '';
                $provinceDesc = $province['stateProvinceDesc'] ?? '';

                // Try both string and numeric comparison
                if ($provinceCode == $code || $provinceCode === (string)$code || $provinceCode === (int)$code) {
                    Log::info('Province mapping found', [
                        'code' => $code,
                        'matched_province_code' => $provinceCode,
                        'description' => $provinceDesc
                    ]);
                    return $provinceDesc ?: $code;
                }
            }

            Log::warning('Province mapping not found', [
                'code' => $code,
                'returning_original' => $code
            ]);
            return $code; // Return original if not found
        };

        // Helper function to find UoM description by ID
        $getUoMDesc = function($id) use ($referenceData) {
            $uoMsData = $referenceData['uoMs'] ?? [];

            Log::info('=== UOM MAPPING DEBUG ===', [
                'searching_for_id' => $id,
                'id_type' => gettype($id),
                'uoms_count' => count($uoMsData),
                'uoms_sample' => array_slice($uoMsData, 0, 3)
            ]);

            foreach ($uoMsData as $uom) {
                $uomId = $uom['uoM_ID'] ?? $uom['id'] ?? '';
                $uomDesc = $uom['uoM_DESC'] ?? $uom['description'] ?? '';

                // Try both string and numeric comparison
                if ($uomId == $id || $uomId === (string)$id || $uomId === (int)$id) {
                    Log::info('UoM mapping found', [
                        'id' => $id,
                        'matched_uom_id' => $uomId,
                        'description' => $uomDesc
                    ]);
                    return $uomDesc ?: $id;
                }
            }

            Log::warning('UoM mapping not found', [
                'id' => $id,
                'returning_original' => $id
            ]);
            return $id; // Return original if not found
        };

        // Helper function to find transaction type description by ID
        $getTransactionTypeDesc = function($id) use ($referenceData) {
            $transactionTypesData = $referenceData['transactionTypes'] ?? [];

            Log::info('=== TRANSACTION TYPE MAPPING DEBUG ===', [
                'searching_for_id' => $id,
                'id_type' => gettype($id),
                'transaction_types_count' => count($transactionTypesData),
                'transaction_types_sample' => array_slice($transactionTypesData, 0, 3)
            ]);

            foreach ($transactionTypesData as $type) {
                $typeId = $type['transactioN_TYPE_ID'] ?? '';
                $typeDesc = $type['transactioN_DESC'] ?? '';

                // Try both string and numeric comparison
                if ($typeId == $id || $typeId === (string)$id || $typeId === (int)$id) {
                    Log::info('Transaction type mapping found', [
                        'id' => $id,
                        'matched_type_id' => $typeId,
                        'description' => $typeDesc
                    ]);
                    return $typeDesc ?: $id;
                }
            }

            Log::warning('Transaction type mapping not found', [
                'id' => $id,
                'returning_original' => $id
            ]);
            return $id; // Return original if not found
        };

                // Prepare items array
        $items = [];
        foreach ($requestData['items'] as $item) {
            // Rate comes directly from FBR API response (rate_desc) and is already in correct format
            $rate = $item['rate'] ?? '';

            // Convert UoM ID to description
            $uoMLabel = $getUoMDesc($item['uoM'] ?? '');

            // Convert sale type ID to description
            $saleTypeLabel = $getTransactionTypeDesc($item['saleType'] ?? '');

            $items[] = [
                'hsCode' => $item['hsCode'],
                'productDescription' => $item['productDescription'],
                'rate' => $rate, // Rate from FBR API (e.g., "Rs 5" or "Exempt")
                'uom' => $uoMLabel, // Use description instead of ID
                'quantity' => (int) $item['quantity'],
                'totalValues' => (int) $item['totalValues'],
                'valueSalesExcludingST' => (int) $item['valueSalesExcludingST'],
                'fixedNotifiedValueOrRetailPrice' => (int) ($item['fixedNotifiedValueOrRetailPrice'] ?? 0),
                'salesTaxApplicable' => (int) $item['salesTaxApplicable'],
                'salesTaxWithheldAtSource' => (int) ($item['salesTaxWithheldAtSource'] ?? 0),
                'extraTax' => $item['extraTax'] ?? '',
                'furtherTax' => (int) ($item['furtherTax'] ?? 0),
                'sroScheduleNo' => $item['sroScheduleNo'] ?? '',
                'fedPayable' => (int) ($item['fedPayable'] ?? 0),
                'discount' => (int) ($item['discount'] ?? 0),
                'saleType' => $saleTypeLabel, // Use description instead of ID
                'sroItemSerialNo' => $item['sroItemSerialNo'] ?? ''
            ];
        }

        // Convert province codes to descriptions
        $sellerProvinceLabel = $getProvinceDesc($requestData['sellerProvince'] ?? '');
        $buyerProvinceLabel = $getProvinceDesc($requestData['buyerProvince'] ?? '');

        // Create flat structure to match expected payload
        $invoiceData = [
            'invoiceType' => $requestData['invoiceType'],
            'invoiceDate' => $requestData['invoiceDate'],
            'sellerNTNCNIC' => $requestData['sellerNTNCNIC'],
            'sellerBusinessName' => $requestData['sellerBusinessName'],
            'sellerProvince' => $sellerProvinceLabel, // Use description instead of code
            'sellerAddress' => $requestData['sellerAddress'],
            'buyerNTNCNIC' => $requestData['buyerNTNCNIC'],
            'buyerBusinessName' => $requestData['buyerBusinessName'],
            'buyerProvince' => $buyerProvinceLabel, // Use description instead of code
            'buyerAddress' => $requestData['buyerAddress'],
            'buyerRegistrationType' => $requestData['buyerRegistrationType'],
            'invoiceRefNo' => $requestData['invoiceRefNo'] ?? '',
            'items' => $items
        ];

        // Only include scenarioId if present in request data (sandbox mode)
        if (isset($requestData['scenarioId'])) {
            $invoiceData['scenarioId'] = $requestData['scenarioId'];
        }

        // Log the payload for debugging with ID to label mappings
        Log::info('Prepared Invoice Data Payload with Label Mappings', [
            'payload' => $invoiceData,
            'date_format' => $requestData['invoiceDate'],
            'items_count' => count($items),
            'label_mappings' => [
                'seller_province' => $requestData['sellerProvince'] . ' -> ' . $sellerProvinceLabel,
                'buyer_province' => $requestData['buyerProvince'] . ' -> ' . $buyerProvinceLabel,
                'sample_uom_mapping' => isset($items[0]) ? ($requestData['items'][0]['uoM'] ?? '') . ' -> ' . ($items[0]['uom'] ?? '') : 'No items',
                'sample_saletype_mapping' => isset($items[0]) ? ($requestData['items'][0]['saleType'] ?? '') . ' -> ' . ($items[0]['saleType'] ?? '') : 'No items'
            ]
        ]);

        return $invoiceData;
    }

    /**
     * Load reference data for mapping
     */
    private function loadReferenceData($accessToken)
    {
        $provinces = [];
        $hsCodes = [];
        $uoMs = [];
        $transactionTypes = [];

        try {
            $fbrService = $this->getFbrApiService();

            // Load provinces
            $provincesResult = $fbrService->getProvinceCodes($accessToken);
            if ($provincesResult['success']) {
                $provinces = $provincesResult['data'] ?? [];
            }

            // Load HS codes (Item Description Codes)
            $hsCodesResult = $fbrService->getItemDescriptionCodes($accessToken);
            if ($hsCodesResult['success']) {
                $hsCodes = $hsCodesResult['data'] ?? [];
            }

            // Load Units of Measurement
            $uoMsResult = $fbrService->getUnitsOfMeasurement($accessToken);
            if ($uoMsResult['success']) {
                $uoMs = $uoMsResult['data'] ?? [];
            }

            // Load Transaction Types
            $transactionTypesResult = $fbrService->getTransactionTypeCodes($accessToken);
            if ($transactionTypesResult['success']) {
                $transactionTypes = $transactionTypesResult['data'] ?? [];
            }

            // Debug log the loaded reference data
            Log::info('=== REFERENCE DATA LOADED ===', [
                'provinces_count' => count($provinces),
                'provinces_sample' => array_slice($provinces, 0, 2),
                'uoms_count' => count($uoMs),
                'uoms_sample' => array_slice($uoMs, 0, 2),
                'transaction_types_count' => count($transactionTypes),
                'transaction_types_sample' => array_slice($transactionTypes, 0, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading reference data', [
                'error' => $e->getMessage()
            ]);
        }

        return [
            'provinces' => $provinces,
            'hsCodes' => $hsCodes,
            'uoMs' => $uoMs,
            'transactionTypes' => $transactionTypes
        ];
    }

    /**
     * Handle unregistered supplier logic
     * When buyerRegistrationType is "Unregistered", set specific values for buyer fields
     */
    private function handleUnregisteredSupplier(array $invoiceData): array
    {
        if (isset($invoiceData['buyerRegistrationType']) &&
            $invoiceData['buyerRegistrationType'] === 'Unregistered') {

            // Keep original buyerNTNCNIC — FBR still requires a valid 7-digit NTN or 13-digit CNIC
            // Set fallback for business name and address if empty
            if (empty($invoiceData['buyerBusinessName'])) {
                $invoiceData['buyerBusinessName'] = 'Unregistered Supplies';
            }
            if (empty($invoiceData['buyerAddress'])) {
                $invoiceData['buyerAddress'] = '';
            }

            Log::info('Applied unregistered supplier logic', [
                'kept_ntn_cnic' => $invoiceData['buyerNTNCNIC'] ?? 'not set',
                'business_name' => $invoiceData['buyerBusinessName'] ?? 'not set',
                'address' => $invoiceData['buyerAddress'] ?? 'not set'
            ]);
        }

        return $invoiceData;
    }

    /**
     * Get buyers for autocomplete
     */
    public function getBuyers(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search', '');
        $limit = min((int) $request->query('limit', 10), 50);

        try {
$query = \App\Models\Buyer::query();
            if (!empty($search)) {
                $query->search($search);
            }

            $buyers = $query->orderBy('updated_at', 'desc')
                          ->limit($limit)
                          ->where('cid',auth()->user()->c_id)
                          ->get(['id', 'ntn_cnic', 'business_name', 'address', 'registration_type', 'province']);

            return response()->json([
                'success' => true,
                'data' => $buyers,
                'total' => $buyers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching buyers', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch buyers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search buyers by NTN/CNIC for autocomplete
     */
    public function searchBuyersByNtn(Request $request)
    {
        $user = Auth::user();
        $ntnSearch = $request->query('ntn', '');
        $limit = min((int) $request->query('limit', 10), 20);

        if (strlen($ntnSearch) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Please enter at least 2 characters'
            ]);
        }

        try {
            $buyers = Buyer::searchByNtnCnic($ntnSearch)
                          ->orderBy('updated_at', 'desc')
                          ->limit($limit)
                          ->where('cid',auth()->user()->c_id)
                          ->get(['id', 'ntn_cnic', 'business_name', 'address', 'registration_type', 'province']);

            return response()->json([
                'success' => true,
                'data' => $buyers,
                'total' => $buyers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching buyers by NTN', [
                'user_id' => $user->id,
                'ntn_search' => $ntnSearch,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search buyers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get buyer details by ID
     */
    public function getBuyerById(Request $request, $id)
    {
        $user = Auth::user();

        try {
            $buyer = $user->buyers()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $buyer
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching buyer by ID', [
                'user_id' => $user->id,
                'buyer_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Buyer not found'
            ], 404);
        }
    }
}