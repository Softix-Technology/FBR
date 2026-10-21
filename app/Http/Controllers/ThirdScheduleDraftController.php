<?php

namespace App\Http\Controllers;

use App\Models\DraftInvoice;
use Illuminate\Http\Request;
use App\Services\FbrApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ThirdScheduleDraftController extends Controller
{
    private $fbrApiService;

    public function __construct(FbrApiService $fbrApiService)
    {
        $this->middleware('auth');
        $this->fbrApiService = $fbrApiService;
    }

    private function getFbrApiService()
    {
        $user = Auth::user();
        return $this->fbrApiService->setUser($user);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = DraftInvoice::where('user_id', $user->id)
            ->where('cid', $user->c_id)
            ->where('is_third_schedule', true);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('buyer_business_name', 'like', "%$search%")
                  ->orWhere('buyer_ntn_cnic', 'like', "%$search%")
                  ->orWhere('invoice_ref_no', 'like', "%$search%");
            });
        }
        $drafts = $query->orderByDesc('created_at')->paginate(12)->appends(['search' => $search]);
        return view('third_schedule_draft.index', compact('drafts', 'user', 'search'));
    }

    public function saveDraft(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'sellerNTNCNIC' => 'nullable|string|max:50',
            'sellerBusinessName' => 'required|string|max:255',
            'sellerProvince' => 'required|string|max:100',
            'sellerAddress' => 'nullable|string|max:500',
            'invoiceType' => 'required|string|max:100',
            'invoiceDate' => 'required|date',
            'invoiceRefNo' => 'nullable|string|max:100',
            'buyerNTNCNIC' => 'nullable|string|max:50',
            'buyerBusinessName' => 'required|string|max:255',
            'buyerProvince' => 'required|string|max:100',
            'buyerRegistrationType' => 'required|string|max:100',
            'buyerAddress' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.hsCode' => 'nullable|string|max:50',
            'items.*.productDescription' => 'nullable|string|max:500',
            'items.*.rate' => 'nullable|string|max:500',
            'items.*.saleType' => 'nullable|string|max:100',
            'items.*.uoM' => 'nullable|string|max:50',
            'items.*.quantity' => 'nullable|string|max:50',
            'items.*.totalValues' => 'nullable|string|max:50',
            'items.*.valueSalesExcludingST' => 'nullable|string|max:50',
            'items.*.furtherTax' => 'nullable',
            'items.*.salesTaxApplicable' => 'nullable|string|max:50',
            'items.*.sroScheduleNo' => 'nullable|string|max:255',
            'items.*.rateValues' => 'nullable|string|max:50',
            'items.*.fixedNotifiedValueOrRetailPrice' => 'nullable|string|max:50',
            'items.*.salesTaxWithheldAtSource' => 'nullable|string|max:50',
            'items.*.extraTax' => 'nullable|string|max:255',
            'items.*.fedPayable' => 'nullable|string|max:50',
            'items.*.discount' => 'nullable|string|max:50',
            'items.*.sroItemSerialNo' => 'nullable|string|max:255',
            'items.*.discountPercent' => 'nullable|string|max:50',
            'items.*.discountAmount' => 'nullable|string|max:50',
            'items.*.discountPercentInput' => 'nullable|string|max:50',
            'items.*.ghPercent' => 'nullable|string|max:50',
            'items.*.ghAmount' => 'nullable|string|max:50',
            'items.*.discountType' => 'nullable|string|max:50',
        ]);

        $draftData = [
            'user_id' => $user->id,
            'cid' => $user->c_id,
            'title' => '3rd Schedule Draft - ' . now()->format('Y-m-d H:i:s'),
            'notes' => null,
            'seller_ntn_cnic' => $validated['sellerNTNCNIC'] ?? null,
            'seller_business_name' => $validated['sellerBusinessName'],
            'seller_province' => $validated['sellerProvince'],
            'seller_address' => $validated['sellerAddress'] ?? null,
            'invoice_type' => $validated['invoiceType'],
            'invoice_date' => $validated['invoiceDate'],
            'invoice_ref_no' => $validated['invoiceRefNo'] ?? null,
            'buyer_ntn_cnic' => $validated['buyerNTNCNIC'] ?? null,
            'buyer_business_name' => $validated['buyerBusinessName'],
            'buyer_province' => $validated['buyerProvince'],
            'buyer_registration_type' => $validated['buyerRegistrationType'],
            'buyer_address' => $validated['buyerAddress'] ?? null,
            'items' => $validated['items'],
            'status' => 0,
            'expense_col' => $request->input('furtherexpense'),
            'is_third_schedule' => true,
        ];

        $draft = DraftInvoice::create($draftData);

        return response()->json([
            'success' => true,
            'message' => '3rd Schedule Draft saved successfully.',
            'draft' => $draft->fresh(),
        ]);
    }

    public function saveStandardDraft(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'sellerNTNCNIC' => 'nullable|string|max:50',
            'sellerBusinessName' => 'required|string|max:255',
            'sellerProvince' => 'required|string|max:100',
            'sellerAddress' => 'nullable|string|max:500',
            'invoiceType' => 'required|string|max:100',
            'invoiceDate' => 'required|date',
            'invoiceRefNo' => 'nullable|string|max:100',
            'buyerNTNCNIC' => 'nullable|string|max:50',
            'buyerBusinessName' => 'required|string|max:255',
            'buyerProvince' => 'required|string|max:100',
            'buyerRegistrationType' => 'required|string|max:100',
            'buyerAddress' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.hsCode' => 'nullable|string|max:50',
            'items.*.productDescription' => 'nullable|string|max:500',
            'items.*.rate' => 'nullable|string|max:500',
            'items.*.saleType' => 'nullable|string|max:100',
            'items.*.uoM' => 'nullable|string|max:50',
            'items.*.quantity' => 'nullable|string|max:50',
            'items.*.totalValues' => 'nullable|string|max:50',
            'items.*.valueSalesExcludingST' => 'nullable|string|max:50',
            'items.*.furtherTax' => 'nullable',
            'items.*.salesTaxApplicable' => 'nullable|string|max:50',
            'items.*.sroScheduleNo' => 'nullable|string|max:255',
            'items.*.rateValues' => 'nullable|string|max:50',
            'items.*.fixedNotifiedValueOrRetailPrice' => 'nullable|string|max:50',
            'items.*.salesTaxWithheldAtSource' => 'nullable|string|max:50',
            'items.*.extraTax' => 'nullable|string|max:255',
            'items.*.fedPayable' => 'nullable|string|max:50',
            'items.*.discount' => 'nullable|string|max:50',
            'items.*.sroItemSerialNo' => 'nullable|string|max:255',
            'items.*.discountPercent' => 'nullable|string|max:50',
            'items.*.discountAmount' => 'nullable|string|max:50',
            'items.*.discountPercentInput' => 'nullable|string|max:50',
            'items.*.ghPercent' => 'nullable|string|max:50',
            'items.*.ghAmount' => 'nullable|string|max:50',
            'items.*.discountType' => 'nullable|string|max:50',
        ]);

        $draftData = [
            'user_id' => $user->id,
            'cid' => $user->c_id,
            'title' => 'Standard Draft - ' . now()->format('Y-m-d H:i:s'),
            'notes' => null,
            'seller_ntn_cnic' => $validated['sellerNTNCNIC'] ?? '',
            'seller_business_name' => $validated['sellerBusinessName'],
            'seller_province' => $validated['sellerProvince'],
            'seller_address' => $validated['sellerAddress'] ?? '',
            'invoice_type' => $validated['invoiceType'],
            'invoice_date' => $validated['invoiceDate'],
            'invoice_ref_no' => $validated['invoiceRefNo'] ?? '',
            'buyer_ntn_cnic' => $validated['buyerNTNCNIC'] ?? '',
            'buyer_business_name' => $validated['buyerBusinessName'],
            'buyer_province' => $validated['buyerProvince'],
            'buyer_registration_type' => $validated['buyerRegistrationType'],
            'buyer_address' => $validated['buyerAddress'] ?? '',
            'items' => $validated['items'],
            'status' => 0,
            'expense_col' => $request->input('furtherexpense'),
            'is_third_schedule' => false,
        ];

        $draft = DraftInvoice::create($draftData);

        return response()->json([
            'success' => true,
            'message' => 'Standard Draft saved successfully.',
            'draft' => $draft->fresh(),
        ]);
    }

    public function indexCommercial(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = DraftInvoice::where('user_id', $user->id)
            ->where('cid', $user->c_id)
            ->where('is_commercial', true);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('buyer_business_name', 'like', "%$search%")
                  ->orWhere('buyer_ntn_cnic', 'like', "%$search%")
                  ->orWhere('invoice_ref_no', 'like', "%$search%");
            });
        }
        $drafts = $query->orderByDesc('created_at')->paginate(12)->appends(['search' => $search]);
        return view('commercial_draft.index', compact('drafts', 'user', 'search'));
    }

    public function saveCommercialDraft(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'sellerNTNCNIC' => 'nullable|string|max:50',
            'sellerBusinessName' => 'required|string|max:255',
            'sellerProvince' => 'required|string|max:100',
            'sellerAddress' => 'nullable|string|max:500',
            'invoiceType' => 'required|string|max:100',
            'invoiceDate' => 'required|date',
            'invoiceRefNo' => 'nullable|string|max:100',
            'buyerNTNCNIC' => 'nullable|string|max:50',
            'buyerBusinessName' => 'required|string|max:255',
            'buyerProvince' => 'required|string|max:100',
            'buyerRegistrationType' => 'required|string|max:100',
            'buyerAddress' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.hsCode' => 'nullable|string|max:50',
            'items.*.productDescription' => 'nullable|string|max:500',
            'items.*.rate' => 'nullable|string|max:500',
            'items.*.saleType' => 'nullable|string|max:100',
            'items.*.uoM' => 'nullable|string|max:50',
            'items.*.quantity' => 'nullable|string|max:50',
            'items.*.totalValues' => 'nullable|string|max:50',
            'items.*.valueSalesExcludingST' => 'nullable|string|max:50',
            'items.*.furtherTax' => 'nullable',
            'items.*.salesTaxApplicable' => 'nullable|string|max:50',
            'items.*.sroScheduleNo' => 'nullable|string|max:255',
            'items.*.rateValues' => 'nullable|string|max:50',
            'items.*.fixedNotifiedValueOrRetailPrice' => 'nullable|string|max:50',
            'items.*.salesTaxWithheldAtSource' => 'nullable|string|max:50',
            'items.*.extraTax' => 'nullable|string|max:255',
            'items.*.fedPayable' => 'nullable|string|max:50',
            'items.*.discount' => 'nullable|string|max:50',
            'items.*.sroItemSerialNo' => 'nullable|string|max:255',
            'items.*.discountPercent' => 'nullable|string|max:50',
            'items.*.discountAmount' => 'nullable|string|max:50',
            'items.*.discountPercentInput' => 'nullable|string|max:50',
            'items.*.ghPercent' => 'nullable|string|max:50',
            'items.*.ghAmount' => 'nullable|string|max:50',
            'items.*.discountType' => 'nullable|string|max:50',
        ]);

        $draftData = [
            'user_id' => $user->id,
            'cid' => $user->c_id,
            'title' => 'Commercial Draft - ' . now()->format('Y-m-d H:i:s'),
            'notes' => null,
            'seller_ntn_cnic' => $validated['sellerNTNCNIC'] ?? '',
            'seller_business_name' => $validated['sellerBusinessName'],
            'seller_province' => $validated['sellerProvince'],
            'seller_address' => $validated['sellerAddress'] ?? '',
            'invoice_type' => $validated['invoiceType'],
            'invoice_date' => $validated['invoiceDate'],
            'invoice_ref_no' => $validated['invoiceRefNo'] ?? '',
            'buyer_ntn_cnic' => $validated['buyerNTNCNIC'] ?? '',
            'buyer_business_name' => $validated['buyerBusinessName'],
            'buyer_province' => $validated['buyerProvince'],
            'buyer_registration_type' => $validated['buyerRegistrationType'],
            'buyer_address' => $validated['buyerAddress'] ?? '',
            'items' => $validated['items'],
            'status' => 0,
            'expense_col' => $request->input('furtherexpense'),
            'is_commercial' => true,
        ];

        $draft = DraftInvoice::create($draftData);

        return response()->json([
            'success' => true,
            'message' => 'Commercial Draft saved successfully.',
            'draft' => $draft->fresh(),
        ]);
    }

    public function edit($id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())
            ->where('is_third_schedule', true)
            ->findOrFail($id);
        $user = Auth::user();

        $provinces = [];
        $hsCodes = [];
        $uoMs = [];
        $transactionTypes = [];
        try {
            $fbrService = $this->getFbrApiService();

            $provincesResult = $fbrService->getProvinceCodes($user->fbr_access_token);
            if ($provincesResult['success']) {
                $provinces = $provincesResult['data'] ?? [];
            }

            $hsCodesResult = $fbrService->getItemDescriptionCodes($user->fbr_access_token);
            if ($hsCodesResult['success']) {
                $hsCodes = $hsCodesResult['data'] ?? [];
            }

            $uoMsResult = $fbrService->getUnitsOfMeasurement($user->fbr_access_token);
            if ($uoMsResult['success']) {
                $uoMs = $uoMsResult['data'] ?? [];
            }

            $transactionTypesResult = $fbrService->getTransactionTypeCodes($user->fbr_access_token);
            if ($transactionTypesResult['success']) {
                $transactionTypes = $transactionTypesResult['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading FBR data for 3rd Schedule draft', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return view('draftinvoicing.edit', compact(
            'draftInvoice',
            'user',
            'provinces',
            'hsCodes',
            'uoMs',
            'transactionTypes'
        ));
    }

    public function update(Request $request, $id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())
            ->where('is_third_schedule', true)
            ->findOrFail($id);

        $data = $request->all();
        $draftInvoice->title = $data['title'] ?? null;
        $draftInvoice->notes = $data['notes'] ?? null;
        $draftInvoice->seller_ntn_cnic = $data['sellerNTNCNIC'] ?? null;
        $draftInvoice->seller_business_name = $data['sellerBusinessName'] ?? null;
        $draftInvoice->seller_province = $data['sellerProvince'] ?? null;
        $draftInvoice->seller_address = $data['sellerAddress'] ?? null;
        $draftInvoice->invoice_type = $data['invoiceType'] ?? null;
        $draftInvoice->invoice_date = $data['invoiceDate'] ?? null;
        $draftInvoice->invoice_ref_no = $data['invoiceRefNo'] ?? null;
        $draftInvoice->scenario_id = $data['scenarioId'] ?? null;
        $draftInvoice->buyer_ntn_cnic = $data['buyerNTNCNIC'] ?? null;
        $draftInvoice->buyer_business_name = $data['buyerBusinessName'] ?? null;
        $draftInvoice->buyer_province = $data['buyerProvince'] ?? null;
        $draftInvoice->buyer_registration_type = $data['buyerRegistrationType'] ?? null;
        $draftInvoice->buyer_address = $data['buyerAddress'] ?? null;
        $draftInvoice->items = $request->items;
        $draftInvoice->expense_col = $request->furtherexpense;
        $draftInvoice->save();

        return response()->json(['success' => true, 'message' => '3rd Schedule Draft updated successfully']);
    }

    public function editCommercial($id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())
            ->where('is_commercial', true)
            ->findOrFail($id);
        $user = Auth::user();

        $provinces = [];
        $hsCodes = [];
        $uoMs = [];
        $transactionTypes = [];
        try {
            $fbrService = $this->getFbrApiService();

            $provincesResult = $fbrService->getProvinceCodes($user->fbr_access_token);
            if ($provincesResult['success']) {
                $provinces = $provincesResult['data'] ?? [];
            }

            $hsCodesResult = $fbrService->getItemDescriptionCodes($user->fbr_access_token);
            if ($hsCodesResult['success']) {
                $hsCodes = $hsCodesResult['data'] ?? [];
            }

            $uoMsResult = $fbrService->getUnitsOfMeasurement($user->fbr_access_token);
            if ($uoMsResult['success']) {
                $uoMs = $uoMsResult['data'] ?? [];
            }

            $transactionTypesResult = $fbrService->getTransactionTypeCodes($user->fbr_access_token);
            if ($transactionTypesResult['success']) {
                $transactionTypes = $transactionTypesResult['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading FBR data for Commercial draft', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return view('draftinvoicing.edit', compact(
            'draftInvoice',
            'user',
            'provinces',
            'hsCodes',
            'uoMs',
            'transactionTypes'
        ));
    }

    public function updateCommercial(Request $request, $id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())
            ->where('is_commercial', true)
            ->findOrFail($id);

        $data = $request->all();
        $draftInvoice->title = $data['title'] ?? null;
        $draftInvoice->notes = $data['notes'] ?? null;
        $draftInvoice->seller_ntn_cnic = $data['sellerNTNCNIC'] ?? null;
        $draftInvoice->seller_business_name = $data['sellerBusinessName'] ?? null;
        $draftInvoice->seller_province = $data['sellerProvince'] ?? null;
        $draftInvoice->seller_address = $data['sellerAddress'] ?? null;
        $draftInvoice->invoice_type = $data['invoiceType'] ?? null;
        $draftInvoice->invoice_date = $data['invoiceDate'] ?? null;
        $draftInvoice->invoice_ref_no = $data['invoiceRefNo'] ?? null;
        $draftInvoice->scenario_id = $data['scenarioId'] ?? null;
        $draftInvoice->buyer_ntn_cnic = $data['buyerNTNCNIC'] ?? null;
        $draftInvoice->buyer_business_name = $data['buyerBusinessName'] ?? null;
        $draftInvoice->buyer_province = $data['buyerProvince'] ?? null;
        $draftInvoice->buyer_registration_type = $data['buyerRegistrationType'] ?? null;
        $draftInvoice->buyer_address = $data['buyerAddress'] ?? null;
        $draftInvoice->items = $request->items;
        $draftInvoice->expense_col = $request->furtherexpense;
        $draftInvoice->save();

        return response()->json(['success' => true, 'message' => 'Commercial Draft updated successfully']);
    }

    public function submit($id)
    {
        $user = Auth::user();
        $draftInvoice = DraftInvoice::where('user_id', $user->id)
            ->where('is_third_schedule', true)
            ->findOrFail($id);

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $invoiceData = [
                'invoiceType' => $draftInvoice->invoice_type,
                'invoiceDate' => \Carbon\Carbon::parse($draftInvoice->invoice_date)->format('Y-m-d'),
                'sellerNTNCNIC' => $draftInvoice->seller_ntn_cnic,
                'sellerBusinessName' => $draftInvoice->seller_business_name,
                'sellerProvince' => $draftInvoice->seller_province,
                'sellerAddress' => $draftInvoice->seller_address ?? '',
                'buyerNTNCNIC' => $draftInvoice->buyer_ntn_cnic ?? '',
                'buyerBusinessName' => $draftInvoice->buyer_business_name ?? '',
                'buyerProvince' => $draftInvoice->buyer_province ?? '',
                'buyerAddress' => $draftInvoice->buyer_address ?? '',
                'buyerRegistrationType' => $draftInvoice->buyer_registration_type ?? '',
                'invoiceRefNo' => $draftInvoice->invoice_ref_no ?? '',
                'scenarioId' => $draftInvoice->scenario_id ?? '',
                'items' => $draftInvoice->items ?? [],
            ];

            foreach ($invoiceData['items'] as &$item) {
                if (isset($item['quantity'])) {
                    $item['quantity'] = round((float) $item['quantity'], 4);
                }
                foreach (['totalValues', 'valueSalesExcludingST', 'salesTaxApplicable',
                         'fixedNotifiedValueOrRetailPrice', 'salesTaxWithheldAtSource',
                         'furtherTax', 'fedPayable', 'discount'] as $numField) {
                    if (isset($item[$numField])) {
                        $item[$numField] = round((float) $item[$numField], 2);
                    }
                }
                if (isset($item['rate'])) {
                    $rateParsed = json_decode($item['rate'], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($rateParsed['rate_desc'])) {
                        $item['rate'] = $rateParsed['rate_desc'];
                    }
                }
                unset($item['rateValues']);
            }

            if (!$user->use_sandbox) {
                unset($invoiceData['scenarioId']);
            }

            $result = $this->getFbrApiService()->postInvoiceData($user->fbr_access_token, $invoiceData);

            if ($result['success'] ?? false) {
                if (is_null($result['data'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'FBR returned empty response. Please try again.',
                    ], 502);
                }

                $validationResponse = $result['data']['validationResponse'] ?? null;
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
                    $message = $errorDetails ?: ($validationResponse['error'] ?? 'FBR validation failed');
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 400);
                }

                $draftInvoice->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice submitted successfully to FBR',
                    'data' => $result
                ]);
            }

            Log::warning('3rd Schedule draft submission failed', [
                'user_id' => $user->id,
                'draft_id' => $id,
                'error' => $result['message'] ?? 'Unknown error'
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invoice submission failed',
                'errors' => $result['errors'] ?? null
            ], $result['status_code'] ?? 400);

        } catch (\Exception $e) {
            Log::error('3rd Schedule draft submission exception', [
                'user_id' => $user->id,
                'draft_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitCommercial($id)
    {
        $user = Auth::user();
        $draftInvoice = DraftInvoice::where('user_id', $user->id)
            ->where('is_commercial', true)
            ->findOrFail($id);

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            $invoiceData = [
                'invoiceType' => $draftInvoice->invoice_type,
                'invoiceDate' => \Carbon\Carbon::parse($draftInvoice->invoice_date)->format('Y-m-d'),
                'sellerNTNCNIC' => $draftInvoice->seller_ntn_cnic,
                'sellerBusinessName' => $draftInvoice->seller_business_name,
                'sellerProvince' => $draftInvoice->seller_province,
                'sellerAddress' => $draftInvoice->seller_address ?? '',
                'buyerNTNCNIC' => $draftInvoice->buyer_ntn_cnic ?? '',
                'buyerBusinessName' => $draftInvoice->buyer_business_name ?? '',
                'buyerProvince' => $draftInvoice->buyer_province ?? '',
                'buyerAddress' => $draftInvoice->buyer_address ?? '',
                'buyerRegistrationType' => $draftInvoice->buyer_registration_type ?? '',
                'invoiceRefNo' => $draftInvoice->invoice_ref_no ?? '',
                'scenarioId' => $draftInvoice->scenario_id ?? '',
                'items' => $draftInvoice->items ?? [],
            ];

            foreach ($invoiceData['items'] as &$item) {
                if (isset($item['quantity'])) {
                    $item['quantity'] = round((float) $item['quantity'], 4);
                }
                foreach (['totalValues', 'valueSalesExcludingST', 'salesTaxApplicable',
                         'fixedNotifiedValueOrRetailPrice', 'salesTaxWithheldAtSource',
                         'furtherTax', 'fedPayable', 'discount'] as $numField) {
                    if (isset($item[$numField])) {
                        $item[$numField] = round((float) $item[$numField], 2);
                    }
                }
                if (isset($item['rate'])) {
                    $rateParsed = json_decode($item['rate'], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($rateParsed['rate_desc'])) {
                        $item['rate'] = $rateParsed['rate_desc'];
                    }
                }
                unset($item['rateValues']);
            }

            if (!$user->use_sandbox) {
                unset($invoiceData['scenarioId']);
            }

            $result = $this->getFbrApiService()->postInvoiceData($user->fbr_access_token, $invoiceData);

            if ($result['success'] ?? false) {
                if (is_null($result['data'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'FBR returned empty response. Please try again.',
                    ], 502);
                }

                $validationResponse = $result['data']['validationResponse'] ?? null;
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
                    $message = $errorDetails ?: ($validationResponse['error'] ?? 'FBR validation failed');
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 400);
                }

                $draftInvoice->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice submitted successfully to FBR',
                    'data' => $result
                ]);
            }

            Log::warning('Commercial draft submission failed', [
                'user_id' => $user->id,
                'draft_id' => $id,
                'error' => $result['message'] ?? 'Unknown error'
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invoice submission failed',
                'errors' => $result['errors'] ?? null
            ], $result['status_code'] ?? 400);

        } catch (\Exception $e) {
            Log::error('Commercial draft submission exception', [
                'user_id' => $user->id,
                'draft_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyCommercial($id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())
            ->where('is_commercial', true)
            ->where('id', $id)
            ->firstOrFail();

        $draftInvoice->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Commercial draft deleted successfully'
            ]);
        }

        return redirect()
            ->route('commercial.drafts.index')
            ->with('success', 'Commercial draft deleted successfully.');
    }

    public function destroy($id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())
            ->where('is_third_schedule', true)
            ->where('id', $id)
            ->firstOrFail();

        $draftInvoice->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '3rd Schedule draft deleted successfully'
            ]);
        }

        return redirect()
            ->route('third.schedule.drafts.index')
            ->with('success', '3rd Schedule draft deleted successfully.');
    }
}
