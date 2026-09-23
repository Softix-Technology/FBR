<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Tax Invoice - {{ $invoice->seller_business_name ?? '' }}</title>
<style>
  * { box-sizing: border-box; }
  body {
    font-family: Georgia, 'Times New Roman', serif;
    background: #e9e9e9;
    margin: 0;
    padding: 30px 0;
  }
  .page {
    width: 850px;
    margin: 0 auto;
    background: #fff;
    padding: 40px 50px;
    box-shadow: 0 0 8px rgba(0,0,0,0.2);
  }
  .company-name {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    margin: 0 0 6px 0;
  }
  .company-address {
    text-align: center;
    font-size: 14px;
    margin: 0;
    line-height: 1.4;
  }
  .stn-ntn-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    margin: 18px 0 10px 0;
  }
  .stn-ntn-row b { font-weight: bold; }

  .logo-qr-row {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px 0 10px 0;
    align-items: center;
  }
  .logo-qr-row .fbr-img { height: 70px; object-fit: contain; }
  .logo-qr-row #qrcode { width: 70px; height: 70px; }
  .no-qr-placeholder {
    width: 70px; height: 70px;
    border: 1px solid #999;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; color: #888; font-style: italic;
  }

  .digital-invoice-line {
    text-align: center;
    font-weight: bold;
    font-size: 14px;
    margin: 14px 0 22px 0;
  }

  .invoice-title {
    font-size: 22px;
    font-weight: normal;
    margin: 0 0 14px 0;
  }

  .buyer-meta-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
  }
  .buyer-info { font-size: 14px; line-height: 1.5; }
  .buyer-info .name { font-weight: bold; }
  .buyer-info b { font-weight: bold; }
  .invoice-meta { font-size: 14px; line-height: 1.6; text-align: left; }
  .invoice-meta b { font-weight: bold; }

  table.items {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    margin-bottom: 6px;
  }
  table.items th, table.items td {
    border: 1px solid #000;
    padding: 8px 10px;
  }
  table.items th {
    background: #f2f2f2;
    font-weight: bold;
    text-align: right;
  }
  table.items th.left { text-align: left; }
  table.items td { text-align: right; }
  table.items td.left { text-align: left; }

  .total-row {
    display: flex;
    justify-content: flex-end;
    align-items: baseline;
    gap: 30px;
    border-top: 1px solid #000;
    padding-top: 10px;
    margin-top: 4px;
    font-size: 16px;
  }
  .total-row .label { font-weight: bold; }
  .total-row .amount { font-weight: bold; }

  .footer-note {
    text-align: center;
    font-size: 12px;
    border-top: 1px solid #000;
    padding-top: 10px;
    margin-top: 30px;
  }

  .page-number {
    text-align: right;
    font-size: 12px;
    margin-top: 60px;
  }

  .print-btn { position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background: #2c3e50; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; z-index: 1000; }

  @media print {
    body { background: #fff; padding: 0; }
    .page { box-shadow: none; padding: 40px 50px; }
    .print-btn { display: none; }
  }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
  <button class="print-btn" onclick="window.print()">Print Invoice</button>

  <div class="page">

    <p class="company-name">{{ $invoice->seller_business_name ?? '' }}</p>
    <p class="company-address">
      {{ $invoice->seller_address ?? '' }}<br>
      {{ $invoice->seller_province ?? '' }}
    </p>

    <div class="stn-ntn-row">
      <div><b>STN :</b> {{ $invoice->user->strn ?? $invoice->seller_ntn_cnic ?? '' }}</div>
      <div><b>NTN :</b> {{ $invoice->seller_ntn_cnic ?? '' }}</div>
    </div>

    <div class="logo-qr-row">
      @if(file_exists(public_path('fbr.jpg')))
        <img src="{{ asset('fbr.jpg') }}" alt="FBR Logo" class="fbr-img">
      @else
        <div style="border:1px solid #999;width:70px;height:70px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#888;text-align:center;">FBR<br>LOGO</div>
      @endif
      <div id="qrcode"></div>
    </div>

    <p class="digital-invoice-line">Digital Invoice #: {{ $invoice->fbr_invoice_no ?? 'N/A' }}</p>

    <p class="invoice-title">Sales Tax Invoice</p>

    @php
      $items = is_string($invoice->items) ? json_decode($invoice->items, true) : ($invoice->items ?? []);
      $itemsCollection = collect($items);
      $hsCode = '';
      if ($itemsCollection->count() > 0) {
          $firstItem = is_array($itemsCollection->first()) ? $itemsCollection->first() : (array) $itemsCollection->first();
          $hsCode = $firstItem['hsCode'] ?? '';
      }
      $grandTotal = 0;
       $grandQty = 0;
       $grandValueExcl = 0;
       $grandSalesTax = 0;
       $grandSTInclusive = 0;
       $grandFurtherTax = 0;
    @endphp

    <div class="buyer-meta-row">
      <div class="buyer-info">
        <div class="name">{{ $invoice->buyer_business_name ?? '' }}</div>
        <div>{{ $invoice->buyer_address ?? '' }}</div>
        <div>{{ $invoice->buyer_province ?? '' }}</div>
        <div>PAKISTAN</div>
        <div><b>NTN</b> {{ $invoice->buyer_ntn_cnic ?? '' }}</div>
      </div>
      <div class="invoice-meta">
        <div><b>Invoice No.</b> {{ $invoice->invoice_ref_no ?? 'N/A' }}</div>
        <div><b>Date</b> {{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('d/m/Y') }}</div>
        <div><b>HS Code</b> {{ $hsCode ?: '-' }}</div>
      </div>
    </div>

    <table class="items">
      <thead>
        <tr>
          <th class="left">Particulars</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>Value Excl<br>S.Tax</th>
          <th>Amount of<br>S.Tax {{ ($invoice->cid == 8 || $invoice->cid == 9 || $invoice->cid == 10) ? '18%' : '' }}</th>
          <th>FURTHER<br>TAX</th>
          <th>Value Included<br>S.Tax</th>
        </tr>
      </thead>
      <tbody>
        @foreach($itemsCollection as $item)
          @php
            $itemArray = is_array($item) ? $item : (array) $item;
            $qty = floatval($itemArray['quantity'] ?? 0);
            $valueExcl = floatval($itemArray['valueSalesExcludingST'] ?? 0);
            $salesTax = floatval($itemArray['salesTaxApplicable'] ?? 0);
            $furtherTax = floatval($itemArray['furtherTax'] ?? 0);
            $unitPrice = $qty > 0 ? $valueExcl / $qty : 0;
            $stInclusive = $valueExcl + $salesTax + $furtherTax;
            $rowTotal = $valueExcl + $salesTax + $furtherTax;
            $grandQty += $qty;
            $grandValueExcl += $valueExcl;
            $grandSalesTax += $salesTax;
            $grandSTInclusive += $stInclusive;
            $grandFurtherTax += $furtherTax;
            $grandTotal += $rowTotal;
          @endphp
          <tr>
            <td class="left">{{ $itemArray['product_description'] ?? $itemArray['productDescription'] ?? '-' }}</td>
            <td>{{ number_format($qty, 0) }}</td>
            <td>{{ number_format($unitPrice, 3) }}</td>
            <td>{{ number_format($valueExcl, 0) }}</td>
            <td>{{ number_format($salesTax, 0) }}</td>
            <td>{{ number_format($furtherTax, 0) }}</td>
            <td>{{ number_format($stInclusive, 0) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="font-weight:bold;background:#f9f9f9;">
          <td class="left">Total</td>
          <td>{{ number_format($grandQty, 0) }}</td>
          <td></td>
          <td>{{ number_format($grandValueExcl, 0) }}</td>
          <td>{{ number_format($grandSalesTax, 0) }}</td>
          <td>{{ number_format($grandFurtherTax, 0) }}</td>
          <td>{{ number_format($grandSTInclusive, 0) }}</td>
        </tr>
      </tfoot>
    </table>

    @php
      $expenseCol = floatval($invoice->expense_col ?? 0);
      if ($expenseCol > 0) $grandTotal += $expenseCol;
    @endphp


    <p class="footer-note">This is a system generated invoice and does not require any signatures.</p>

    <p class="page-number">Page 1</p>

  </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrData = "{{ $invoice->fbr_invoice_no ?? '' }}";
    var qrContainer = document.getElementById("qrcode");
    if (qrData && qrData.trim() !== '') {
        try {
            new QRCode(qrContainer, { text: qrData, width: 70, height: 70 });
        } catch(e) {
            qrContainer.innerHTML = '<div class="no-qr-placeholder">No QR Code</div>';
        }
    } else {
        qrContainer.innerHTML = '<div class="no-qr-placeholder">No QR Code</div>';
    }
});
</script>
</body>
</html>