<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->seller_name ?? 'IJAZ AHMAD' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .invoice-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .invoice-header {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-header h1 {
            font-size: 32px;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .invoice-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .qr-fbr-container {
            background-color: #4d6a94; 
            border-radius: 10px; 
            width: 250px;
            display: flex; 
            align-items: center;
            margin-right: 20px;
            padding: 5px;
        }

        #qrcode {
            width: 120px;
            height: 120px;
        }

        .fbr-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
        }

        .invoice-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 30px;
        }

        .section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            border-left: 4px solid #4a6491;
        }

        .section h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e1e5eb;
            font-size: 18px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            width: 150px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .invoice-summary {
            padding: 0 30px;
        }

        .summary-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .summary-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }

        .summary-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .invoice-table th {
            background: #2c3e50;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }

        .invoice-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e1e5eb;
        }

        .invoice-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .invoice-table tr:hover {
            background: #eef2f7;
        }

        .total-row {
            background: #eef7ff !important;
            font-weight: 600;
        }

        .amount-in-words {
            background: #f8f9fa;
            padding: 20px;
            margin: 0 30px 30px;
            border-radius: 6px;
            border-left: 4px solid #4a6491;
        }

        .amount-in-words .summary-label {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .amount-in-words .summary-value {
            font-size: 14px;
            font-weight: normal;
            color: #333;
            line-height: 1.4;
        }

        .invoice-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e1e5eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .signature-area {
            text-align: center;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #2c3e50;
            margin: 30px auto 5px;
        }

        .no-items {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .invoice-sections {
                grid-template-columns: 1fr;
            }

            .summary-row {
                grid-template-columns: 1fr 1fr;
            }

            .invoice-table {
                display: block;
                overflow-x: auto;
            }

            .invoice-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .qr-fbr-container {
                width: 100%;
                max-width: 250px;
                margin: 0 auto;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4a6491;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Invoice</button>

    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div>
                <h1>{{ $invoice->seller_business_name ?? 'N/A' }}</h1>
                <p>Sales Tax Invoice</p>
            </div>

            <div class="qr-fbr-container">
                <!-- QR Code container -->
                <div id="qrcode"></div>

                <!-- FBR Logo Image -->
                @if(file_exists(public_path('fbr.jpg')))
                    <img src="{{ asset('fbr.jpg') }}" alt="FBR Logo" class="fbr-logo" />
                @else
                    <div style="color: white; text-align: center; padding: 10px;">
                        <p>FBR Logo</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Invoice Sections -->
        <div class="invoice-sections">
            <!-- Seller Information -->
            <div class="section">
                <h2>Seller Information</h2>
                @if(isset($invoice->seller_business_name) && $invoice->seller_business_name)
                <div class="info-row">
                    <div class="info-label">Business Name:</div>
                    <div class="info-value">{{ $invoice->seller_business_name }}</div>
                </div>
                @endif
                @if(isset($invoice->user->strn) && $invoice->user->strn)
                <div class="info-row">
                    <div class="info-label">STRN:</div>
                    <div class="info-value">{{ $invoice->user->strn }}</div>
                </div>
                @endif
                @if(isset($invoice->seller_ntn_cnic) && $invoice->seller_ntn_cnic)
                <div class="info-row">
                    <div class="info-label">NTN/CNIC No.:</div>
                    <div class="info-value">{{ $invoice->seller_ntn_cnic }}</div>
                </div>
                @endif
                @if(isset($invoice->seller_province) && $invoice->seller_province)
                <div class="info-row">
                    <div class="info-label">Province:</div>
                    <div class="info-value">{{ $invoice->seller_province }}</div>
                </div>
                @endif
                @if(isset($invoice->seller_address) && $invoice->seller_address)
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $invoice->seller_address }}</div>
                </div>
                @endif
                @if(isset($invoice->seller_phone) && $invoice->seller_phone)
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $invoice->seller_phone }}</div>
                </div>
                @endif
            </div>

            <!-- Buyer Information -->
          <!-- Buyer Information -->
            <div class="section">
                <h2>Buyer Information</h2>
                @if(isset($invoice->buyer_business_name) && $invoice->buyer_business_name)
                <div class="info-row">
                    <div class="info-label">Business Name:</div>
                    <div class="info-value">{{ $invoice->buyer_business_name }}</div>
                </div>
                @endif
                @if(isset($invoice->buyer_ntn_cnic) && $invoice->buyer_ntn_cnic)
                <div class="info-row">
                    <div class="info-label">CNIC/NTN No.:</div>
                    <div class="info-value">{{ $invoice->buyer_ntn_cnic }}</div>
                </div>
                @endif
                @if(isset($invoice->buyer_province) && $invoice->buyer_province)
                <div class="info-row">
                    <div class="info-label">Province:</div>
                    <div class="info-value">{{ $invoice->buyer_province }}</div>
                </div>
                @endif
                @if(isset($invoice->buyer_address) && $invoice->buyer_address)
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $invoice->buyer_address }}</div>
                </div>
                @endif
                @if(!empty($invoice->buyer_mobile) || !empty($invoice->buyer_phone))
                <div class="info-row">
                    <div class="info-label">Mobile / Phone:</div>
                    <div class="info-value">{{ $invoice->buyer_mobile ?: $invoice->buyer_phone }}</div>
                </div>
                @endif
            </div>
        </div>

        @php
            $items = is_string($invoice->items) ? json_decode($invoice->items, true) : ($invoice->items ?? []);
            $itemsCollection = collect($items);
            $hsCode = '';
            
            if($itemsCollection->count() > 0) {
                foreach($itemsCollection as $item) {
                    $itemArray = is_array($item) ? $item : (array) $item;
                    $hsCode = $itemArray['hsCode'] ?? '';
                    if ($hsCode) break;
                }
            }
        @endphp

        <!-- Invoice Summary -->
        <div class="invoice-summary">
            <div class="summary-row">
                <div class="summary-item">
                    <div class="summary-label">FBR Invoice No.</div>
                    <div class="summary-value">{{ $invoice->fbr_invoice_no ?? 'N/A' }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Invoice Date</div>
                    <div class="summary-value">{{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('d-M-Y') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">PO#</div>
                    <div class="summary-value">{{ $invoice->invoice_ref_no ?? '-' }}</div>
                </div>
                @if($invoice->cid == 8 || $invoice->cid == 9 || $invoice->cid == 10 )
                <div class="summary-item">
                    <div class="summary-label">HS Code</div>
                    <div class="summary-value">{{ $hsCode ?: '-' }}</div>
                </div>
                @else
                <div class="summary-item">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">{{ $invoice->status ?? 'Active' }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div style="padding: 0 30px;">
            @php
                $items = is_string($invoice->items) ? json_decode($invoice->items, true) : ($invoice->items ?? []);
                $itemsCollection = collect($items);
                $totalSalesValue = 0;
                $totalSalesTax = 0;
                $totalAmount = 0;
            @endphp

            @if($itemsCollection->count() > 0)
                 <table class="invoice-table">
    <thead>
        <tr>
            <th>Sr. No.</th>
            @if($invoice->cid != 8 && $invoice->cid != 9 && $invoice->cid != 10)
                <th>HS Code</th>
            @endif
            <th>Product Description</th>
            <th>Quantity</th>
            <th>Units</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalSalesValue = 0;
            $totalSalesTax = 0;
            $totalAmount = 0;
            $furtherTotal = 0;
            $showFed = false;
        @endphp
        
        @foreach($itemsCollection as $index => $item)
            @php
                $itemArray = is_array($item) ? $item : (array) $item;
                $quantity = floatval($itemArray['quantity'] ?? 0);
           $unitPrice = floatval($itemArray['rateValues'] ?? $itemArray['valueSalesExcludingST'] ?? 0);
                $furtherTax = floatval($itemArray['furtherTax'] ?? 0);
                $furtherTotal+=$furtherTax;
               
                $itemTotal = $quantity * $unitPrice;
          
                $salesValue = floatval($itemArray['salesTaxApplicable'] ?? 0);
                $salesTax = floatval($itemArray['valueSalesExcludingST'] ?? 0);
                $salesAmount = floatval($itemArray['totalValues'] ?? $itemTotal);

                $totalSalesValue += $salesValue;
                $totalSalesTax += $salesTax;
                $totalAmount += $salesAmount;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                @if($invoice->cid != 8 && $invoice->cid != 9 && $invoice->cid != 10)
                    <td>{{ $itemArray['hsCode'] ?? '-' }}</td>
                @endif
                <td>{{ $itemArray['product_description'] ?? $itemArray['productDescription'] ?? '-' }}</td>
                <td>{{ number_format($quantity, 2) }}</td>
                <td>{{ $itemArray['uoM'] ?? '-' }}</td>
       <td>{{ number_format($itemArray['valueSalesExcludingST'] / floatval($itemArray['quantity']), 2) }}</td>


                <td>{{ number_format($itemArray['valueSalesExcludingST'] ?? 0, 2) }}</td>
                
            </tr>
        @endforeach

                          @php
                           $colspan = ($invoice->cid == 8 && $invoice->cid == 9 && $invoice->cid == 10) ? 4 : 5;
                          @endphp

                        <tr class="total-row">
                       
                            <td colspan={{$colspan}} style="text-align: right;">Sub Total:</td>
                             <td>{{ number_format($totalSalesTax, 2) }}</td>
                           
                        </tr>
                        <tr class="total-row">
                       
                      <td colspan="{{ $colspan }}" style="text-align: right;">
    GST {{ ($invoice->cid == 8 || $invoice->cid == 9 || $invoice->cid == 10) ? "18%" : '' }}:
</td>
                             <td>{{ number_format($totalSalesValue, 2) }}</td>
                         
                        </tr>
                         <tr class="total-row">
                       <td colspan={{$colspan}} style="text-align: right;">Transportation Charges:</td>
                             <td>{{ number_format($invoice->expense_col, 2) }}</td>
                           
                            </tr>
                            <tr class="total-row">
                       
                            <td colspan={{$colspan}} style="text-align: right;">Further Tax:</td>
                             <td>{{ number_format($furtherTotal, 2) }}</td>
                           
                            </tr>
                           
<tr class="total-row">
                       
                              <td colspan={{$colspan}} style="text-align: right;">Grand Total:</td>
                              <td>
    {{ number_format($totalAmount + $furtherTotal + ($invoice->expense_col ?? 0) , 2) }}
</td>

                           
                           
                        </tr>
    </tbody>
</table>
            @else
                <div class="no-items">
                    <h3>No Items Found</h3>
                    <p>This invoice doesn't contain any items.</p>
                </div>
            @endif
        </div>

        <!-- Amount in Words -->
        <div class="amount-in-words">
            <div class="summary-label">Amount in Words</div>
            @php
                function numberToWords($num) {
                    if ($num == 0) return "zero";
                    $below20 = ["","one","two","three","four","five","six","seven","eight","nine",
                        "ten","eleven","twelve","thirteen","fourteen","fifteen","sixteen",
                        "seventeen","eighteen","nineteen"];
                    $tens = ["","", "twenty","thirty","forty","fifty","sixty","seventy","eighty","ninety"];
                    $helper = function($n) use (&$helper, $below20, $tens) {
                        if ($n == 0) return "";
                        elseif ($n < 20) return $below20[$n]." ";
                        elseif ($n < 100) return $tens[intval($n/10)]." ".$helper($n%10);
                        else return $below20[intval($n/100)]." hundred ".$helper($n%100);
                    };
                    $words=""; $i=0; $thousands=["","thousand","million","billion"];
                    while($num>0) {
                        if($num%1000!=0) $words=$helper($num%1000).$thousands[$i]." ".$words;
                        $num=intval($num/1000); $i++;
                    }
                    return trim($words);
                }
                $capitalized = ucwords(numberToWords(intval($totalAmount + $furtherTotal + ($invoice->expense_col ?? 0)))) . " Rupees Only";
            @endphp
            <div class="summary-value">{{ $capitalized }}</div>
        </div>

        <!-- Additional Info -->
        @if(isset($invoice->additional_info) || isset($invoice->notes))
            <div style="padding: 0 30px 30px;">
                <div class="section">
                    <h2>Additional Information</h2>
                    @if(isset($invoice->additional_info) && $invoice->additional_info)
                    <div class="info-row">
                        <div class="info-label">Additional Info:</div>
                        <div class="info-value">{{ $invoice->additional_info }}</div>
                    </div>
                    @endif
                    @if(isset($invoice->notes) && $invoice->notes)
                    <div class="info-row">
                        <div class="info-label">Notes:</div>
                        <div class="info-value">{{ $invoice->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="signature-area">
                <div class="signature-line"></div>
                <p>Authorized Signature</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrData = "{{ $invoice->fbr_invoice_no ?? 'N/A' }}";
            if (qrData && qrData !== 'N/A') {
                new QRCode(document.getElementById("qrcode"), {
                    text: qrData,
                    width: 120,
                    height: 120
                });
            } else {
                document.getElementById("qrcode").innerHTML = '<div style="color: white; text-align: center; padding: 50px 10px;">No QR Data</div>';
            }
        });
    </script>
</body>
</html>