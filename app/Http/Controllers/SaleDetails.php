<?php

namespace App\Http\Controllers;

use App\Models\SaleInvoiceFbr;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\GeneralBilling;
use App\Models\ItemMaster;
use App\Models\ItemType;
use App\Models\ProductMaster;
use App\Models\Member as Party;
use App\Models\SaleDetail;
use App\Models\TRNDTL;
use App\Models\ErpParam; 
use App\Models\SaleDetail as SalesInvoice; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SaleDetails extends Controller
{
    /**
     * Display a listing of the resource.
     */
     // Controller
public function invoice($id)
{
    // dd("WORKING");
       $invoice = SaleInvoiceFbr::findOrFail($id);
    return view('SaleInvoice.invoice', compact('invoice'));

}

public function thirdSchedule($id)
{
    $invoice = SaleInvoiceFbr::with('user')->findOrFail($id);
    return view('SaleInvoice.third_schedule', compact('invoice'));
}

public function standardInvoice($id)
{
    $invoice = SaleInvoiceFbr::with('user')->findOrFail($id);
    return view('SaleInvoice.standard', compact('invoice'));
}

public function commercialPrint($id)
{
    $invoice = SaleInvoiceFbr::with('user')->findOrFail($id);
    return view('SaleInvoice.commercial_print', compact('invoice'));
}
    
public function index(Request $request)
{
    $query = SaleInvoiceFbr::query();

    // Apply date filters (by invoice_date, not upload date)
    if ($request->filled('start_date')) {
        $query->whereDate('invoice_date', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('invoice_date', '<=', $request->end_date);
    }

    // Apply bill number filter
    if ($request->filled('bill_no')) {
        $query->where('fbr_invoice_no', $request->bill_no);
    }

    // Apply client search filter
    if ($request->filled('client') && $request->client != '') {
        $query->where('buyer_business_name', 'like', '%' . $request->client . '%');
    }

    $salesInvoices = $query->where('cid', auth()->user()->c_id)->orderBy('invoice_date', 'asc')->orderBy('id', 'asc')->get();

    // Pass the filtered results + data for dropdowns (e.g., availableBillNumbers, parties)
    $availableBillNumbers = SaleInvoiceFbr::where('cid', auth()->user()->c_id)->distinct()->pluck('fbr_invoice_no');
    $parties = Party::all(); // adjust Party model name

    // Build supply register rows + GH 236 total (same as ReportController@SaleReport)
    $registerRows = [];
    $ghTotal = 0;

    foreach ($salesInvoices as $inv) {
        $items = is_string($inv->items) ? json_decode($inv->items, true) : ($inv->items ?? []);

        foreach ($items as $it) {
            $it = (array) $it;
            $qty = floatval($it['quantity'] ?? 0);
            $rate = floatval($it['rateValues'] ?? 0);
            $valueExcl = floatval($it['valueSalesExcludingST'] ?? ($rate * $qty));
            $stax = floatval($it['salesTaxApplicable'] ?? 0);
            $gh = floatval($it['ghAmount'] ?? 0);
            $ft = floatval($it['furtherTax'] ?? 0);
            $discount = floatval($it['discountAmount'] ?? ($it['discount'] ?? 0));
            $retailExcl = floatval($it['fixedNotifiedValueOrRetailPrice'] ?? 0);

            $ghTotal += $gh;

            $tradeExcl = $retailExcl > 0 ? ($retailExcl - $discount) : $valueExcl;

            $registerRows[] = [
                'date' => \Carbon\Carbon::parse($inv->invoice_date ?? now())->format('d-m-y'),
                'invoice_no' => $inv->fbr_invoice_no ?? '-',
                'customer' => $inv->buyer_business_name ?? '-',
                'product' => $it['productDescription'] ?? $it['product_description'] ?? '-',
                'qty' => $qty,
                'unit' => $it['uoMText'] ?? $it['unit_of_measure'] ?? $it['unit'] ?? '',
                'rate' => $rate,
                'value_excl' => $valueExcl,
                'stax_rate' => floatval($it['stax_per'] ?? 18),
                'stax_amt' => $stax,
                'value_inc' => $valueExcl + $stax,
                'retail_excl' => $retailExcl,
                'retail_tax' => $retailExcl * 0.18,
                'retail_incl' => $retailExcl * 1.18,
                'discount' => $discount,
                'trade_excl' => $tradeExcl,
                'trade_tax' => $stax,
                'trade_with_tax' => $tradeExcl + $stax,
                'us236' => $gh,
                'further_tax' => $ft,
                'amount' => $tradeExcl + $stax + $gh,
            ];
        }
    }

    $user = auth()->user();
    $company = \App\Models\Company::where('cid', $user->c_id)->first();
    $companyInfo = [
        'name' => $company->cname ?? ($salesInvoices->first()->seller_business_name ?? ''),
        'address' => $user->address ?? '',
        'ntn' => $user->cinc_ntn ?? '',
        'strn' => $user->strn ?? '',
    ];

    $invoiceDates = $salesInvoices->map(function ($inv) {
        return $inv->invoice_date ?: $inv->created_at;
    })->filter();

    $reportStart = $request->filled('start_date')
        ? $request->start_date
        : ($invoiceDates->min() ? \Carbon\Carbon::parse($invoiceDates->min())->format('Y-m-d') : '');
    $reportEnd = $request->filled('end_date')
        ? $request->end_date
        : ($invoiceDates->max() ? \Carbon\Carbon::parse($invoiceDates->max())->format('Y-m-d') : '');

    return view('SaleInvoice.index', compact('salesInvoices', 'availableBillNumbers', 'parties', 'registerRows', 'ghTotal', 'companyInfo', 'reportStart', 'reportEnd'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user=Auth::user();
         $accounts = \App\Models\AccountMaster::where('c_id',$user->c_id)->get();   // Party dropdown
        $products = \App\Models\ItemMaster::where('c_id',$user->c_id)->get();   // Product dropdown
         $saleAc = ErpParam::with('saleAcc')->where('c_id',$user->c_id)->first();
         $clients = \App\Models\Member::where('type','customer')->get();
        return view('SaleInvoice.list', compact('accounts', 'products','saleAc','clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
     $request->validate([
            // 'c_id' => 'required|exists:accounts,id',
            'entries' => 'required|array|min:1',
            'entries.*.prod_id' => 'required|exists:item_masters,id',
            'entries.*.rate' => 'required|numeric|min:0',
            'entries.*.qty' => 'required|numeric|min:1',
            'entries.*.stax_per' => 'required|numeric|min:0',
            'entries.*.stax_Amount' => 'required|numeric|min:0',
            's_account'=>"required|numeric",
            'entryParty'=>"required|numeric"
        ]);

    $now = Carbon::now();
    $userCId = Auth::user()->c_id;
// dd($request->entryParty);
    $entries = $request->input('entries');
    $accountId = $request->entryParty;
    $cashId = $request->s_account;
    $preparedBy = auth()->user()->name;

    $vno = SalesInvoice::max('v_no') + 1;
    $bill = SalesInvoice::max('bill_no') + 1;
    $totalCredit = 0;

   foreach ($entries as $entry) {
    $rate = $entry['rate'];
    $qty = $entry['qty'];
    $tax = $entry['stax_Amount'];
    $exclusive = $rate * $qty;
    $inclusive = $exclusive + $tax;

    // Insert SaleInvoice
    $sale = SalesInvoice::create([
        'prod_id' => $entry['prod_id'],
        'c_id' => $userCId,
        'rate' => $rate,
        'qty' => $qty,
        'stax_per' => $entry['stax_per'],
        'stax_Amount' => $tax,
        'created_at' => $now,
        'updated_at' => $now,
        'v_no'=>$vno,
        'fk_parties_id'=>$request->entryParty,
        'bill_no'=>$bill
    ]);

    // Calculate pre balance for credit account *before* this insert
    $preBal = DB::table('t_r_n_d_t_l_s')
        ->where('account_id', $cashId)
        ->where(function($query) use ($now) {
            $query->whereDate('date', '<', $now->toDateString())
                  ->orWhere(function($q) use ($now) {
                      $q->whereDate('date', '=', $now->toDateString())
                        ->whereTime('created_at', '<', $now->toTimeString());
                  });
        })
        ->selectRaw('IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0) AS pre_bal')
        ->value('pre_bal');

    // Insert TRNDTL with up-to-date pre_bal
    TRNDTL::create([
        'v_no' => $vno,
        'date' => $now->toDateString(),
        'description' => 'Sales Credit Entry',
        'account_id' => $cashId,
        'cash_id' => null,
        'preparedby' => $preparedBy,
        'debit' => 0,
        'credit' => $inclusive,
        'status' => 'unofficial',
        'v_type' => 'SIN',
        'r_id' => $sale->id,
        'pre_bal' => round($preBal, 2),
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}


    return redirect()->route('premiertax.sales.index')->with('success', 'Sale and credit accounting entry saved!');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($billNo)
{
    // Start a database transaction
    DB::beginTransaction();

    try {
        // Find all sales entries with this bill_no
        $sales = SalesInvoice::where('bill_no', $billNo)->get();
        
        // Delete related TRNDTL entries for each sale
        foreach ($sales as $sale) {
            TRNDTL::where('r_id', $sale->id)->delete();
        }
        
        // Delete all sales entries with this bill_no
        SalesInvoice::where('bill_no', $billNo)->delete();
        
        // Commit the transaction if all operations succeed
        DB::commit();
        
        return redirect()->back()
               ->with('success', 'Invoice #' . $billNo . ' and all related entries deleted successfully.');
               
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();
        
        return redirect()->back()
               ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
    }
}
}
