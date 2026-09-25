@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Purchase Invoice Form -->
                <form id="invoiceForm" method="POST" action="{{ isset($editInvoice) ? route('purchase.invoicing.update', $editInvoice->id) : route('purchase.invoicing.store') }}" class="space-y-8">
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
                        <div id="sellerAccordionContent" class="px-6 pb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="sellerNTNCNIC" class="block text-sm font-medium text-gray-700 mb-1" required>CNIC/NTN</label>
                                    <input type="text" id="sellerNTNCNIC" name="sellerNTNCNIC" placeholder="0000000000000" value="{{ $editInvoice->seller_ntn_cnic ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="sellerBusinessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                    <input type="text" id="sellerBusinessName" name="sellerBusinessName" placeholder="Seller Business Name" value="{{ $editInvoice->seller_business_name ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="hidden">
                                    <label for="sellerProvince" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                    <select id="sellerProvince" name="sellerProvince" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 province-select">
                                        <option value="{{ $editInvoice->seller_province ?? $user->province ?? '7' }}" selected>Select Province</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 mb-4">
                                    <label for="sellerAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea id="sellerAddress" name="sellerAddress" placeholder="Seller Address" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $editInvoice->seller_address ?? '' }}</textarea>
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
                                <select id="invoiceType" name="invoiceType" required class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 invoice-type-select">
                                    <option value="">Loading invoice types...</option>
                                </select>
                            </div>
                            <div>
                                <label for="invoiceDate" class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                                <input type="date" id="invoiceDate" name="invoiceDate" value="{{ isset($editInvoice) && $editInvoice->invoice_date ? \Carbon\Carbon::parse($editInvoice->invoice_date)->format('Y-m-d') : '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="invoiceRefNo" class="block text-sm font-medium text-gray-700 mb-1">Invoice Reference No.</label>
                                <input type="text" id="invoiceRefNo" name="invoiceRefNo" placeholder="Enter reference number" value="{{ $editInvoice->invoice_ref_no ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Buyer Information -->
                    <div class="bg-gray-50 p-2 rounded-lg">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Buyer Information (Our Company)</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="relative">
                                <label for="buyerNTNCNIC" class="block text-sm font-medium text-gray-700 mb-1">NTN/CNIC</label>
                                <input type="text" id="buyerNTNCNIC" name="buyerNTNCNIC" placeholder="0000000000000" value="{{ $editInvoice->buyer_ntn_cnic ?? $user->cinc_ntn ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" autocomplete="off">
                                
                                <!-- Autocomplete suggestions dropdown -->
                                <div id="buyerNTNAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden" style="max-height: 250px; overflow-y: auto;">
                                    <div class="p-2 text-sm text-gray-500 text-center">
                                        Start typing to search buyers...
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="buyerBusinessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                <input type="text" id="buyerBusinessName" name="buyerBusinessName" placeholder="Buyer Business Name" value="{{ $editInvoice->buyer_business_name ?? $user->business_name ?? $user->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="hidden">
                                <label for="buyerProvince" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                <select id="buyerProvince" name="buyerProvince" class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 province-select">
                                    <option value="{{ $editInvoice->buyer_province ?? $user->province ?? '7' }}" selected>Select Province</option>
                                </select>
                            </div>
                            <div>
                                <label for="buyerRegistrationType" class="block text-sm font-medium text-gray-700 mb-1">Registration Type</label>
                                <input type="text" id="buyerRegistrationType" name="buyerRegistrationType" value="{{ $editInvoice->buyer_registration_type ?? 'Registered' }}" readonly class="mt-1 block w-40 rounded-md border-gray-300 bg-gray-100 text-gray-700 shadow-sm font-semibold cursor-not-allowed">
                            </div>
                            <div class="md:col-span-4">
                                <label for="buyerAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea id="buyerAddress" name="buyerAddress" placeholder="Buyer Address" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 !h-[50px]">{{ $editInvoice->buyer_address ?? $user->address ?? '' }}</textarea>
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
                                    <tr id="noItemsRow">
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
                                        <td class="px-4 py-3 text-sm text-center">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Hidden container for form inputs -->
                        <div id="hiddenItemsContainer" style="display: none;"></div>
                    </div>

                    <div>
                        <table>
                            <tr>
                                <td>Transportation Charges</td>
                                <td><input name="furtherexpense" value="{{ $editInvoice->expense_col ?? 0 }}" id="furthertaxexpense" type="number" min="0" step="any" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Submit Section - Only Save Invoice button -->
                    <div class="flex justify-end space-x-4">
                        <button type="submit" id="submitBtn" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd" />
                            </svg>
                            {{ isset($editInvoice) ? 'Update Invoice' : 'Save Invoice' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Messages -->
<div id="statusMessages" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Add Item Modal -->
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
                        <div class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">HS Code</label>
                            <select id="modalHsCode" name="hsCode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm hs-code-select">
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
                            <input type="number" id="modalSalesTaxApplicable" name="salesTaxApplicable" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sales-tax-field">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fixed Notified Value/Retail Price <span class="text-red-500 schedule-3rd-fn-required hidden">*</span></label>
                            <input type="number" id="modalFixedNotifiedValueOrRetailPrice" name="fixedNotifiedValueOrRetailPrice" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <!-- 3rd Schedule specific fields (shown when 3rd Schedule selected) -->
                        <div class="schedule-3rd-field hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">G/H (%)</label>
                            <input type="number" id="modalGhPercent" name="ghPercent" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="schedule-3rd-field hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">G/H Amount</label>
                            <input type="number" id="modalGhAmount" name="ghAmount" placeholder="Auto-calculated" min="0" step="any" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="schedule-3rd-field hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount (%)</label>
                            <input type="number" id="modalDiscountPercent" name="discountPercent" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="schedule-3rd-field hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount</label>
                            <input type="number" id="modalDiscountAmount" name="discountAmount" placeholder="Auto-calculated" min="0" step="any" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="hidden">
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
                        <div class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                SRO Schedule No.
                                <span class="text-red-500 hidden sro-schedule-required">*</span>
                            </label>
                            <select id="modalSroScheduleNo" name="sroScheduleNo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sro-schedule-select">
                                <option value="">Select SRO Schedule</option>
                            </select>
                        </div>
                        <div class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">FED Payable</label>
                            <input type="number" id="modalFedPayable" name="fedPayable" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="schedule-standard-field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Discount
                                <select id="modalDiscountType" class="ml-1 text-xs border rounded px-1 py-0.5">
                                    <option value="fixed">Fixed</option>
                                    <option value="percent">%</option>
                                </select>
                            </label>
                            <input type="number" id="modalDiscount" name="discount" placeholder="0" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="hidden">
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
            <div class="flex items-center justify-end pt-6 border-t space-x-3">
                <button type="button" id="cancelModalBtn" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-600 disabled:opacity-25 transition">
                    Cancel
                </button>
                <button type="button" id="addItemFromModalBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Item
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Configuration
    const API_BASE = '{{ url('/') }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';
</script>

<script>
    // Pass data from backend to JavaScript
    window.appData = {
        provinces: @json($provinces ?? []),
        hsCodes: @json($hsCodes ?? []),
        uoMs: @json($uoMs ?? []),
        transactionTypes: @json($transactionTypes ?? []),
        editInvoice: @json($editInvoice ?? null),
        user: {
            cinc_ntn: @json($user->cinc_ntn ?? ''),
            business_name: @json($user->business_name ?? $user->name ?? ''),
            province: @json($user->province ?? ''),
            address: @json($user->address ?? ''),
            use_sandbox: @json($user->use_sandbox ?? true)
        }
    };
</script>

<script>
    // Global variables
    let itemCounter = 0;
    let provinces = window.appData.provinces;
    let hsCodes = window.appData.hsCodes;
    let uoMs = window.appData.uoMs;
    let transactionTypes = window.appData.transactionTypes;
    let userProfile = window.appData.user;
    let documentTypes = []; // For storing invoice/document types from FBR API

    // Global storage for SRO data (populated dynamically)
    let sroSchedules = new Map(); // Map<sroId, sroDesc>
    let sroItems = new Map(); // Map<sroItemId, sroItemDesc>

    // Client-side cache for HS codes search results
    let hsCodesCache = new Map();
    const CACHE_DURATION = 300000; // 5 minutes in milliseconds

    // Client-side cache for UoM data by HS code
    let uomCache = new Map();
    const UOM_CACHE_DURATION = 600000; // 10 minutes in milliseconds

    // Optimized HS codes search with client-side caching
    function searchHsCodesWithCache(params, success, failure) {
        const searchTerm = params.data.term || '';
        const page = params.data.page || 1;
        const cacheKey = `${searchTerm}_${page}`;

        // Check cache first
        const cachedResult = hsCodesCache.get(cacheKey);
        if (cachedResult && (Date.now() - cachedResult.timestamp) < CACHE_DURATION) {
            success(cachedResult.data);
            return;
        }

        // Make API request
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `${API_BASE}/premiertax/api/fbr/item-description-codes/search?search=${encodeURIComponent(searchTerm)}&page=${page}&limit=20`);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const raw = xhr.responseText;
                    console.log('RAW HS RESPONSE:', raw);

                    let cleanText = raw.trim();

                    // Remove invalid prefix (like "c")
                    if (!cleanText.startsWith('{')) {
                        cleanText = cleanText.substring(cleanText.indexOf('{'));
                    }

                    const data = JSON.parse(cleanText);
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

                        // Cache the result
                        hsCodesCache.set(cacheKey, {
                            data: result,
                            timestamp: Date.now()
                        });

                        // Clean up old cache entries (keep only last 50 searches)
                        if (hsCodesCache.size > 50) {
                            const oldestKey = hsCodesCache.keys().next().value;
                            hsCodesCache.delete(oldestKey);
                        }

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

    // Pre-warm cache with common HS codes (run in background)
    function warmHsCodesCache() {
        const commonSearchTerms = ['', '8', '84', '85', '39', '73', '62', '61', '90', '87'];
        commonSearchTerms.forEach((term, index) => {
            setTimeout(() => {
                searchHsCodesWithCache({
                    data: { term: term, page: 1 }
                }, () => {
                    // Success callback
                }, () => {
                    // Failure callback
                });
            }, index * 100);
        });
    }

    // Wait for dependencies to be loaded
    function waitForDependencies(callback) {
        if (window.dependenciesLoaded && typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            callback();
        } else {
            setTimeout(() => waitForDependencies(callback), 10);
        }
    }

    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
        waitForDependencies(function() {
            // Set today's date if creating new invoice
            if (!window.appData.editInvoice) {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('invoiceDate').value = today;
            } else if (window.appData.editInvoice.items) {
                let rawItems = window.appData.editInvoice.items;
                itemsData = Array.isArray(rawItems) ? rawItems : (typeof rawItems === 'string' ? JSON.parse(rawItems) : []);
                updateItemsTable();
                updateHiddenFormInputs();
            }

            // Populate data first, then initialize Select2
            populateProvinceSelects();
            populateTransactionTypeSelects();

            // Initialize Select2 after data is populated
            initializeSelect2();

            // Load only essential data (UoM is now loaded based on HS code selection)
            setTimeout(() => {
                loadAndPopulateDocumentTypes();
            }, 100);

            // Load transaction types if not already loaded
            if (!transactionTypes || transactionTypes.length === 0) {
                loadTransactionTypesFromAPI();
            }

            // Setup event listeners
            setupEventListeners();

            // Perform initial validation to set correct button state
            setTimeout(() => {
                handleRegistrationTypeChange();
                validateBuyerRequirements();
            }, 100);

            // Add a secondary validation after Select2 is fully initialized
            setTimeout(() => {
                handleRegistrationTypeChange();
                validateBuyerRequirements();
            }, 500);

            // Pre-warm HS codes cache in background
            setTimeout(() => {
                warmHsCodesCache();
            }, 2000);
        });
    });

    // Handle registration type changes
    function handleRegistrationTypeChange() {
        const registrationType = $('#buyerRegistrationType').val();
        const ntnCnicField = document.getElementById('buyerNTNCNIC');
        const businessNameField = document.getElementById('buyerBusinessName');
        const addressField = document.getElementById('buyerAddress');

        if (registrationType === 'Unregistered') {
            ntnCnicField.readOnly = false;
            businessNameField.readOnly = false;
            addressField.readOnly = false;

            ntnCnicField.classList.remove('bg-gray-100', 'text-gray-500');
            businessNameField.classList.remove('bg-gray-100', 'text-gray-500');
            addressField.classList.remove('bg-gray-100', 'text-gray-500');
        } else if (registrationType === 'Registered') {
            ntnCnicField.readOnly = false;
            businessNameField.readOnly = false;
            addressField.readOnly = false;

            ntnCnicField.classList.remove('bg-gray-100', 'text-gray-500');
            businessNameField.classList.remove('bg-gray-100', 'text-gray-500');
            addressField.classList.remove('bg-gray-100', 'text-gray-500');

            if (businessNameField.value === 'Unregistered Supplies') {
                businessNameField.value = '';
            }
        } else {
            ntnCnicField.readOnly = false;
            businessNameField.readOnly = false;
            addressField.readOnly = false;

            ntnCnicField.classList.remove('bg-gray-100', 'text-gray-500');
            businessNameField.classList.remove('bg-gray-100', 'text-gray-500');
            addressField.classList.remove('bg-gray-100', 'text-gray-500');
        }
    }

    // Validate buyer requirements for adding items
    function validateBuyerRequirements() {
        const buyerProvince = $('#buyerProvince').val();
        const buyerRegistrationType = $('#buyerRegistrationType').val();
        const addItemBtn = document.getElementById('addItemBtn');
        const requirementMsg = document.getElementById('addItemRequirement');

        const isValid = buyerProvince && buyerProvince.trim() !== '' &&
                       buyerRegistrationType && buyerRegistrationType.trim() !== '';

        if (isValid) {
            addItemBtn.disabled = false;
            addItemBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            addItemBtn.classList.add('hover:bg-blue-500');
            requirementMsg.classList.add('hidden');
            addItemBtn.title = '';
        } else {
            addItemBtn.disabled = true;
            addItemBtn.classList.add('opacity-50', 'cursor-not-allowed');
            addItemBtn.classList.remove('hover:bg-blue-500');
            requirementMsg.classList.remove('hidden');
            addItemBtn.title = 'Please select buyer province and registration type first';
        }

        return isValid;
    }

    // Setup event listeners
    function setupEventListeners() {
        document.getElementById('addItemBtn').addEventListener('click', openAddItemModal);
        document.getElementById('closeModalBtn').addEventListener('click', closeAddItemModal);
        document.getElementById('cancelModalBtn').addEventListener('click', closeAddItemModal);
        document.getElementById('addItemFromModalBtn').addEventListener('click', addItemFromModal);
        document.getElementById('invoiceForm').addEventListener('submit', submitInvoice);

        // Add event listeners for buyer field validation
        $('#buyerProvince').on('select2:select select2:clear change', validateBuyerRequirements);
        $('#buyerRegistrationType').on('select2:select select2:clear change', function() {
            handleRegistrationTypeChange();
            validateBuyerRequirements();
        });

        // Also add regular change event listeners as fallback
        document.getElementById('buyerProvince').addEventListener('change', validateBuyerRequirements);
        document.getElementById('buyerRegistrationType').addEventListener('change', function() {
            handleRegistrationTypeChange();
            validateBuyerRequirements();
        });

        // Add event listeners for rate calculation with Select2
        $('#buyerProvince').on('select2:select', calculateRates);
        document.getElementById('invoiceDate').addEventListener('change', calculateRates);

        // Delegate event listener for sale type changes in dynamic items using Select2
        $(document).on('select2:select', '.sale-type-select', function(e) {
            calculateRatesForItem(e.target);
        });

        // Delegate event listener for SRO Schedule selection changes
        $(document).on('select2:select', '.sro-schedule-select', function(e) {
            fetchSroItems(e.target);
            updateSroRequiredIndicators(e.target);
        });

        $(document).on('change', '.sro-schedule-select, #modalSroScheduleNo', function(e) {
            if ($(this).val()) {
                fetchSroItems(e.target);
                updateSroRequiredIndicators(e.target);
            }
        });

        $(document).on('select2:clear', '.sro-schedule-select', function(e) {
            updateSroRequiredIndicators(e.target);
        });

        $(document).on('select2:select', '.sro-item-select', function(e) {
            updateSroRequiredIndicators(e.target);
        });

        $(document).on('select2:clear', '.sro-item-select', function(e) {
            updateSroRequiredIndicators(e.target);
        });

        $(document).on('select2:select', '.hs-code-select', function(e) {
            fetchUomByHsCode(e.target);
        });

        $(document).on('input change', 'input[name*="[valueSalesExcludingST]"], #modalValueSalesExcludingST', function(e) {
            calculateSalesTaxForItem(e.target);
        });

        // Add event listener for rate*quantity and fixedNotifiedValueOrRetailPrice
        let _updatingFixedNotified = false;
        $(document).on('input', 'input[name*="[rateValues]"],input[name*="[quantity]"], #modalRateValues,#modalQuantity,#modalFixedNotifiedValueOrRetailPrice,#modalDiscount,#modalValueSalesExcludingST', function(e) {
            const isModal =
                e.target.closest('#addItemModal') !== null ||
                e.target.id === 'modalRateValues' ||
                e.target.id === 'modalFixedNotifiedValueOrRetailPrice' ||
                e.target.id === 'modalDiscount' ||
                e.target.id === 'modalValueSalesExcludingST';

            if (isModal) {
                const isThirdParty = is3rdScheduleSelected();

                let rateSelecter = document.getElementById('modalRateValues');
                let quantity = document.getElementById('modalQuantity');
                let rateTax = document.getElementById('modalRate');

                let rateData;
                try {
                    rateData = JSON.parse(rateTax.value);
                    if (!rateData || typeof rateData !== 'object' || rateData.rate_value === undefined) {
                        const parsed = parseFloat(rateTax.value.toString().replace('%', ''));
                        rateData = { rate_value: isNaN(parsed) ? 0 : parsed };
                    }
                } catch (err) {
                    const parsed = parseFloat(rateTax.value.toString().replace('%', ''));
                    rateData = { rate_value: isNaN(parsed) ? 0 : parsed };
                }

                let rate = 0;
                if (rateTax.value) {
                    try {
                        const rd = JSON.parse(rateTax.value);
                        if (rd && typeof rd === 'object' && rd.rate_value !== undefined) {
                            rate = parseFloat(rd.rate_value) || 0;
                        } else {
                            rate = parseFloat(rateTax.value.toString().replace('%', '')) || 0;
                        }
                    } catch (err) {
                        rate = parseFloat(rateTax.value.toString().replace('%', '')) || 0;
                    }
                }

                let valueSalesField = document.getElementById('modalValueSalesExcludingST');
                let valueFurtherField = document.getElementById('modalFurtherTax');
                let salesTaxField = document.getElementById('modalSalesTaxApplicable');
                let discountField = document.getElementById('modalDiscount');

                if (!rateTax || !valueSalesField || !salesTaxField) {
                    console.warn('Could not find required fields for sales tax calculation');
                    return;
                }

                const qty = parseFloat(quantity.value) || 0;
                const taxter = parseFloat(rateData.rate_value) || 0;

                if (isThirdParty) {
                    if (_updatingFixedNotified) return;
                    _updatingFixedNotified = true;
                    try {
                        const basePrice = parseFloat(document.getElementById('modalFixedNotifiedValueOrRetailPrice').value) || 0;
                        const ghPercent = parseFloat(document.getElementById('modalGhPercent').value) || 0;
                        const discountPercent = parseFloat(document.getElementById('modalDiscountPercent').value) || 0;

                        const discountAmount = basePrice * discountPercent / 100;
                        $('#modalDiscountAmount').val(discountAmount.toFixed(2));
                        discountField.value = discountAmount.toFixed(2);

                        const salesTax3rd = basePrice * rate / 100;
                        salesTaxField.value = salesTax3rd.toFixed(2);

                        const exclVal = basePrice - discountAmount;
                        $('#modalValueSalesExcludingST').val(exclVal.toFixed(2));

                        const ghAmount = (exclVal + salesTax3rd) * ghPercent / 100;
                        $('#modalGhAmount').val(ghAmount.toFixed(2));

                        if (rate > 0 && basePrice > 0) {
                            salesTaxField.classList.remove('bg-gray-50', 'bg-red-50');
                            salesTaxField.classList.add('bg-green-50');
                            salesTaxField.title = `3rd Schedule Tax: ${basePrice.toFixed(2)} × ${rate}% = ${salesTax3rd.toFixed(2)}`;
                        }

                        const furtherTaxAmt = exclVal * 4 / 100;
                        if ($('#buyerRegistrationType').val() === 'Unregistered') {
                            valueFurtherField.value = furtherTaxAmt.toFixed(2);
                        }
                    } finally {
                        _updatingFixedNotified = false;
                    }
                } else {
                    const rater = parseFloat(rateSelecter.value) || 0;
                    const totalTaxvalue = (rater * taxter) / 100;
                    const total = (rater + totalTaxvalue) * qty;
                    const totalDM = rater * qty;

                    $('#modalTotalValues').val(total.toFixed(2));
                    $('#modalValueSalesExcludingST').val(totalDM.toFixed(2));

                    const valueSales = parseFloat(valueSalesField.value) || 0;
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

                    if ($('#buyerRegistrationType').val() === 'Unregistered') {
                        valueFurtherField.value = (totalDM * 4 / 100).toFixed(2);
                    }
                }
            }
        });

        // Add event listener for rate changes
        $(document).on('select2:select', '.rate-select', function(e) {
            calculateSalesTaxForItem(e.target);

            if (e.target.id === 'modalRate') {
                $('#modalRateValues').trigger('input');
            } else {
                const itemContainer = e.target.closest('div.bg-white');
                if (itemContainer) {
                    const rateValuesInput = itemContainer.querySelector('input[name*="[rateValues]"]');
                    if (rateValuesInput) {
                        $(rateValuesInput).trigger('input');
                    }
                }
            }

            const rateSelect = e.target;
            const itemContainer = rateSelect.closest('div.bg-white') || rateSelect.closest('#addItemModal');
            const invoiceDate = document.getElementById('invoiceDate').value;
            const buyerProvince = $('#buyerProvince').val();

            if (rateSelect.value && invoiceDate && buyerProvince) {
                try {
                    const rateData = JSON.parse(rateSelect.value);
                    const rateId = rateData.rate_id;
                    if (rateId) {
                        fetchSroSchedule(rateId, invoiceDate, buyerProvince, itemContainer);
                    }
                } catch (error) {
                    console.warn('Could not parse rate data for SRO schedule:', error);
                }
            }
        });

        // Modal-specific event listeners
        $(document).on('select2:select', '#modalSaleType', function(e) {
            calculateRatesForItem(e.target);
            toggleScheduleFields(e.target);
        });

        $(document).on('select2:clear', '#modalSaleType', function(e) {
            toggleScheduleFields(e.target);
        });

        $(document).on('select2:select', '#modalHsCode', function(e) {
            fetchUomByHsCode(e.target);
            // Also reload rates when HS Code changes (rates depend on HS code)
            const saleTypeEl = document.getElementById('modalSaleType');
            if (saleTypeEl && saleTypeEl.value) {
                calculateRatesForItem(saleTypeEl);
            }
        });

        $(document).on('select2:select', '#modalSroScheduleNo', function(e) {
            fetchSroItems(e.target);
            updateSroRequiredIndicators(e.target);
            toggleScheduleFields(e.target);
        });

        $(document).on('select2:clear', '#modalSroScheduleNo', function(e) {
            updateSroRequiredIndicators(e.target);
            toggleScheduleFields(e.target);
        });

        $(document).on('select2:select', '#modalSroItemSerialNo', function(e) {
            updateSroRequiredIndicators(e.target);
        });

        $(document).on('select2:clear', '#modalSroItemSerialNo', function(e) {
            updateSroRequiredIndicators(e.target);
        });

        $(document).on('change', '#modalGhPercent', function() {
            recalculate3rdSchedule();
        });

        $(document).on('input', '#modalDiscountPercent', function() {
            recalculate3rdSchedule();
        });

        $(document).on('input', '#modalFixedNotifiedValueOrRetailPrice', function() {
            const saleTypeSelect = document.getElementById('modalSaleType');
            const saleTypeText = saleTypeSelect.options[saleTypeSelect.selectedIndex]?.text || '';
            const is3rdSchedule = saleTypeText.toLowerCase().includes('3rd schedule') || saleTypeText.toLowerCase().includes('3rd party');
            if (is3rdSchedule) {
                recalculate3rdSchedule();
            }
        });

        // Buyer autocomplete event listeners
        document.getElementById('buyerNTNCNIC').addEventListener('input', handleBuyerNTNInput);
        document.getElementById('buyerNTNCNIC').addEventListener('focus', handleBuyerNTNFocus);
        document.getElementById('buyerNTNCNIC').addEventListener('blur', handleBuyerNTNBlur);

        document.addEventListener('click', function(e) {
            const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
            const ntnInput = document.getElementById('buyerNTNCNIC');

            if (!ntnInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
                hideBuyerAutocomplete();
            }
        });
    }

    // Check if 3rd Schedule is active
    function is3rdScheduleSelected() {
        const saleTypeSelect = document.getElementById('modalSaleType');
        const saleTypeText = saleTypeSelect.options[saleTypeSelect.selectedIndex]?.text || '';
        const isSaleType3rd = saleTypeText.toLowerCase().includes('3rd schedule') || saleTypeText.toLowerCase().includes('3rd party');

        const sroScheduleSelect = document.getElementById('modalSroScheduleNo');
        const sroScheduleText = sroScheduleSelect.options[sroScheduleSelect.selectedIndex]?.text || '';
        const isSro3rd = sroScheduleText.toLowerCase().includes('3rd schedule') || sroScheduleText.toLowerCase().includes('3rd party');

        return isSaleType3rd || isSro3rd;
    }

    // Toggle 3rd Schedule specific fields
    function toggleScheduleFields(saleTypeSelect) {
        const is3rdSchedule = is3rdScheduleSelected();

        document.querySelectorAll('.schedule-3rd-field').forEach(el => {
            if (is3rdSchedule) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });

        document.querySelectorAll('.schedule-standard-field').forEach(el => {
            if (is3rdSchedule) {
                el.classList.add('hidden');
            } else {
                el.classList.remove('hidden');
            }
        });

        const fnField = document.getElementById('modalFixedNotifiedValueOrRetailPrice');
        const fnRequired = document.querySelector('.schedule-3rd-fn-required');

        if (is3rdSchedule) {
            fnField.required = true;
            fnField.setAttribute('aria-required', 'true');
            if (fnRequired) fnRequired.classList.remove('hidden');
        } else {
            fnField.required = false;
            fnField.removeAttribute('aria-required');
            if (fnRequired) fnRequired.classList.add('hidden');
        }

        document.querySelectorAll('.schedule-standard-field input[required], .schedule-standard-field select[required]').forEach(el => {
            if (is3rdSchedule) {
                el.required = false;
                el.removeAttribute('aria-required');
            } else {
                el.required = true;
                el.setAttribute('aria-required', 'true');
            }
        });

        const visibleFields = ['modalRateValues', 'modalTotalValues', 'modalValueSalesExcludingST'];
        visibleFields.forEach(id => {
            const field = document.getElementById(id);
            if (is3rdSchedule) {
                field.required = false;
                field.removeAttribute('aria-required');
            } else {
                field.required = true;
                field.setAttribute('aria-required', 'true');
            }
        });

        if (is3rdSchedule) {
            recalculate3rdSchedule();
        }
    }

    // Recalculate 3rd Schedule fields
    function recalculate3rdSchedule() {
        const fnField = document.getElementById('modalFixedNotifiedValueOrRetailPrice');
        const ghPercentField = document.getElementById('modalGhPercent');
        const ghAmountField = document.getElementById('modalGhAmount');
        const discountPercentField = document.getElementById('modalDiscountPercent');
        const discountAmountField = document.getElementById('modalDiscountAmount');
        const discountField = document.getElementById('modalDiscount');
        const valueSalesField = document.getElementById('modalValueSalesExcludingST');
        const salesTaxField = document.getElementById('modalSalesTaxApplicable');
        const rateSelect = document.getElementById('modalRate');

        const fnv = parseFloat(fnField.value) || 0;
        const ghPercent = parseFloat(ghPercentField.value) || 0;
        const discountPercent = parseFloat(discountPercentField.value) || 0;

        let rate = 0;
        if (rateSelect.value) {
            try {
                const rateData = JSON.parse(rateSelect.value);
                if (rateData && typeof rateData === 'object' && rateData.rate_value !== undefined) {
                    rate = parseFloat(rateData.rate_value) || 0;
                } else {
                    rate = parseFloat(rateSelect.value.toString().replace('%', '')) || 0;
                }
            } catch (e) {
                rate = parseFloat(rateSelect.value.toString().replace('%', '')) || 0;
            }
        }

        const discountAmount = fnv * discountPercent / 100;
        discountAmountField.value = discountAmount.toFixed(2);
        discountField.value = discountAmount.toFixed(2);

        const valueSales = fnv - discountAmount;
        valueSalesField.value = valueSales.toFixed(2);

        const salesTax = fnv * rate / 100;
        salesTaxField.value = salesTax.toFixed(2);

        const ghAmount = (valueSales + salesTax) * ghPercent / 100;
        ghAmountField.value = ghAmount.toFixed(2);
    }

    // Global variables for buyer autocomplete
    let buyerAutocompleteTimeout;
    let buyerAutocompleteCache = new Map();
    const BUYER_CACHE_DURATION = 300000;

    function handleBuyerNTNInput(e) {
        const searchValue = e.target.value.trim();
        if (buyerAutocompleteTimeout) {
            clearTimeout(buyerAutocompleteTimeout);
        }
        buyerAutocompleteTimeout = setTimeout(() => {
            if (searchValue.length >= 2) {
                searchBuyers(searchValue);
            } else if (searchValue.length === 0) {
                showAllRecentBuyers();
            } else {
                hideBuyerAutocomplete();
            }
        }, 300);
    }

    function handleBuyerNTNFocus(e) {
        const searchValue = e.target.value.trim();
        if (searchValue.length >= 2) {
            searchBuyers(searchValue);
        } else {
            showAllRecentBuyers();
        }
    }

    function handleBuyerNTNBlur() {
        setTimeout(() => {
            hideBuyerAutocomplete();
        }, 200);
    }

    async function searchBuyers(searchTerm) {
        try {
            const cacheKey = `search_${searchTerm}`;
            const cachedResult = buyerAutocompleteCache.get(cacheKey);

            if (cachedResult && (Date.now() - cachedResult.timestamp) < BUYER_CACHE_DURATION) {
                displayBuyerSuggestions(cachedResult.data);
                return;
            }

            const response = await fetch(`${API_BASE}/premiertax/api/buyers/search?ntn=${encodeURIComponent(searchTerm)}&limit=10`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            if (result.success && result.data) {
                buyerAutocompleteCache.set(cacheKey, {
                    data: result.data,
                    timestamp: Date.now()
                });
                displayBuyerSuggestions(result.data);
            } else {
                showNoBuyersMessage();
            }
        } catch (error) {
            console.error('Error searching buyers:', error);
        }
    }

    async function showAllRecentBuyers() {
        try {
            const cacheKey = 'recent_buyers';
            const cachedResult = buyerAutocompleteCache.get(cacheKey);

            if (cachedResult && (Date.now() - cachedResult.timestamp) < BUYER_CACHE_DURATION) {
                displayBuyerSuggestions(cachedResult.data);
                return;
            }

            const response = await fetch(`${API_BASE}/premiertax/api/buyers?limit=10`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            if (result.success && result.data) {
                buyerAutocompleteCache.set(cacheKey, {
                    data: result.data,
                    timestamp: Date.now()
                });
                displayBuyerSuggestions(result.data, 'Recent buyers:');
            } else {
                showNoBuyersMessage();
            }
        } catch (error) {
            console.error('Error loading recent buyers:', error);
        }
    }

    function displayBuyerSuggestions(buyers, titleText = 'Suggested Buyers:') {
        const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
        autocompleteDiv.innerHTML = '';

        if (!buyers || buyers.length === 0) {
            showNoBuyersMessage();
            return;
        }

        const title = document.createElement('div');
        title.className = 'p-2 text-xs font-semibold text-gray-500 border-b bg-gray-50';
        title.textContent = titleText;
        autocompleteDiv.appendChild(title);

        buyers.forEach(buyer => {
            const div = document.createElement('div');
            div.className = 'p-2 hover:bg-gray-100 cursor-pointer text-sm border-b transition-colors duration-150';
            div.dataset.ntn = buyer.buyerNTNCNIC || buyer.cinc_ntn;
            div.dataset.name = buyer.buyerBusinessName || buyer.business_name || buyer.name;
            div.dataset.address = buyer.buyerAddress || buyer.address || '';
            div.dataset.province = buyer.buyerProvince || buyer.province || '';
            div.dataset.regType = buyer.buyerRegistrationType || buyer.registration_type || 'Unregistered';

            div.innerHTML = `
                <div class="font-medium text-gray-900">${buyer.buyerBusinessName || buyer.business_name || buyer.name}</div>
                <div class="text-xs text-gray-500">NTN/CNIC: ${buyer.buyerNTNCNIC || buyer.cinc_ntn} | ${getProvinceDescription(buyer.buyerProvince || buyer.province)}</div>
            `;

            div.addEventListener('click', selectBuyerSuggestion);
            autocompleteDiv.appendChild(div);
        });

        autocompleteDiv.classList.remove('hidden');
    }

    function selectBuyerSuggestion(e) {
        const div = e.currentTarget;
        document.getElementById('buyerNTNCNIC').value = div.dataset.ntn;
        document.getElementById('buyerBusinessName').value = div.dataset.name;
        document.getElementById('buyerAddress').value = div.dataset.address;

        const provinceSelect = document.getElementById('buyerProvince');
        $(provinceSelect).val(div.dataset.province).trigger('change');

        const regTypeSelect = document.getElementById('buyerRegistrationType');
        $(regTypeSelect).val(div.dataset.regType).trigger('change');

        hideBuyerAutocomplete();
    }

    function hideBuyerAutocomplete() {
        document.getElementById('buyerNTNAutocomplete').classList.add('hidden');
    }

    function showNoBuyersMessage() {
        const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
        autocompleteDiv.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">No buyers found</div>';
        autocompleteDiv.classList.remove('hidden');
    }

    function getProvinceDescription(provinceCode) {
        if (!provinces || !provinceCode) return provinceCode;
        const province = provinces.find(p => p.stateProvinceCode === provinceCode);
        return province ? province.stateProvinceDesc : provinceCode;
    }

    // Populate transaction types (Sale Type) selects
    function populateTransactionTypeSelects() {
        if (transactionTypes && Array.isArray(transactionTypes)) {
            $('.sale-type-select').each(function() {
                const select = this;
                select.innerHTML = '<option value="">Select Sale Type</option>';
                transactionTypes.forEach(type => {
                    const typeId = type.transactioN_TYPE_ID;
                    const typeDesc = type.transactioN_DESC;
                    if (typeId && typeDesc) {
                        const option = document.createElement('option');
                        option.value = typeId;
                        option.textContent = typeDesc;
                        select.appendChild(option);
                    }
                });
            });
        }
    }

    // Populate province selects
    function populateProvinceSelects() {
        const defaultProv = userProfile.province || '7';
        if (provinces && Array.isArray(provinces) && provinces.length > 0) {
            $('.province-select').each(function() {
                const select = this;
                const currentVal = $(select).val() || defaultProv;
                select.innerHTML = '';
                provinces.forEach(province => {
                    const provinceCode = province.stateProvinceCode;
                    const provinceDesc = province.stateProvinceDesc;
                    if (provinceCode && provinceDesc) {
                        const option = document.createElement('option');
                        option.value = provinceCode;
                        option.textContent = provinceDesc;
                        if (provinceCode == currentVal) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    }
                });
                if (!$(select).val()) {
                    $(select).val(defaultProv);
                }
            });
        } else {
            $('.province-select').each(function() {
                if (!this.value) {
                    this.value = defaultProv;
                }
            });
        }
        if (!$('#sellerProvince').val()) $('#sellerProvince').val(defaultProv);
        if (!$('#buyerProvince').val()) $('#buyerProvince').val(defaultProv);
    }

    // Load fallbacks if FBR is down
    function loadDefaultDocumentTypes() {
        documentTypes = [
            { docTypeId: "Sale Invoice", docDescription: "Sale Invoice" },
            { docTypeId: "Debit Note", docDescription: "Debit Note" },
            { docTypeId: "Credit Note", docDescription: "Credit Note" },
            { docTypeId: "Purchase Invoice", docDescription: "Purchase Invoice" }
        ];
        populateDocumentTypeSelects();
        $('.invoice-type-select').select2('destroy').select2({
            placeholder: 'Select Invoice Type',
            allowClear: true,
            width: 'resolve'
        });
        // Auto-select Purchase Invoice as default using Select2 API
        const targetType = (window.appData.editInvoice && window.appData.editInvoice.invoice_type) ? window.appData.editInvoice.invoice_type : 'Purchase Invoice';
        if (!$('#invoiceType').val() || $('#invoiceType').val() !== targetType) {
            $('#invoiceType').val(targetType).trigger('change');
        }
    }

    function populateDocumentTypeSelects() {
        if (documentTypes && Array.isArray(documentTypes)) {
            $('.invoice-type-select').each(function() {
                const select = this;
                select.innerHTML = '<option value="">Select Invoice Type</option>';
                documentTypes.forEach(docType => {
                    const docDescription = docType.docDescription;
                    if (docDescription) {
                        const option = document.createElement('option');
                        option.value = docDescription;
                        option.textContent = docDescription;
                        select.appendChild(option);
                    }
                });
            });
        }
    }

    async function loadAndPopulateDocumentTypes() {
        try {
            const response = await fetch(`${API_BASE}/premiertax/api/fbr/doctypecode`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            if (result.success && result.data) {
                documentTypes = result.data;
                // Ensure Purchase Invoice is present
                if (!documentTypes.some(dt => dt.docDescription === 'Purchase Invoice')) {
                    documentTypes.push({ docTypeId: 'Purchase Invoice', docDescription: 'Purchase Invoice' });
                }
                populateDocumentTypeSelects();
                $('.invoice-type-select').select2('destroy').select2({
                    placeholder: 'Select Invoice Type',
                    allowClear: true,
                    width: 'resolve'
                });
                // Auto-select Purchase Invoice as default using Select2 API
                const targetType = (window.appData.editInvoice && window.appData.editInvoice.invoice_type) ? window.appData.editInvoice.invoice_type : 'Purchase Invoice';
                if (!$('#invoiceType').val() || $('#invoiceType').val() !== targetType) {
                    $('#invoiceType').val(targetType).trigger('change');
                }
            } else {
                loadDefaultDocumentTypes();
            }
        } catch (error) {
            loadDefaultDocumentTypes();
        }
    }

    async function loadTransactionTypesFromAPI() {
        try {
            const response = await fetch(`${API_BASE}/premiertax/api/fbr/transaction-types`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            if (result.success && result.data) {
                transactionTypes = result.data;
                populateTransactionTypeSelects();
                $('.sale-type-select').select2('destroy').select2({
                    placeholder: 'Select Sale Type',
                    allowClear: true,
                    width: '100%'
                });
            }
        } catch (error) {
            console.error('Error loading transaction types:', error);
        }
    }

    // Initialize Select2
    function initializeSelect2() {
        $('.province-select').select2({
            placeholder: 'Select Province',
            allowClear: true,
            width: 'resolve'
        });

        $('.invoice-type-select').select2({
            placeholder: 'Select Invoice Type',
            allowClear: true,
            width: 'resolve'
        });

        $('.sro-schedule-select').select2({
            placeholder: 'Select SRO Schedule',
            allowClear: true,
            width: 'resolve'
        });

        $('.sro-item-select').select2({
            placeholder: 'Select SRO Item',
            allowClear: true,
            width: 'resolve'
        });
    }

    // Calculate rates on global inputs changes
    async function calculateRates() {
        const rateSelects = document.querySelectorAll('.rate-select');
        for (const rateSelect of rateSelects) {
            if (rateSelect.id === 'modalRate' && document.getElementById('addItemModal').classList.contains('hidden')) continue;
            await calculateRateForField(rateSelect);
        }
    }

    // Calculate rate for a specific item when its sale type changes
    async function calculateRatesForItem(saleTypeField) {
        let rateSelect;
        if (saleTypeField.id === 'modalSaleType' || saleTypeField.closest('#addItemModal') !== null) {
            rateSelect = document.getElementById('modalRate');
        }
        if (rateSelect) {
            await calculateRateForField(rateSelect);
        }
    }

    // Calculate rate for a specific rate field (mirrors sale invoice logic)
    async function calculateRateForField(rateSelect) {
        let saleTypeField, loader;

        if (rateSelect.id === 'modalRate') {
            saleTypeField = document.getElementById('modalSaleType');
            loader = document.querySelector('#addItemModal .rate-loader');
        }

        if (!saleTypeField) {
            console.warn('Could not find sale type field for rate calculation');
            return;
        }

        const invoiceDate = document.getElementById('invoiceDate').value;
        const buyerProvince = $('#buyerProvince').val() || document.getElementById('buyerProvince').value;
        const saleType = ($(saleTypeField).hasClass('select2-hidden-accessible') ? $(saleTypeField).val() : null) || saleTypeField.value || '';

        if (!invoiceDate || !buyerProvince || !saleType) {
            rateSelect.innerHTML = '<option value="">Select Rate</option>';
            $(rateSelect).val('').trigger('change');
            rateSelect.classList.remove('bg-green-50', 'bg-red-50');
            rateSelect.classList.add('bg-gray-50');
            calculateSalesTaxForItem(rateSelect);
            return;
        }

        const transTypeId = saleType;
        const provinceCode = buyerProvince;

        if (!provinceCode) {
            rateSelect.innerHTML = '<option value="">Select Rate</option>';
            $(rateSelect).val('').trigger('change');
            rateSelect.classList.remove('bg-green-50');
            rateSelect.classList.add('bg-red-50');
            return;
        }

        try {
            if (loader) loader.classList.remove('hidden');
            rateSelect.innerHTML = '<option value="">Loading rates...</option>';
            $(rateSelect).val('').trigger('change');
            rateSelect.classList.remove('bg-gray-50', 'bg-red-50');
            rateSelect.classList.add('bg-blue-50');

            const response = await fetch(`${API_BASE}/premiertax/api/fbr/sale-type-to-rate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    date: invoiceDate,
                    trans_type_id: parseInt(transTypeId),
                    origination_supplier: parseInt(provinceCode)
                })
            });

            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            if (result.success && result.data && result.data.length > 0) {
                // Destroy existing Select2 before modifying options
                if ($(rateSelect).data('select2')) {
                    $(rateSelect).select2('destroy');
                }
                $(rateSelect).removeClass('select2-hidden-accessible');
                $(rateSelect).removeAttr('data-select2-id tabindex aria-hidden');

                rateSelect.innerHTML = '<option value="">Select Rate</option>';

                let targetOptionValue = '';
                result.data.forEach((rateData, index) => {
                    const rateValue = rateData.ratE_VALUE || rateData.rate_value || 0;
                    const rateId = rateData.ratE_ID || rateData.rate_id;
                    const rateDesc = rateData.ratE_DESC || rateData.rate_desc || '';

                    const option = document.createElement('option');
                    const optionValueObj = {
                        rate_id: rateId,
                        rate_value: rateValue,
                        rate_desc: rateDesc
                    };
                    option.value = JSON.stringify(optionValueObj);
                    const displayText = rateDesc && rateDesc.trim() !== '' ? rateDesc : `${rateValue}%`;
                    option.textContent = displayText;
                    option.title = rateDesc || `Rate: ${rateValue}%`;
                    rateSelect.appendChild(option);

                    // Auto-select first rate disabled to allow custom input
                });

                // Re-initialize Select2 with new options and tags support
                const dropdownParent = rateSelect.closest('#addItemModal') ? $('#addItemModal') : $('body');
                $(rateSelect).select2({
                    placeholder: 'Select Rate',
                    allowClear: true,
                    width: 'resolve',
                    dropdownParent: dropdownParent,
                    tags: true,
                    createTag: function (params) {
                        const term = $.trim(params.term);
                        if (term === '') return null;
                        const numericVal = parseFloat(term.replace('%', ''));
                        if (isNaN(numericVal)) return null;
                        const cleanTerm = numericVal.toString();
                        return {
                            id: cleanTerm,
                            text: cleanTerm + '%',
                            newTag: true
                        };
                    }
                });

                const finalVal = '';
                $(rateSelect).val(finalVal).trigger('change');

                rateSelect.classList.remove('bg-blue-50');
                rateSelect.classList.add('bg-green-50');
                rateSelect.title = `${result.data.length} rate(s) available`;

                // Trigger sales tax calculation with selected rate
                calculateSalesTaxForItem(rateSelect);
            } else {
                const errMsg = result.message || 'No rates found';
                rateSelect.innerHTML = `<option value="">⚠ ${errMsg}</option>`;
                $(rateSelect).val('').trigger('change');
                rateSelect.classList.remove('bg-blue-50');
                rateSelect.classList.add('bg-yellow-50');
                calculateSalesTaxForItem(rateSelect);
            }
        } catch (error) {
            console.error('Error calculating rates:', error);
            rateSelect.innerHTML = '<option value="">Error loading rates</option>';
            $(rateSelect).val('').trigger('change');
            rateSelect.classList.remove('bg-blue-50');
            rateSelect.classList.add('bg-red-50');
            calculateSalesTaxForItem(rateSelect);
        } finally {
            if (loader) loader.classList.add('hidden');
        }
    }

    // Calculate sales tax for item
    function calculateSalesTaxForItem(triggerField) {
        let rateSelect, valueSalesField, salesTaxField, valueFurtherField;

        const isModal = triggerField.closest('#addItemModal') !== null ||
                       triggerField.id === 'modalValueSalesExcludingST' ||
                       triggerField.id === 'modalRate';

        if (isModal) {
            rateSelect = document.getElementById('modalRate');
            valueSalesField = document.getElementById('modalValueSalesExcludingST');
            valueFurtherField = document.getElementById('modalFurtherTax');
            salesTaxField = document.getElementById('modalSalesTaxApplicable');
        }

        if (!rateSelect || !valueSalesField || !salesTaxField) {
            return;
        }

        let rate = 0;
        if (rateSelect.value) {
            try {
                const rateData = JSON.parse(rateSelect.value);
                if (rateData && typeof rateData === 'object' && rateData.rate_value !== undefined) {
                    rate = parseFloat(rateData.rate_value) || 0;
                } else {
                    rate = parseFloat(rateSelect.value.toString().replace('%', '')) || 0;
                }
            } catch (e) {
                rate = parseFloat(rateSelect.value.toString().replace('%', '')) || 0;
            }
        }
        const valueSales = parseFloat(valueSalesField.value) || 0;

        let baseForTax, salesTax;
        if (isModal) {
            const isThirdParty = is3rdScheduleSelected();
            if (isThirdParty) {
                const basePrice = parseFloat(document.getElementById('modalFixedNotifiedValueOrRetailPrice').value) || 0;
                salesTax = basePrice * rate / 100;
                salesTaxField.value = salesTax.toFixed(2);

                const discountPercent = parseFloat(document.getElementById('modalDiscountPercent').value) || 0;
                const discountAmount = basePrice * discountPercent / 100;
                $('#modalDiscountAmount').val(discountAmount.toFixed(2));

                const exclVal = basePrice - discountAmount;
                $('#modalValueSalesExcludingST').val(exclVal.toFixed(2));
                
                const ghPercent = parseFloat(document.getElementById('modalGhPercent').value) || 0;
                const ghBase = exclVal + salesTax;
                const ghAmount = ghBase * ghPercent / 100;
                $('#modalGhAmount').val(ghAmount.toFixed(2));

                if (rate > 0 && basePrice > 0) {
                    salesTaxField.classList.remove('bg-gray-50', 'bg-red-50');
                    salesTaxField.classList.add('bg-green-50');
                    salesTaxField.title = `3rd Schedule Tax: ${basePrice.toFixed(2)} × ${rate}% = ${salesTax.toFixed(2)}`;
                } else {
                    salesTaxField.classList.remove('bg-green-50');
                    salesTaxField.classList.add('bg-gray-50');
                    salesTaxField.title = 'Enter rate and base price to calculate';
                }
                return;
            } else {
                baseForTax = valueSales;
                salesTax = (valueSales * rate) / 100;
            }
        }

        const salesfur = (valueSales * 4) / 100;
        const taxTyper = $('#buyerRegistrationType').val();
        if (taxTyper === 'Unregistered' && valueFurtherField) {
            valueFurtherField.value = salesfur.toFixed(2);
        }
        salesTaxField.value = salesTax.toFixed(2);

        if (rate > 0 && baseForTax > 0) {
            salesTaxField.classList.remove('bg-gray-50', 'bg-red-50');
            salesTaxField.classList.add('bg-green-50');
            salesTaxField.title = `Calculated: ${baseForTax} × ${rate}% = ${salesTax.toFixed(2)}`;
        } else {
            salesTaxField.classList.remove('bg-green-50');
            salesTaxField.classList.add('bg-gray-50');
            salesTaxField.title = 'Enter rate and value sales to calculate';
        }
    }

    // Fetch UOM by HS Code
    async function fetchUomByHsCode(hsCodeSelect, skipCacheCheck = false) {
        let uomSelect;
        if (hsCodeSelect.id === 'modalHsCode') {
            uomSelect = document.getElementById('modalUoM');
        }

        const hsCode = hsCodeSelect.value;

        if (!hsCode) {
            if (uomSelect) {
                $(uomSelect).val('').trigger('change');
                uomSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
                uomSelect.classList.add('bg-gray-50');
                uomSelect.disabled = true;
            }
            return;
        }

        if (!skipCacheCheck) {
            const cachedUom = uomCache.get(hsCode);
            if (cachedUom && (Date.now() - cachedUom.timestamp) < UOM_CACHE_DURATION) {
                populateUomSelect(uomSelect, cachedUom.data, hsCode, cachedUom.fallback);
                return;
            }
        }

        try {
            if (uomSelect) {
                uomSelect.innerHTML = '<option value="">Loading UOM...</option>';
                uomSelect.classList.remove('bg-gray-50');
                uomSelect.classList.add('bg-blue-50');
                uomSelect.disabled = true;
            }

            const response = await fetch(`${API_BASE}/premiertax/api/fbr/uom-by-hs-code`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ hs_code: hsCode })
            });

            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            if (result.success && result.data && result.data.length > 0) {
                uomCache.set(hsCode, {
                    data: result.data,
                    fallback: result.fallback || false,
                    timestamp: Date.now()
                });
                populateUomSelect(uomSelect, result.data, hsCode, result.fallback);
            }
        } catch (error) {
            console.error('Error fetching UOM:', error);
        }
    }

    function populateUomSelect(select, data, hsCode, isFallback) {
        if (!select) return;
        select.innerHTML = '';
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.uoM_ID || item.id;
            opt.textContent = item.uoM_DESC || item.description;
            select.appendChild(opt);
        });
        select.disabled = false;
        select.classList.remove('bg-blue-50', 'bg-gray-50');
        select.classList.add('bg-green-50');
        $(select).trigger('change');
        calculateRates();
    }

    // Fetch SRO Schedule
    async function fetchSroSchedule(rateId, date, provinceCode, itemContainer) {
        const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
        if (!sroScheduleSelect) return;

        try {
            sroScheduleSelect.innerHTML = '<option value="">Loading SRO...</option>';
            sroScheduleSelect.disabled = true;

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

            sroScheduleSelect.innerHTML = '<option value="">Select SRO Schedule</option>';
            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.srO_SCHEDULE_ID;
                    opt.textContent = item.srO_NO + ' - ' + item.schedule;
                    sroScheduleSelect.appendChild(opt);
                    sroSchedules.set(String(item.srO_SCHEDULE_ID), item.srO_NO + ' - ' + item.schedule);
                });
                sroScheduleSelect.disabled = false;
                $(sroScheduleSelect).trigger('change');
            }
        } catch (error) {
            console.error('Error fetching SRO schedules:', error);
        } finally {
            sroScheduleSelect.disabled = false;
        }
    }

    // Fetch SRO Items
    async function fetchSroItems(sroScheduleSelect) {
        const itemContainer = sroScheduleSelect.closest('#addItemModal');
        const sroItemSelect = itemContainer.querySelector('.sro-item-select');
        const sroScheduleId = sroScheduleSelect.value;

        if (!sroItemSelect) return;
        if (!sroScheduleId) {
            sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';
            $(sroItemSelect).val('').trigger('change');
            return;
        }

        try {
            sroItemSelect.innerHTML = '<option value="">Loading SRO Items...</option>';
            sroItemSelect.disabled = true;

            const response = await fetch(`${API_BASE}/premiertax/api/fbr/sro-items`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ sro_schedule_id: parseInt(sroScheduleId) })
            });

            const text = await response.text();
            const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
            const result = JSON.parse(cleanText);

            sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';
            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.srO_ITEM_SERIAL_NO_ID;
                    opt.textContent = item.srO_ITEM_SERIAL_NO + ' - ' + item.description;
                    sroItemSelect.appendChild(opt);
                    sroItems.set(String(item.srO_ITEM_SERIAL_NO_ID), item.srO_ITEM_SERIAL_NO + ' - ' + item.description);
                });
                sroItemSelect.disabled = false;
                $(sroItemSelect).trigger('change');
            }
        } catch (error) {
            console.error('Error fetching SRO items:', error);
        } finally {
            sroItemSelect.disabled = false;
        }
    }

    function updateSroRequiredIndicators(triggerElement) {
        const itemContainer = triggerElement.closest('#addItemModal');
        if (!itemContainer) return;

        const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
        const sroItemSelect = itemContainer.querySelector('.sro-item-select');

        const sroScheduleValue = sroScheduleSelect ? sroScheduleSelect.value : '';
        const sroItemValue = sroItemSelect ? sroItemSelect.value : '';

        const sroScheduleHasOptions = sroScheduleSelect && sroScheduleSelect.options.length > 1;
        const sroItemHasOptions = sroItemSelect && sroItemSelect.options.length > 1;

        const sroScheduleRequired = itemContainer.querySelector('.sro-schedule-required');
        const sroItemRequired = itemContainer.querySelector('.sro-item-required');

        if (sroScheduleRequired) {
            if (sroScheduleHasOptions) {
                sroScheduleRequired.classList.remove('hidden');
            } else {
                sroScheduleRequired.classList.add('hidden');
            }
        }

        if (sroItemRequired) {
            if (sroScheduleValue && sroScheduleValue.trim() !== '' && sroItemHasOptions) {
                sroItemRequired.classList.remove('hidden');
            } else if (sroItemValue && sroItemValue.trim() !== '') {
                sroItemRequired.classList.remove('hidden');
            } else {
                sroItemRequired.classList.add('hidden');
            }
        }
    }

    // Global storage of items
    let itemsData = [];
    let editingItemIndex = -1;

    function openAddItemModal() {
        if (!validateBuyerRequirements()) {
            showMessage('Please select buyer province and registration type before adding items', 'warning');
            return;
        }
        clearModalForm();
        editingItemIndex = -1;

        document.querySelector('#addItemModal h3').textContent = 'Add Invoice Item';
        document.getElementById('addItemFromModalBtn').innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Item
        `;
        document.getElementById('addItemModal').classList.remove('hidden');
        populateModalSelects();
        initializeModalSelect2();
    }

    function closeAddItemModal() {
        document.getElementById('addItemModal').classList.add('hidden');
        clearModalForm();
    }

    function clearModalForm() {
        const form = document.getElementById('itemForm');
        form.reset();

        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            if ($(select).hasClass('select2-hidden-accessible')) {
                $(select).val('').trigger('change');
            }
        });

        const uomSelect = document.getElementById('modalUoM');
        if (window.appData && window.appData.uoMs && window.appData.uoMs.length > 0) {
            uomSelect.innerHTML = '<option value="">Select Unit of Measure</option>';
            window.appData.uoMs.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.uoM_ID || item.id;
                opt.textContent = item.uoM_DESC || item.description;
                uomSelect.appendChild(opt);
            });
            uomSelect.disabled = false;
            uomSelect.classList.remove('bg-blue-50', 'bg-gray-50');
            uomSelect.classList.add('bg-green-50');
        } else {
            uomSelect.innerHTML = '<option value="">Select HS Code first</option>';
            uomSelect.disabled = true;
            uomSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
            uomSelect.classList.add('bg-gray-50');
        }

        const salesTaxField = document.getElementById('modalSalesTaxApplicable');
        if (salesTaxField) {
            salesTaxField.classList.remove('bg-green-50', 'bg-red-50');
            salesTaxField.classList.add('bg-gray-50');
            salesTaxField.title = 'Enter rate and value sales to calculate';
        }
    }

    function initializeModalSelect2() {
        $('#modalSaleType').select2({
            placeholder: 'Select Sale Type',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addItemModal')
        });

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

        $('#modalUoM').select2({
            placeholder: 'Select Unit of Measure',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addItemModal')
        });

        $('#modalRate').select2({
            placeholder: 'Select Rate',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addItemModal'),
            tags: true,
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                const numericVal = parseFloat(term.replace('%', ''));
                if (isNaN(numericVal)) return null;
                const cleanTerm = numericVal.toString();
                return {
                    id: cleanTerm,
                    text: cleanTerm + '%',
                    newTag: true
                };
            }
        });

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

    function populateModalSelects() {
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

    function addItemFromModal() {
        try {
            const form = document.getElementById('itemForm');
            
            // Custom validation check for better feedback and to avoid hidden Select2 element validation silent failures
            const requiredFields = [
                { id: 'modalSaleType', label: 'Sale Type' },
                { id: 'modalProductDescription', label: 'Product Description' },
                { id: 'modalRate', label: 'Rate (%)' },
                { id: 'modalUoM', label: 'Unit of Measure' },
                { id: 'modalQuantity', label: 'Quantity' },
                { id: 'modalRateValues', label: 'Rate' }
            ];

            const is3rd = is3rdScheduleSelected();
            if (is3rd) {
                requiredFields.push({ id: 'modalFixedNotifiedValueOrRetailPrice', label: 'Fixed Notified Value/Retail Price' });
            } else {
                requiredFields.push({ id: 'modalTotalValues', label: 'Total Values' });
                requiredFields.push({ id: 'modalValueSalesExcludingST', label: 'Value Sales Excluding ST' });
            }

            for (const field of requiredFields) {
                const el = document.getElementById(field.id);
                if (!el || !el.value || el.value.trim() === '') {
                    showMessage(`Please fill in the required field: ${field.label}`, 'warning');
                    if (el) {
                        if ($(el).hasClass('select2-hidden-accessible')) {
                            $(el).select2('open');
                        } else {
                            el.focus();
                        }
                    }
                    return;
                }
            }

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const itemData = {};

            for (let [key, value] of formData.entries()) {
                itemData[key] = value;
            }

            itemData.saleTypeText = getSelectText('modalSaleType');
            itemData.hsCodeText = getSelectText('modalHsCode');
            itemData.uoMText = getSelectText('modalUoM');
            itemData.rateText = getSelectText('modalRate');
            itemData.sroScheduleNoText = getSelectText('modalSroScheduleNo');
            itemData.sroItemSerialNoText = getSelectText('modalSroItemSerialNo');

            if (itemData.rate) {
                try {
                    const rateData = JSON.parse(itemData.rate);
                    if (rateData && typeof rateData === 'object' && rateData.rate_value !== undefined) {
                        itemData.rateValue = parseFloat(rateData.rate_value) || 0;
                    } else {
                        itemData.rateValue = parseFloat(itemData.rate.toString().replace('%', '')) || 0;
                    }
                } catch (e) {
                    itemData.rateValue = parseFloat(itemData.rate.toString().replace('%', '')) || 0;
                }
            } else {
                itemData.rateValue = 0;
            }

            const discountType = document.getElementById('modalDiscountType').value;
            itemData.discountType = discountType;
            const discountVal = parseFloat(itemData.discount) || 0;
            if (discountType === 'percent' && discountVal > 0) {
                itemData.discountPercentInput = discountVal;
                const rateVal = parseFloat(itemData.rateValues) || 0;
                const qtyVal = parseFloat(itemData.quantity) || 0;
                const baseForDiscount = rateVal * qtyVal;
                itemData.discount = (baseForDiscount * discountVal / 100).toFixed(2);
            }

            if (editingItemIndex >= 0) {
                itemsData[editingItemIndex] = itemData;
                showMessage('Item updated successfully!', 'success');
            } else {
                itemsData.push(itemData);
                showMessage('Item added successfully!', 'success');
            }

            updateItemsTable();
            updateHiddenFormInputs();
            closeAddItemModal();
        } catch (error) {
            console.error('Error adding item:', error);
            showMessage('An error occurred while adding the item: ' + error.message, 'error');
        }
    }

    function getSelectText(selectId) {
        const select = document.getElementById(selectId);
        if (select && select.selectedIndex >= 0) {
            return select.options[select.selectedIndex].text;
        }
        return '';
    }

    function updateTableTotals() {
        const tableFooter = document.getElementById('itemsTableFooter');
        const totalQuantityEl = document.getElementById('totalQuantity');
        const totalValueSalesEl = document.getElementById('totalValueSales');
        const totalSalesTaxEl = document.getElementById('totalSalesTax');

        if (itemsData.length === 0) {
            tableFooter.classList.add('hidden');
            return;
        }

        tableFooter.classList.remove('hidden');

        let totalQuantity = 0;
        let totalValueSales = 0;
        let totalSalesTax = 0;

        itemsData.forEach(item => {
            totalQuantity += parseFloat(item.quantity || 0);
            totalValueSales += parseFloat(item.valueSalesExcludingST || 0);
            totalSalesTax += parseFloat(item.salesTaxApplicable || 0);
        });

        totalQuantityEl.textContent = totalQuantity % 1 === 0 ? totalQuantity.toFixed(0) : totalQuantity.toFixed(2);
        totalValueSalesEl.textContent = totalValueSales.toFixed(2);
        totalSalesTaxEl.textContent = totalSalesTax.toFixed(2);
    }

    function updateItemsTable() {
        const tableBody = document.getElementById('itemsTableBody');
        const noItemsRow = document.getElementById('noItemsRow');

        const existingRows = tableBody.querySelectorAll('tr:not(#noItemsRow)');
        existingRows.forEach(row => row.remove());

        if (itemsData.length === 0) {
            noItemsRow.style.display = '';
            updateTableTotals();
            return;
        }

        noItemsRow.style.display = 'none';

        itemsData.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            row.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${item.productDescription || '-'}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${item.hsCodeText || '-'}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${item.quantity || 0}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${item.rateValue || 0}%</td>
                <td class="px-4 py-3 text-sm text-gray-900">${parseFloat(item.valueSalesExcludingST || 0).toFixed(2)}</td>
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

        updateTableTotals();
    }

    function editItem(index) {
        if (index < 0 || index >= itemsData.length) return;

        if (!validateBuyerRequirements()) {
            showMessage('Please select buyer province and registration type before editing items', 'warning');
            return;
        }

        editingItemIndex = index;
        const item = itemsData[index];

        document.getElementById('addItemModal').classList.remove('hidden');
        initializeModalSelect2();
        populateModalSelects();

        setTimeout(async () => {
            // 1. Sale Type
            const saleTypeSelect = document.getElementById('modalSaleType');
            const targetSaleType = item.saleType || item.saleTypeId || '';
            const targetSaleTypeText = item.saleTypeText || item.saleType || '';

            if (saleTypeSelect) {
                let foundSaleType = false;
                for (let i = 0; i < saleTypeSelect.options.length; i++) {
                    const opt = saleTypeSelect.options[i];
                    if ((targetSaleType && opt.value == targetSaleType) || (targetSaleTypeText && opt.text.trim().toLowerCase() === targetSaleTypeText.trim().toLowerCase())) {
                        $(saleTypeSelect).val(opt.value).trigger('change');
                        foundSaleType = true;
                        break;
                    }
                }
                if (!foundSaleType && (targetSaleType || targetSaleTypeText)) {
                    const optVal = targetSaleType || targetSaleTypeText;
                    const optText = targetSaleTypeText || targetSaleType;
                    const newOpt = new Option(optText, optVal, true, true);
                    $(saleTypeSelect).append(newOpt).trigger('change');
                }
            }

            // 2. Direct input values
            if (item.productDescription !== undefined) document.getElementById('modalProductDescription').value = item.productDescription;
            if (item.quantity !== undefined) document.getElementById('modalQuantity').value = item.quantity;
            if (item.rateValues !== undefined) document.getElementById('modalRateValues').value = item.rateValues;
            if (item.totalValues !== undefined) document.getElementById('modalTotalValues').value = item.totalValues;
            if (item.valueSalesExcludingST !== undefined) document.getElementById('modalValueSalesExcludingST').value = item.valueSalesExcludingST;
            if (item.salesTaxApplicable !== undefined) document.getElementById('modalSalesTaxApplicable').value = item.salesTaxApplicable;
            if (item.fixedNotifiedValueOrRetailPrice !== undefined) document.getElementById('modalFixedNotifiedValueOrRetailPrice').value = item.fixedNotifiedValueOrRetailPrice;
            if (item.extraTax !== undefined) document.getElementById('modalExtraTax').value = item.extraTax;
            if (item.furtherTax !== undefined) document.getElementById('modalFurtherTax').value = item.furtherTax;
            if (item.discountType !== undefined) document.getElementById('modalDiscountType').value = item.discountType;

            if (item.discount !== undefined) {
                document.getElementById('modalDiscount').value = (item.discountType === 'percent' && item.discountPercentInput !== undefined) ? item.discountPercentInput : item.discount;
            }

            // 3. Unit of Measure
            const uomSelect = document.getElementById('modalUoM');
            const targetUoM = item.uoM || '';
            const targetUoMText = item.uoMText || item.uoM || '';

            if (uomSelect) {
                let foundUoM = false;
                for (let i = 0; i < uomSelect.options.length; i++) {
                    const opt = uomSelect.options[i];
                    if ((targetUoM && opt.value == targetUoM) || (targetUoMText && opt.text.trim().toLowerCase() === targetUoMText.trim().toLowerCase())) {
                        $(uomSelect).val(opt.value).trigger('change');
                        foundUoM = true;
                        break;
                    }
                }
                if (!foundUoM && (targetUoM || targetUoMText)) {
                    const optVal = targetUoM || targetUoMText;
                    const optText = targetUoMText || targetUoM;
                    const newOpt = new Option(optText, optVal, true, true);
                    $(uomSelect).append(newOpt).trigger('change');
                }
            }

            // 4. Rate (%)
            const rateSelect = document.getElementById('modalRate');
            let rawRateVal = item.rateValue !== undefined && item.rateValue !== '' ? item.rateValue : (item.rate || '');
            let cleanRateNum = rawRateVal.toString().replace(/%/g, '').trim();
            let targetRateText = item.rateText || (cleanRateNum ? cleanRateNum + '%' : '');
            targetRateText = targetRateText.toString().replace(/%+$/, '').trim() + '%';

            if (rateSelect) {
                let foundRate = false;
                for (let i = 0; i < rateSelect.options.length; i++) {
                    const opt = rateSelect.options[i];
                    const optCleanText = opt.text.replace(/%/g, '').trim();
                    if (opt.value == item.rate || (cleanRateNum !== '' && optCleanText === cleanRateNum)) {
                        $(rateSelect).val(opt.value).trigger('change');
                        foundRate = true;
                        break;
                    }
                }
                if (!foundRate && cleanRateNum !== '') {
                    const optVal = typeof item.rate === 'object' ? JSON.stringify(item.rate) : (item.rate || cleanRateNum);
                    const newOpt = new Option(targetRateText, optVal, true, true);
                    $(rateSelect).append(newOpt).trigger('change');
                }
            }

            // 5. 3rd Schedule fields check
            const saleTypeText = item.saleTypeText || targetSaleTypeText || '';
            if (saleTypeText.toLowerCase().includes('3rd schedule') || saleTypeText.toLowerCase().includes('3rd party')) {
                if (item.ghPercent !== undefined) document.getElementById('modalGhPercent').value = item.ghPercent;
                if (item.discountPercent !== undefined) document.getElementById('modalDiscountPercent').value = item.discountPercent;
                recalculate3rdSchedule();
            }

            // 6. SRO Schedule & Item
            if (item.sroScheduleNo) {
                $('#modalSroScheduleNo').val(item.sroScheduleNo).trigger('change');
            }
            if (item.sroItemSerialNo) {
                $('#modalSroItemSerialNo').val(item.sroItemSerialNo).trigger('change');
            }

            document.querySelector('#addItemModal h3').textContent = 'Edit Invoice Item';
            document.getElementById('addItemFromModalBtn').innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.828-2.828z" />
                </svg>
                Update Item
            `;
        }, 150);
    }

    function deleteItem(index) {
        if (index < 0 || index >= itemsData.length) return;
        if (confirm('Are you sure you want to delete this item?')) {
            itemsData.splice(index, 1);
            updateItemsTable();
            updateHiddenFormInputs();
            showMessage('Item deleted successfully!', 'success');
        }
    }

    function updateHiddenFormInputs() {
        const container = document.getElementById('hiddenItemsContainer');
        container.innerHTML = '';

        itemsData.forEach((item, index) => {
            Object.keys(item).forEach(key => {
                if (key.endsWith('Text') || key === 'rateValue') return;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `items[${index}][${key}]`;
                input.value = item[key];
                container.appendChild(input);
            });
        });
    }

    function convertIdsToLabels(data) {
        const convertedData = JSON.parse(JSON.stringify(data));
        if (convertedData.buyerProvince && provinces) {
            const buyerProvince = provinces.find(p => p.stateProvinceCode == convertedData.buyerProvince);
            if (buyerProvince) {
                convertedData.buyerProvince = buyerProvince.stateProvinceDesc;
            }
        }

        if (convertedData.items && Array.isArray(convertedData.items)) {
            convertedData.items.forEach(item => {
                if (item.uoM && uoMs) {
                    const uom = uoMs.find(u => (u.uoM_ID || u.id) == item.uoM);
                    if (uom) {
                        item.uoM = uom.uoM_DESC || uom.description || item.uoM;
                    }
                }
                if (item.saleType && transactionTypes) {
                    const saleType = transactionTypes.find(t => t.transactioN_TYPE_ID == item.saleType);
                    if (saleType) {
                        item.saleType = saleType.transactioN_DESC || item.saleType;
                    }
                }
                if (item.sroScheduleNo && sroSchedules.has(String(item.sroScheduleNo))) {
                    item.sroScheduleNo = sroSchedules.get(String(item.sroScheduleNo));
                }
                if (item.sroItemSerialNo && sroItems.has(String(item.sroItemSerialNo))) {
                    item.sroItemSerialNo = sroItems.get(String(item.sroItemSerialNo));
                }
            });
        }
        return convertedData;
    }

    // Submit invoice
    async function submitInvoice(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        // Client-side pre-validation before sending to server
        const requiredFormFields = [
            { id: 'sellerNTNCNIC',         label: 'Seller CNIC/NTN' },
            { id: 'sellerBusinessName',    label: 'Seller Business Name' },
            { id: 'sellerProvince',        label: 'Seller Province' },
            { id: 'sellerAddress',         label: 'Seller Address' },
            { id: 'invoiceType',           label: 'Invoice Type' },
            { id: 'invoiceDate',           label: 'Invoice Date' },
            { id: 'buyerNTNCNIC',          label: 'Buyer NTN/CNIC' },
            { id: 'buyerBusinessName',     label: 'Buyer Business Name' },
            { id: 'buyerProvince',         label: 'Buyer Province' },
            { id: 'buyerRegistrationType', label: 'Buyer Registration Type' },
            { id: 'buyerAddress',          label: 'Buyer Address' },
        ];
        const missingFields = [];
        for (const field of requiredFormFields) {
            const el = document.getElementById(field.id);
            if (!el || !el.value || el.value.trim() === '') {
                missingFields.push(field.label);
            }
        }
        if (missingFields.length > 0) {
            showMessage('Please fill in: ' + missingFields.join(', '), 'error');
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd"/></svg> Save Invoice`;
            return;
        }

        const formData = new FormData(e.target);
        const data = formDataToObjectWithLabels(formData);

        if (!data) {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd"/></svg> Save Invoice`;
            return;
        }

        try {
            showMessage('Saving purchase invoice...', 'info');

            // Log the data being sent for debugging
            console.log('Submitting purchase invoice data:', data);

            const response = await fetch(e.target.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const text = await response.text();
            console.log('Server response (status ' + response.status + '):', text);

            let result;
            try {
                const jsonStart = text.indexOf('{');
                const cleanText = jsonStart >= 0 ? text.substring(jsonStart) : text;
                result = JSON.parse(cleanText);
            } catch (parseErr) {
                console.error('Could not parse server response as JSON:', text);
                showMessage('Save failed: Server returned an unexpected response (HTTP ' + response.status + '). Check browser console for details.', 'error');
                return;
            }

            if (result.success) {
                showMessage(result.message || 'Purchase Invoice saved successfully!', 'success');
                setTimeout(() => {
                    window.location.href = "{{ route('purchase.invoicing.index') }}";
                }, 1200);
            } else {
                // Show detailed errors if available
                let errorMsg = result.message || 'Unknown error';
                if (result.errors) {
                    const fieldErrors = Object.entries(result.errors)
                        .map(([field, msgs]) => `${field}: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                        .join('\n');
                    console.error('Validation errors:', result.errors);
                    errorMsg = 'Validation failed:\n' + fieldErrors;
                }
                showMessage('Save failed: ' + errorMsg, 'error');
            }
        } catch (error) {
            console.error('Save error:', error);
            showMessage('Save failed: ' + error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd"/></svg> Save Invoice`;
        }
    }

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
                    } else if (['quantity', 'totalValues', 'valueSalesExcludingST', 'salesTaxApplicable',
                         'fixedNotifiedValueOrRetailPrice', 'salesTaxWithheldAtSource', 'furtherTax',
                         'fedPayable', 'discount'].includes(fieldName)) {
                        items[itemIndex][fieldName] = parseFloat(value) || 0;
                    } else {
                        items[itemIndex][fieldName] = value;
                    }
                }
            } else {
                if (key === 'buyerProvince' && provinces) {
                    const province = provinces.find(p => p.stateProvinceCode == value);
                    obj[key] = province ? province.stateProvinceDesc : value;
                } else if (key === 'sellerProvince' && provinces) {
                    const province = provinces.find(p => p.stateProvinceCode == value);
                    obj[key] = province ? province.stateProvinceDesc : value;
                } else {
                    obj[key] = value;
                }
            }
        }

        if (Object.keys(items).length === 0 && itemsData.length === 0) {
            showMessage('Please add at least one item to the invoice!', 'error');
            return null;
        }

        obj.items = Object.values(items);
        return obj;
    }

    // Show status message
    function showMessage(message, type = 'info') {
        const messagesContainer = document.getElementById('statusMessages');
        if (!messagesContainer) return;
        
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
            case 'warning':
                bgColor = 'bg-yellow-50 border-yellow-200';
                textColor = 'text-yellow-800';
                iconSvg = '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />';
                break;
            default:
                bgColor = 'bg-blue-50 border-blue-200';
                textColor = 'text-blue-800';
                iconSvg = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />';
        }

        messageEl.className = `border rounded-md p-4 ${bgColor} shadow-lg max-w-md`;
        messageEl.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${textColor}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        ${iconSvg}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium ${textColor}">
                        ${message}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" onclick="this.closest('div.border').remove()" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 ${textColor} hover:bg-opacity-20 hover:bg-gray-800 transition-colors">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        messagesContainer.appendChild(messageEl);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => messageEl.remove(), 500);
            }
        }, 5000);
    }

    function showStatusMessage(message, type = 'info') {
        showMessage(message, type);
    }

    // Toggle seller accordion
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
</script>
@endsection
