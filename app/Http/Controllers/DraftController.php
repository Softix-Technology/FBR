<?php

namespace App\Http\Controllers;

use App\Models\DraftInvoice;
use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Services\FbrApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class DraftController extends Controller
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = DraftInvoice::where('user_id', $user->id)->where( 'cid',$user->c_id);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('buyer_business_name', 'like', "%$search%")
                  ->orWhere('buyer_ntn_cnic', 'like', "%$search%")
                  ->orWhere('invoice_ref_no', 'like', "%$search%")
                ;
            });
        }
        $drafts = $query->orderByDesc('created_at')->paginate(12)->appends(['search' => $search]);
        return view('draftinvoicing.index', compact('drafts', 'user', 'search'));
    }

    public function indexStandard(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = DraftInvoice::where('user_id', $user->id)->where('cid', $user->c_id)->where('is_third_schedule', false)->where('is_commercial', false);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('buyer_business_name', 'like', "%$search%")
                  ->orWhere('buyer_ntn_cnic', 'like', "%$search%")
                  ->orWhere('invoice_ref_no', 'like', "%$search%");
            });
        }
        $drafts = $query->orderByDesc('created_at')->paginate(12)->appends(['search' => $search]);
        return view('draftinvoicing.index', compact('drafts', 'user', 'search'));
    }


