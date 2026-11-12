<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Commercial Draft - {{ (($invoice->cid ?? null) == 12 || optional($invoice->user)->c_id == 12) ? ($invoice->user->business_name ?? 'JINNAH INDUSTRY') : ($invoice->seller_business_name ?? '') }}</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
  * { box-sizing: border-box; }
  body { font-family: 'Times New Roman', Times, serif; font-variant-numeric: lining-nums tabular-nums; background: #e9e9e9; margin: 0; padding: 30px 0; }
  .page { width: 850px; max-width: 100%; margin: 0 auto; background: #fff; padding: 40px 50px; box-shadow: 0 0 8px rgba(0,0,0,0.2); }
  .company-name { text-align: center; font-size: 28px; font-weight: bold; margin: 0 0 6px 0; }
  .company-address { text-align: center; font-size: 14px; margin: 0; line-height: 1.4; }
  .stn-ntn-row { display: flex; justify-content: space-between; font-size: 14px; margin: 18px 0 10px 0; flex-wrap: wrap; gap: 6px; }
  .stn-ntn-row b { font-weight: bold; }
  .logo-qr-row { display: center; justify-content: center; gap: 20px; margin: 20px 0 10px 0; align-items: center; flex-wrap: wrap; }
  .logo-qr-row .fbr-img { height: 70px; object-fit: contain; }
  .digital-invoice-line { text-align: center; font-weight: bold; font-size: 14px; margin: 14px 0 22px 0; }
  .invoice-title { font-size: 22px; font-weight: normal; margin: 0 0 14px 0; }
  .buyer-meta-row { display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
  .buyer-info { font-size: 14px; line-height: 1.5; }
  .buyer-info .name { font-weight: bold; }
  .buyer-info b { font-weight: bold; }
  .invoice-meta { font-size: 14px; line-height: 1.6; text-align: left; }
  .invoice-meta b { font-weight: bold; }
  table.items { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 6px; }
  table.items th, table.items td { border: 1px solid #000; padding: 8px 10px; }
  table.items th { background: #f2f2f2; font-weight: bold; text-align: right; }
  table.items th.left { text-align: left; }
  table.items td { text-align: right; }
  table.items td.left { text-align: left; }
  .total-row { display: flex; justify-content: flex-end; align-items: baseline; gap: 30px; border-top: 1px solid #000; padding-top: 10px; margin-top: 4px; font-size: 16px; flex-wrap: wrap; }
  .total-row .label { font-weight: bold; }
  .total-row .amount { font-weight: bold; }
  .declaration { margin-top: 20px; padding: 12px 14px; border: 1px solid #000; background: #f9f9f9; font-size: 13px; font-weight: 600; text-align: justify; line-height: 1.5; }
  .footer-note { text-align: center; font-size: 12px; border-top: 1px solid #000; padding-top: 10px; margin-top: 30px; }
  .page-number { text-align: right; font-size: 12px; margin-top: 60px; }
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
  @media print { body { background: #fff; padding: 0; } .page { box-shadow: none; padding: 40px 50px; } .print-btn, .download-btn { display: none !important; } }
  @media (max-width: 600px) { .page { padding: 20px; } .company-name { font-size: 22px; } table.items { font-size: 11px; } table.items th, table.items td { padding: 4px 5px; } }
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
    <div class="logo-qr-row">
      @if(file_exists(public_path('fbr.jpg')))
        <img src="{{ asset('fbr.jpg') }}" alt="FBR Logo" class="fbr-img">
      @endif
      @if(!$isJinnah)
        <div style="width:70px;height:70px;border:1px solid #999;display:flex;align-items:center;justify-content:center;font-size:10px;color:#888;">DRAFT</div>
      @endif
    </div>
    @if(!$isJinnah)
      <p class="digital-invoice-line">DRAFT INVOICE</p>
    @endif
    <p class="invoice-title">Commercial Invoice</p>
    @php
      $items = is_string($invoice->items) ? json_decode($invoice->items, true) : ($invoice->items ?? []);
      $itemsCollection = collect($items);
      $hsCode = $itemsCollection->count() > 0 ? (is_array($itemsCollection->first()) ? ($itemsCollection->first()['hsCode'] ?? '') : '') : '';
      $grandTotal = 0;
       $grandQty = 0;
       $grandValueExcl = 0;
       $grandSalesTax = 0;
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
        <div><b>{{ $isJinnah ? 'No.' : 'Draft No.' }}</b> #{{ $invoice->invoice_ref_no ?? $invoice->id }}</div>
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
          <th>Value Excl S.Tax</th>
          <th>Amount of S.Tax</th>
          <th>Further Tax</th>
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
            $rowTotal = $valueExcl + $salesTax + $furtherTax;
            $grandQty += $qty;
            $grandValueExcl += $valueExcl;
            $grandSalesTax += $salesTax;
            $grandFurtherTax += $furtherTax;
            $grandTotal += $rowTotal;
          @endphp
          <tr>
            <td class="left">{{ $itemArray['productDescription'] ?? '-' }}</td>
            <td>{{ number_format($qty, 0) }}</td>
            <td>{{ number_format($unitPrice, $isJinnah ? 2 : 3) }}</td>
            <td>{{ number_format($valueExcl, 2) }}</td>
            <td>{{ number_format($salesTax, 2) }}</td>
            <td>{{ number_format($furtherTax, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="font-weight:bold;background:#f9f9f9;">
          <td class="left">Total</td>
          <td>{{ number_format($grandQty, 0) }}</td>
          <td></td>
          <td>{{ number_format($grandValueExcl, 2) }}</td>
          <td>{{ number_format($grandSalesTax, 2) }}</td>
          <td>{{ number_format($grandFurtherTax, 2) }}</td>
        </tr>
      </tfoot>
    </table>
    @php $grandTotal += floatval($invoice->expense_col ?? 0); @endphp
    <div class="total-row">
      <span class="label">Total:</span>
      <span class="amount">Rs. {{ number_format($grandTotal, 2) }}</span>
    </div>
    <div class="declaration">
      WE HEREBY CONFIRM THAT THE PRODUCTS SUPPLIED HAVE BEEN IMPORTED BY US AND INCOME TAX UNDER SECTION 148 HAS BEEN PAID AT IMPORT STAGE ON IMPORT OF ABOVE PRODUCTS. THEREFORE, NO WITHHOLDING TAX TO BE DEDUCTED UNDER SECTION 153 (1)(a).
    </div>
    <p class="footer-note" @if($isJinnah) style="font-size: 10px;" @endif>This is a draft invoice — not yet submitted to FBR.</p>
    <p class="page-number">Page 1</p>
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
      const filename = 'Commercial_Draft_{{ $invoice->invoice_ref_no ?? $invoice->id }}.pdf';
      const opt = {
        margin: [8, 8, 8, 8],
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
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