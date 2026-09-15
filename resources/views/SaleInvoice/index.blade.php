@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item active">Sale Invoices</li>
                    </ol>
                </div>
                <h3 class="page-title">Sales Invoices</h3>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-8">
            <form method="GET" action="{{ route('premiertax.sales.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="bill_no" class="form-label">Bill No</label>
                    <select class="form-control select2" id="bill_no" name="bill_no">
                        <option value="">All Bill Numbers</option>
                        @foreach($availableBillNumbers as $billNo)
                            <option value="{{ $billNo }}" 
                                {{ request('bill_no') == $billNo ? 'selected' : '' }}>
                                {{ $billNo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="client" class="form-label">Client</label>
                    <input type="text" class="form-control" id="client" name="client" 
                           value="{{ request('client') }}" placeholder="Search client...">
                </div>
   <div class="col-md-3">
    <label for="invoiceRefFilter" class="form-label">Invoice Ref No</label>
    <input type="text" class="form-control" id="invoiceRefFilter" 
           placeholder="Search invoice ref no..." autocomplete="off">
</div>
                
                <div class="col-md-12 mt-3">
                     <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('premiertax.sales.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
     
    </div>

    <div class="row mb-2">
        <div class="col-md-12 d-flex flex-wrap gap-2 align-items-center">
            <button onclick="printSectionReport('supplierRegisterSection')" class="btn btn-info">
                <i class="mdi mdi-printer"></i> Supplier Register
            </button>
            <button onclick="printSectionReport('thirdScheduleSection')" class="btn btn-warning">
                <i class="mdi mdi-printer"></i> Third Schedule
            </button>
            <button onclick="printSectionReport('supplierRegisterSection')" class="btn btn-success">
                <i class="mdi mdi-printer"></i> Customer Print
            </button>
            <h5 class="mb-0 badge bg-primary fs-6" id="ghTotalCount">
                GH 236 Total: {{ number_format($ghTotal ?? 0, 2) }}
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Bill No</th>
                            <th>Invoice Ref No</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesInvoices as $invoice)
                            <tr>
                                <td>{{ $invoice->fbr_invoice_no }}</td>
                                <td>{{ $invoice->invoice_ref_no ?? 'N/A' }}</td>
                                <td>{{ $invoice->buyer_business_name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</td>
                                <td>
                                                                        <a href="{{ route('premiertax.sale.invoice', $invoice->id) }}" 
                                       class="btn btn-primary btn-sm" target="_blank">
                                        <i class="mdi mdi-printer"></i> Print
                                    </a>
                                    <a href="{{ route('premiertax.sale.third-schedule', $invoice->id) }}" 
                                       class="btn btn-warning btn-sm" target="_blank">
                                        <i class="mdi mdi-printer"></i> Print (3rd Schedule)
                                    </a>
                                    <a href="{{ route('premiertax.sale.standard-invoice', $invoice->id) }}" 
                                       class="btn btn-info btn-sm" target="_blank">
                                        <i class="mdi mdi-printer"></i> Standard Invoice
                                    </a>
                                    <a href="{{ route('premiertax.sale.commercial-print', $invoice->id) }}" 
                                       class="btn btn-success btn-sm" target="_blank">
                                        <i class="mdi mdi-printer"></i> Commercial Print
                                    </a>
                                    <form action="{{ route('reports.sales.delete', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="mdi mdi-delete"></i> Delete
                                        </button>
                                    </form>
                        

                        
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No sales invoices found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Register Print Section -->
<div id="supplierRegisterSection" style="display: none;" data-title="Supply Register">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .register-report .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin: 6px 0;
        }
        .register-report {
            width: 100%;
            margin: 0 auto;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            color: #000;
            background: #fff;
            box-sizing: border-box;
        }
        .register-report .header {
            position: relative;
        }
        .register-report .page-label {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 10px;
        }
        .register-report .company {
            text-align: center;
            margin-bottom: 5px;
        }
        .register-report .company h2 {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .register-report .company .address,
        .register-report .company .taxno {
            font-size: 10px;
            margin-top: 3px;
        }
        .register-report .title {
            text-align: center;
            color: blue;
            font-style: italic;
            font-size: 28px;
            font-weight: bold;
            margin: 6px 0;
        }
        .register-report .date-box {
            width: 220px;
            border: 1px solid #000;
            padding: 5px;
            margin: 0;
        }
        .register-report .date-box .date-label {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }
        .register-report .date-box table {
            width: 100%;
            border: none;
        }
        .register-report .date-box td {
            border: none;
            padding: 1px 0;
        }
        .register-report .date-box td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .register-report hr {
            border: 1px solid #000;
            margin: 5px 0;
        }
        .register-report table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .register-report table.data th,
        .register-report table.data td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            word-wrap: break-word;
        }
        .register-report table.data th {
            background: #d9f2b4;
            font-weight: bold;
        }
        .register-report table.data td.no {
            text-align: right;
            white-space: nowrap;
        }
        .register-report .footer-notes {
            margin-top: 6px;
            text-align: right;
            font-size: 10px;
        }
    </style>
    <div class="register-report">
        <div class="header">
            <div class="page-label">Page 1 of 1</div>
            <div class="company">
                <h2>{{ $companyInfo['name'] ?? '' }}</h2>
                <div class="address">{{ $companyInfo['address'] ?? '' }}</div>
                <div class="taxno">STRN NO: {{ $companyInfo['strn'] ?? ($companyInfo['ntn'] ?? '') }}</div>
            </div>
            <div class="title">Supply Register</div>
            <div class="header-flex">
                <div class="date-box">
                    <div class="date-label">Date</div>
                    <table>
                        <tr>
                            <td>Date From :</td>
                            <td>{{ $reportStart ?? '' ? date('d-m-y', strtotime($reportStart)) : 'Start' }}</td>
                        </tr>
                        <tr>
                            <td>Date To :</td>
                            <td>{{ $reportEnd ?? '' ? date('d-m-y', strtotime($reportEnd)) : 'End' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="no-print" style="margin-bottom: 2px;">
                    <button type="button" onclick="exportTableToExcel('Supply_Register.xlsx')" style="background-color: #28a745; color: #fff; border: 1px solid #1e7e34; padding: 6px 16px; font-size: 12px; font-weight: bold; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-family: sans-serif;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        Export Excel
                    </button>
                </div>
            </div>
            <hr>
        </div>
        <table class="data">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>INVOICE NO</th>
                    <th>CUSTOMER NAME</th>
                    <th>PRODUCT NAME</th>
                    <th>QTY</th>
                    <th>VALUE EXC.SALES TAX</th>
                    <th>SALES TAX RATE</th>
                    <th>SALES TAX AMOUNT</th>
                    <th>VALUE INC.SALES TAX</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registerRows ?? [] as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['invoice_no'] }}</td>
                    <td>{{ $row['customer'] }}</td>
                    <td>{{ $row['product'] }}</td>
                    <td class="no">{{ number_format($row['qty'], 2) }}{{ $row['unit'] ? ' ' . $row['unit'] : '' }}</td>
                    <td class="no">{{ number_format($row['value_excl'], 2) }}</td>
                    <td class="no">{{ number_format($row['stax_rate'], 2) }}%</td>
                    <td class="no">{{ number_format($row['stax_amt'], 2) }}</td>
                    <td class="no">{{ number_format($row['value_inc'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            @if(count($registerRows ?? []) > 0)
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;font-weight:bold;">TOTAL</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('qty'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('value_excl'), 2) }}</td>
                    <td></td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('stax_amt'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('value_inc'), 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="footer-notes">Printed on: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<!-- Third Schedule Print Section -->
<div id="thirdScheduleSection" style="display: none;" data-title="Third Schedule Register">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .register-report .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin: 6px 0;
        }
        .register-report {
            width: 100%;
            margin: 0 auto;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            color: #000;
            background: #fff;
            box-sizing: border-box;
        }
        .register-report .header {
            position: relative;
        }
        .register-report .page-label {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 10px;
        }
        .register-report .company {
            text-align: center;
            margin-bottom: 5px;
        }
        .register-report .company h2 {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .register-report .company .address,
        .register-report .company .taxno {
            font-size: 10px;
            margin-top: 3px;
        }
        .register-report .title {
            text-align: center;
            color: blue;
            font-style: italic;
            font-size: 28px;
            font-weight: bold;
            margin: 6px 0;
        }
        .register-report .date-box {
            width: 220px;
            border: 1px solid #000;
            padding: 5px;
            margin: 0;
        }
        .register-report .date-box .date-label {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }
        .register-report .date-box table {
            width: 100%;
            border: none;
        }
        .register-report .date-box td {
            border: none;
            padding: 1px 0;
        }
        .register-report .date-box td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .register-report hr {
            border: 1px solid #000;
            margin: 5px 0;
        }
        .register-report table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .register-report table.data th,
        .register-report table.data td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            word-wrap: break-word;
        }
        .register-report table.data th {
            background: #d9f2b4;
            font-weight: bold;
        }
        .register-report table.data td.no {
            text-align: right;
            white-space: nowrap;
        }
        .register-report .footer-notes {
            margin-top: 6px;
            text-align: right;
            font-size: 10px;
        }
    </style>
    <div class="register-report">
        <div class="header">
            <div class="page-label">Page 1 of 1</div>
            <div class="company">
                <h2>{{ $companyInfo['name'] ?? '' }}</h2>
                <div class="address">{{ $companyInfo['address'] ?? '' }}</div>
                <div class="taxno">STRN NO: {{ $companyInfo['strn'] ?? ($companyInfo['ntn'] ?? '') }}</div>
            </div>
            <div class="title">Third Schedule Register</div>
            <div class="header-flex">
                <div class="date-box">
                    <div class="date-label">Date</div>
                    <table>
                        <tr>
                            <td>Date From :</td>
                            <td>{{ $reportStart ?? '' ? date('d-m-y', strtotime($reportStart)) : 'Start' }}</td>
                        </tr>
                        <tr>
                            <td>Date To :</td>
                            <td>{{ $reportEnd ?? '' ? date('d-m-y', strtotime($reportEnd)) : 'End' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="no-print" style="margin-bottom: 2px;">
                    <button type="button" onclick="exportTableToExcel('Third_Schedule_Register.xlsx')" style="background-color: #28a745; color: #fff; border: 1px solid #1e7e34; padding: 6px 16px; font-size: 12px; font-weight: bold; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-family: sans-serif;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        Export Excel
                    </button>
                </div>
            </div>
            <hr>
        </div>
        <table class="data">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>INVOICE NO</th>
                    <th>CUSTOMER NAME</th>
                    <th>PRODUCT NAME</th>
                    <th>QTY</th>
                    <th style="display: none;">RATE</th>
                    <th>RETAIL EXCL.</th>
                    <th>RETAIL S.TAX</th>
                    <th>RETAIL INCL.</th>
                    <th>DISCOUNT</th>
                    <th>TRADE EXCL.</th>
                    <th>TRADE S.TAX</th>
                    <th>VALUE WITH S.TAX</th>
                    <th>U/S 236 G/H</th>
                    <th>FURTHER TAX</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registerRows ?? [] as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['invoice_no'] }}</td>
                    <td>{{ $row['customer'] }}</td>
                    <td>{{ $row['product'] }}</td>
                    <td class="no">{{ number_format($row['qty'], 2) }}{{ $row['unit'] ? ' ' . $row['unit'] : '' }}</td>
                    <td class="no" style="display: none;">{{ number_format($row['rate'], 4) }}</td>
                    <td class="no">{{ number_format($row['retail_excl'], 2) }}</td>
                    <td class="no">{{ number_format($row['retail_tax'], 2) }}</td>
                    <td class="no">{{ number_format($row['retail_incl'], 2) }}</td>
                    <td class="no">{{ number_format($row['discount'], 2) }}</td>
                    <td class="no">{{ number_format($row['trade_excl'], 2) }}</td>
                    <td class="no">{{ number_format($row['trade_tax'], 2) }}</td>
                    <td class="no">{{ number_format($row['trade_with_tax'], 2) }}</td>
                    <td class="no">{{ number_format($row['us236'], 2) }}</td>
                    <td class="no">{{ number_format($row['further_tax'], 2) }}</td>
                    <td class="no">{{ number_format($row['amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            @if(count($registerRows ?? []) > 0)
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;font-weight:bold;">TOTAL</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('qty'), 2) }}</td>
                    <td style="display: none;"></td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('retail_excl'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('retail_tax'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('retail_incl'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('discount'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('trade_excl'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('trade_tax'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('trade_with_tax'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('us236'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('further_tax'), 2) }}</td>
                    <td class="no" style="font-weight:bold;">{{ number_format(collect($registerRows)->sum('amount'), 2) }}</td>
                </tr>
                <tr>
                    <td colspan="14" style="text-align:right;font-weight:bold;">GH 236 TOTAL</td>
                    <td class="no" style="font-weight:bold;">{{ number_format($ghTotal ?? 0, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="footer-notes">Printed on: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: function() {
                return $(this).data('placeholder') || "Select an option";
            },
            allowClear: true
        });
        $(document).ready(function() {
    var $table = $('table.dt-responsive tbody');
    var $rows = $table.find('tr').get();

    $rows.sort(function(a, b) {
        var refA = parseInt($(a).find('td').eq(1).text().trim(), 10) || 0;
        var refB = parseInt($(b).find('td').eq(1).text().trim(), 10) || 0;
        return refA - refB;
    });

    $.each($rows, function(index, row) {
        $table.append(row);
    });
});
    });
    $('#invoiceRefFilter').on('keyup', function() {
    var value = $(this).val().toLowerCase().trim();
    $('table.dt-responsive tbody tr').each(function() {
        var refCell = $(this).find('td').eq(1); // 2nd column = Invoice Ref No
        var text = refCell.text().toLowerCase();
        $(this).toggle(text.indexOf(value) > -1);
    });
});

    function buildExcelTableFromContainer(container, defaultTitle) {
        var table = container.querySelector("table.data") || container.querySelector("table");
        if (!table) return null;

        var title = defaultTitle || container.getAttribute('data-title') || document.title || "Register";
        var cloneTable = table.cloneNode(true);
        var hiddenEls = cloneTable.querySelectorAll('[style*="display: none"], [style*="display:none"]');
        hiddenEls.forEach(function(el) { el.remove(); });

        var colCount = 1;
        var firstRow = cloneTable.querySelector('thead tr') || cloneTable.querySelector('tr');
        if (firstRow) {
            colCount = firstRow.children.length;
        }
        colCount = Math.max(colCount, 2);

        var pageLabelEl = container.querySelector('.page-label');
        var pageLabel = pageLabelEl ? pageLabelEl.innerText.trim() : 'Page 1 of 1';

        var companyH2 = container.querySelector('.company h2');
        var companyName = companyH2 ? companyH2.innerText.trim() : '';

        var companyAddrEl = container.querySelector('.company .address');
        var companyAddr = companyAddrEl ? companyAddrEl.innerText.trim() : '';

        var companyTaxEl = container.querySelector('.company .taxno');
        var companyTax = companyTaxEl ? companyTaxEl.innerText.trim() : '';

        var titleEl = container.querySelector('.title');
        var reportTitle = titleEl ? titleEl.innerText.trim() : title;

        var dateFrom = '';
        var dateTo = '';
        var dateBoxTable = container.querySelector('.date-box table');
        if (dateBoxTable) {
            var dRows = dateBoxTable.querySelectorAll('tr');
            if (dRows.length >= 1 && dRows[0].children.length >= 2) {
                dateFrom = dRows[0].children[1].innerText.trim();
            }
            if (dRows.length >= 2 && dRows[1].children.length >= 2) {
                dateTo = dRows[1].children[1].innerText.trim();
            }
        }

        var topRows = '';
        topRows += '<tr><td colspan="' + colCount + '" class="no-border" style="text-align: right; font-size: 10pt; font-family: \'Times New Roman\', serif; border: none;">' + pageLabel + '</td></tr>';
        if (companyName) {
            topRows += '<tr><td colspan="' + colCount + '" class="no-border" style="text-align: center; font-size: 16pt; font-weight: bold; font-family: \'Times New Roman\', serif; text-transform: uppercase; border: none;">' + companyName + '</td></tr>';
        }
        if (companyAddr) {
            topRows += '<tr><td colspan="' + colCount + '" class="no-border" style="text-align: center; font-size: 10pt; font-family: \'Times New Roman\', serif; border: none;">' + companyAddr + '</td></tr>';
        }
        if (companyTax) {
            topRows += '<tr><td colspan="' + colCount + '" class="no-border" style="text-align: center; font-size: 10pt; font-family: \'Times New Roman\', serif; border: none;">' + companyTax + '</td></tr>';
        }
        if (reportTitle) {
            topRows += '<tr><td colspan="' + colCount + '" class="no-border" style="text-align: center; font-size: 20pt; font-weight: bold; font-style: italic; color: #0000FF; font-family: \'Times New Roman\', serif; padding: 6px 0; border: none;">' + reportTitle + '</td></tr>';
        }
        topRows += '<tr>'
            + '<td colspan="2" class="date-border-top" style="font-weight: bold; font-family: \'Times New Roman\', serif; padding: 3px 5px; background-color: #f8f9fa;">Date</td>'
            + (colCount > 2 ? '<td colspan="' + (colCount - 2) + '" class="no-border" style="border: none;"></td>' : '')
            + '</tr>';
        topRows += '<tr>'
            + '<td class="date-border-mid" style="font-family: \'Times New Roman\', serif; padding: 2px 5px;">Date From :</td>'
            + '<td class="date-border-mid" style="font-weight: bold; text-align: right; font-family: \'Times New Roman\', serif; padding: 2px 5px;">' + dateFrom + '</td>'
            + (colCount > 2 ? '<td colspan="' + (colCount - 2) + '" class="no-border" style="border: none;"></td>' : '')
            + '</tr>';
        topRows += '<tr>'
            + '<td class="date-border-bot" style="font-family: \'Times New Roman\', serif; padding: 2px 5px;">Date To :</td>'
            + '<td class="date-border-bot" style="font-weight: bold; text-align: right; font-family: \'Times New Roman\', serif; padding: 2px 5px;">' + dateTo + '</td>'
            + (colCount > 2 ? '<td colspan="' + (colCount - 2) + '" class="no-border" style="border: none;"></td>' : '')
            + '</tr>';
        topRows += '<tr><td colspan="' + colCount + '" class="no-border" style="height: 12px; border: none;"></td></tr>';

        var thead = cloneTable.querySelector('thead');
        if (thead) {
            thead.insertAdjacentHTML('afterbegin', topRows);
        } else {
            cloneTable.insertAdjacentHTML('afterbegin', '<thead>' + topRows + '</thead>');
        }

        return cloneTable;
    }

    function printSectionReport(sectionId) {
        var section = document.getElementById(sectionId);
        var styleHTML = section.querySelector('style').outerHTML;
        var reportHTML = section.querySelector('.register-report').outerHTML;
        var title = section.getAttribute('data-title') || 'Register';

        var scriptHTML = '<script>\n'
            + 'function exportTableToExcel(filename) {\n'
            + '    var container = document.querySelector(".register-report") || document.body;\n'
            + '    var fname = filename || "Register.xlsx";\n'
            + '    if (window.opener && typeof window.opener.doExcelExport === "function") {\n'
            + '        window.opener.doExcelExport(container, fname);\n'
            + '    } else {\n'
            + '        alert("Export not available. Please open the report again from the main window.");\n'
            + '    }\n'
            + '}\n'
            + '<\/script>';

        var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + title + '</title>'
            + styleHTML
            + '</head><body>' + reportHTML + scriptHTML + '</body></html>';
        var url = URL.createObjectURL(new Blob([html], { type: 'text/html' }));
        var newTab = window.open(url, '_blank');
        if (!newTab) {
            alert('Please allow popups for this site to open the report.');
            return;
        }
        setTimeout(function() {
            newTab.focus();
            newTab.print();
        }, 400);
    }

    async function doExcelExport(container, filename) {
        var ExcelJS = window.ExcelJS;
        if (!ExcelJS) { alert('Excel library not available. Please refresh the page.'); return; }

        var title = (container.getAttribute && container.getAttribute('data-title')) || document.title || 'Register';
        var fname = filename || (title.replace(/\s+/g, '_') + '.xlsx');
        var fnameXlsx = fname.endsWith('.xlsx') ? fname : (fname.replace(/\.xls$/i, '') + '.xlsx');

        var table = container.querySelector('table.data') || container.querySelector('table');
        if (!table) { alert('No data found to export.'); return; }

        var companyName = (container.querySelector('.company h2') || {}).innerText || '';
        var addr       = (container.querySelector('.company .address') || {}).innerText || '';
        var tax        = (container.querySelector('.company .taxno') || {}).innerText || '';
        var pageLabel  = (container.querySelector('.page-label') || {}).innerText || '';
        var reportTitle= (container.querySelector('.title') || {}).innerText || title;
        companyName = companyName.trim(); addr = addr.trim(); tax = tax.trim();
        pageLabel = pageLabel.trim(); reportTitle = reportTitle.trim();

        var dateFrom = '', dateTo = '';
        var dateBoxTbl = container.querySelector('.date-box table');
        if (dateBoxTbl) {
            var dr = dateBoxTbl.querySelectorAll('tr');
            if (dr.length >= 1 && dr[0].children.length >= 2) dateFrom = dr[0].children[1].innerText.trim();
            if (dr.length >= 2 && dr[1].children.length >= 2) dateTo   = dr[1].children[1].innerText.trim();
        }

        var headers = [];
        table.querySelectorAll('thead th').forEach(function(th) {
            if (th.style.display !== 'none') headers.push(th.innerText.trim());
        });

        var bodyRows = [];
        table.querySelectorAll('tbody tr').forEach(function(tr) {
            var row = [];
            tr.querySelectorAll('td').forEach(function(td) {
                if (td.style.display !== 'none') row.push(td.innerText.trim());
            });
            if (row.length > 0) bodyRows.push(row);
        });

        var footerRows = [];
        table.querySelectorAll('tfoot tr').forEach(function(tr) {
            var row = [];
            tr.querySelectorAll('td').forEach(function(td) {
                if (td.style.display !== 'none') row.push(td.innerText.trim());
            });
            if (row.length > 0) footerRows.push(row);
        });

        var colCount = headers.length || 2;
        var workbook = new ExcelJS.Workbook();
        var ws = workbook.addWorksheet(reportTitle.substring(0, 31));
        var r = 1;

        function thin() { return { style: 'thin' }; }
        function borderAll() { return { top: thin(), left: thin(), bottom: thin(), right: thin() }; }

        // Page label — right-aligned in last column
        if (pageLabel) {
            var pc = ws.getRow(r).getCell(colCount);
            pc.value = pageLabel;
            pc.alignment = { horizontal: 'right' };
            pc.font = { name: 'Times New Roman', size: 10 };
            r++;
        }

        // Company Name — bold, 16pt, centered, merged
        if (companyName) {
            ws.mergeCells(r, 1, r, colCount);
            var c = ws.getRow(r).getCell(1);
            c.value = companyName.toUpperCase();
            c.font = { bold: true, size: 16, name: 'Times New Roman' };
            c.alignment = { horizontal: 'center' };
            r++;
        }

        // Address — 10pt, centered
        if (addr) {
            ws.mergeCells(r, 1, r, colCount);
            var c = ws.getRow(r).getCell(1);
            c.value = addr;
            c.font = { size: 10, name: 'Times New Roman' };
            c.alignment = { horizontal: 'center' };
            r++;
        }

        // STRN — 10pt, centered
        if (tax) {
            ws.mergeCells(r, 1, r, colCount);
            var c = ws.getRow(r).getCell(1);
            c.value = tax;
            c.font = { size: 10, name: 'Times New Roman' };
            c.alignment = { horizontal: 'center' };
            r++;
        }

        // Report Title — bold, italic, 18pt, blue, centered
        if (reportTitle) {
            ws.mergeCells(r, 1, r, colCount);
            var c = ws.getRow(r).getCell(1);
            c.value = reportTitle;
            c.font = { bold: true, italic: true, size: 18, color: { argb: 'FF0000FF' }, name: 'Times New Roman' };
            c.alignment = { horizontal: 'center' };
            ws.getRow(r).height = 28;
            r++;
        }

        // Date box — bordered like print preview
        var dRow1 = ws.getRow(r);
        dRow1.getCell(1).value = 'Date';
        dRow1.getCell(1).font = { bold: true, name: 'Times New Roman', size: 10 };
        dRow1.getCell(1).border = { top: thin(), left: thin(), right: thin() };
        dRow1.getCell(2).border = { top: thin(), right: thin() };
        r++;

        var dRow2 = ws.getRow(r);
        dRow2.getCell(1).value = 'Date From :';
        dRow2.getCell(1).font = { name: 'Times New Roman', size: 10 };
        dRow2.getCell(1).border = { left: thin(), right: thin() };
        dRow2.getCell(2).value = dateFrom;
        dRow2.getCell(2).font = { bold: true, name: 'Times New Roman', size: 10 };
        dRow2.getCell(2).alignment = { horizontal: 'right' };
        dRow2.getCell(2).border = { right: thin() };
        r++;

        var dRow3 = ws.getRow(r);
        dRow3.getCell(1).value = 'Date To :';
        dRow3.getCell(1).font = { name: 'Times New Roman', size: 10 };
        dRow3.getCell(1).border = { left: thin(), bottom: thin(), right: thin() };
        dRow3.getCell(2).value = dateTo;
        dRow3.getCell(2).font = { bold: true, name: 'Times New Roman', size: 10 };
        dRow3.getCell(2).alignment = { horizontal: 'right' };
        dRow3.getCell(2).border = { bottom: thin(), right: thin() };
        r++;

        r++; // Spacer row

        // Table header row — bold, green fill, centered, bordered
        if (headers.length > 0) {
            var hRow = ws.getRow(r);
            headers.forEach(function(h, i) {
                var cell = hRow.getCell(i + 1);
                cell.value = h;
                cell.font = { bold: true, name: 'Times New Roman', size: 9 };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9F2B4' } };
                cell.alignment = { horizontal: 'center', wrapText: true, vertical: 'middle' };
                cell.border = borderAll();
            });
            hRow.height = 30;
            r++;
        }

        // Data rows
        bodyRows.forEach(function(rowData) {
            var dRow = ws.getRow(r);
            rowData.forEach(function(val, i) {
                var cell = dRow.getCell(i + 1);
                cell.value = val;
                cell.font = { name: 'Times New Roman', size: 9 };
                cell.border = borderAll();
            });
            r++;
        });

        // Footer / totals rows — bold
        footerRows.forEach(function(rowData) {
            var dRow = ws.getRow(r);
            rowData.forEach(function(val, i) {
                var cell = dRow.getCell(i + 1);
                cell.value = val;
                cell.font = { bold: true, name: 'Times New Roman', size: 9 };
                cell.border = borderAll();
            });
            r++;
        });

        // Column widths
        var colWidths = [];
        for (var ci = 0; ci < colCount; ci++) {
            var w = 14;
            if (ci === 0) w = 11;
            else if (ci === 1) w = 22;
            else if (ci === 2) w = 24;
            else if (ci === 3) w = 18;
            colWidths.push({ width: w });
        }
        ws.columns = colWidths;

        // Write and download
        var buffer = await workbook.xlsx.writeBuffer();
        var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = fnameXlsx;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function exportSectionToExcel(sectionId, customFilename) {
        var section = document.getElementById(sectionId);
        if (!section) return;
        var title = section.getAttribute('data-title') || 'Report';
        var filename = customFilename || (title.replace(/\s+/g, '_') + '.xlsx');
        doExcelExport(section, filename);
    }
</script>
@endsection
