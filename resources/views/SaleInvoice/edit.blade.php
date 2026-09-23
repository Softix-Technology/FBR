@extends('layouts.app')
@section('content')
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="font-semibold text-xl text-gray-800 m-0" style="font-weight: 700;">
                    <i class="mdi mdi-square-edit-outline text-primary me-1"></i> Edit & Resubmit FBR Sale Invoice
                </h3>
                <a href="{{ route('premiertax.sales.index') }}"
                   class="btn btn-secondary btn-sm shadow-sm"
                   style="font-weight: 600; display: inline-flex; align-items: center;">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Sales Invoices
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- 72-Hour Banner -->
                    <div style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1.5px solid #6366f1; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px;" class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <span style="background-color: #4f46e5; color: #ffffff !important; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 50px; display: inline-flex; align-items: center; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);">
                                <i class="mdi mdi-clock-fast me-1" style="font-size: 16px;"></i> 72-Hour Correction Mode
                            </span>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #1e1b4b;">
                                    Original FBR Invoice #: <span style="color: #4338ca; font-family: monospace; font-size: 16px; font-weight: 800; background: #ffffff; padding: 2px 8px; border-radius: 4px; border: 1px solid #c7d2fe;">{{ $invoice->fbr_invoice_no }}</span>
                                </div>
                                <div style="font-size: 13px; color: #475569; margin-top: 3px;">
                                    <i class="mdi mdi-calendar me-1"></i> Issued: <strong>{{ \Carbon\Carbon::parse($invoice->created_at ?: $invoice->invoice_date)->format('d M Y, h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="text-md-end">
                            <span style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center;">
                                ⏳ {{ $hoursRemaining }} hours remaining for correction
                            </span>
                        </div>
                    </div>

                    <!-- Invoice Form -->
                    <form id="invoiceForm" method="POST" action="{{ route('premiertax.sale.resubmit', $invoice->id) }}" class="space-y-8">
                        @csrf

                        <!-- Seller Information -->
                        <div class="bg-gray-50 rounded-lg">
                            <div class="seller-accordion-header cursor-pointer p-6 flex items-center justify-between hover:bg-gray-100 transition-colors duration-200" onclick="toggleSellerAccordion()">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-900">Seller Information</h2>
                                </div>
                                <svg id="sellerAccordionIcon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div id="sellerAccordionContent" class="hidden px-6 pb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="sellerNTNCNIC" class="block text-sm font-medium text-gray-700 mb-1">CNIC/NTN</label>
                                        <input type="text" id="sellerNTNCNIC" name="sellerNTNCNIC" placeholder="0000000000000" value="{{ $invoice->seller_ntn_cnic ?: ($user->cinc_ntn ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="sellerBusinessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                        <input type="text" id="sellerBusinessName" name="sellerBusinessName" placeholder="Your Business Name" value="{{ $invoice->seller_business_name ?: ($user->business_name ?? $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="sellerProvince" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                        <select id="sellerProvince" name="sellerProvince" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 province-select">
                                            <option value="">Select Province</option>
                                            @if($provinces)
                                                @foreach($provinces as $province)
                                                    <option value="{{ $province['stateProvinceCode'] }}"
                                                        {{ ($invoice->seller_province == $province['stateProvinceCode'] || ($user->province == $province['stateProvinceCode'] && !$invoice->seller_province)) ? 'selected' : '' }}>
                                                        {{ $province['stateProvinceDesc'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="md:col-span-2 mb-4">
                                        <label for="sellerAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <textarea id="sellerAddress" name="sellerAddress" placeholder="Seller Address" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $invoice->seller_address ?: ($user->address ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Information -->
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900">Invoice Information</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label for="invoiceType" class="block text-sm font-medium text-gray-700 mb-1">Invoice Type</label>
                                    <select id="invoiceType" name="invoiceType" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 invoice-type-select">
                                        <option value="">Loading invoice types...</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="invoiceDate" class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                                    <input type="date" id="invoiceDate" name="invoiceDate" value="{{ $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="invoiceRefNo" class="block text-sm font-medium text-gray-700 mb-1">Invoice Reference No. (Original FBR No)</label>
                                    <input type="text" id="invoiceRefNo" name="invoiceRefNo" placeholder="Enter reference number" value="{{ $invoice->fbr_invoice_no ?: $invoice->invoice_ref_no }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly title="Linked to original FBR Invoice">
                                </div>
                                @if($user->use_sandbox)
                                <div>
                                    <label for="scenarioId" class="block text-sm font-medium text-gray-700 mb-1">Scenario ID</label>
                                    <input type="text" id="scenarioId" name="scenarioId" value="{{ $invoice->scenario_id ?: 'SN000' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Buyer Information -->
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900">Buyer Information</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="relative">
                                    <label for="buyerNTNCNIC" class="block text-sm font-medium text-gray-700 mb-1">NTN/CNIC</label>
                                    <input type="text" id="buyerNTNCNIC" name="buyerNTNCNIC" placeholder="0000000000000" value="{{ $invoice->buyer_ntn_cnic }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" autocomplete="off">

                                    <!-- Autocomplete suggestions dropdown -->
                                    <div id="buyerNTNAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden" style="max-height: 250px; overflow-y: auto;">
                                        <div class="p-2 text-sm text-gray-500 text-center">
                                            Start typing to search buyers...
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="buyerBusinessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                    <input type="text" id="buyerBusinessName" name="buyerBusinessName" placeholder="Buyer Business Name" value="{{ $invoice->buyer_business_name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="buyerProvince" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                    <select id="buyerProvince" name="buyerProvince" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 province-select">
                                        <option value="">Select Province</option>
                                        @if($provinces)
                                            @foreach($provinces as $province)
                                                <option value="{{ $province['stateProvinceCode'] }}"
                                                    {{ $invoice->buyer_province == $province['stateProvinceCode'] ? 'selected' : '' }}>
                                                    {{ $province['stateProvinceDesc'] }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label for="buyerRegistrationType" class="block text-sm font-medium text-gray-700 mb-1">
                                        Registration Type
                                    </label>
                                    <select id="buyerRegistrationType" name="buyerRegistrationType" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Registration Type</option>
                                        <option value="Unregistered" {{ $invoice->buyer_registration_type == 'Unregistered' ? 'selected' : '' }}>Unregistered</option>
                                        <option value="Registered" {{ $invoice->buyer_registration_type == 'Registered' ? 'selected' : '' }}>Registered</option>
                                    </select>
                                </div>
                                <div class="md:col-span-4">
                                    <label for="buyerAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea id="buyerAddress" name="buyerAddress" placeholder="Buyer Address" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 !h-[50px]">{{ $invoice->buyer_address }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="bg-gray-50 p-2 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-900">Invoice Items</h2>
                                </div>
                                <div class="flex flex-col items-end">
                                    <button type="button" id="addItemBtn" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition opacity-50 cursor-not-allowed" title="Please select buyer province and registration type first">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Add Item
                                    </button>
                                    <div id="addItemRequirement" class="mt-1 text-xs text-red-600">
                                        Please select buyer province and registration type first
                                        <button type="button" onclick="validateBuyerRequirements()" class="ml-2 text-blue-600 underline hover:text-blue-800">
                                            Check now
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table id="itemsTable" class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Description</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HS Code</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate (%)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value Sales Excluding ST</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Tax</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                                        <tr id="noItemsRow" class="hidden">
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-6a2 2 0 00-2 2v3a2 2 0 002 2h6a2 2 0 002-2v-3a2 2 0 00-2-2z" />
                                                </svg>
                                                No items added yet. Click "Add Item" to get started.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot id="itemsTableFooter" class="bg-gray-100 border-t-2 border-gray-300 hidden">
                                        <tr class="font-semibold text-gray-900">
                                            <td class="px-4 py-3 text-sm font-bold">Total</td>
                                            <td class="px-4 py-3 text-sm text-center">-</td>
                                            <td class="px-4 py-3 text-sm text-center">-</td>
                                            <td class="px-4 py-3 text-sm font-bold" id="totalQuantity">0</td>
                                            <td class="px-4 py-3 text-sm text-center">-</td>
                                            <td class="px-4 py-3 text-sm font-bold" id="totalValueSales">0.00</td>
                                            <td class="px-4 py-3 text-sm font-bold" id="totalSalesTax">0.00</td>
                                            <td class="px-4 py-3 text-sm text-center" id="overall">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                                 <div>
                            <table>
                                <tr>
                                    <td>
                                        Transportation Charges
                                    </td>
                                    <td>
                                        <input name="furtherexpense" value={{$invoice->expense_col??0}} id='furthertaxexpense'>
                                    </td>
                                   
                                    
                                </tr>
                            </table>
                        </div>
                            <!-- Hidden container for form inputs -->
                            <div id="hiddenItemsContainer" style="display: none;">
                                <!-- Form inputs will be generated here for submission -->
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-4 pt-3 border-top">
                            <a href="{{ route('premiertax.sales.index') }}" 
                               class="btn btn-secondary px-4 py-2" 
                               style="font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; border-radius: 6px;">
                                <i class="mdi mdi-arrow-left me-1"></i> Cancel & Back
                            </a>
                            <button type="button" id="submitBtn" 
                                    class="btn btn-primary px-4 py-2 shadow" 
                                    style="background-color: #4f46e5 !important; border-color: #4f46e5 !important; color: #ffffff !important; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; border-radius: 6px;">
                                <i class="mdi mdi-check-circle-outline me-2" style="font-size: 18px;"></i> Resubmit Correction to FBR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    <div id="statusMessages" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Add Item Modal - Complete version from main invoicing page -->
    <div id="addItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-8 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Add Invoice Item</h3>
                    <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-6">
                    <form id="itemForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sale Type <span class="text-red-500">*</span></label>
                                <select id="modalSaleType" name="saleType" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sale-type-select">
                                    <option value="">Select Sale Type</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">HS Code <span class="text-red-500">*</span></label>
                                <select id="modalHsCode" name="hsCode" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm hs-code-select">
                                    <option value="">Select HS Code</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product Description <span class="text-red-500">*</span></label>
                                <input type="text" id="modalProductDescription" name="productDescription" placeholder="Enter product description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Rate (%) <span class="text-red-500">*</span>
                                    <span class="rate-loader hidden ml-2 text-blue-600">Loading...</span>
                                </label>
                                <select id="modalRate" name="rate" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm rate-select">
                                    <option value="">Select Rate</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Unit of Measure <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500">(Select HS Code first)</span>
                                </label>
                                <select id="modalUoM" name="uoM" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm uom-select">
                                    <option value="">Select Unit of Measure</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" id="modalQuantity" name="quantity" placeholder="0" min="0" step="any" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rate <span class="text-red-500">*</span></label>
                                <input type="number" id="modalRateValues" name="rateValues" placeholder="0" min="0" step="any" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Values <span class="text-red-500">*</span></label>
                                <input type="number" id="modalTotalValues" name="totalValues" placeholder="0" min="0" step="any" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Value Sales Excluding ST <span class="text-red-500">*</span></label>
                                <input type="number" id="modalValueSalesExcludingST" name="valueSalesExcludingST" placeholder="0" min="0" step="any" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sales Tax Applicable</label>
                                <input type="number" id="modalSalesTaxApplicable" name="salesTaxApplicable" placeholder="0.00" min="0" step="any" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sales-tax-field">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fixed Notified Value/Retail Price <span class="text-red-500 schedule-3rd-fn-required hidden">*</span></label>
                                <input type="number" id="modalFixedNotifiedValueOrRetailPrice" name="fixedNotifiedValueOrRetailPrice" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <!-- 3rd Schedule specific fields (shown only when 3rd Schedule sale type is selected) -->
                            <div class="schedule-3rd-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">G/H (%)</label>
                                <input type="number" id="modalGhPercent" name="ghPercent" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="schedule-3rd-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">G/H Amount</label>
                                <input type="number" id="modalGhAmount" name="ghAmount" placeholder="0.00" min="0" step="any" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="schedule-3rd-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount (%)</label>
                                <input type="number" id="modalDiscountPercent" name="discountPercent" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="schedule-3rd-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount</label>
                                <input type="number" id="modalDiscountAmount" name="discountAmount" placeholder="0.00" min="0" step="any" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sales Tax Withheld at Source</label>
                                <input type="number" id="modalSalesTaxWithheldAtSource" name="salesTaxWithheldAtSource" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Extra Tax</label>
                                <input type="text" id="modalExtraTax" name="extraTax" placeholder="Enter extra tax details" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Further Tax</label>
                                <input type="number" id="modalFurtherTax" name="furtherTax" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    SRO Schedule No.
                                    <span class="text-red-500 hidden sro-schedule-required">*</span>
                                </label>
                                <select id="modalSroScheduleNo" name="sroScheduleNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sro-schedule-select">
                                    <option value="">Select SRO Schedule</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">FED Payable</label>
                                <input type="number" id="modalFedPayable" name="fedPayable" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    SRO Item Serial No.
                                    <span class="text-red-500 hidden sro-item-required">*</span>
                                </label>
                                <select id="modalSroItemSerialNo" name="sroItemSerialNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sro-item-select">
                                    <option value="">Select SRO Item</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="d-flex align-items-center justify-content-end pt-3 border-top gap-2">
                    <button type="button" id="cancelModalBtn" class="btn btn-secondary px-3 py-1.5" style="font-size: 13px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" id="addItemFromModalBtn" class="btn btn-primary px-3 py-1.5" style="font-size: 13px; font-weight: 600; display: inline-flex; align-items: center;">
                        <i class="mdi mdi-plus me-1"></i> Add Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const API_BASE = '{{ url('/') }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const INVOICE_ID = {{ $invoice->id }};
        const IS_EDIT_MODE = true;
    </script>

    <script>
        // Pass data from backend to JavaScript
        window.appData = {
            provinces: @json($provinces ?? []),
            hsCodes: @json($hsCodes ?? []),
            uoMs: @json($uoMs ?? []),
            transactionTypes: @json($transactionTypes ?? []),
            user: {
                cinc_ntn: @json($user->cinc_ntn ?? ''),
                business_name: @json($user->business_name ?? $user->name ?? ''),
                province: @json($user->province ?? ''),
                address: @json($user->address ?? ''),
                use_sandbox: @json($user->use_sandbox ?? true)
            },
            @php
                $existingItems = $invoice->items;
                if (!is_array($existingItems)) {
                    $existingItems = json_decode($existingItems ?? '[]', true);
                }
            @endphp
            draftData: @json($existingItems ?? [])
        };
    </script>

    <!-- Include the main invoicing page's JavaScript functionality -->
        <script>
        // All the variables and functions from the main invoicing page
        let itemCounter = 0;
        let editingItemIndex = -1;
        let provinces = window.appData.provinces;
        let hsCodes = window.appData.hsCodes;
        let uoMs = window.appData.uoMs;
        let transactionTypes = window.appData.transactionTypes;
        let userProfile = window.appData.user;
        let documentTypes = [];
        let itemsData = [];
        // Global storage for SRO data
        let sroSchedules = new Map();
        let sroItems = new Map();

        // Load default document types as fallback
        function loadDefaultDocumentTypes() {
            documentTypes = [
                { docTypeId: "Sale Invoice", docDescription: "Sale Invoice" },
                { docTypeId: "Debit Note", docDescription: "Debit Note" },
                { docTypeId: "Credit Note", docDescription: "Credit Note" },
                { docTypeId: "Purchase Invoice", docDescription: "Purchase Invoice" }
            ];

            populateDocumentTypeSelects();
            console.log('Document Types loaded from fallback:', documentTypes.length, 'items');
        }

        // Populate document type selects with HTML options
        function populateDocumentTypeSelects() {
            if (documentTypes && Array.isArray(documentTypes)) {
                // Update all document type selects
                $('.invoice-type-select').each(function() {
                    const select = this;
                    select.innerHTML = '<option value="">Select Invoice Type</option>';

                    documentTypes.forEach(type => {
                        const typeId = type.docTypeId;
                        const typeDesc = type.docDescription;

                        if (typeId && typeDesc) {
                            const option = document.createElement('option');
                            option.value = typeId;
                            option.textContent = typeDesc;
                            select.appendChild(option);
                        }
                    });
                });
                console.log('Document Types populated:', documentTypes.length, 'items');
            }
        }

        // Helper function to find province code by name (for backwards compatibility)
        function findProvinceCodeByName(provinceName) {
            if (!provinceName) return '';
            const cleanName = String(provinceName).trim().toLowerCase();

            // Direct mapping dictionary for all Pakistan provinces/territories
            const map = {
                'balochistan': 2,
                'azad jammu and kashmir': 4,
                'ajk': 4,
                'capital territory': 5,
                'islamabad': 5,
                'khyber pakhtunkhwa': 6,
                'kpk': 6,
                'punjab': 7,
                'sindh': 8,
                'gilgit baltistan': 9,
                'gb': 9
            };
            if (map[cleanName]) return map[cleanName];

            if (provinces && Array.isArray(provinces)) {
                const province = provinces.find(p =>
                    p.stateProvinceDesc &&
                    p.stateProvinceDesc.toLowerCase() === cleanName
                );
                if (province && province.stateProvinceCode) {
                    return province.stateProvinceCode;
                }
            }

            if (!isNaN(parseInt(provinceName))) {
                return parseInt(provinceName);
            }

            return provinceName;
        }

        // Load and populate Document Types (Invoice Types) from FBR API
        async function loadAndPopulateDocumentTypes() {
            try {
                console.log('Loading Document Types from FBR API...');
                const response = await fetch(`${API_BASE}/premiertax/api/fbr/doctypecode`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);
                console.log('Document Types API response:', result);

                if (result.success && result.data && Array.isArray(result.data)) {
                    documentTypes = result.data;
                    populateDocumentTypeSelects();

                    // Re-initialize Select2 for document type selects after loading data
                    $('.invoice-type-select').each(function() {
                        // Only destroy if Select2 is already initialized
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                        // Initialize Select2
                        $(this).select2({
                            placeholder: 'Select Invoice Type',
                            allowClear: true,
                            width: '100%'
                        });
                    });

                    console.log('Document Types loaded successfully:', documentTypes.length, 'items');
                    return true; // Indicate success
                } else {
                    console.error('Failed to load Document Types - Invalid response:', result);
                    if (result.message) {
                        showMessage('Failed to load Invoice Types: ' + result.message, 'error');
                    }
                    return false;
                }
            } catch (error) {
                console.error('Error loading Document Types:', error);
                showMessage('Error loading Invoice Types from FBR server: ' + error.message, 'error');
                return false;
            }
        }

        // Populate Document Type (Invoice Type) selects with HTML options
        function populateDocumentTypeSelects() {
            if (documentTypes && Array.isArray(documentTypes)) {
                // Update all document type selects
                $('.invoice-type-select').each(function() {
                    const select = this;
                    select.innerHTML = '<option value="">Select Invoice Type</option>';

                    documentTypes.forEach(docType => {
                        const docDescription = docType.docDescription;

                        if (docDescription) {
                            const option = document.createElement('option');
                            // Use docDescription for both value and label as requested
                            option.value = docDescription;
                            option.textContent = docDescription;
                            select.appendChild(option);
                        }
                    });
                });
                console.log('Document Types populated:', documentTypes.length, 'items');
            }
        }



        // Populate province selects with HTML options
        function populateProvinceSelects() {
            if (provinces && Array.isArray(provinces)) {
                // Update all province selects
                $('.province-select').each(function() {
                    const select = this;
                    const currentValue = select.value; // Preserve current selection
                    select.innerHTML = '<option value="">Select Province</option>';

                    provinces.forEach(province => {
                        const provinceCode = province.stateProvinceCode;
                        const provinceDesc = province.stateProvinceDesc;

                        if (provinceCode && provinceDesc) {
                            const option = document.createElement('option');
                            option.value = provinceCode;
                            option.textContent = provinceDesc;
                            if (provinceCode === currentValue) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        }
                    });
                });
                console.log('Provinces populated:', provinces.length, 'items');
            }
        }

        // Initialize the page with draft data
        document.addEventListener('DOMContentLoaded', function() {
            // Load and populate data first
            populateProvinceSelects();

            // Load the draft items if they exist
            if (window.appData.draftData && window.appData.draftData.length > 0) {
                window.appData.draftData.forEach(item => {
                    addItemToTable(item);
                });
            }

            // Set invoice type if available — wait for doc types API then use Select2 API to select
            // Wait for document types to load, then set the invoice type
            setTimeout(async () => {
                // Wait for document types to be loaded from API
                await loadAndPopulateDocumentTypes();

                // Small extra delay so Select2 is fully re-initialized after loadAndPopulateDocumentTypes
                setTimeout(() => {
                    const savedInvoiceType = '{{ $invoice->invoice_type }}';
                    if (savedInvoiceType) {
                        // Must use Select2 API (.val + .trigger) — plain .value won't update Select2 display
                        $('#invoiceType').val(savedInvoiceType).trigger('change');
                        console.log('Invoice type pre-selected:', savedInvoiceType);
                    }

                    // Ensure provinces are selected correctly (using Select2 API)
                    if ('{{ $invoice->seller_province }}') {
                        const sellerProvinceValue = findProvinceCodeByName('{{ $invoice->seller_province }}');
                        $('#sellerProvince').val(sellerProvinceValue).trigger('change');
                        console.log('Seller province set to:', sellerProvinceValue);
                    }

                    if ('{{ $invoice->buyer_province }}') {
                        const buyerProvinceValue = findProvinceCodeByName('{{ $invoice->buyer_province }}');
                        $('#buyerProvince').val(buyerProvinceValue).trigger('change');
                        console.log('Buyer province set to:', buyerProvinceValue);
                    }
                }, 300);
            }, 1000); // Timeout to allow API call to complete

            // Validate buyer requirements on load - after all data is loaded
            setTimeout(() => {
                console.log('Running final validation check...');

                // Force trigger change events to ensure Select2 is in sync
                $('#buyerProvince').trigger('change');
                $('#buyerRegistrationType').trigger('change');

                // Wait a bit more for Select2 to process, then validate
                setTimeout(() => {
                    validateBuyerRequirements();
                }, 200);
            }, 2000); // Increased timeout even more
        });

        // Submit function for resubmitting invoice to FBR
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            submitCorrectionInvoice(e);
        });

        // Submit corrected invoice to FBR
        async function submitCorrectionInvoice(e) {
            e.preventDefault();

            const formData = new FormData(document.getElementById('invoiceForm'));
            const data = formDataToObjectWithLabels(formData);

            // Use raw itemsData for items instead of converted form data
            data.items = itemsData.map(item => {
                const clean = { ...item };
                // Map saleType ID to FBR description text if transactionTypes reference data is available
                if (clean.saleType && transactionTypes && Array.isArray(transactionTypes) && transactionTypes.length > 0) {
                    const matchedType = transactionTypes.find(t => String(t.transactioN_TYPE_ID) === String(clean.saleType));
                    if (matchedType && matchedType.transactioN_DESC) {
                        clean.saleType = matchedType.transactioN_DESC;
                    }
                }
                // Map uoM ID to description text if uoMs reference data is available
                if (clean.uoM && uoMs && Array.isArray(uoMs) && uoMs.length > 0) {
                    const matchedUom = uoMs.find(u => String(u.uoM_ID || u.id) === String(clean.uoM));
                    if (matchedUom && (matchedUom.uoM_DESC || matchedUom.description)) {
                        clean.uoM = matchedUom.uoM_DESC || matchedUom.description;
                    }
                }
                Object.keys(clean).forEach(k => {
                    if (k.endsWith('Text') || k === 'rateValue' || k === 'rowId' || k === 'index') delete clean[k];
                });
                return clean;
            });

            delete data._method;
            const apiUrl = '{{ route('premiertax.sale.resubmit', $invoice->id) }}';

            console.log('=== RESUBMIT INVOICE TO FBR API CALL ===');
            console.log('API URL:', apiUrl);
            console.log('Raw Form Data:', data);

            try {
                showMessage('Submitting corrected invoice to FBR...', 'info');
                document.getElementById('submitBtn').disabled = true;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const text = await response.text();
                const cleanText = text.trim().startsWith('{')
                    ? text
                    : text.substring(text.indexOf('{'));

                const result = JSON.parse(cleanText);

                if (result.success) {
                    showMessage(result.message || 'Invoice updated & resubmitted successfully to FBR!', 'success');
                    const newInvoiceNo = result.data?.new_invoice_no || result.data?.invoiceNumber;
                    if (newInvoiceNo) {
                        showMessage('New FBR Invoice Number: ' + newInvoiceNo, 'info');
                    }
                    setTimeout(() => {
                        window.location.href = result.redirect_url || '{{ route('premiertax.sales.index') }}';
                    }, 2000);
                } else {
                    showMessage('Failed to resubmit: ' + (result.message || 'Unknown error'), 'error');
                    if (result.errors) {
                        console.error('Validation errors:', result.errors);
                    }
                }
            } catch (error) {
                console.error('Submit error:', error);
                showMessage('Failed to submit: ' + error.message, 'error');
            } finally {
                document.getElementById('submitBtn').disabled = false;
            }
        }


        // Include all other functions from the main invoicing page
        // (formDataToObject, validateBuyerRequirements, toggleSellerAccordion, etc.)
        // These would be copied from the main page for consistency

        function toggleSellerAccordion() {
            const content = document.getElementById('sellerAccordionContent');
            const icon = document.getElementById('sellerAccordionIcon');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function showMessage(message, type = 'info') {
            const messagesContainer = document.getElementById('statusMessages');
            const messageEl = document.createElement('div');

            let bgColor, textColor, iconSvg;
            switch (type) {
                case 'success':
                    bgColor = 'bg-green-50 border-green-200';
                    textColor = 'text-green-800';
                    iconSvg = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />';
                    break;
                case 'error':
                    bgColor = 'bg-red-50 border-red-200';
                    textColor = 'text-red-800';
                    iconSvg = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />';
                    break;
                default:
                    bgColor = 'bg-blue-50 border-blue-200';
                    textColor = 'text-blue-800';
                    iconSvg = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />';
            }

            messageEl.className = `border rounded-md p-4 ${bgColor} shadow-lg max-w-md`;
            messageEl.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 ${textColor}" viewBox="0 0 20 20" fill="currentColor">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium ${textColor}">
                            ${message}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.remove()" class="${textColor} hover:${textColor.replace('800', '900')}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            messagesContainer.appendChild(messageEl);

            setTimeout(() => {
                if (messageEl.parentElement) {
                    messageEl.remove();
                }
            }, 5000);
        }

        // Simplified formDataToObject for draft saving
        function formDataToObject(formData) {
            const obj = {};

            for (let [key, value] of formData.entries()) {
                if (key.startsWith('items[')) {
                    // Handle items data
                    continue; // Items will be handled separately
                } else {
                    obj[key] = value;
                }
            }

            // Get items from the current itemsData array
            obj.items = itemsData;

            return obj;
        }

                // Item management functions
        function addItemToTable(item) {
            if (!item) return;

            itemCounter++;
            const rowId = `item-row-${itemCounter}`;

            // Add to itemsData array
            itemsData.push({
                ...item,
                rowId: rowId,
                index: itemCounter
            });

            // Update table display
            updateItemsTable();
            updateTotals();
        }

        function updateItemsTable() {
            const tableBody = document.getElementById('itemsTableBody');
            const noItemsRow = document.getElementById('noItemsRow');
            const tableFooter = document.getElementById('itemsTableFooter');

            // Clear existing rows except no-items row
            const rows = tableBody.querySelectorAll('tr:not(#noItemsRow)');
            rows.forEach(row => row.remove());

            if (itemsData.length === 0) {
                noItemsRow.classList.remove('hidden');
                tableFooter.classList.add('hidden');
                return;
            }

            noItemsRow.classList.add('hidden');
            tableFooter.classList.remove('hidden');

            // Add rows for each item
            itemsData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.id = `item-row-${index}`;
                row.className = 'border-b border-gray-200 hover:bg-gray-50';

                row.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${item.productDescription || '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <div class="text-xs text-gray-600">${item.hsCode || '-'}</div>
                        <div class="text-xs text-gray-500 mt-1">${item.hsCodeText || 'Product description'}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">${item.quantity || 0}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${(() => {
                        // rateValue is transient; parse from rate JSON if missing
                        if (item.rateValue !== undefined && item.rateValue !== null && item.rateValue !== 0 && item.rateValue !== '') {
                            return item.rateValue;
                        }
                        if (item.rate) {
                            try { return JSON.parse(item.rate).rate_value || 0; } catch(e) {}
                        }
                        return 0;
                    })()}%</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${item.fixedNotifiedValueOrRetailPrice
                            ? '<div class="text-xs text-gray-500">FNV: ' + parseFloat(item.fixedNotifiedValueOrRetailPrice || 0).toFixed(2) + '</div>'
                            : ''}
                        ${parseFloat(item.valueSalesExcludingST || 0).toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">${parseFloat(item.salesTaxApplicable || 0).toFixed(2)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <div class="flex space-x-2">
                            <button type="button" onclick="editItem(${index})" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</button>
                            <button type="button" onclick="deleteItem(${index})" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                        </div>
                    </td>
                `;

                tableBody.appendChild(row);
            });

            // Update hidden form inputs
            updateHiddenFormInputs();
        }

        function updateHiddenFormInputs() {
            const container = document.getElementById('hiddenItemsContainer');
            container.innerHTML = '';

            itemsData.forEach((item, index) => {
                Object.keys(item).forEach(key => {
                    if (key.endsWith('Text') || key === 'rateValue' || key === 'rowId' || key === 'index') return;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${index}][${key}]`;
                    input.value = item[key] || '';
                    container.appendChild(input);
                });
            });
        }

        function removeItem(index) {
            if (index >= 0 && index < itemsData.length) {
                itemsData.splice(index, 1);
                updateItemsTable();
                updateTotals();
            }
        }

        // Edit item function - opens modal for editing
        function editItem(index) {
            if (index < 0 || index >= itemsData.length) return;

            if (!validateBuyerRequirements()) {
                showMessage('Please select buyer province and registration type before editing items', 'warning');
                return;
            }

            editingItemIndex = index;
            const item = itemsData[index];

            document.getElementById('addItemModal').classList.remove('hidden');

            const modalTitle = document.querySelector('#addItemModal h3');
            if (modalTitle) {
                modalTitle.textContent = 'Edit Invoice Item';
            }

            // Populate selects and init Select2 BEFORE async
            populateModalSelects();
            initializeModalSelect2();

            setTimeout(async () => {
                try {
                    // --- Step 1: Set all plain input fields ---
                    Object.keys(item).forEach(key => {
                        const element = document.getElementById('modal' + key.charAt(0).toUpperCase() + key.slice(1));
                        if (element && key !== 'uoM' && key !== 'hsCode' && key !== 'saleType' && key !== 'rate') {
                            if (element.tagName !== 'SELECT') {
                                element.value = item[key] !== null && item[key] !== undefined ? item[key] : '';
                            }
                        }
                    });

                    // --- Step 2: Resolve Sale Type ID (handle both numeric ID and text description) ---
                    let resolvedSaleTypeId = null;
                    if (item.saleType) {
                        const rawSt = String(item.saleType).trim().toLowerCase();
                        if (transactionTypes && Array.isArray(transactionTypes) && transactionTypes.length > 0) {
                            // First: try to match by numeric ID
                            const byId = transactionTypes.find(t => String(t.transactioN_TYPE_ID) === String(item.saleType).trim());
                            if (byId) {
                                resolvedSaleTypeId = String(byId.transactioN_TYPE_ID);
                            } else {
                                // Fallback: match by description text with trim and substring comparison
                                const byDesc = transactionTypes.find(t => {
                                    if (!t.transactioN_DESC) return false;
                                    const desc = t.transactioN_DESC.trim().toLowerCase();
                                    return desc === rawSt || desc.includes(rawSt) || rawSt.includes(desc);
                                });
                                if (byDesc) {
                                    resolvedSaleTypeId = String(byDesc.transactioN_TYPE_ID);
                                } else if (rawSt.includes('3rd schedule') || rawSt.includes('third schedule')) {
                                    // Specific fallback for 3rd schedule
                                    const thirdSched = transactionTypes.find(t => String(t.transactioN_TYPE_ID) === '23');
                                    resolvedSaleTypeId = thirdSched ? '23' : String(item.saleType).trim();
                                } else {
                                    resolvedSaleTypeId = String(item.saleType).trim();
                                }
                            }
                        } else {
                            resolvedSaleTypeId = String(item.saleType).trim();
                        }

                        // Ensure the option exists in #modalSaleType or append it
                        const saleTypeEl = document.getElementById('modalSaleType');
                        if (saleTypeEl) {
                            let optionExists = false;
                            for (let i = 0; i < saleTypeEl.options.length; i++) {
                                if (String(saleTypeEl.options[i].value) === String(resolvedSaleTypeId)) {
                                    optionExists = true;
                                    break;
                                }
                            }
                            if (!optionExists && resolvedSaleTypeId) {
                                const newOpt = new Option(item.saleTypeText || item.saleType, resolvedSaleTypeId, true, true);
                                $(saleTypeEl).append(newOpt);
                            }
                        }
                        $('#modalSaleType').val(resolvedSaleTypeId).trigger('change');
                    }

                    // --- Step 3: Set HS Code (AJAX Select2 — must inject option first) ---
                    if (item.hsCode) {
                        const hsCodeEl = document.getElementById('modalHsCode');
                        // Create & append the option so Select2 can find it
                        const hsOption = new Option(
                            item.hsCodeText || item.hsCode,
                            item.hsCode,
                            true,
                            true
                        );
                        $(hsCodeEl).append(hsOption).trigger('change');
                    }

                    // --- Step 4: Fetch rates using the resolved Sale Type ID ---
                    if (resolvedSaleTypeId) {
                        await fetchRatesForEdit(resolvedSaleTypeId);
                        if (item.rate) {
                            const rateSelect = document.getElementById('modalRate');
                            if (rateSelect) {
                                let matchedOptionValue = null;

                                // Extract numeric rate (e.g., "18%" -> 18, or from JSON object)
                                let savedRateNum = null;
                                let savedRateId = null;
                                let savedRateText = String(item.rate).trim().toLowerCase();

                                try {
                                    const parsed = JSON.parse(item.rate);
                                    if (parsed) {
                                        savedRateId = parsed.rate_id ?? null;
                                        savedRateNum = parsed.rate_value !== undefined ? parseFloat(parsed.rate_value) : null;
                                    }
                                } catch (e) {
                                    const numMatch = String(item.rate).match(/[\d\.]+/);
                                    if (numMatch) {
                                        savedRateNum = parseFloat(numMatch[0]);
                                    }
                                }

                                for (let i = 0; i < rateSelect.options.length; i++) {
                                    const opt = rateSelect.options[i];
                                    if (!opt.value) continue;

                                    let optId = null;
                                    let optNum = null;
                                    let optText = opt.textContent.trim().toLowerCase();

                                    try {
                                        const optParsed = JSON.parse(opt.value);
                                        optId = optParsed.rate_id;
                                        optNum = parseFloat(optParsed.rate_value);
                                    } catch (e) {
                                        const numMatch = opt.value.match(/[\d\.]+/);
                                        if (numMatch) optNum = parseFloat(numMatch[0]);
                                    }

                                    // Match by rate_id
                                    if (savedRateId && optId && String(savedRateId) === String(optId)) {
                                        matchedOptionValue = opt.value;
                                        break;
                                    }
                                    // Match by percentage numeric value (e.g. 18 === 18)
                                    if (savedRateNum !== null && optNum !== null && Math.abs(savedRateNum - optNum) < 0.01) {
                                        matchedOptionValue = opt.value;
                                        break;
                                    }
                                    // Match by text (e.g. "18%" or "Standard Rate (18%)")
                                    if (optText === savedRateText || optText.includes(savedRateText) || savedRateText.includes(optText)) {
                                        matchedOptionValue = opt.value;
                                        break;
                                    }
                                }

                                if (matchedOptionValue) {
                                    $(rateSelect).val(matchedOptionValue).trigger('change');
                                } else {
                                    // If no exact match in API options, keep the saved rate as a selectable option
                                    const fallbackOpt = new Option(String(item.rate), String(item.rate), true, true);
                                    $(rateSelect).append(fallbackOpt).val(String(item.rate)).trigger('change');
                                }
                            }
                        }
                    }

                    // --- Step 5: Fetch UoM options for the saved HS Code, then restore saved UoM ---
                    if (item.hsCode) {
                        const uomElement = document.getElementById('modalUoM');
                        await fetchUomByHsCode(item.hsCode);
                        if (item.uoM && uomElement) {
                            const targetUom = String(item.uoM).trim().toLowerCase();
                            let matchedUomValue = null;

                            for (let i = 0; i < uomElement.options.length; i++) {
                                const opt = uomElement.options[i];
                                if (!opt.value) continue;

                                const optVal = String(opt.value).trim().toLowerCase();
                                const optText = opt.textContent.trim().toLowerCase();

                                if (optVal === targetUom || optText === targetUom || optText.includes(targetUom) || targetUom.includes(optText)) {
                                    matchedUomValue = opt.value;
                                    break;
                                }
                            }

                            if (matchedUomValue) {
                                $(uomElement).val(matchedUomValue).trigger('change');
                            } else {
                                // Fallback: check global uoMs list
                                if (uoMs && Array.isArray(uoMs)) {
                                    const uomObj = uoMs.find(u =>
                                        (u.uoM_DESC && u.uoM_DESC.trim().toLowerCase() === targetUom) ||
                                        String(u.uoM_ID || u.id) === targetUom
                                    );
                                    if (uomObj) {
                                        const uId = String(uomObj.uoM_ID || uomObj.id);
                                        const opt = new Option(uomObj.uoM_DESC || targetUom, uId, true, true);
                                        $(uomElement).append(opt).val(uId).trigger('change');
                                        matchedUomValue = uId;
                                    }
                                }
                                if (!matchedUomValue) {
                                    const opt = new Option(String(item.uoM), String(item.uoM), true, true);
                                    $(uomElement).append(opt).val(String(item.uoM)).trigger('change');
                                }
                            }
                        }
                    } else {
                        const uomElement = document.getElementById('modalUoM');
                        if (uomElement) {
                            uomElement.innerHTML = '<option value="">Select HS Code first</option>';
                            uomElement.disabled = true;
                        }
                    }

                    // --- Step 6: Restore fixedNotifiedValueOrRetailPrice and rateValues explicitly ---
                    if (item.fixedNotifiedValueOrRetailPrice !== undefined && item.fixedNotifiedValueOrRetailPrice !== null) {
                        const fnField = document.getElementById('modalFixedNotifiedValueOrRetailPrice');
                        if (fnField) fnField.value = item.fixedNotifiedValueOrRetailPrice;
                    }
                    if (item.rateValues !== undefined && item.rateValues !== null) {
                        const rvField = document.getElementById('modalRateValues');
                        if (rvField) rvField.value = item.rateValues;
                    }

                    // --- Step 7: Restore discount percent if applicable ---
                    if (item.discountType === 'percent' && item.discountPercentInput !== undefined) {
                        const discEl = document.getElementById('modalDiscount');
                        if (discEl) discEl.value = item.discountPercentInput;
                    }

                    // --- Step 8: 3rd Schedule specific fields ---
                    // Call toggleScheduleFields to show/hide fields based on the restored sale type
                    toggleScheduleFields();

                    const saleTypeText = item.saleTypeText || '';
                    const isThirdSched = saleTypeText.toLowerCase().includes('3rd schedule') ||
                                        saleTypeText.toLowerCase().includes('3rd party') ||
                                        is3rdScheduleSelected();
                    if (isThirdSched) {
                        if (item.ghPercent !== undefined) {
                            const ghEl = document.getElementById('modalGhPercent');
                            if (ghEl) ghEl.value = item.ghPercent;
                        }
                        if (item.discountPercent !== undefined) {
                            const dpEl = document.getElementById('modalDiscountPercent');
                            if (dpEl) dpEl.value = item.discountPercent;
                        }
                        if (item.ghAmount !== undefined) {
                            const gaEl = document.getElementById('modalGhAmount');
                            if (gaEl) gaEl.value = item.ghAmount;
                        }
                        if (item.discountAmount !== undefined) {
                            const daEl = document.getElementById('modalDiscountAmount');
                            if (daEl) daEl.value = item.discountAmount;
                        }
                        recalculate3rdSchedule();
                    }

                    // --- Step 9: Restore SRO data if any ---
                    if (item.sroScheduleNo) {
                        setTimeout(async () => {
                            const rateSelect = document.getElementById('modalRate');
                            const invoiceDate = document.getElementById('invoiceDate').value;
                            const buyerProvince = $('#buyerProvince').val();
                            if (rateSelect && rateSelect.value) {
                                try {
                                    const rateData = JSON.parse(rateSelect.value);
                                    const rateId = rateData.rate_id;
                                    await fetchSroSchedule(rateId, invoiceDate, buyerProvince, document.getElementById('addItemModal'));
                                    setTimeout(() => {
                                        $('#modalSroScheduleNo').val(item.sroScheduleNo).trigger('change');
                                        setTimeout(() => {
                                            if (item.sroItemSerialNo) {
                                                $('#modalSroItemSerialNo').val(item.sroItemSerialNo).trigger('change');
                                            }
                                        }, 300);
                                    }, 300);
                                } catch (error) {
                                    console.error('Error setting SRO data:', error);
                                }
                            }
                        }, 500);
                    }

                    console.log('All item values populated successfully for edit!');

                } catch (error) {
                    console.error('Error populating edit form:', error);
                }
            }, 300);
        }

        // Delete item function - same as removeItem but with confirmation
        function deleteItem(index) {
            if (confirm('Are you sure you want to delete this item?')) {
                removeItem(index);
            }
        }

        function updateTotals() {
            let totalQuantity = 0;
            let totalValueSales = 0;
            let totalSalesTax = 0;

            itemsData.forEach(item => {
                totalQuantity += parseFloat(item.quantity) || 0;
                totalValueSales += parseFloat(item.valueSalesExcludingST) || 0;
                totalSalesTax += parseFloat(item.salesTaxApplicable) || 0;
            });
            total=totalValueSales+totalSalesTax;
            document.getElementById('totalQuantity').textContent = totalQuantity.toFixed(0);
            document.getElementById('totalValueSales').textContent = totalValueSales.toFixed(2);
            document.getElementById('totalSalesTax').textContent = totalSalesTax.toFixed(2);
            document.getElementById('overall').textContent = total.toFixed(2);
        }

        function validateBuyerRequirements() {
            // Get values from both Select2 and regular elements
            const buyerProvince = $('#buyerProvince').val() || document.getElementById('buyerProvince').value;
            const buyerRegistrationType = $('#buyerRegistrationType').val() || document.getElementById('buyerRegistrationType').value;
            const addItemBtn = document.getElementById('addItemBtn');
            const requirementMsg = document.getElementById('addItemRequirement');

            console.log('Validating buyer requirements:', {
                province: buyerProvince,
                registrationType: buyerRegistrationType,
                provinceElement: document.getElementById('buyerProvince').value,
                registrationElement: document.getElementById('buyerRegistrationType').value
            });

            const isValid = buyerProvince && buyerProvince.trim() !== '' &&
                           buyerRegistrationType && buyerRegistrationType.trim() !== '';

            if (isValid) {
                addItemBtn.disabled = false;
                addItemBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                addItemBtn.classList.add('hover:bg-blue-500');
                requirementMsg.classList.add('hidden');
                addItemBtn.title = '';
                console.log('Add Item button enabled');
                return true;
            } else {
                addItemBtn.disabled = true;
                addItemBtn.classList.add('opacity-50', 'cursor-not-allowed');
                addItemBtn.classList.remove('hover:bg-blue-500');
                requirementMsg.classList.remove('hidden');
                addItemBtn.title = 'Please select buyer province and registration type first';
                console.log('Add Item button disabled - Province:', buyerProvince, 'Registration:', buyerRegistrationType);
                return false;
            }
        }

        // Initialize Select2 for modal fields
        function initializeModalSelect2() {
            // Clean up existing instances and their bound events first
            ['#modalSaleType', '#modalHsCode', '#modalUoM', '#modalRate', '#modalSroScheduleNo', '#modalSroItemSerialNo'].forEach(selector => {
                if ($(selector).data('select2')) {
                    $(selector).off('select2:select select2:clear');
                    $(selector).select2('destroy');
                }
                $(selector).removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                $(selector).find('option').removeAttr('data-select2-id');
            });

            // Initialize Select2 for modal sale type
            $('#modalSaleType').select2({
                placeholder: 'Select Sale Type',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });
            // Attach select2:select directly on the element (document delegation fails with dropdownParent)
            $('#modalSaleType').on('select2:select', function(e) {
                const selectedSaleType = e.params && e.params.data ? e.params.data.id : $(this).val();
                console.log('[Sale Type] select2:select fired, value:', selectedSaleType);
                toggleScheduleFields();
                if (selectedSaleType) {
                    fetchRatesForEdit(String(selectedSaleType));
                }
            });
            $('#modalSaleType').on('select2:clear', function(e) {
                const rateSelect = document.getElementById('modalRate');
                if (rateSelect) {
                    rateSelect.innerHTML = '<option value="">Select Rate</option>';
                    rateSelect.disabled = true;
                    if ($(rateSelect).data('select2')) $(rateSelect).select2('destroy');
                    $(rateSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                    $(rateSelect).select2({ placeholder: 'Select Rate', allowClear: true, width: '100%', dropdownParent: $('#addItemModal') });
                }
                toggleScheduleFields();
            });

            // Initialize Select2 for modal HS Code with AJAX
            $('#modalHsCode').select2({
                placeholder: 'Search HS Code...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 2,
                dropdownParent: $('#addItemModal'),
                ajax: {
                    delay: 150,
                    transport: function (params, success, failure) {
                        searchHsCodesWithCache(params, success, failure);
                    }
                }
            });
            // Attach select2:select directly on the element (document delegation fails with dropdownParent)
            $('#modalHsCode').on('select2:select', function(e) {
                const selectedHsCode = e.params && e.params.data ? e.params.data.id : $(this).val();
                const selectedText = e.params && e.params.data ? e.params.data.text : '';
                console.log('[HS Code] select2:select fired, value:', selectedHsCode);

                // Auto-fill Product Description if empty
                if (selectedText && selectedText.includes(' - ')) {
                    const parts = selectedText.split(' - ');
                    parts.shift();
                    const desc = parts.join(' - ').trim();
                    const prodDescEl = document.getElementById('modalProductDescription');
                    if (prodDescEl && (!prodDescEl.value || prodDescEl.value.trim() === '')) {
                        prodDescEl.value = desc;
                    }
                }

                if (selectedHsCode) {
                    fetchUomByHsCode(String(selectedHsCode));
                }
            });

            // Initialize Select2 for modal UoM
            $('#modalUoM').select2({
                placeholder: 'Select Unit of Measure',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });

            // Initialize Select2 for modal rate
            $('#modalRate').off('select2:select.rateHandler change.rateHandler').on('select2:select.rateHandler change.rateHandler', function() {
                onModalRateChange();
            });
            $('#modalRate').select2({
                placeholder: 'Select Rate',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });

            // Initialize Select2 for modal SRO fields
            $('#modalSroScheduleNo').select2({
                placeholder: 'Select SRO Schedule',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });

            $('#modalSroItemSerialNo').select2({
                placeholder: 'Select SRO Item',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });
        }

        // Populate modal selects with data
        function populateModalSelects() {
            // Populate sale types
            const saleTypeSelect = document.getElementById('modalSaleType');
            if (transactionTypes && Array.isArray(transactionTypes)) {
                saleTypeSelect.innerHTML = '<option value="">Select Sale Type</option>';
                transactionTypes.forEach(type => {
                    const typeId = type.transactioN_TYPE_ID;
                    const typeDesc = type.transactioN_DESC;
                    if (typeId && typeDesc) {
                        const option = document.createElement('option');
                        option.value = typeId;
                        option.textContent = typeDesc;
                        saleTypeSelect.appendChild(option);
                    }
                });
            }
        }

        // Get selected text from select element
        function getSelectText(selectId) {
            const select = document.getElementById(selectId);
            if (select && select.selectedIndex >= 0) {
                return select.options[select.selectedIndex].text;
            }
            return '';
        }

        // Simplified HS codes search function for modal
        function searchHsCodesWithCache(params, success, failure) {
            const searchTerm = params.data.term || '';
            const page = params.data.page || 1;

            // Make API request
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `${API_BASE}/premiertax/api/fbr/item-description-codes/search?search=${encodeURIComponent(searchTerm)}&page=${page}&limit=20`);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.success && data.data) {
                            const result = {
                                results: data.data.map(item => ({
                                    id: item.hS_CODE || item.code,
                                    text: `${item.hS_CODE || item.code} - ${item.description || item.itemDescription}`
                                })),
                                pagination: {
                                    more: data.has_more || false
                                }
                            };
                            success(result);
                        } else {
                            success({ results: [] });
                        }
                    } catch (e) {
                        console.error('Error parsing HS codes response:', e);
                        failure();
                    }
                } else {
                    console.error('HS codes request failed:', xhr.status);
                    failure();
                }
            };

            xhr.onerror = function() {
                console.error('HS codes network error');
                failure();
            };

            xhr.send();
        }

        // Fetch UOM based on HS code
        async function fetchUomByHsCode(hsCode) {
            console.log('[fetchUomByHsCode] called with hsCode:', hsCode);
            if (!hsCode) {
                const uomSelect = document.getElementById('modalUoM');
                if (uomSelect) {
                    uomSelect.innerHTML = '<option value="">Select Unit of Measure</option>';
                    $(uomSelect).val('').trigger('change');
                    uomSelect.disabled = true;
                }
                return [];
            }

            try {
                const uomSelect = document.getElementById('modalUoM');
                if (uomSelect) {
                    uomSelect.innerHTML = '<option value="">Loading UOM...</option>';
                    uomSelect.disabled = true;
                }

                const cleanHsCode = String(hsCode).trim();

                const response = await fetch(`${API_BASE}/premiertax/api/fbr/uom-by-hs-code`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        hs_code: cleanHsCode
                    })
                });

                const text = await response.text();
                console.log('RAW UOM RESPONSE:', text);

                const cleanText = text.trim().startsWith('{')
                    ? text
                    : text.substring(text.indexOf('{'));

                const result = JSON.parse(cleanText);

                if (result.success && result.data && result.data.length > 0) {
                    if (uomSelect) {
                        uomSelect.innerHTML = '<option value="">Select Unit of Measure</option>';
                        let addedCount = 0;
                        const seen = new Set();
                        result.data.forEach(uom => {
                            const uomId = uom.uoM_ID ?? uom.id ?? uom.uom_id ?? uom.uoM_Code;
                            const uomDesc = uom.uoM_DESC ?? uom.description ?? uom.desc ?? uom.uom_desc;
                            if (uomId !== undefined && uomId !== null && uomDesc && !seen.has(String(uomId))) {
                                seen.add(String(uomId));
                                const option = document.createElement('option');
                                option.value = uomId;
                                option.textContent = uomDesc;
                                uomSelect.appendChild(option);
                                addedCount++;
                            }
                        });

                        if (addedCount > 0) {
                            uomSelect.disabled = false;
                        } else {
                            uomSelect.innerHTML = '<option value="">No UOM available</option>';
                            uomSelect.disabled = true;
                        }

                        if ($(uomSelect).data('select2')) {
                            $(uomSelect).select2('destroy');
                        }
                        $(uomSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                        $(uomSelect).find('option').removeAttr('data-select2-id');
                        $(uomSelect).select2({
                            placeholder: 'Select Unit of Measure',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addItemModal')
                        });

                        // If exactly 1 UOM is available, auto-select it
                        if (addedCount === 1 && uomSelect.options[1]) {
                            $(uomSelect).val(uomSelect.options[1].value).trigger('change');
                        }
                    }

                    console.log('UOM options loaded for HS Code:', hsCode, result.data.length, 'options');
                    return result.data;
                } else {
                    if (uomSelect) {
                        uomSelect.innerHTML = '<option value="">No UOM available for this HS Code</option>';
                        uomSelect.disabled = true;
                        if ($(uomSelect).data('select2')) {
                            $(uomSelect).select2('destroy');
                        }
                        $(uomSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                        $(uomSelect).find('option').removeAttr('data-select2-id');
                        $(uomSelect).select2({
                            placeholder: 'No UOM available',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addItemModal')
                        });
                    }
                    console.warn('No UOM found for HS Code:', hsCode);
                    return [];
                }

            } catch (error) {
                console.error('Error fetching UOM for HS Code:', error);
                const uomSelect = document.getElementById('modalUoM');
                if (uomSelect) {
                    uomSelect.innerHTML = '<option value="">Error loading UOM</option>';
                    uomSelect.disabled = true;
                }
                return [];
            }
        }

        // Fetch rates based on sale type and buyer/seller province - using existing API
        async function fetchRatesForEdit(saleType) {
            console.log('[fetchRatesForEdit] called with saleType:', saleType);
            const rawProvince = $('#buyerProvince').val() || $('#sellerProvince').val() || '{{ $invoice->buyer_province }}' || '{{ $invoice->seller_province }}' || '';
            const buyerProvinceCode = findProvinceCodeByName(rawProvince) || 7;
            const invoiceDate = (document.getElementById('invoiceDate') && document.getElementById('invoiceDate').value)
                ? document.getElementById('invoiceDate').value
                : new Date().toISOString().split('T')[0];

            const parsedSaleType = parseInt(saleType);
            const parsedProvince = parseInt(buyerProvinceCode);

            console.log('[fetchRatesForEdit] Parsed params:', { parsedSaleType, parsedProvince, invoiceDate });

            if (isNaN(parsedSaleType) || isNaN(parsedProvince)) {
                console.warn('Invalid params for rate fetch:', { parsedSaleType, parsedProvince, invoiceDate });
                return [];
            }

            try {
                const rateSelect = document.getElementById('modalRate');
                if (rateSelect) {
                    rateSelect.innerHTML = '<option value="">Loading rates...</option>';
                    rateSelect.disabled = true;
                }

                const response = await fetch(`${API_BASE}/premiertax/api/fbr/sale-type-to-rate`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        date: invoiceDate,
                        trans_type_id: parsedSaleType,
                        origination_supplier: parsedProvince
                    })
                });

                const text = await response.text();
                console.log('RAW RATE RESPONSE:', text);

                const cleanText = text.trim().startsWith('{')
                    ? text
                    : text.substring(text.indexOf('{'));

                const result = JSON.parse(cleanText);

                if (result.success && result.data && result.data.length > 0) {
                    if (rateSelect) {
                        rateSelect.innerHTML = '<option value="">Select Rate</option>';
                        let targetOptionValue = '';

                        result.data.forEach((rateData, index) => {
                            const rateValue = rateData.ratE_VALUE ?? rateData.rate_value ?? rateData.rateValue ?? 0;
                            const rateId = rateData.ratE_ID ?? rateData.rate_id ?? rateData.rateId;
                            const rateDesc = rateData.ratE_DESC ?? rateData.rate_desc ?? rateData.rateDesc ?? '';

                            const option = document.createElement('option');
                            const optionValueObj = {
                                rate_id: rateId,
                                rate_value: rateValue,
                                rate_desc: rateDesc
                            };
                            option.value = JSON.stringify(optionValueObj);

                            let displayText = rateDesc && rateDesc.trim() !== '' ? rateDesc : `${rateValue}%`;
                            option.textContent = displayText;
                            option.title = rateDesc || `Rate: ${rateValue}%`;
                            rateSelect.appendChild(option);

                            // If editing an item, restore the previously selected rate
                            if (editingItemIndex >= 0 && itemsData[editingItemIndex]) {
                                const savedItem = itemsData[editingItemIndex];
                                if (savedItem && savedItem.rate) {
                                    let savedNum = null;
                                    let savedId = null;
                                    let savedTxt = String(savedItem.rate).trim().toLowerCase();
                                    try {
                                        const savedRateObj = JSON.parse(savedItem.rate);
                                        savedId = savedRateObj.rate_id;
                                        savedNum = parseFloat(savedRateObj.rate_value);
                                    } catch (e) {
                                        const m = String(savedItem.rate).match(/[\d\.]+/);
                                        if (m) savedNum = parseFloat(m[0]);
                                    }

                                    const optNum = parseFloat(rateValue);
                                    if ((savedId && String(savedId) === String(rateId)) ||
                                        (savedNum !== null && !isNaN(optNum) && Math.abs(savedNum - optNum) < 0.01) ||
                                        (savedTxt === displayText.toLowerCase()) ||
                                        (savedTxt === (rateDesc ? rateDesc.toLowerCase() : ''))) {
                                        option.selected = true;
                                        targetOptionValue = option.value;
                                    }
                                }
                            }

                            // Select the first rate by default if not set by edit mode
                            if (!targetOptionValue && index === 0) {
                                option.selected = true;
                                targetOptionValue = option.value;
                            }
                        });

                        rateSelect.disabled = false;

                        if ($(rateSelect).data('select2')) {
                            $(rateSelect).select2('destroy');
                        }
                        $(rateSelect).off('select2:select.rateHandler change.rateHandler').on('select2:select.rateHandler change.rateHandler', function() {
                            onModalRateChange();
                        });

                        $(rateSelect).select2({
                            placeholder: 'Select Rate',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addItemModal')
                        });

                        const finalVal = targetOptionValue || (rateSelect.options[1]?.value || '');
                        $(rateSelect).val(finalVal).trigger('change');
                        if (finalVal) {
                            $(rateSelect).trigger('select2:select');
                        }

                        // Trigger sales tax calculation
                        calculateModalSalesTax();
                    }

                    console.log('Rates loaded:', result.data.length, 'options');
                    return result.data;
                } else {
                    if (rateSelect) {
                        rateSelect.innerHTML = '<option value="">No rates available</option>';
                        rateSelect.disabled = true;
                        if ($(rateSelect).data('select2')) {
                            $(rateSelect).select2('destroy');
                        }
                        $(rateSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
                        $(rateSelect).find('option').removeAttr('data-select2-id');
                        $(rateSelect).select2({
                            placeholder: 'No rates available',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addItemModal')
                        });
                    }
                    return [];
                }

            } catch (error) {
                console.error('Error fetching rates:', error);
                const rateSelect = document.getElementById('modalRate');
                if (rateSelect) {
                    rateSelect.innerHTML = '<option value="">Error loading rates</option>';
                    rateSelect.disabled = true;
                }
                return [];
            }
        }

        // Handler when modal rate changes
        function onModalRateChange() {
            calculateModalSalesTax();
            const rateSelect = document.getElementById('modalRate');
            const invoiceDate = (document.getElementById('invoiceDate') && document.getElementById('invoiceDate').value)
                ? document.getElementById('invoiceDate').value
                : new Date().toISOString().split('T')[0];
            const rawProvince = $('#buyerProvince').val() || $('#sellerProvince').val() || '';
            const buyerProvince = findProvinceCodeByName(rawProvince) || 7;

            if (rateSelect && rateSelect.value && invoiceDate && buyerProvince) {
                try {
                    const rateData = JSON.parse(rateSelect.value);
                    const rateId = rateData.rate_id;
                    if (rateId) {
                        fetchSroSchedule(rateId, invoiceDate, buyerProvince, document.getElementById('addItemModal'));
                    }
                } catch (error) {
                    console.warn('Could not parse rate data for SRO schedule:', error);
                }
            }
        }

        // =====================================================================
        // SRO Helper Functions (required for edit mode SRO restoration)
        // =====================================================================

        async function fetchSroSchedule(rateId, date, provinceCode, itemContainer) {
            try {
                const response = await fetch(`${API_BASE}/premiertax/api/fbr/sro-schedule`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        rate_id: parseInt(rateId),
                        date: date,
                        origination_supplier_csv: parseInt(provinceCode)
                    })
                });
                const text = await response.text();
                const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
                const result = JSON.parse(cleanText);
                if (result.success && result.data && result.data.length > 0) {
                    displaySroInformation(result.data, itemContainer);
                } else {
                    clearSroInformation(itemContainer);
                }
            } catch (error) {
                console.error('Error fetching SRO schedule:', error);
                clearSroInformation(itemContainer);
            }
        }

        function displaySroInformation(sroData, itemContainer) {
            const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
            if (sroScheduleSelect && sroData.length > 0) {
                sroScheduleSelect.innerHTML = '<option value="">Select SRO Schedule</option>';
                sroData.forEach(sro => {
                    const sroId = sro.srO_ID || sro.sro_id;
                    const sroDesc = sro.srO_DESC || sro.sro_desc || sro.description;
                    if (sroId && sroDesc) {
                        sroSchedules.set(String(sroId), sroDesc);
                        const option = document.createElement('option');
                        option.value = sroId;
                        option.textContent = sroDesc;
                        sroScheduleSelect.appendChild(option);
                    }
                });
                if ($(sroScheduleSelect).data('select2')) $(sroScheduleSelect).select2('destroy');
                $(sroScheduleSelect).select2({
                    placeholder: 'Select SRO Schedule',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#addItemModal')
                });
                if (sroData.length === 1) {
                    const onlyOption = sroScheduleSelect.options[1];
                    if (onlyOption) {
                        $(sroScheduleSelect).val(onlyOption.value).trigger('change');
                        fetchSroItemsForModal(sroScheduleSelect);
                    }
                }
            }
        }

        function clearSroInformation(itemContainer) {
            const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
            if (sroScheduleSelect) {
                sroScheduleSelect.innerHTML = '<option value="">Select SRO Schedule</option>';
            }
            const sroItemSelect = itemContainer.querySelector('.sro-item-select');
            if (sroItemSelect) {
                sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';
            }
        }

        async function fetchSroItemsForModal(sroScheduleSelect) {
            const sroItemSelect = document.getElementById('modalSroItemSerialNo');
            if (!sroItemSelect) return;
            sroItemSelect.innerHTML = '<option value="">Loading SRO Items...</option>';
            const sroId = sroScheduleSelect.value;
            const invoiceDate = document.getElementById('invoiceDate').value;
            if (!sroId || !invoiceDate) return;
            try {
                const response = await fetch(`${API_BASE}/premiertax/api/fbr/sro-item`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ sro_id: parseInt(sroId), date: invoiceDate })
                });
                const text = await response.text();
                const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
                const result = JSON.parse(cleanText);
                if (result.success && result.data && result.data.length > 0) {
                    sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';
                    result.data.forEach(item => {
                        const itemId = item.srO_ITEM_ID || item.sro_item_id;
                        const itemDesc = item.srO_ITEM_DESC || item.sro_item_desc || item.description;
                        if (itemId && itemDesc) {
                            sroItems.set(String(itemId), itemDesc);
                            const option = document.createElement('option');
                            option.value = itemId;
                            option.textContent = itemDesc;
                            sroItemSelect.appendChild(option);
                        }
                    });
                    if ($(sroItemSelect).data('select2')) $(sroItemSelect).select2('destroy');
                    $(sroItemSelect).select2({
                        placeholder: 'Select SRO Item',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#addItemModal')
                    });
                } else {
                    sroItemSelect.innerHTML = '<option value="">No SRO Items available</option>';
                }
            } catch (error) {
                console.error('Error fetching SRO items for modal:', error);
                sroItemSelect.innerHTML = '<option value="">Error loading SRO Items</option>';
            }
        }

        // Wire up SRO Schedule change to load SRO Items in the modal
        $(document).on('change', '#modalSroScheduleNo', function() {
            fetchSroItemsForModal(this);
        });

        // =====================================================================
        // Toggle 3rd Schedule specific fields (show/hide based on sale type)
        // =====================================================================
        function toggleScheduleFields() {
            const is3rdSchedule = is3rdScheduleSelected();

            // Show/hide 3rd Schedule fields
            document.querySelectorAll('#addItemModal .schedule-3rd-field').forEach(el => {
                if (is3rdSchedule) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });

            // Toggle required * indicator on Fixed Notified Value
            const fnRequired = document.querySelector('#addItemModal .schedule-3rd-fn-required');
            const fnField = document.getElementById('modalFixedNotifiedValueOrRetailPrice');
            if (fnRequired) {
                if (is3rdSchedule) {
                    fnRequired.classList.remove('hidden');
                    if (fnField) { fnField.required = true; }
                } else {
                    fnRequired.classList.add('hidden');
                    if (fnField) { fnField.required = false; }
                }
            }

            // Recalculate if switching to 3rd Schedule
            if (is3rdSchedule) {
                recalculate3rdSchedule();
            }
        }

        // =====================================================================
        // Check if 3rd Schedule is selected in the modal
        function is3rdScheduleSelected() {
            const saleTypeSelect = document.getElementById('modalSaleType');
            if (!saleTypeSelect) return false;
            let selectedText = '';
            if (saleTypeSelect.selectedIndex >= 0) {
                selectedText = saleTypeSelect.options[saleTypeSelect.selectedIndex].text;
            }
            return selectedText.toLowerCase().includes('3rd schedule') || selectedText.toLowerCase().includes('3rd party');
        }

        // Recalculate 3rd Schedule fields
        function recalculate3rdSchedule() {
            calculateModalSalesTax();
        }

        // Modal functions
        function openAddItemModal() {
            document.getElementById('addItemModal').classList.remove('hidden');
            // Reset editing state
            editingItemIndex = -1;

            // Update modal title
            const modalTitle = document.querySelector('#addItemModal h3');
            if (modalTitle) {
                modalTitle.textContent = 'Add Invoice Item';
            }

            // Clear all form fields
            document.getElementById('itemForm').reset();

            // Reset all Select2 dropdowns to their default state
            const selectIds = [
                'modalSaleType', 'modalHsCode', 'modalUoM', 'modalRate',
                'modalSroScheduleNo', 'modalSroItemSerialNo'
            ];

            selectIds.forEach(selectId => {
                const selectElement = document.getElementById(selectId);
                if (selectElement) {
                    // Clear the select and reset to default option
                    selectElement.innerHTML = '';

                    // Add default option based on select type
                    let defaultOption = '';
                    switch(selectId) {
                        case 'modalSaleType':
                            defaultOption = '<option value="">Select Sale Type</option>';
                            break;
                        case 'modalHsCode':
                            defaultOption = '<option value="">Select HS Code</option>';
                            break;
                        case 'modalUoM':
                            defaultOption = '<option value="">Select Unit of Measure</option>';
                            break;
                        case 'modalRate':
                            defaultOption = '<option value="">Select Rate</option>';
                            break;
                        case 'modalSroScheduleNo':
                            defaultOption = '<option value="">Select SRO Schedule</option>';
                            break;
                        case 'modalSroItemSerialNo':
                            defaultOption = '<option value="">Select SRO Item</option>';
                            break;
                    }
                    selectElement.innerHTML = defaultOption;
                }
            });

            // Populate modal selects with fresh data BEFORE initializing Select2
            populateModalSelects();

            // Initialize Select2 for modal fields
            initializeModalSelect2();

            // Hide 3rd Schedule fields by default (no sale type selected yet)
            toggleScheduleFields();

            console.log('Add Item modal opened with fresh/reset form');
        }

        function closeAddItemModal() {
            document.getElementById('addItemModal').classList.add('hidden');
        }

                        function addItemFromModal() {
            const form = document.getElementById('itemForm');

            // Validate required fields
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Get form data
            const formData = new FormData(form);
            const itemData = {};

            // Extract form data
            for (let [key, value] of formData.entries()) {
                itemData[key] = value;
            }

            // Get display texts for selects
            itemData.saleTypeText = getSelectText('modalSaleType');
            itemData.hsCodeText = getSelectText('modalHsCode');
            itemData.uoMText = getSelectText('modalUoM');
            itemData.rateText = getSelectText('modalRate');
            itemData.sroScheduleNoText = getSelectText('modalSroScheduleNo');
            itemData.sroItemSerialNoText = getSelectText('modalSroItemSerialNo');

            // Parse rate data to get numeric value
            if (itemData.rate) {
                try {
                    const rateData = JSON.parse(itemData.rate);
                    itemData.rateValue = parseFloat(rateData.rate_value) || 0;
                } catch (e) {
                    itemData.rateValue = 0;
                }
            } else {
                itemData.rateValue = 0;
            }

            if (editingItemIndex >= 0) {
                // Update existing item
                itemsData[editingItemIndex] = itemData;
                editingItemIndex = -1;
                showMessage('Item updated successfully!', 'success');
            } else {
                // Add new item
                itemsData.push(itemData);
                showMessage('Item added successfully!', 'success');
            }

            // Update table display
            updateItemsTable();

            // Update totals
            updateTotals();

            // Close modal
            closeAddItemModal();
        }

        // Add event listeners
        document.getElementById('buyerProvince').addEventListener('change', validateBuyerRequirements);
        document.getElementById('buyerRegistrationType').addEventListener('change', validateBuyerRequirements);

        // NOTE: Sale Type and HS Code select2:select listeners are now bound DIRECTLY on the
        // elements inside initializeModalSelect2() above. Document-level delegation does not
        // work reliably when dropdownParent is set on Select2.
        // Unified Modal Sales Tax Calculation function
        function calculateModalSalesTax() {
            const isThirdParty = is3rdScheduleSelected();

            const rateValuesInput = document.getElementById('modalRateValues');
            const quantityInput = document.getElementById('modalQuantity');
            const rateTax = document.getElementById('modalRate');

            let rate = 0;
            if (rateTax && rateTax.value) {
                try {
                    const rd = JSON.parse(rateTax.value);
                    rate = parseFloat(rd.rate_value) || 0;
                } catch (e) {
                    rate = 0;
                }
            }

            const valueSalesField = document.getElementById('modalValueSalesExcludingST');
            const valueFurtherField = document.getElementById('modalFurtherTax');
            const salesTaxField = document.getElementById('modalSalesTaxApplicable');
            const discountField = document.getElementById('modalDiscount');
            const totalValuesField = document.getElementById('modalTotalValues');

            if (!rateTax || !valueSalesField || !salesTaxField) {
                return;
            }

            const qty = parseFloat(quantityInput ? quantityInput.value : 0) || 0;

            if (isThirdParty) {
                const fnField = document.getElementById('modalFixedNotifiedValueOrRetailPrice');
                const basePrice = parseFloat(fnField ? fnField.value : 0) || 0;

                const ghPercentField = document.getElementById('modalGhPercent');
                const ghPercent = parseFloat(ghPercentField ? ghPercentField.value : 0) || 0;

                const discountPercentField = document.getElementById('modalDiscountPercent');
                const discountPercent = parseFloat(discountPercentField ? discountPercentField.value : 0) || 0;

                const discountAmount = basePrice * discountPercent / 100;
                const discountAmtEl = document.getElementById('modalDiscountAmount');
                if (discountAmtEl) discountAmtEl.value = discountAmount.toFixed(2);
                if (discountField) discountField.value = discountAmount.toFixed(2);

                const salesTax3rd = basePrice * rate / 100;
                salesTaxField.value = salesTax3rd.toFixed(2);

                const exclVal = basePrice - discountAmount;
                if (valueSalesField) valueSalesField.value = exclVal.toFixed(2);

                const ghAmount = (exclVal + salesTax3rd) * ghPercent / 100;
                const ghAmtEl = document.getElementById('modalGhAmount');
                if (ghAmtEl) ghAmtEl.value = ghAmount.toFixed(2);

                if (rate > 0 && basePrice > 0) {
                    salesTaxField.classList.remove('bg-gray-50', 'bg-red-50');
                    salesTaxField.classList.add('bg-green-50');
                    salesTaxField.title = `3rd Schedule Tax: ${basePrice.toFixed(2)} × ${rate}% = ${salesTax3rd.toFixed(2)}`;
                } else {
                    salesTaxField.classList.remove('bg-green-50', 'bg-red-50');
                    salesTaxField.classList.add('bg-gray-50');
                }

                const furtherTaxAmt = exclVal * 4 / 100;
                if ($('#buyerRegistrationType').val() === 'Unregistered' && valueFurtherField) {
                    valueFurtherField.value = furtherTaxAmt.toFixed(2);
                }
            } else {
                const rater = parseFloat(rateValuesInput ? rateValuesInput.value : 0) || 0;
                const totalTaxvalue = (rater * rate) / 100;
                const total = (rater + totalTaxvalue) * qty;
                const totalDM = rater * qty;

                if (totalValuesField) totalValuesField.value = total.toFixed(2);
                if (valueSalesField) valueSalesField.value = totalDM.toFixed(2);

                const valueSales = parseFloat(valueSalesField ? valueSalesField.value : 0) || 0;
                const salesTax = (valueSales * rate) / 100;
                salesTaxField.value = salesTax.toFixed(2);

                if (rate > 0 && valueSales > 0) {
                    salesTaxField.classList.remove('bg-gray-50', 'bg-red-50');
                    salesTaxField.classList.add('bg-green-50');
                    salesTaxField.title = `Calculated: ${valueSales} × ${rate}% = ${salesTax.toFixed(2)}`;
                } else {
                    salesTaxField.classList.remove('bg-green-50');
                    salesTaxField.classList.add('bg-gray-50');
                    salesTaxField.title = 'Enter rate and value sales to calculate';
                }

                if ($('#buyerRegistrationType').val() === 'Unregistered' && valueFurtherField) {
                    valueFurtherField.value = (totalDM * 4 / 100).toFixed(2);
                }
            }
        }

        // Add input/change event listener for all calculation-related fields in modal
        $(document).on('input change', 'input[name*="[rateValues]"], input[name*="[quantity]"], #modalRateValues, #modalQuantity, #modalFixedNotifiedValueOrRetailPrice, #modalDiscount, #modalDiscountPercent, #modalGhPercent, #modalValueSalesExcludingST, #modalTotalValues', function(e) {
            calculateModalSalesTax();
        });

        // Modal event listeners
        document.getElementById('addItemBtn').addEventListener('click', openAddItemModal);
        document.getElementById('closeModalBtn').addEventListener('click', closeAddItemModal);
        document.getElementById('cancelModalBtn').addEventListener('click', closeAddItemModal);
        document.getElementById('addItemFromModalBtn').addEventListener('click', addItemFromModal);

        // Close modal when clicking outside
        document.getElementById('addItemModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddItemModal();
            }
        });
        function formDataToObjectWithLabels(formData) {
    const obj = {};
    const items = {};

    for (let [key, value] of formData.entries()) {
        if (key.startsWith('items[')) {
            const matches = key.match(/items\[(\d+)\]\[([^\]]+)\]/);
            if (matches) {
                const itemIndex = matches[1];
                const fieldName = matches[2];

                if (!items[itemIndex]) items[itemIndex] = {};

                if (fieldName === 'uoM' && uoMs) {
                    const uom = uoMs.find(u => (u.uoM_ID || u.id) == value);
                    items[itemIndex][fieldName] = uom ? (uom.uoM_DESC || uom.description) : value;
                } else if (fieldName === 'saleType' && transactionTypes) {
                    const saleType = transactionTypes.find(t => t.transactioN_TYPE_ID == value);
                    items[itemIndex][fieldName] = saleType ? saleType.transactioN_DESC : value;
                } else if (fieldName === 'rate' && value) {
                    try {
                        const rateData = JSON.parse(value);
                        items[itemIndex][fieldName] = rateData.rate_desc || (rateData.rate_value + '%');
                    } catch {
                        items[itemIndex][fieldName] = value;
                    }
                } else if (fieldName === 'sroScheduleNo' && sroSchedules.has(String(value))) {
                    items[itemIndex][fieldName] = sroSchedules.get(String(value));
                } else if (fieldName === 'sroItemSerialNo' && sroItems.has(String(value))) {
                    items[itemIndex][fieldName] = sroItems.get(String(value));
                } else {
                    items[itemIndex][fieldName] = value;
                }
            }
        } else {
            if (key === 'sellerProvince' && provinces) {
                const province = provinces.find(p => p.stateProvinceCode == value);
                obj[key] = province ? province.stateProvinceDesc : value;
            } else if (key === 'buyerProvince' && provinces) {
                const province = provinces.find(p => p.stateProvinceCode == value);
                obj[key] = province ? province.stateProvinceDesc : value;
            } else {
                obj[key] = value;
            }
        }
    }
    obj.items = Object.values(items);
    return obj;
}
    </script>
@endsection
  