public function saveDraft(Request $request)
{
    $user = Auth::user();

    // Validate request
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

    // Update or create buyer if NTN/CNIC is provided
//  if (!empty($validated['buyerNTNCNIC'])) {
//     $exist = Buyer::where('ntn_cnic', $validated['buyerNTNCNIC'])->first();

//     if (!$exist) {
//         $data = [
//             'cid' => $user->c_id,
//             'user_id'=> $user->id,
//             'ntn_cnic' => $validated['buyerNTNCNIC'],
//             'business_name' => $validated['buyerBusinessName'],
//             'address' => $validated['buyerAddress'] ?? null,
//             'registration_type' => $validated['buyerRegistrationType'],
//             'province' => $validated['buyerProvince'],
//         ];
//         Buyer::create($data);
//     }
// }


    // Prepare draft data
    $draftData = [
        'user_id' => $user->id,
        'cid' => $user->c_id,
        'title' => 'Draft Invoice - ' . now()->format('Y-m-d H:i:s'),
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
        'items' => $validated['items'], // Make sure DraftInvoice model casts 'items' as array
        'status' => 0, // 0 = incomplete draft
     'expense_col'=>$request->input('furtherexpense'),
    ];

    // Create draft invoice
    $draft = DraftInvoice::create($draftData);

    return response()->json([
        'success' => true,
        'message' => 'Draft saved successfully.',
        'draft' => $draft->fresh(),
    ]);
}


    // Show the edit form for a draft invoice
    public function edit($id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())->findOrFail($id);
        $user = Auth::user();

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

        return view('draftinvoicing.edit', compact(
            'draftInvoice',
            'user',
            'provinces',
            'hsCodes',
            'uoMs',
            'transactionTypes'
        ));
    }

    // Update the draft invoice
    public function update(Request $request, $id)
    {
        $draftInvoice = DraftInvoice::where('user_id', Auth::id())->findOrFail($id);
        // dd($request->items);
        $data = $request->all();
        // Map form fields to DB columns
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
        $draftInvoice->expense_col=$request->furtherexpense;
        $draftInvoice->save();

        return response()->json(['success' => true, 'message' => 'Draft updated successfully']);
    }

    // Submit the draft as a final invoice to FBR
    public function submit($id)
    {
        $user = Auth::user();
        $draftInvoice = DraftInvoice::where('user_id', $user->id)->findOrFail($id);

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        try {
            // Build FBR payload from draft data
            $invoiceData = [
                'invoiceType' => $draftInvoice->invoice_type,
                'invoiceDate' => $draftInvoice->invoice_date,
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

            // Normalize items: ensure numeric fields are numbers, remove rateValues
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
                unset($item['rateValues']);
            }
            unset($item);

            // Remove scenarioId in production
            if (!$user->use_sandbox) {
                unset($invoiceData['scenarioId']);
            }

            // Submit to FBR
            $result = $this->getFbrApiService()->postInvoiceData($user->fbr_access_token, $invoiceData);

            if ($result['success'] ?? false) {
                // Delete draft after successful submission
                $draftInvoice->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice submitted successfully to FBR',
                    'data' => $result
                ]);
            }

            Log::warning('Draft submission failed', [
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
            Log::error('Draft submission exception', [
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
  public function destroy($id)
{
    // Find the draft invoice for the logged-in user
    // dd("WORKING");
    $draftInvoice = DraftInvoice::where('user_id', Auth::id())
        ->where('id', $id)
        ->firstOrFail();

    // Delete the draft
    $draftInvoice->delete();

    // If API request, return JSON response
    if (request()->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Draft invoice deleted successfully'
        ]);
    }

    // Otherwise, redirect for web requests
    return redirect()
        ->route('drafts.index')
        ->with('success', 'Draft invoice deleted successfully.');
}

    // Bulk submit selected drafts to FBR
    public function bulkSubmit(Request $request)
    {
        $user = Auth::user();
        $ids = $request->input('ids', []);
        $type = $request->input('type', 'standard');

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No draft IDs provided'
            ], 400);
        }

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $query = DraftInvoice::where('user_id', $user->id)->whereIn('id', $ids);

        // Filter by type
        if ($type === 'third_schedule') {
            $query->where('is_third_schedule', true);
        } elseif ($type === 'commercial') {
            $query->where('is_commercial', true);
        } else {
            $query->where('is_third_schedule', false)->where('is_commercial', false);
        }

        $drafts = $query->get();

        if ($drafts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid drafts found for the selected type'
            ], 400);
        }

        $submitted = 0;
        $failed = 0;
        $failedDetails = [];

        foreach ($drafts as $draft) {
            try {
                // Build FBR payload from draft data
                $invoiceData = [
                    'invoiceType' => $draft->invoice_type,
                    'invoiceDate' => \Carbon\Carbon::parse($draft->invoice_date)->format('Y-m-d'),
                    'sellerNTNCNIC' => $draft->seller_ntn_cnic,
                    'sellerBusinessName' => $draft->seller_business_name,
                    'sellerProvince' => $draft->seller_province,
                    'sellerAddress' => $draft->seller_address ?? '',
                    'buyerNTNCNIC' => $draft->buyer_ntn_cnic ?? '',
                    'buyerBusinessName' => $draft->buyer_business_name ?? '',
                    'buyerProvince' => $draft->buyer_province ?? '',
                    'buyerAddress' => $draft->buyer_address ?? '',
                    'buyerRegistrationType' => $draft->buyer_registration_type ?? '',
                    'invoiceRefNo' => $draft->invoice_ref_no ?? '',
                    'scenarioId' => $draft->scenario_id ?? '',
                    'items' => $draft->items ?? [],
                ];

                // Normalize items
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
                    unset($item['rateValues']);
                }
                unset($item);

                // Remove scenarioId in production
                if (!$user->use_sandbox) {
                    unset($invoiceData['scenarioId']);
                }

                // Submit to FBR
                $result = $this->getFbrApiService()->postInvoiceData($user->fbr_access_token, $invoiceData);

                if ($result['success'] ?? false) {
                    $draft->delete();
                    $submitted++;
                } else {
                    $failed++;
                    $failedDetails[] = "Draft #{$draft->id}: " . ($result['message'] ?? 'Unknown error');
                }

                // Small delay to avoid rate limiting
                usleep(200000); // 0.2 seconds

            } catch (\Exception $e) {
                $failed++;
                $failedDetails[] = "Draft #{$draft->id}: " . $e->getMessage();
                Log::error('Bulk submit exception', [
                    'user_id' => $user->id,
                    'draft_id' => $draft->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => $submitted > 0,
            'message' => $submitted > 0 ? "Successfully submitted {$submitted} draft(s) to FBR" : 'No drafts submitted',
            'submitted_count' => $submitted,
            'failed_count' => $failed,
            'failed' => $failedDetails
        ]);
    }

    // Bulk submit ALL drafts of a type to FBR
    public function bulkSubmitAll(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'standard');

        if (!$user->fbr_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'FBR Access Token is required.'
            ], 400);
        }

        $query = DraftInvoice::where('user_id', $user->id);

        // Filter by type
        if ($type === 'third_schedule') {
            $query->where('is_third_schedule', true);
        } elseif ($type === 'commercial') {
            $query->where('is_commercial', true);
        } else {
            $query->where('is_third_schedule', false)->where('is_commercial', false);
        }

        $drafts = $query->get();

        if ($drafts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No drafts found to submit'
            ], 400);
        }

        $ids = $drafts->pluck('id')->toArray();
        $request->merge(['ids' => $ids]);
        return $this->bulkSubmit($request);
    }

}