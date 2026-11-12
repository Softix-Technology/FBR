<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Tax Invoice -  {{ (($invoice->cid ?? null) == 12 || optional($invoice->user)->c_id == 12) ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? '') }}</title>
<style>
  @page { size: A4; margin: 10mm 8mm; }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    color: #111;
    margin: 0;
    padding: 0;
    font-size: 12px;
  }
  .sheet {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    padding: 14px;
  }

  .logos-container { text-align: center; margin-bottom: 10px; }
  .logos-container .qr-fbr-wrap { display: inline-flex; align-items: center; gap: 15px; }
  .logos-container #qrcode { width: 70px; height: 70px; }
  .logos-container .fbr-img { height: 70px; object-fit: contain; }

  .company-header { text-align: center; margin-bottom: 6px; }
  .company-name {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: 0.5px;
    margin: 0;
  }
  .company-address {
    font-size: 12.5px;
    font-weight: 600;
    margin: 2px 0 0 0;
  }
  .digital-invoice-num {
    font-size: 14px;
    font-weight: bold;
    margin-top: 5px;
  }

  .title-bar {
    background: #bfbfbf;
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 1px;
    padding: 4px 0;
    margin-top: 8px;
    border: 1px solid #000;
    border-bottom: none;
  }

  .meta-row {
    display: flex;
    width: 100%;
    border: 1px solid #000;
  }
  .customer-box {
    flex: 1.6;
    border-right: 1px solid #000;
    padding: 6px 8px;
  }
  .customer-title {
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    border-bottom: 1px solid #000;
    margin: -6px -8px 6px -8px;
    padding: 3px 0;
  }
  .customer-box .line { margin: 1px 0; }
  .customer-box .buyer-name { font-weight: 700; }
  .customer-box .label { font-weight: 700; }
  .customer-box .taxno { margin-top: 4px; }

  .invoice-meta { flex: 1; }
  .invoice-meta table { width: 100%; border-collapse: collapse; height: 100%; }
  .invoice-meta td {
    border: 1px solid #000;
    padding: 3px 6px;
    font-size: 11.5px;
  }
  .invoice-meta td.label { font-weight: 700; width: 45%; background: #f2f2f2; }

  table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    font-size: 10.5px;
  }
  table.items th, table.items td {
    border: 1px solid #000;
    padding: 3px 4px;
    text-align: center;
    vertical-align: middle;
  }
  table.items thead .grp {
    background: #d9d9d9;
    font-weight: 700;
    font-size: 11px;
  }
  table.items thead .sub {
    background: #eeeeee;
    font-weight: 700;
    font-size: 9.5px;
  }
  table.items td.desc { text-align: left; }
  table.items tbody td { font-size: 10.5px; }
  table.items tfoot td {
    background: #d9d9d9;
    font-weight: 700;
  }
  .num { text-align: right; font-variant-numeric: tabular-nums; }

  .totals-block {
    width: 100%;
    margin-top: 0;
    border-collapse: collapse;
    font-size: 11.5px;
  }
  .totals-block td {
    padding: 3px 6px;
    border-left: 1px solid #000;
    border-right: 1px solid #000;
  }
  .totals-block tr:last-child td { border-bottom: 1px solid #000; }
  .totals-block .t-label { text-align: right; font-weight: 600; width: 78%; }
  .totals-block .t-val { text-align: right; font-weight: 700; width: 22%; }
  .totals-block .grand { font-size: 13px; background: #eaeaea; }

  .sign-block {
    margin-top: 46px;
    display: flex;
    justify-content: flex-end;
  }
  .sign-block .box {
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    border-top: 1px solid #000;
    padding-top: 4px;
    width: 220px;
  }

  .note {
    margin-top: 10px;
    font-size: 11px;
    color: #444;
    text-align: center;
    font-weight: bold;
    font-style: italic;
  }
  
  .print-btn { position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background: #2c3e50; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; z-index: 1000; }
  
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .sheet { padding: 0; }
    .print-btn { display: none; }
  }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Invoice</button>

<div class="sheet">
  @php
    $isJinnah = (($invoice->cid ?? null) == 12 || optional($invoice->user)->c_id == 12);
    $displaySellerName = $isJinnah ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? '');
    $displaySellerNtn = $isJinnah ? ($invoice->user->cinc_ntn ?? '8167774') : ($invoice->seller_ntn_cnic ?? '');
  @endphp

  <!-- HEADER -->
  <div class="logos-container">
    <div class="qr-fbr-wrap">
      <div id="qrcode"></div>
      @if(file_exists(public_path('fbr.jpg')))
        <img src="{{ asset('fbr.jpg') }}" alt="FBR Logo" class="fbr-img">
      @else
        <div style="text-align:center;padding:10px;"><p>FBR Logo</p></div>
      @endif
    </div>
  </div>
  
  <div class="company-header">
    <p class="company-name"> {{ $displaySellerName }}</p>
    <p class="company-address">{{ $invoice->seller_address ?? '' }}</p>
    <p class="digital-invoice-num">Digital Invoice Nmbr: {{ $invoice->fbr_invoice_no ?? 'N/A' }}</p>
  </div>
  <div class="title-bar">SALES TAX INVOICE</div>

  <!-- CUSTOMER + INVOICE META -->
  <div class="meta-row">
    <div class="customer-box">
      <div class="customer-title">Customer's Detail</div>
      <div class="line"><span class="label">Buyer's Name:</span> <span class="buyer-name" id="buyerName">{{ $invoice->buyer_business_name ?? '' }}</span></div>
      <div class="line"><span class="label">Address:</span> <span id="buyerAddress">{{ $invoice->buyer_address ?? '' }}</span></div>
      <div class="line taxno"><span class="label">Sales Tax Reg No.</span> <span id="buyerSTRN">{{ $invoice->buyer_ntn_cnic ?? '' }}</span> &nbsp;&nbsp; <span class="label">NTN:</span> <span id="buyerNTN">{{ $invoice->buyer_ntn_cnic ?? '' }}</span></div>
    </div>
    <div class="invoice-meta">
      <table>
        <tr><td class="label">Invoice No.</td><td id="invNo">{{ $invoice->fbr_invoice_no ?? 'N/A' }}</td></tr>
        <tr><td class="label">Invoice Date</td><td id="invDate">{{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('d-M-y') }}</td></tr>
        <tr><td class="label">Sales Tax Reg No.</td><td id="sellerSTRN">{{ $invoice->user->strn ?? $displaySellerNtn }}</td></tr>
        <tr><td class="label">NTN No.</td><td id="sellerNTN">{{ $displaySellerNtn }}</td></tr>
        <tr><td class="label">Serial No.</td><td id="poNo">{{ $invoice->invoice_ref_no ?? '-' }}</td></tr>
      </table>
    </div>
  </div>

  <!-- ITEMS TABLE -->
  @php
      $items = is_string($invoice->items) ? json_decode($invoice->items, true) : ($invoice->items ?? []);
      $itemsCollection = collect($items);
      
      // Grand totals (all per-unit sums)
      $grandQty = 0;
      $grandRetailExcl = 0;
      $grandRetailTax = 0;
      $grandRetailIncl = 0;
      $grandDiscount = 0;
      $grandTradeExcl = 0;
      $grandTradeTax = 0;
      $grandTradeWithTax = 0;
      $grandUS236 = 0;
      $grandAmount = 0;
      $grandFurtherTax = 0;
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
    <tbody id="itemsBody">
      @foreach($itemsCollection as $index => $item)
      @php
          $itemArray = is_array($item) ? $item : (array) $item;
          $qty = floatval($itemArray['quantity'] ?? 0);
          $fixedNotified = floatval($itemArray['fixedNotifiedValueOrRetailPrice'] ?? 0);
          $discountPerUnit = floatval($itemArray['discountAmount'] ?? 0);
          if ($discountPerUnit == 0) {
              $discountPerUnit = floatval($itemArray['discount'] ?? 0);
          }
          $ghPerUnit = floatval($itemArray['ghAmount'] ?? 0);
          $furtherTax = floatval($itemArray['furtherTax'] ?? 0);
          
          $isThirdSchedule = ($fixedNotified > 0);
          
          if ($isThirdSchedule) {
              // ---- 3rd Schedule: all values are per‑unit ----
              $retailExcl = $fixedNotified;                          
              $retailTax = $fixedNotified * 0.18;  
                                
              $retailIncl = $retailExcl + $retailTax;               
              $discount = $discountPerUnit;                          
              $tradeExcl = $fixedNotified - $discount;               
              $tradeTax = $retailTax;                                
              $tradeWithTax = $tradeExcl + $tradeTax;                
              $us236 = $ghPerUnit;                                   
              // Amount is per‑unit, NOT multiplied by quantity
              $amount = $tradeWithTax + $us236;
          } else {
              // ---- Standard item: values are totals (already per unit or total?) ----
              // To keep consistent, we treat them as per‑unit if qty > 0
              // We'll assume the stored values are per‑unit (or we can divide by qty)
              // For simplicity, we'll use the stored values as is (they should be per‑unit for standard as well)
              $retailExcl = floatval($itemArray['rateValues'] ?? 0);
              $retailTax = floatval($itemArray['salesTaxApplicable'] ?? 0);
              // But salesTaxApplicable might be total, so we divide by qty if qty > 0
              if ($qty > 0) {
                  $retailTax = $retailTax / $qty;
              }
              $retailIncl = $retailExcl + $retailTax;
              $discount = floatval($itemArray['discount'] ?? 0);
              // discount might be total, convert to per unit
              if ($qty > 0 && $discount > 0) {
                  $discount = $discount / $qty;
              }
              $tradeExcl = floatval($itemArray['valueSalesExcludingST'] ?? $retailExcl);
              // tradeExcl might be total, convert to per unit
              if ($qty > 0) {
                  $tradeExcl = $tradeExcl / $qty;
              }
              $tradeTax = $retailTax;
              $tradeWithTax = $tradeExcl + $tradeTax;
              $us236 = floatval($itemArray['ghAmount'] ?? 0);
              if ($qty > 0 && $us236 > 0) {
                  $us236 = $us236 / $qty;
              }
              $amount = $tradeWithTax + $us236;
          }
          
          // Accumulate grand totals (all per-unit)
          $grandQty += $qty;
          $grandRetailExcl += $retailExcl;
          $grandRetailTax += $retailTax;
          $grandRetailIncl += $retailIncl;
          $grandDiscount += $discount;
          $grandTradeExcl += $tradeExcl;
          $grandTradeTax += $tradeTax;
          $grandTradeWithTax += $tradeWithTax;
          $grandUS236 += $us236;
          $grandAmount += $amount;
          $grandFurtherTax += $furtherTax;
      @endphp
      <tr>
        <td>{{ $index + 1 }}</td>
        <td class="desc">{{ $itemArray['product_description'] ?? $itemArray['productDescription'] ?? '-' }}</td>
        <td>{{ number_format($qty, 3) }}</td>
        <!-- All values are integers (no decimals) -->
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

      @php
          $extraAmount = floatval($invoice->expense_col ?? 0);
      @endphp
      @if($extraAmount > 0 && $itemsCollection->count() > 0)
      <tr>
        <td>{{ $itemsCollection->count() + 1 }}</td>
        <td class="desc">Transportation Charges</td>
        <td>-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">-</td>
        <td class="num">{{ number_format($extraAmount, 0) }}</td>
      </tr>
      @php
          // Add extra amount to grand total (it is already a total, not per-unit)
          $grandAmount += $extraAmount;
      @endphp
      @endif

    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">Total Value Exclusive of Sales Tax</td>
        <td id="totQty" class="num">{{ number_format($grandQty, 3) }}</td>
        <td id="totExcl" class="num">{{ number_format($grandRetailExcl, 0) }}</td>
        <td id="totTax1" class="num">{{ number_format($grandRetailTax, 0) }}</td>
        <td id="totIncl" class="num">{{ number_format($grandRetailIncl, 0) }}</td>
        <td id="totDisc" class="num">{{ number_format($grandDiscount, 0) }}</td>
        <td id="totDV" class="num">{{ number_format($grandTradeExcl, 0) }}</td>
        <td id="totTax2" class="num">{{ number_format($grandTradeTax, 0) }}</td>
        <td id="totVWST" class="num">{{ number_format($grandTradeWithTax, 0) }}</td>
        <td id="totUS236" class="num">{{ number_format($grandUS236, 0) }}</td>
        <td id="totAmt" class="num">{{ number_format($grandAmount, 0) }}</td>
      </tr>
    </tfoot>
  </table>

  <!-- TOTALS BLOCK -->
  <table class="totals-block">
    <tr>
      <td class="t-label">(+) Further Tax Payable (if applicable)</td>
      <td class="t-val">{{ $grandFurtherTax > 0 ? number_format($grandFurtherTax, 0) : '-' }}</td>
    </tr>
    <tr>
      <td class="t-label grand">Total Amount Payable (Rs.)</td>
      <td class="t-val grand" id="grandTotal">{{ number_format($grandAmount + $grandFurtherTax, 0) }}</td>
    </tr>
  </table>

  <div class="sign-block">
    <div class="box">For  {{ $isJinnah ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? 'CASIO NON STICK COATINGS') }}</div>
  </div>

  <div class="note">
    "It is System Generated Invoice Does Not Need Any Signature or Stamp"
  </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var qrData = "{{ $invoice->fbr_invoice_no ?? 'N/A' }}";
        if (qrData && qrData !== 'N/A') {
            new QRCode(document.getElementById("qrcode"), {
                text: qrData,
                width: 70,
                height: 70
            });
        } else {
            document.getElementById("qrcode").innerHTML = '<div style="text-align:center;padding:15px 5px;">No QR Data</div>';
        }
    });
</script>
</body>
</html>