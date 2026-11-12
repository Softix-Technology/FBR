<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>3rd Schedule Draft - {{ (($invoice->cid ?? null) == 12 || optional($invoice->user)->c_id == 12) ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? '') }}</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; background: #e9e9e9; margin: 0; padding: 30px 0; }
  .page { width: 100%; max-width: 1200px; margin: 0 auto; background: #fff; padding: 30px 40px; box-shadow: 0 0 8px rgba(0,0,0,0.2); }
  .company-name { text-align: center; font-size: 28px; font-weight: bold; margin: 0 0 6px 0; }
  .company-address { text-align: center; font-size: 14px; margin: 0; line-height: 1.4; }
  .stn-ntn-row { display: flex; justify-content: space-between; font-size: 14px; margin: 18px 0 10px 0; flex-wrap: wrap; gap: 6px; }
  .stn-ntn-row b { font-weight: bold; }
  .title-bar { background: #555; color: #fff; padding: 10px 18px; font-weight: bold; font-size: 22px; text-align: center; margin: 20px 0; }
  .buyer-meta-row { display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
  .buyer-info { font-size: 14px; line-height: 1.5; }
  .buyer-info .name { font-weight: bold; }
  .buyer-info b { font-weight: bold; }
  .invoice-meta { font-size: 14px; line-height: 1.6; }
  .invoice-meta b { font-weight: bold; }
  table.items { width: 100%; border-collapse: collapse; font-size: 11px; }
  table.items th, table.items td { border: 1px solid #000; padding: 6px 8px; }
  table.items th { background: #e0e0e0; font-weight: bold; text-align: center; font-size: 10px; }
  table.items td { text-align: right; }
  table.items td.desc { text-align: left; }
  table.items thead tr.grp th { font-size: 12px; }
  table.items thead tr.sub th { font-size: 10px; }
  .totals-block { width: 100%; margin-top: 8px; border-collapse: collapse; font-size: 13px; }
  .totals-block td { padding: 6px 10px; border: 1px solid #000; }
  .totals-block td.t-label { text-align: left; font-weight: bold; }
  .totals-block td.t-val { text-align: right; width: 180px; }
  .totals-block td.grand { font-size: 16px; font-weight: bold; }
  .sign-block { margin-top: 30px; }
  .sign-block .box { border: 1px solid #000; padding: 12px 18px; display: inline-block; font-size: 13px; font-weight: 600; }
  .note { text-align: center; font-size: 11px; margin-top: 30px; font-style: italic; }
  .print-btn { position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background: #2c3e50; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; z-index: 1000; }
  .download-btn {
    position: fixed;
    top: 30px;
    right: 30px;
    padding: 10px 22px;
    background: #1e40af;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    z-index: 1000;
    transition: all 0.2s ease;
  }
  .download-btn:hover {
    background: #1d4ed8;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
  }
  @media print { body { background: #fff; padding: 0; } .page { box-shadow: none; padding: 30px 40px; } .print-btn, .download-btn { display: none !important; } }
  @media (max-width: 600px) { .page { padding: 15px; } .company-name { font-size: 20px; } table.items { font-size: 9px; } table.items th, table.items td { padding: 3px 4px; } }
</style>
</head>
<body>
  <button id="downloadPdfBtn" class="download-btn" onclick="downloadPDF()">
    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
    </svg>
    Download PDF
  </button>
  <button class="print-btn" onclick="window.print()">Print Invoice</button>
  <div class="page">
    @php
      $isJinnah = (($invoice->cid ?? null) == 12 || optional($invoice->user)->c_id == 12);
      $displaySellerName = $isJinnah ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? '');
      $displaySellerNtn = $isJinnah ? ($invoice->user->cinc_ntn ?? '8167774') : ($invoice->seller_ntn_cnic ?? '');
    @endphp
    <p class="company-name">{{ $displaySellerName }}</p>
    <p class="company-address">{{ $invoice->seller_address ?? '' }}<br>{{ $invoice->seller_province ?? '' }}</p>
    <div class="stn-ntn-row">
      <div><b>{{ $isJinnah ? 'STRN' : 'STN' }} :</b> {{ $invoice->user->strn ?? $displaySellerNtn }}</div>
      <div><b>NTN :</b> {{ $displaySellerNtn }}</div>
    </div>
    <div class="title-bar">3rd Schedule Draft — {{ $invoice->buyer_business_name ?? '' }}</div>
    <div class="buyer-meta-row">
      <div class="buyer-info">
        <div class="name">{{ $invoice->buyer_business_name ?? '' }}</div>
        <div>{{ $invoice->buyer_address ?? '' }}</div>
        <div>{{ $invoice->buyer_province ?? '' }}</div>
        <div><b>NTN</b> {{ $invoice->buyer_ntn_cnic ?? '' }}</div>
      </div>
      <div class="invoice-meta">
        <div><b>{{ $isJinnah ? 'No.' : 'Draft No.' }}</b> #{{ $invoice->invoice_ref_no ?? $invoice->id }}</div>
        <div><b>Date</b> {{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('d/m/Y') }}</div>
      </div>
    </div>
    @php
      $items = is_string($invoice->items) ? json_decode($invoice->items, true) : ($invoice->items ?? []);
      $itemsCollection = collect($items);
      $grandQty = 0; $grandRetailExcl = 0; $grandRetailTax = 0; $grandRetailIncl = 0;
      $grandDiscount = 0; $grandTradeExcl = 0; $grandTradeTax = 0; $grandTradeWithTax = 0;
      $grandUS236 = 0; $grandAmount = 0; $grandFurtherTax = 0;
    @endphp
    <table class="items" id="itemsTable">
      <thead>
        <tr class="grp">
          <th rowspan="2">Sr. #</th>
          <th rowspan="2">Description</th>
          <th rowspan="2">Qty</th>
          <th colspan="3">Retail's Price</th>
          <th colspan="5">Trade's Price</th>
          <th rowspan="2">Amount (Rs.)</th>
        </tr>
        <tr class="sub">
          <th>Exclusive Value</th>
          <th>Sales Tax 18%</th>
          <th>Inclusive Value</th>
          <th>Discount</th>
          <th>Discounted Value Excl. GST</th>
          <th>Sales Tax 18%</th>
          <th>Value With Sales Tax</th>
          <th>U/S 236 G/H</th>
        </tr>
      </thead>
      <tbody>
        @foreach($itemsCollection as $index => $item)
        @php
          $itemArray = is_array($item) ? $item : (array) $item;
          $qty = floatval($itemArray['quantity'] ?? 0);
          $fixedNotified = floatval($itemArray['fixedNotifiedValueOrRetailPrice'] ?? 0);
          $discountPerUnit = floatval($itemArray['discountAmount'] ?? 0);
          if ($discountPerUnit == 0) { $discountPerUnit = floatval($itemArray['discount'] ?? 0); }
          $ghPerUnit = floatval($itemArray['ghAmount'] ?? 0);
          $furtherTax = floatval($itemArray['furtherTax'] ?? 0);
          $isThirdSchedule = ($fixedNotified > 0);
          if ($isThirdSchedule) {
            $retailExcl = $fixedNotified;
            $retailTax = $fixedNotified * 0.18;
            $retailIncl = $retailExcl + $retailTax;
            $discount = $discountPerUnit;
            $tradeExcl = $fixedNotified - $discount;
            $tradeTax = $retailTax;
            $tradeWithTax = $tradeExcl + $tradeTax;
            $us236 = $ghPerUnit;
            $amount = $tradeWithTax + $us236;
          } else {
            $retailExcl = floatval($itemArray['rateValues'] ?? 0);
            $retailTax = floatval($itemArray['salesTaxApplicable'] ?? 0);
            if ($qty > 0) { $retailTax = $retailTax / $qty; }
            $retailIncl = $retailExcl + $retailTax;
            $discount = floatval($itemArray['discount'] ?? 0);
            if ($qty > 0 && $discount > 0) { $discount = $discount / $qty; }
            $tradeExcl = floatval($itemArray['valueSalesExcludingST'] ?? $retailExcl);
            if ($qty > 0) { $tradeExcl = $tradeExcl / $qty; }
            $tradeTax = $retailTax;
            $tradeWithTax = $tradeExcl + $tradeTax;
            $us236 = floatval($itemArray['ghAmount'] ?? 0);
            if ($qty > 0 && $us236 > 0) { $us236 = $us236 / $qty; }
            $amount = $tradeWithTax + $us236;
          }
          $grandQty += $qty; $grandRetailExcl += $retailExcl; $grandRetailTax += $retailTax;
          $grandRetailIncl += $retailIncl; $grandDiscount += $discount; $grandTradeExcl += $tradeExcl;
          $grandTradeTax += $tradeTax; $grandTradeWithTax += $tradeWithTax; $grandUS236 += $us236;
          $grandAmount += $amount; $grandFurtherTax += $furtherTax;
        @endphp
        <tr>
          <td>{{ $index + 1 }}</td>
          <td class="desc">{{ $itemArray['productDescription'] ?? '-' }}</td>
          <td>{{ number_format($qty, $isJinnah ? 2 : 3) }}</td>
          <td class="num">{{ number_format($retailExcl, 0) }}</td>
          <td class="num">{{ number_format($retailTax, 0) }}</td>
          <td class="num">{{ number_format($retailIncl, 0) }}</td>
          <td class="num">{{ $discount > 0 ? number_format($discount, 0) : '-' }}</td>
          <td class="num">{{ number_format($tradeExcl, 0) }}</td>
          <td class="num">{{ number_format($tradeTax, 0) }}</td>
          <td class="num">{{ number_format($tradeWithTax, 0) }}</td>
          <td class="num">{{ $us236 > 0 ? number_format($us236, 0) : '-' }}</td>
          <td class="num">{{ number_format($amount, 0) }}</td>
        </tr>
        @endforeach
        @php $extraAmount = floatval($invoice->expense_col ?? 0); @endphp
        @if($extraAmount > 0 && $itemsCollection->count() > 0)
        <tr>
          <td>{{ $itemsCollection->count() + 1 }}</td>
          <td class="desc">Transportation Charges</td>
          <td>-</td>
          <td class="num">-</td><td class="num">-</td><td class="num">-</td>
          <td class="num">-</td><td class="num">-</td><td class="num">-</td>
          <td class="num">-</td><td class="num">-</td>
          <td class="num">{{ number_format($extraAmount, 0) }}</td>
        </tr>
        @php $grandAmount += $extraAmount; @endphp
        @endif
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2">Total Value Exclusive of Sales Tax</td>
          <td class="num">{{ number_format($grandQty, $isJinnah ? 2 : 3) }}</td>
          <td class="num">{{ number_format($grandRetailExcl, 0) }}</td>
          <td class="num">{{ number_format($grandRetailTax, 0) }}</td>
          <td class="num">{{ number_format($grandRetailIncl, 0) }}</td>
          <td class="num">{{ number_format($grandDiscount, 0) }}</td>
          <td class="num">{{ number_format($grandTradeExcl, 0) }}</td>
          <td class="num">{{ number_format($grandTradeTax, 0) }}</td>
          <td class="num">{{ number_format($grandTradeWithTax, 0) }}</td>
          <td class="num">{{ number_format($grandUS236, 0) }}</td>
          <td class="num">{{ number_format($grandAmount, 0) }}</td>
        </tr>
      </tfoot>
    </table>
    <table class="totals-block">
      <tr>
        <td class="t-label">(+) Further Tax Payable (if applicable)</td>
        <td class="t-val">{{ $grandFurtherTax > 0 ? number_format($grandFurtherTax, 0) : '-' }}</td>
      </tr>
      <tr>
        <td class="t-label grand">Total Amount Payable (Rs.)</td>
        <td class="t-val grand">{{ number_format($grandAmount + $grandFurtherTax, 0) }}</td>
      </tr>
    </table>
    <div class="sign-block">
      <div class="box">For M/S {{ $isJinnah ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? 'CASIO NON STICK COATINGS') }}</div>
    </div>
    <div class="note" @if($isJinnah) style="font-size: 9px;" @endif>Draft Invoice — Not yet submitted to FBR</div>
  </div>

  <script>
    function downloadPDF() {
      const btn = document.getElementById('downloadPdfBtn');
      const originalHtml = btn.innerHTML;
      btn.innerHTML = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/></svg> Generating PDF...';
      btn.disabled = true;

      if (typeof html2pdf === 'undefined') {
        window.print();
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        return;
      }

      const element = document.querySelector('.page');
      const filename = '3rd_Schedule_Draft_{{ $invoice->invoice_ref_no ?? $invoice->id }}.pdf';
      const opt = {
        margin: [6, 6, 6, 6],
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
      };

      html2pdf().set(opt).from(element).save().then(function() {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
      }).catch(function(err) {
        console.error('PDF error:', err);
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        window.print();
      });
    }
  </script>
</body>
</html>