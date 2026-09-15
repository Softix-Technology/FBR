@extends('layouts.app')
@section('content')
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header Section -->
                    <!-- <div class="">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-600 text-white p-3 rounded-lg mr-4">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z"/>
                                    <path d="M8 6h4v1H8V6zm0 2h4v1H8V8zm0 2h2v1H8v-1z"/>
                                </svg>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900">Digital Invoicing</h1>
                        </div>
                        @if(!auth()->user()->fbr_access_token)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>calculateRateForField
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            FBR Access Token Required
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>You need to set your FBR Access Token to use the invoicing system.</p>
                                        </div>
                                        <div class="mt-4">
                                            <div class="-mx-2 -my-1.5 flex">
                                                <a href="{{ route('profile.edit') }}" class="bg-yellow-50 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                                    Set Token in Profile
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div> -->


                    <!-- Invoice Form -->
                    <form id="invoiceForm" method="POST" action="{{ route('invoicing.submit') }}" class="space-y-8">
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
                                        <label for="sellerNTNCNIC" class="block text-sm font-medium text-gray-700 mb-1" required>CNIC/NTN</label>
                                        <input type="text" id="sellerNTNCNIC" name="sellerNTNCNIC" placeholder="0000000000000" value="{{ $user->cinc_ntn ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="sellerBusinessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                        <input type="text" id="sellerBusinessName" name="sellerBusinessName" placeholder="Your Business Name" value="{{ $user->business_name ?? $user->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="sellerProvince" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                        
                                        <input name='sellerProvince' value="PUNJAB" readonly>
                                    </div>
                                    <div class="md:col-span-2 mb-4">
                                        <label for="sellerAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <textarea id="sellerAddress" name="sellerAddress" placeholder="Seller Address" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $user->address ?? '' }}</textarea>
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
                                    <input type="date" id="invoiceDate" name="invoiceDate" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="invoiceRefNo" class="block text-sm font-medium text-gray-700 mb-1">Invoice Reference No.</label>
                                    <input type="text" id="invoiceRefNo" name="invoiceRefNo" placeholder="Enter reference number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                @if($user->use_sandbox)
                                <div>
                                    <label for="scenarioId" class="block text-sm font-medium text-gray-700 mb-1">Scenario ID</label>
                                    <input type="text" id="scenarioId" name="scenarioId" value="SN000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                    <input type="text" id="buyerNTNCNIC" name="buyerNTNCNIC" placeholder="0000000000000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" autocomplete="off">

                                    <!-- Autocomplete suggestions dropdown -->
                                    <div id="buyerNTNAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden" style="max-height: 250px; overflow-y: auto;">
                                        <div class="p-2 text-sm text-gray-500 text-center">
                                            Start typing to search buyers...
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="buyerBusinessName" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                    <input type="text" id="buyerBusinessName" name="buyerBusinessName" placeholder="Buyer Business Name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="buyerProvince" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                    <select id="buyerProvince" name="buyerProvince" required class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 province-select">
                                        <option value="">Select Province</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="buyerRegistrationType" class="block text-sm font-medium text-gray-700 mb-1">
                                        Registration Type
                                    </label>
                                    <select id="buyerRegistrationType" name="buyerRegistrationType" required class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Registration Type</option>
                                        <option value="Unregistered">Unregistered</option>
                                        <option value="Registered">Registered</option>
                                    </select>
                                </div>
                                <div class="md:col-span-4">
                                    <label for="buyerAddress" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea id="buyerAddress" name="buyerAddress" placeholder="Buyer Address" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 !h-[50px]"></textarea>
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
                                    <div class="flex items-center space-x-2">
                                         @if((string)($user->c_id ?? '') === '11')
                                             <button type="button" id="uploadExcelBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition shadow-sm hover:opacity-90" style="background-color: #059669 !important; color: #ffffff !important;">
                                                 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke: #ffffff !important;">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                 </svg>
                                                 <span style="color: #ffffff !important; font-weight: 600;">Upload Excel</span>
                                             </button>
                                             <input type="file" id="excelFileInput" accept=".xlsx, .xls" class="hidden">
                                         @endif
                                        <button type="button" id="addItemBtn" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition opacity-50 cursor-not-allowed" title="Please select buyer province and registration type first">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Add Item
                                        </button>
                                    </div>
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
                            <div id="hiddenItemsContainer" style="display: none;">
                                <!-- Form inputs will be generated here for submission -->
                            </div>
                        </div>
                        <div>
                            <table>
                                <tr>
                                    <td>
                                        Transportation Charges
                                    </td>
                                    <td>
                                        <input name="furtherexpense" value=0 id='furthertaxexpense'>
                                    </td>
                                   
                                    
                                </tr>
                            </table>
                        </div>

                        <!-- Submit Section -->
                        <div class="flex justify-end space-x-4">
                            <button type="button" id="clearBtn" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-600 disabled:opacity-25 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                Clear
                            </button>
                            <button type="button" id="validateBtn" class="!hidden inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 active:bg-yellow-600 disabled:opacity-25 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Validate Invoice
                            </button>
                            <button type="submit" id="submitBtn" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd" />
                                </svg>
                                Generate Invoice
                            </button>
                            <button  id="Draft" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd" />
                                </svg>
                                Draft Invoice
</button>
                          </div>
                    </form>

                    <!-- Uploaded Excel Invoices Section (Restricted to CID 11 ONLY) -->
                    @if((string)($user->c_id ?? '') === '11')
                    <div id="uploadedExcelContainer" class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm p-6 hidden">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900">Uploaded Excel Invoices</h2>
                                <span id="uploadedItemCountBadge" class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">0 rows</span>
                            </div>
                            <div>
                                <button type="button" onclick="clearUploadedExcelData()" class="text-xs text-red-600 hover:text-red-800 font-medium underline">Clear Uploaded Data</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table id="uploadedExcelTable" class="min-w-full divide-y divide-gray-200 text-xs border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">CNIC</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Buyer Name</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Inv Date</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Product Name</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">HS Code</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Retail Val</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Disc %</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Discount</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Ex Value</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Sales Tax</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Inclusive</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Status / Error</th>
                                        <th class="px-3 py-2 text-center font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="uploadedExcelTableBody" class="bg-white divide-y divide-gray-200">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Edit Uploaded Item Modal -->
                    <div id="editExcelModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEditExcelModal()"></div>
                            <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-900">Edit Uploaded Invoice Item</h3>
                                    <button type="button" onclick="closeEditExcelModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <input type="hidden" id="editExcelRowIndex">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">CNIC</label>
                                            <input type="text" id="editExcelCnic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Buyer Name</label>
                                            <input type="text" id="editExcelName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Invoice Date</label>
                                            <input type="text" id="editExcelDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Product Name</label>
                                            <input type="text" id="editExcelProductName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">HS Code</label>
                                            <input type="text" id="editExcelHsCode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Per Unit Sale Price</label>
                                            <input type="number" step="0.01" id="editExcelPrice" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Quantity</label>
                                            <input type="number" id="editExcelQty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Retail Value</label>
                                            <input type="number" step="0.01" id="editExcelRetailValue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Discount %</label>
                                            <input type="number" step="0.01" id="editExcelDiscPct" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Discount Amount</label>
                                            <input type="number" step="0.01" id="editExcelDiscount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Ex Value</label>
                                            <input type="number" step="0.01" id="editExcelExValue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Sales Tax</label>
                                            <input type="number" step="0.01" id="editExcelSalesTax" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-700">Inclusive Value</label>
                                            <input type="number" step="0.01" id="editExcelInclusiveValue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
                                    <button type="button" onclick="closeEditExcelModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                                    <button type="button" onclick="saveEditedExcelRow()" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">HS Code <span class="text-red-500">*</span></label>
                                <select id="modalHsCode" name="hsCode" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm hs-code-select">
                                    <option value="">Select HS Code</option>
                                </select>
                            </div>
                            <div>
                                 <div class="relative">
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Product Description <span class="text-red-500">*</span></label>
                                     <input type="text" id="modalProductDescription" name="productDescription" placeholder="Enter product description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" autocomplete="off">
                                     <!-- Product Autocomplete suggestions dropdown -->
                                     <div id="productDescriptionAutocomplete" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden" style="max-height: 200px; overflow-y: auto;"></div>
                                 </div>
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
                                <input type="number" id="modalSalesTaxApplicable" name="salesTaxApplicable" placeholder="Auto-calculated" min="0" step="any" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sales-tax-field">
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

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

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
            existingProducts: @json($existingProducts ?? []),
            user: {
                c_id: @json($user->c_id ?? ''),
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
           

            // Common search terms that users typically look for
            const commonSearchTerms = ['', '8', '84', '85', '39', '73', '62', '61', '90', '87'];

            commonSearchTerms.forEach((term, index) => {
                setTimeout(() => {
                    searchHsCodesWithCache({
                        data: { term: term, page: 1 }
                    }, () => {
                        // Success callback - cache is now warmed
                    }, () => {
                        // Failure callback - silently ignore for cache warming
                    });
                }, index * 100); // Stagger requests to avoid overwhelming server
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
              

                // Set today's date
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('invoiceDate').value = today;

                            // Populate data first, then initialize Select2
            populateProvinceSelects();
            populateTransactionTypeSelects();

            // Initialize Select2 after data is populated
            initializeSelect2();

            // Ensure seller province is properly selected in Select2
            if (userProfile.province) {
                $('#sellerProvince').val(userProfile.province).trigger('change');
              
            }

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
                initProductDescriptionAutocomplete();

                // Perform initial validation to set correct button state
                setTimeout(() => {
                    handleRegistrationTypeChange(); // Handle any pre-selected registration type
                    validateBuyerRequirements();
                }, 100);

                // Add a secondary validation after Select2 is fully initialized
                setTimeout(() => {
                   
                    handleRegistrationTypeChange(); // Ensure registration type is handled
                    validateBuyerRequirements();
                }, 500);

                // Pre-warm HS codes cache in background (after everything else is loaded)
                setTimeout(() => {
                    warmHsCodesCache();
                }, 2000); // Wait 2 seconds after page load
            });
        });

                        // Handle registration type changes
        function handleRegistrationTypeChange() {
            const registrationType = $('#buyerRegistrationType').val();
            const ntnCnicField = document.getElementById('buyerNTNCNIC');
            const businessNameField = document.getElementById('buyerBusinessName');
            const addressField = document.getElementById('buyerAddress');

          

            if (registrationType === 'Unregistered') {
               // Enable fields for registered suppliers
                ntnCnicField.readOnly = false;
                businessNameField.readOnly = false;
                addressField.readOnly = false;

                // Remove readonly styling
                ntnCnicField.classList.remove('bg-gray-100', 'text-gray-500');
                businessNameField.classList.remove('bg-gray-100', 'text-gray-500');
                addressField.classList.remove('bg-gray-100', 'text-gray-500');

             
            } else if (registrationType === 'Registered') {
                // Enable fields for registered suppliers
                ntnCnicField.readOnly = false;
                businessNameField.readOnly = false;
                addressField.readOnly = false;

                // Remove readonly styling
                ntnCnicField.classList.remove('bg-gray-100', 'text-gray-500');
                businessNameField.classList.remove('bg-gray-100', 'text-gray-500');
                addressField.classList.remove('bg-gray-100', 'text-gray-500');

                // Clear the fields to allow user input
                if (businessNameField.value === 'Unregistered Supplies') {
                    businessNameField.value = '';
                }

               
            } else {
                // Default state - enable all fields
                ntnCnicField.readOnly = false;
                businessNameField.readOnly = false;
                addressField.readOnly = false;

                // Remove readonly styling
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
                // Enable the button
                addItemBtn.disabled = false;
                addItemBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                addItemBtn.classList.add('hover:bg-blue-500');
                requirementMsg.classList.add('hidden');
                addItemBtn.title = '';
               
            } else {
                // Disable the button
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
            document.getElementById('clearBtn').addEventListener('click', clearForm);
            document.getElementById('invoiceForm').addEventListener('submit', submitInvoice);
            document.getElementById('validateBtn').addEventListener('click', validateInvoice);
            document.getElementById('Draft').addEventListener('click', submitDraft);

            // Add blur event listener for buyer NTN/CNIC field
            // document.getElementById('buyerNTNCNIC').addEventListener('blur', fetchRegistrationType);

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

            // FIX: select2:select does not fire when SRO Schedule value is set
            // programmatically (e.g. auto-select after rate change). This ensures
            // SRO Items still populate in that case, without touching the existing flow.
            $(document).on('change', '.sro-schedule-select, #modalSroScheduleNo', function(e) {
                if ($(this).val()) {
                    fetchSroItems(e.target);
                    updateSroRequiredIndicators(e.target);
                }
            });

            // Delegate event listener for SRO Schedule clear/unselect
            $(document).on('select2:clear', '.sro-schedule-select', function(e) {
                updateSroRequiredIndicators(e.target);
            });

            // Delegate event listener for SRO Item selection changes
            $(document).on('select2:select', '.sro-item-select', function(e) {
                updateSroRequiredIndicators(e.target);
            });

            // Delegate event listener for SRO Item clear/unselect
            $(document).on('select2:clear', '.sro-item-select', function(e) {
                updateSroRequiredIndicators(e.target);
            });

            // Delegate event listener for HS Code selection changes
            $(document).on('select2:select', '.hs-code-select', function(e) {
                fetchUomByHsCode(e.target);
            });

           $(document).on('input change', 'input[name*="[valueSalesExcludingST]"], #modalValueSalesExcludingST', function(e) {
    calculateSalesTaxForItem(e.target);
});

            // Add event listener for rate*quantity and fixedNotifiedValueOrRetailPrice
            let _updatingFixedNotified = false;
            $(document).on('input', 'input[name*="[rateValues]"],input[name*="[quantity]"], #modalRateValues,#modalQuantity,#modalFixedNotifiedValueOrRetailPrice,#modalDiscount,#modalValueSalesExcludingST', function(e) {

// Check if we're in the modal context
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
    } catch (e) {
        rateData = { rate_value: 0 };
    }

    let rate = 0;
    if (rateTax.value) {
        try {
            const rd = JSON.parse(rateTax.value);
            rate = parseFloat(rd.rate_value) || 0;
        } catch (e) {
            rate = 0;
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
        // Guard: skip if we're auto-updating FixedNotifiedValue
        if (_updatingFixedNotified) return;
        _updatingFixedNotified = true;
        try {
            const basePrice = parseFloat(document.getElementById('modalFixedNotifiedValueOrRetailPrice').value) || 0;
            
            // Get percentage values for 3rd Schedule
            const ghPercent = parseFloat(document.getElementById('modalGhPercent').value) || 0;
            const discountPercent = parseFloat(document.getElementById('modalDiscountPercent').value) || 0;

            // Discount Amount = FNV * Discount% / 100
            const discountAmount = basePrice * discountPercent / 100;
            $('#modalDiscountAmount').val(discountAmount.toFixed(2));
            discountField.value = discountAmount.toFixed(2); // Update hidden discount field

            // *** FIX 1: Sales Tax = basePrice × rate% (quantity NOT applied) ***
            const salesTax3rd = basePrice * rate / 100;
            salesTaxField.value = salesTax3rd.toFixed(2);

            // *** FIX 2: Value Sales Excluding ST = FNV - Discount Amount (quantity NOT applied) ***
            const exclVal = basePrice - discountAmount;
            $('#modalValueSalesExcludingST').val(exclVal.toFixed(2));

            // G/H Amount = (Value Sales Excluding ST + Sales Tax Applicable) * GH% / 100
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
        // ===== Standard Calculation =====
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
        
        // Further Tax for unregistered buyers
        if ($('#buyerRegistrationType').val() === 'Unregistered') {
            valueFurtherField.value = (totalDM * 4 / 100).toFixed(2);
        }
    }
}

            });

            // Add event listener for rate changes (when rate selection changes)
            $(document).on('select2:select', '.rate-select', function(e) {
                
                calculateSalesTaxForItem(e.target);

                // Trigger input event on rate values/unit price field to recalculate totals
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

                // Also update SRO schedule when rate changes
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

            // Also handle clear event for sale type
            $(document).on('select2:clear', '#modalSaleType', function(e) {
                toggleScheduleFields(e.target);
            });

            $(document).on('select2:select', '#modalHsCode', function(e) {
                fetchUomByHsCode(e.target);
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

        // G/H % change handler
        $(document).on('change', '#modalGhPercent', function() {
            recalculate3rdSchedule();
        });
        
        // Discount % change handler
        $(document).on('input', '#modalDiscountPercent', function() {
            recalculate3rdSchedule();
        });
        
        // Also recalculate when FixedNotifiedValue changes for 3rd Schedule
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

            // Click outside to close autocomplete
            document.addEventListener('click', function(e) {
                const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
                const ntnInput = document.getElementById('buyerNTNCNIC');

                if (!ntnInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
                    hideBuyerAutocomplete();
                }
            });
        }

// Check if 3rd Schedule is active from either Sale Type or SRO Schedule No.
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
            
            // Show/hide 3rd Schedule fields
            document.querySelectorAll('.schedule-3rd-field').forEach(el => {
                if (is3rdSchedule) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
            
            // Show/hide standard fields
            document.querySelectorAll('.schedule-standard-field').forEach(el => {
                if (is3rdSchedule) {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });
            
            // Toggle required attributes for FixedNotifiedValue
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
            
            // Toggle required for Discount (inside schedule-standard-field)
            document.querySelectorAll('.schedule-standard-field input[required], .schedule-standard-field select[required]').forEach(el => {
                if (is3rdSchedule) {
                    el.required = false;
                    el.removeAttribute('aria-required');
                } else {
                    el.required = true;
                    el.setAttribute('aria-required', 'true');
                }
            });
            
            // Toggle required for visible fields (Rate Values, Total Values, Value Sales Excluding ST)
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
            
            // Recalculate when switching to 3rd Schedule
            if (is3rdSchedule) {
                recalculate3rdSchedule();
            }
        }
        
        // *** FIX 3: Recalculate 3rd Schedule fields WITHOUT multiplying by quantity ***
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
                    rate = parseFloat(JSON.parse(rateSelect.value).rate_value) || 0;
                } catch (e) {}
            }

            // Discount Amount = FNV * Discount% / 100 (per-unit)
            const discountAmount = fnv * discountPercent / 100;
            discountAmountField.value = discountAmount.toFixed(2);
            discountField.value = discountAmount.toFixed(2);

            // Value Sales Excluding ST = FNV - Discount (quantity NOT applied)
            const valueSales = fnv - discountAmount;
            valueSalesField.value = valueSales.toFixed(2);

            // Sales Tax = FNV × rate% (quantity NOT applied)
            const salesTax = fnv * rate / 100;
            salesTaxField.value = salesTax.toFixed(2);

            // G/H Amount = (Value Sales Excluding ST + Sales Tax) * GH% / 100
            const ghAmount = (valueSales + salesTax) * ghPercent / 100;
            ghAmountField.value = ghAmount.toFixed(2);
        }

        // Global variables for buyer autocomplete
        let buyerAutocompleteTimeout;
        let buyerAutocompleteCache = new Map();
        const BUYER_CACHE_DURATION = 300000; // 5 minutes

        // Handle buyer NTN/CNIC input for autocomplete
        function handleBuyerNTNInput(e) {
            const searchValue = e.target.value.trim();

            // Clear previous timeout
            if (buyerAutocompleteTimeout) {
                clearTimeout(buyerAutocompleteTimeout);
            }

            // Debounce the search
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

        // Handle buyer NTN/CNIC focus to show recent buyers
        function handleBuyerNTNFocus(e) {
            const searchValue = e.target.value.trim();

            if (searchValue.length >= 2) {
                searchBuyers(searchValue);
            } else {
                showAllRecentBuyers();
            }
        }

        // Handle buyer NTN/CNIC blur with delay to allow clicks
        function handleBuyerNTNBlur(e) {
            setTimeout(() => {
                hideBuyerAutocomplete();
            }, 200);
        }

        // Search buyers via API
        async function searchBuyers(searchTerm) {
            try {
                // Check cache first
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
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success && result.data) {
                    // Cache the result
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
                showErrorMessage('Failed to search buyers');
            }
        }

        // Show all recent buyers
        async function showAllRecentBuyers() {
            try {
                // Check cache first
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
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success && result.data) {
                    // Cache the result
                    buyerAutocompleteCache.set(cacheKey, {
                        data: result.data,
                        timestamp: Date.now()
                    });

                    displayBuyerSuggestions(result.data, 'Recent buyers:');
                } else {
                    showNoBuyersMessage();
                }

            } catch (error) {
                console.error('Error fetching recent buyers:', error);
                showErrorMessage('Failed to load recent buyers');
            }
        }

        // Display buyer suggestions in dropdown
        function displayBuyerSuggestions(buyers, headerText = null) {
            const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');

            if (!buyers || buyers.length === 0) {
                showNoBuyersMessage();
                return;
            }

            let html = '';

            if (headerText) {
                html += `<div class="px-3 py-1 text-xs font-medium text-gray-500 bg-gray-50 border-b">${headerText}</div>`;
            }

            buyers.forEach(buyer => {
                html += `
                    <div class="buyer-suggestion px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                         data-buyer-id="${buyer.id}"
                         data-ntn-cnic="${buyer.ntn_cnic}"
                         data-business-name="${buyer.business_name}"
                         data-address="${buyer.address}"
                         data-registration-type="${buyer.registration_type}"
                         data-province="${buyer.province}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium text-sm text-gray-900">${buyer.ntn_cnic}</div>
                                <div class="text-sm text-gray-600">${buyer.business_name}</div>
                                <div class="text-xs text-gray-500">${buyer.registration_type}</div>
                            </div>
                            <div class="text-xs text-gray-500 ml-2">
                                ${getProvinceDescription(buyer.province)}
                            </div>
                        </div>
                    </div>
                `;
            });

            autocompleteDiv.innerHTML = html;
            autocompleteDiv.classList.remove('hidden');

            // Add click listeners to suggestions
            autocompleteDiv.querySelectorAll('.buyer-suggestion').forEach(suggestion => {
                suggestion.addEventListener('click', function() {
                    selectBuyer(this);
                });
            });
        }

        // Get province description from code
        function getProvinceDescription(provinceCode) {
            if (!provinces || !provinceCode) return provinceCode;

            const province = provinces.find(p => p.stateProvinceCode === provinceCode);
            return province ? province.stateProvinceDesc : provinceCode;
        }

        // Select a buyer from suggestions
        function selectBuyer(suggestionElement) {
            const buyerId = suggestionElement.dataset.buyerId;
            const ntnCnic = suggestionElement.dataset.ntnCnic;
            const businessName = suggestionElement.dataset.businessName;
            const address = suggestionElement.dataset.address;
            const registrationType = suggestionElement.dataset.registrationType;
            const province = suggestionElement.dataset.province;

            // Fill in the buyer fields
            document.getElementById('buyerNTNCNIC').value = ntnCnic;
            document.getElementById('buyerBusinessName').value = businessName;
            document.getElementById('buyerAddress').value = address;

            // Set registration type
            const registrationTypeSelect = document.getElementById('buyerRegistrationType');
            registrationTypeSelect.value = registrationType;
            $(registrationTypeSelect).trigger('change');

            // Set province using Select2
            const provinceSelect = document.getElementById('buyerProvince');
            $(provinceSelect).val(province).trigger('change');

            // Hide autocomplete
            hideBuyerAutocomplete();

            // Handle registration type logic
            handleRegistrationTypeChange();

            // Validate buyer requirements
            validateBuyerRequirements();

           
        }

        // Show no buyers found message
        function showNoBuyersMessage() {
            const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
            autocompleteDiv.innerHTML = `
                <div class="px-3 py-4 text-sm text-gray-500 text-center">
                    <svg class="mx-auto h-6 w-6 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    No buyers found
                </div>
            `;
            autocompleteDiv.classList.remove('hidden');
        }

        // Show error message
        function showErrorMessage(message) {
            const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
            autocompleteDiv.innerHTML = `
                <div class="px-3 py-4 text-sm text-red-500 text-center">
                    <svg class="mx-auto h-6 w-6 text-red-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    ${message}
                </div>
            `;
            autocompleteDiv.classList.remove('hidden');
        }

        // Hide buyer autocomplete
        function hideBuyerAutocomplete() {
            const autocompleteDiv = document.getElementById('buyerNTNAutocomplete');
            autocompleteDiv.classList.add('hidden');
        }

        // ==========================================
        // Product Description – Smart Autocomplete
        // ==========================================
        let existingProductsList = [];

        function getProductStorageKey() {
            const cid = window.appData?.user?.c_id || window.appData?.user?.cinc_ntn || 'default';
            return 'saved_product_descriptions_' + cid;
        }

        function initProductDescriptionAutocomplete() {
            const serverProducts = window.appData.existingProducts || [];
            let localProducts = [];
            try {
                localProducts = JSON.parse(localStorage.getItem(getProductStorageKey()) || '[]');
            } catch (e) {
                localProducts = [];
            }

            const map = new Map();
            [...serverProducts, ...localProducts].forEach(prod => {
                if (prod && typeof prod === 'string' && prod.trim()) {
                    const clean = prod.trim();
                    const key = clean.toLowerCase();
                    if (!map.has(key)) {
                        map.set(key, clean);
                    }
                }
            });
            existingProductsList = Array.from(map.values());

            const input = document.getElementById('modalProductDescription');
            if (!input) return;

            input.removeEventListener('input', handleProductDescriptionInput);
            input.removeEventListener('focus', handleProductDescriptionInput);
            input.addEventListener('input', handleProductDescriptionInput);
            input.addEventListener('focus', handleProductDescriptionInput);

            document.addEventListener('click', function(e) {
                const autocompleteDiv = document.getElementById('productDescriptionAutocomplete');
                const prodInput = document.getElementById('modalProductDescription');
                if (autocompleteDiv && prodInput && !prodInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
                    hideProductDescriptionAutocomplete();
                }
            });
        }

        function handleProductDescriptionInput() {
            const input = document.getElementById('modalProductDescription');
            const autocompleteDiv = document.getElementById('productDescriptionAutocomplete');
            if (!input || !autocompleteDiv) return;

            const query = input.value.trim().toLowerCase();
            if (!query) {
                hideProductDescriptionAutocomplete();
                return;
            }

            const matches = existingProductsList.filter(prod => prod.toLowerCase().includes(query));

            if (matches.length === 0) {
                hideProductDescriptionAutocomplete();
                return;
            }

            let html = '';
            matches.slice(0, 30).forEach(product => {
                const escaped = escapeHtmlStr(product);
                html += `
                    <div class="product-suggestion px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 text-sm text-gray-800" data-product="${escaped}">
                        <div class="font-medium">${escaped}</div>
                    </div>
                `;
            });

            autocompleteDiv.innerHTML = html;
            autocompleteDiv.classList.remove('hidden');

            autocompleteDiv.querySelectorAll('.product-suggestion').forEach(item => {
                item.addEventListener('click', function() {
                    const selectedProduct = this.getAttribute('data-product');
                    input.value = selectedProduct;
                    hideProductDescriptionAutocomplete();
                });
            });
        }

        function hideProductDescriptionAutocomplete() {
            const autocompleteDiv = document.getElementById('productDescriptionAutocomplete');
            if (autocompleteDiv) {
                autocompleteDiv.classList.add('hidden');
            }
        }

        function escapeHtmlStr(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function saveNewProductDescription(productDesc) {
            if (!productDesc || !productDesc.trim()) return;
            const clean = productDesc.trim();
            const key = clean.toLowerCase();

            if (!existingProductsList.some(p => p.toLowerCase() === key)) {
                existingProductsList.push(clean);
                try {
                    const storageKey = getProductStorageKey();
                    let localProducts = JSON.parse(localStorage.getItem(storageKey) || '[]');
                    if (!localProducts.some(p => p.toLowerCase() === key)) {
                        localProducts.push(clean);
                        localStorage.setItem(storageKey, JSON.stringify(localProducts));
                    }
                } catch (e) {
                    console.error('Error saving product to localStorage', e);
                }
            }
        }

        // Fetch registration type when NTN/CNIC field loses focus
        // COMMENTED OUT: Registration type is now a manual select field
        /*
        async function fetchRegistrationType() {
            const ntnCnicField = document.getElementById('buyerNTNCNIC');
            const registrationTypeField = document.getElementById('buyerRegistrationType');
            const loader = document.getElementById('registrationTypeLoader');

            const registrationNo = ntnCnicField.value.trim();

            // Reset if field is empty
            if (!registrationNo) {
                registrationTypeField.value = '';
                return;
            }

            // Basic validation for NTN/CNIC format
            if (registrationNo.length < 7) {
                registrationTypeField.value = '';
                showMessage('Please enter a valid NTN/CNIC number', 'error');
                return;
            }

            try {
                // Show loading state
                loader.classList.remove('hidden');
                registrationTypeField.value = 'Checking...';
                registrationTypeField.classList.add('bg-blue-50');

                const response = await fetch(`${API_BASE}/api/fbr/registration-type`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        registration_no: registrationNo
                    })
                });

                const result = await response.json();

                if (result.success && result.data) {
                    // Set the registration type
                    const registrationType = result.data.registration_type;
                    registrationTypeField.value = registrationType.charAt(0).toUpperCase() + registrationType.slice(1).toLowerCase();
                    registrationTypeField.classList.remove('bg-blue-50');
                    registrationTypeField.classList.add('bg-green-50');

                    showMessage(`Registration type updated: ${registrationTypeField.value}`, 'success');
                } else {
                    // Handle API error
                    registrationTypeField.value = 'Unregistered';
                    registrationTypeField.classList.remove('bg-blue-50');
                    registrationTypeField.classList.add('bg-yellow-50');

                    showMessage(result.message || 'Could not determine registration type. Defaulted to Unregistered.', 'warning');
                }

            } catch (error) {
                console.error('Error fetching registration type:', error);
                registrationTypeField.value = 'Unregistered';
                registrationTypeField.classList.remove('bg-blue-50');
                registrationTypeField.classList.add('bg-red-50');

                showMessage('Failed to check registration type. Defaulted to Unregistered.', 'error');
            } finally {
                // Hide loading state
                loader.classList.add('hidden');
            }
        }
        */

        // Calculate rates for all items when global fields change
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

            // Check if we're in the modal context
            if (saleTypeField.id === 'modalSaleType') {
                rateSelect = document.getElementById('modalRate');
            } else {
                // Regular form context
                const itemContainer = saleTypeField.closest('div.bg-white');
                rateSelect = itemContainer ? itemContainer.querySelector('.rate-select') : null;
            }

            if (rateSelect) {
                await calculateRateForField(rateSelect);
            }
        }

        // Calculate rate for a specific rate field
        async function calculateRateForField(rateSelect) {
            let saleTypeField, loader;

            // Check if we're in the modal context
            if (rateSelect.id === 'modalRate') {
                saleTypeField = document.getElementById('modalSaleType');
                loader = document.querySelector('#addItemModal .rate-loader');
            } else {
                // Regular form context
                const itemContainer = rateSelect.closest('div.bg-white');
                saleTypeField = itemContainer ? itemContainer.querySelector('select[name*="[saleType]"]') : null;
                loader = itemContainer ? itemContainer.querySelector('.rate-loader') : null;
            }

            if (!saleTypeField) {
                console.warn('Could not find sale type field for rate calculation');
                return;
            }

            const invoiceDate = document.getElementById('invoiceDate').value;
            // Use Select2 val() to get the actual selected value (more reliable than .value)
            const buyerProvince = $('#buyerProvince').val() || document.getElementById('buyerProvince').value;
            // Read from Select2 first, fall back to native .value
            const saleType = ($(saleTypeField).hasClass('select2-hidden-accessible') ? $(saleTypeField).val() : null) || saleTypeField.value || '';

            console.log('[Rate Fetch] invoiceDate:', invoiceDate, '| buyerProvince:', buyerProvince, '| saleType:', saleType);


            // Reset rate select if any required field is missing
            if (!invoiceDate || !buyerProvince || !saleType) {
                rateSelect.innerHTML = '<option value="">Select Rate</option>';
                $(rateSelect).val('').trigger('change');
                rateSelect.classList.remove('bg-green-50', 'bg-red-50');
                rateSelect.classList.add('bg-gray-50');

                // Trigger sales tax calculation when rate is cleared
                calculateSalesTaxForItem(rateSelect);
                return;
            }

            // Sale type now contains only the transaction type ID
            const transTypeId = saleType;

            // Buyer province now contains the province code directly
            const provinceCode = buyerProvince;
            if (!provinceCode) {
                rateSelect.innerHTML = '<option value="">Select Rate</option>';
                $(rateSelect).val('').trigger('change');
                rateSelect.classList.remove('bg-green-50');
                rateSelect.classList.add('bg-red-50');
                showMessage('Invalid province selected', 'error');
                return;
            }

            try {
                // Show loading state
                if (loader) {
                    loader.classList.remove('hidden');
                }
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
                console.log('[Rate Fetch] Sent payload:', { date: invoiceDate, trans_type_id: parseInt(transTypeId), origination_supplier: parseInt(provinceCode) });

                const text = await response.text();
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));


const result = JSON.parse(cleanText);

                if (result.success && result.data && result.data.length > 0) {
                    // Destroy existing Select2 before modifying options
                    if ($(rateSelect).data('select2')) {
                        $(rateSelect).select2('destroy');
                    }
                    $(rateSelect).removeClass('select2-hidden-accessible');
                    $(rateSelect).removeAttr('data-select2-id tabindex aria-hidden');

                    // Clear the select and add default option
                    rateSelect.innerHTML = '<option value="">Select Rate</option>';

                    let targetOptionValue = '';
                    // Populate with all available rates
                    result.data.forEach((rateData, index) => {
                        const rateValue = rateData.ratE_VALUE || 0;
                        const rateId = rateData.ratE_ID;
                        const rateDesc = rateData.ratE_DESC || '';

                        const option = document.createElement('option');
                        const optionValueObj = {
                            rate_id: rateId,
                            rate_value: rateValue,
                            rate_desc: rateDesc
                        };
                        option.value = JSON.stringify(optionValueObj);

                        // Use rate description from FBR directly, fallback to percentage format
                        let displayText = rateDesc && rateDesc.trim() !== '' ? rateDesc : `${rateValue}%`;

                        option.textContent = displayText;
                        option.title = rateDesc || `Rate: ${rateValue}%`;

                        rateSelect.appendChild(option);

                        // If editing an item, restore the previously selected rate
                        if (rateSelect.id === 'modalRate' && editingItemIndex >= 0) {
                            const savedItem = itemsData[editingItemIndex];
                            if (savedItem && savedItem.rate) {
                                try {
                                    const savedRateObj = JSON.parse(savedItem.rate);
                                    if (savedRateObj.rate_id == rateId || savedRateObj.rate_value == rateValue) {
                                        option.selected = true;
                                        targetOptionValue = option.value;
                                    }
                                } catch (e) {
                                    console.warn('Could not parse saved item rate:', e);
                                }
                            }
                        }

                        // Select the first rate by default if not set by edit mode
                        if (!targetOptionValue && index === 0) {
                            option.selected = true;
                            targetOptionValue = option.value;
                        }
                    });

                    // Re-initialize Select2 with new options
                    const dropdownParent = rateSelect.closest('#addItemModal') ? $('#addItemModal') : $('body');
                    $(rateSelect).select2({
                        placeholder: 'Select Rate',
                        allowClear: true,
                        width: 'resolve',
                        dropdownParent: dropdownParent
                    });

                    const finalVal = targetOptionValue || (rateSelect.options[1]?.value || '');

                    // Update Select2 display and trigger rate change handlers
                    $(rateSelect).val(finalVal).trigger('change');
                    // Explicitly trigger select2:select for the rate handler to fetch SRO
                    if (finalVal) {
                        $(rateSelect).trigger('select2:select');
                    }

                    rateSelect.classList.remove('bg-blue-50');
                    rateSelect.classList.add('bg-green-50');
                    rateSelect.title = `${result.data.length} rate(s) available`;

                   

                    // Trigger sales tax calculation with the selected rate
                    calculateSalesTaxForItem(rateSelect);
                } else {
                    // No rate found or API returned failure
                    const errMsg = result.message || 'No rates found';
                    rateSelect.innerHTML = `<option value="">⚠ ${errMsg} — change sale type or retry</option>`;
                    $(rateSelect).val('').trigger('change');
                    rateSelect.classList.remove('bg-blue-50');
                    rateSelect.classList.add('bg-yellow-50');
                    rateSelect.title = 'No rates found — try changing sale type or click Retry below';

                    console.warn('No rates found for the given criteria. API response:', result);
                    showMessage('No rates found. Please try a different sale type or check FBR connectivity.', 'warning');

                    // Trigger sales tax calculation when no rates available
                    calculateSalesTaxForItem(rateSelect);
                }

            } catch (error) {
                console.error('Error calculating rates:', error);
                const isTimeout = error.message && (error.message.includes('timeout') || error.message.includes('network'));
                const errLabel = isTimeout ? 'FBR timeout - Retry' : 'Error loading rates';
                rateSelect.innerHTML = `<option value="">${errLabel}</option>`;
                $(rateSelect).val('').trigger('change');
                rateSelect.classList.remove('bg-blue-50');
                rateSelect.classList.add('bg-red-50');
                rateSelect.title = 'Error loading rates — ' + error.message;

                showMessage('Failed to load rates from FBR server. Please check your internet/FBR access token and try again.', 'error');

                // Trigger sales tax calculation when error occurs
                calculateSalesTaxForItem(rateSelect);
            } finally {
                // Hide loading state
                if (loader) {
                    loader.classList.add('hidden');
                }
            }
        }





                                // Populate province selects with HTML options
        function populateProvinceSelects() {
            if (provinces && Array.isArray(provinces)) {
                // Update all province selects
                $('.province-select').each(function() {
                    const select = this;
                    select.innerHTML = '<option value="">Select Province</option>';

                    provinces.forEach(province => {
                        const provinceCode = province.stateProvinceCode;
                        const provinceDesc = province.stateProvinceDesc;

                        if (provinceCode && provinceDesc) {
                            const option = document.createElement('option');
                            option.value = provinceCode;
                            option.textContent = provinceDesc;

                            // Pre-select user's province for seller province field
                            if (select.id === 'sellerProvince' && userProfile.province &&
                                (provinceCode == userProfile.province ||
                                 String(provinceCode) === String(userProfile.province))) {
                                option.selected = true;
                               
                            }

                            select.appendChild(option);
                        }
                    });
                });
              
            }
        }

        // Initialize Select2 for all select elements
        function initializeSelect2() {
            // Initialize Select2 for province selects (smaller dataset, can be pre-loaded)
            $('.province-select').select2({
                placeholder: 'Select Province',
                allowClear: true,
                width: 'resolve'
            });

            // Initialize Select2 for invoice type selects (loaded from FBR API)
            $('.invoice-type-select').select2({
                placeholder: 'Select Invoice Type',
                allowClear: true,
                width: 'resolve'
            });

            // Initialize Select2 for HS Code selects with AJAX (large dataset, lazy load)
            $('.hs-code-select').select2({
                placeholder: 'Search HS Code...',
                allowClear: true,
                width: 'resolve',
                minimumInputLength: 2,
                ajax: {
                    delay: 150, // Reduced delay for faster response
                    transport: function (params, success, failure) {
                        searchHsCodesWithCache(params, success, failure);
                    }
                }
            });

            // Initialize Select2 for UoM selects (medium dataset, can be pre-loaded)
            $('.uom-select').select2({
                placeholder: 'Select Unit of Measure',
                allowClear: true,
                width: 'resolve'
            });

            // Initialize Select2 for transaction type selects (small dataset, pre-loaded)
            $('.sale-type-select').select2({
                placeholder: 'Select Sale Type',
                allowClear: true,
                width: 'resolve'
            });

            // Initialize Select2 for rate selects (populated dynamically when needed)
            $('.rate-select').select2({
                placeholder: 'Select Rate',
                allowClear: true,
                width: 'resolve',
            });

            // Initialize Select2 for SRO schedules (loaded dynamically when needed)
            $('.sro-schedule-select').select2({
                placeholder: 'Select SRO Schedule',
                allowClear: true,
                width: 'resolve',
            });

            // Initialize Select2 for SRO items (loaded dynamically when needed)
            $('.sro-item-select').select2({
                placeholder: 'Select SRO Item',
                allowClear: true,
                width: 'resolve',
            });

          
        }



        // HS codes are now loaded via AJAX on demand - no need for pre-population

        // HS codes now use AJAX search - heavy loading function removed for performance

        // Load and populate UoM from API
        async function loadAndPopulateUoM() {
            try {
               
                const response = await fetch(`${API_BASE}/premiertax/api/fbr/uom`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const text = await response.text();
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success && result.data) {
                    uoMs = result.data;
                    populateUoMSelects();

                    // Re-initialize Select2 for UoM selects after loading data
                    $('.uom-select').select2('destroy').select2({
                        placeholder: 'Select Unit of Measure',
                        allowClear: true,
                        width: 'resolve'
                    });

                  
                } else {
                    console.error('Failed to load UoM:', result.message);
                    showMessage('Failed to load Units of Measurement: ' + (result.message || 'Unknown error'), 'warning');
                }
            } catch (error) {
                console.error('Error loading UoM:', error);
                showMessage('Error loading Units of Measurement from server', 'error');
            }
        }

        // Load and populate Document Types (Invoice Types) from FBR API
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
        console.log('RAW RESPONSE:', text);

        const cleanText = text.trim().startsWith('{')
            ? text
            : text.substring(text.indexOf('{'));

        const result = JSON.parse(cleanText);

        if (result.success && result.data) {
            documentTypes = result.data;
            populateDocumentTypeSelects();

            $('.invoice-type-select').select2('destroy').select2({
                placeholder: 'Select Invoice Type',
                allowClear: true,
                width: 'resolve'
            });

        } else {
            console.error('Failed to load Document Types:', result.message);
            showMessage(
                'Failed to load Invoice Types: ' + (result.message || 'Unknown error'),
                'warning'
            );
            loadDefaultDocumentTypes();
        }

    } catch (error) {
        console.error('Error loading Document Types:', error);
        showMessage('Error loading Invoice Types from FBR server', 'error');
        loadDefaultDocumentTypes();
    }
}



                                // Populate UoM selects with HTML options
        function populateUoMSelects() {
            if (uoMs && Array.isArray(uoMs)) {
                // Update all UoM selects
                $('.uom-select').each(function() {
                    const select = this;
                    select.innerHTML = '<option value="">Select Unit of Measure</option>';

                    uoMs.forEach(item => {
                        // Handle API response format (uoM_ID, uoM_DESC) and legacy format (id, description)
                        const uomId = item.uoM_ID || item.id;
                        const description = item.uoM_DESC || item.description;

                        if (uomId && description) {
                            const option = document.createElement('option');
                            option.value = uomId;
                            option.textContent = description;
                            select.appendChild(option);
                        }
                    });
                });
              
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
               
            }
        }

        // Load default document types as fallback
        function loadDefaultDocumentTypes() {
            documentTypes = [
                { docTypeId: "Sale Invoice", docDescription: "Sale Invoice" },
                { docTypeId: "Debit Note", docDescription: "Debit Note" },
                { docTypeId: "Credit Note", docDescription: "Credit Note" },
                { docTypeId: "Purchase Invoice", docDescription: "Purchase Invoice" }
            ];

            populateDocumentTypeSelects();

            // Initialize Select2 for fallback data
            $('.invoice-type-select').select2('destroy').select2({
                placeholder: 'Select Invoice Type',
                allowClear: true,
                width: 'resolve'
            });

        }

                                        // Populate transaction type selects with HTML options
        function populateTransactionTypeSelects() {
            if (transactionTypes && Array.isArray(transactionTypes)) {
                // Update all transaction type selects
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

        // Load transaction types dynamically from API
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
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                                if (result.success && result.data) {
                    transactionTypes = result.data;
                    populateTransactionTypeSelects();

                    // Re-initialize Select2 for transaction type selects after loading data
                    $('.sale-type-select').select2('destroy').select2({
                        placeholder: 'Select Sale Type',
                        allowClear: true,
                        width: '100%',
                        dropdownAutoWidth: true
                    });

                } else {
                    console.error('Failed to load transaction types:', result.message);
                    showMessage('Failed to load transaction types: ' + (result.message || 'Unknown error'), 'warning');
                }
            } catch (error) {
                console.error('Error loading transaction types:', error);
                showMessage('Error loading transaction types from server', 'error');
                        }
        }

        // Calculate sales tax for a specific item
        function calculateSalesTaxForItem(triggerField) {
            let rateSelect, valueSalesField, salesTaxField, valueFurtherField;

            // Check if we're in the modal context
            const isModal = triggerField.closest('#addItemModal') !== null ||
                           triggerField.id === 'modalValueSalesExcludingST' ||
                           triggerField.id === 'modalRate';

            if (isModal) {
                rateSelect = document.getElementById('modalRate');
                valueSalesField = document.getElementById('modalValueSalesExcludingST');
                valueFurtherField = document.getElementById('modalFurtherTax');
                salesTaxField = document.getElementById('modalSalesTaxApplicable');
            } else {
                // Regular form context
                const itemContainer = triggerField.closest('div.bg-white');
                rateSelect = itemContainer ? itemContainer.querySelector('.rate-select') : null;
                valueSalesField = itemContainer ? itemContainer.querySelector('input[name*="[valueSalesExcludingST]"]') : null;
                salesTaxField = itemContainer ? itemContainer.querySelector('.sales-tax-field') : null;
            }

            if (!rateSelect || !valueSalesField || !salesTaxField) {
                console.warn('Could not find required fields for sales tax calculation');
                return;
            }

            // Extract rate value from the selected option's JSON data
            let rate = 0;
            if (rateSelect.value) {
                try {
                    const rateData = JSON.parse(rateSelect.value);
                    rate = parseFloat(rateData.rate_value) || 0;
                } catch (e) {
                    console.warn('Could not parse rate data:', e);
                    rate = 0;
                }
            }
            const valueSales = parseFloat(valueSalesField.value) || 0;

            let baseForTax, salesTax;
            if (isModal) {
                const isThirdParty = is3rdScheduleSelected();
                if (isThirdParty) {
                    // *** FIX: 3rd Schedule calculations (quantity NOT applied) ***
                    const basePrice = parseFloat(document.getElementById('modalFixedNotifiedValueOrRetailPrice').value) || 0;
                    const qty = parseFloat(document.getElementById('modalQuantity').value) || 0;
                    // Sales Tax = basePrice × rate% (no qty)
                    salesTax = basePrice * rate / 100;
                    salesTaxField.value = salesTax.toFixed(2);

                    // Calculate Discount Amount from Discount %
                    const discountPercent = parseFloat(document.getElementById('modalDiscountPercent').value) || 0;
                    const discountAmount = basePrice * discountPercent / 100;
                    $('#modalDiscountAmount').val(discountAmount.toFixed(2));

                    // Value Sales Excluding ST = FixedNotifiedValue - Discount Amount (no qty)
                    const exclVal = basePrice - discountAmount;
                    $('#modalValueSalesExcludingST').val(exclVal.toFixed(2));
                    
                    // Calculate G/H Amount = (Value Sales Excluding ST + Sales Tax) * GH% / 100
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
            } else {
                baseForTax = valueSales;
                salesTax = (valueSales * rate) / 100;
            }
 const salesfur = (valueSales * 4) / 100;
 const taxTyper=$('#buyerRegistrationType').val();
              if (taxTyper === 'Unregistered') {
                  valueFurtherField.value=salesfur;
              }
            // Update the sales tax field
            salesTaxField.value = salesTax.toFixed(2);

            // Visual feedback
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
     $('#modalHsCode').on('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const hsCode = selectedOption.value;
    const fullDescription = selectedOption.textContent;
    
    // Remove the HS code prefix if needed
    const descriptionOnly = fullDescription.replace(hsCode + ' - ', '');
    
    console.log('HS Code:', hsCode);
    console.log('Full description:', fullDescription);
    console.log('Description only:', descriptionOnly);
    
    // Use the description as needed
    // e.g., populate another field, store in variable, etc.
});

        // Fetch UOM based on HS code with caching
        async function fetchUomByHsCode(hsCodeSelect, skipCacheCheck = false) {
            let uomSelect;
            console.log("Triger DONE Softix")
            console.log(hsCodeSelect)
            const firstOption = hsCodeSelect.options[1];

console.log("DONE ITEM DONE"); // 6909.1200
console.log(firstOption.text);  // Full text
const fullText = firstOption.text;

// Split by " - " and remove the first part (the code)
const parts = fullText.split(' - ');
parts.shift(); // Remove the first element (HS code)
const description = parts.join(' - ');

console.log(description);

// Output: "GLASS AND GLASSWARE. - GLASS FIBRES (INCLUDING GLASS WOOL) AND ARTICLES THEREOF (FOR EXAMPLE, YEARN, WOVEN FABRICS). - OPEN WOVEN FABRICS OF A WIDTH NOT EXCEEDING 30 CM"
            // Check if we're in the modal context
            if (hsCodeSelect.id === 'modalHsCode') {
                uomSelect = document.getElementById('modalUoM');
            } else {
                // Regular form context
                const itemContainer = hsCodeSelect.closest('div.bg-white');
                uomSelect = itemContainer ? itemContainer.querySelector('.uom-select') : null;
            }

            const hsCode = hsCodeSelect.value;

            if (!hsCode) {
                // Reset UOM select when HS code is cleared
                if (uomSelect) {
                    $(uomSelect).val('').trigger('change');
                    uomSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
                    uomSelect.classList.add('bg-gray-50');
                    uomSelect.disabled = true;
                }
                return;
            }

            // Check cache first (unless skipCacheCheck is true)
            if (!skipCacheCheck) {
                const cachedUom = uomCache.get(hsCode);
                if (cachedUom && (Date.now() - cachedUom.timestamp) < UOM_CACHE_DURATION) {
                   
                    populateUomSelect(uomSelect, cachedUom.data, hsCode, cachedUom.fallback);
                    return;
                }
            }

            try {
                // Show loading state
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
                    body: JSON.stringify({
                        hs_code: hsCode
                    })
                });

                const text = await response.text();
                console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success && result.data && result.data.length > 0) {
                    // Cache the result
                    uomCache.set(hsCode, {
                        data: result.data,
                        fallback: result.fallback || false,
                        timestamp: Date.now()
                    });

                    // Clean up old cache entries (keep only last 50 HS codes)
                    if (uomCache.size > 50) {
                        const oldestKey = uomCache.keys().next().value;
                        uomCache.delete(oldestKey);
                    }

                    // Populate UOM dropdown
                    populateUomSelect(uomSelect, result.data, hsCode, result.fallback);


                } else {
                    // No UOM found for this HS code - cache empty result
                    uomCache.set(hsCode, {
                        data: [],
                        fallback: false,
                        timestamp: Date.now()
                    });

                    // No UOM found for this HS code
                    if (uomSelect) {
                        uomSelect.innerHTML = '<option value="">No UOM available for this HS Code</option>';
                        uomSelect.classList.remove('bg-blue-50');
                        uomSelect.classList.add('bg-yellow-50');
                        uomSelect.title = 'No UOM options found for this HS Code';
                        uomSelect.disabled = true;
                    }
                    console.warn('No UOM found for HS Code:', hsCode);
                }

            } catch (error) {
                console.error('Error fetching UOM for HS Code:', error);
                if (uomSelect) {
                    uomSelect.innerHTML = '<option value="">Error loading UOM</option>';
                    uomSelect.classList.remove('bg-blue-50');
                    uomSelect.classList.add('bg-red-50');
                    uomSelect.title = 'Error loading UOM options';
                    uomSelect.disabled = true;
                }
            }
        }

        // Helper function to populate UOM select
function populateUomSelect(uomSelect, uomData, hsCode, fallback = false) {
    if (!uomSelect || !uomData || uomData.length === 0) {
        return;
    }

    // Clear all existing options
    uomSelect.innerHTML = '';
    
    // Use a Set to track unique UOM values and prevent duplicates
    const uniqueUoms = new Map();
    
    uomData.forEach(uom => {
        const uomId = uom.uoM_ID || uom.id;
        const uomDesc = uom.uoM_DESC || uom.description;
        
        if (uomId && uomDesc && !uniqueUoms.has(uomId)) {
            uniqueUoms.set(uomId, uomDesc);
        }
    });
    
    // Add unique options to select
    uniqueUoms.forEach((desc, id) => {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = desc;
        uomSelect.appendChild(option);
    });

    // Visual feedback and enable the select
    uomSelect.classList.remove('bg-blue-50', 'bg-gray-50');
    uomSelect.classList.add('bg-green-50');
    uomSelect.title = `${uniqueUoms.size} UOM option(s) available for HS Code: ${hsCode}`;
    uomSelect.disabled = false;

    // Show fallback message if applicable
    if (fallback) {
        uomSelect.title += ' (using general UOM options)';
    }

    // Destroy existing Select2 completely
    if ($(uomSelect).data('select2')) {
        $(uomSelect).select2('destroy');
    }
    
    // Remove the hidden-accessible class and any select2 attributes
    $(uomSelect).removeClass('select2-hidden-accessible');
    $(uomSelect).removeAttr('data-select2-id');
    $(uomSelect).removeAttr('tabindex');
    $(uomSelect).removeAttr('aria-hidden');
    
    // Re-initialize Select2
    $(uomSelect).select2({
        placeholder: 'Select Unit of Measure',
        allowClear: true,
        width: 'resolve',
        dropdownParent: uomSelect.closest('#addItemModal') ? $('#addItemModal') : $('body')
    });
    
    // Auto-select the first option
    if (uomSelect.options.length > 0) {
        $(uomSelect).val(uomSelect.options[0].value).trigger('change');
    }
}
// Fetch SRO Schedule based on rate ID, date, and province
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


const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success && result.data && result.data.length > 0) {
                    // Display SRO information
                    displaySroInformation(result.data, itemContainer);
                  
                } else {
                    console.warn('No SRO schedule found for the given criteria');
                    clearSroInformation(itemContainer);
                }

            } catch (error) {
                console.error('Error fetching SRO schedule:', error);
                clearSroInformation(itemContainer);
            }
        }

                        // Display SRO information in the item container
        function displaySroInformation(sroData, itemContainer) {
            // Find the SRO Schedule select and populate it
            const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
            if (sroScheduleSelect && sroData.length > 0) {
                // Clear existing options and add default
                sroScheduleSelect.innerHTML = '<option value="">Select SRO Schedule</option>';

                // Add options from API response and store in global Map
                sroData.forEach(sro => {
                    const sroId = sro.srO_ID || sro.sro_id;
                    const sroDesc = sro.srO_DESC || sro.sro_desc || sro.description;

                    if (sroId && sroDesc) {
                        // Store in global Map for label conversion
                        sroSchedules.set(String(sroId), sroDesc);

                        const option = document.createElement('option');
                        option.value = sroId;
                        option.textContent = sroDesc;
                        sroScheduleSelect.appendChild(option);
                    }
                });

                // Visual feedback that options are loaded
                sroScheduleSelect.classList.remove('bg-gray-50');
                sroScheduleSelect.classList.add('bg-green-50');
                sroScheduleSelect.title = `${sroData.length} SRO schedule(s) available`;

                // Update required indicators now that options are available
                updateSroRequiredIndicators(sroScheduleSelect);

                    // Re-initialize Select2 to reflect new options
                 if ($(sroScheduleSelect).data('select2')) {
                     $(sroScheduleSelect).select2('destroy');
                 }
                 $(sroScheduleSelect).removeClass('select2-hidden-accessible');
                 $(sroScheduleSelect).removeAttr('data-select2-id tabindex aria-hidden');
                 $(sroScheduleSelect).select2({
                     placeholder: 'Select SRO Schedule',
                     allowClear: true,
                     width: 'resolve',
                     dropdownParent: sroScheduleSelect.closest('#addItemModal') ? $('#addItemModal') : $('body')
                 });

                 // FIX: if exactly one SRO schedule is returned, auto-select it and
                 // explicitly fetch its SRO Items — programmatic .trigger('change')
                 // alone does not fire select2:select, so this was previously skipped.
                 if (sroData.length === 1) {
                     const onlyOption = sroScheduleSelect.options[1]; // index 0 is the placeholder
                     if (onlyOption) {
                         $(sroScheduleSelect).val(onlyOption.value).trigger('change');
                         fetchSroItems(sroScheduleSelect);
                         updateSroRequiredIndicators(sroScheduleSelect);
                     }
                 }
            }
        }

        // Clear SRO information from the item container
        function clearSroInformation(itemContainer) {
            // Clear the SRO Schedule select
            const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
            if (sroScheduleSelect) {
                sroScheduleSelect.innerHTML = '<option value="">Select SRO Schedule</option>';
                sroScheduleSelect.classList.remove('bg-green-50');
                sroScheduleSelect.classList.add('bg-gray-50');
                sroScheduleSelect.title = 'SRO schedules will be loaded based on rate and province selection';

                // Update required indicators now that options are cleared
                updateSroRequiredIndicators(sroScheduleSelect);
            }

            // Clear the SRO Item select
            clearSroItems(itemContainer);
        }

        // Fetch SRO Items when SRO Schedule is selected
        async function fetchSroItems(sroScheduleSelect) {
            const itemContainer = sroScheduleSelect.closest('div.bg-white');
            const sroItemSelect = itemContainer.querySelector('.sro-item-select');

            // Clear existing SRO items first
            clearSroItems(itemContainer);

            const sroId = sroScheduleSelect.value;
            const invoiceDate = document.getElementById('invoiceDate').value;

            if (!sroId || !invoiceDate) {
               
                return;
            }

            try {
               

                // Show loading state
                if (sroItemSelect) {
                    sroItemSelect.innerHTML = '<option value="">Loading SRO Items...</option>';
                    sroItemSelect.classList.remove('bg-gray-50');
                    sroItemSelect.classList.add('bg-blue-50');
                }

                const response = await fetch(`${API_BASE}/premiertax/api/fbr/sro-item`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        sro_id: parseInt(sroId),
                        date: invoiceDate
                    })
                });

                const text = await response.text();
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success && result.data && result.data.length > 0) {
                    // Populate SRO Items dropdown
                    populateSroItems(result.data, itemContainer);
                   
                } else {
                    console.warn('No SRO items found for the given criteria');
                    // Show no items available
                    if (sroItemSelect) {
                        sroItemSelect.innerHTML = '<option value="">No SRO Items available</option>';
                        sroItemSelect.classList.remove('bg-blue-50');
                        sroItemSelect.classList.add('bg-yellow-50');
                    }
                }

            } catch (error) {
                console.error('Error fetching SRO items:', error);
                if (sroItemSelect) {
                    sroItemSelect.innerHTML = '<option value="">Error loading SRO Items</option>';
                    sroItemSelect.classList.remove('bg-blue-50');
                    sroItemSelect.classList.add('bg-red-50');
                }
            }
        }

        // Populate SRO Items dropdown
        function populateSroItems(sroItemsData, itemContainer) {
            const sroItemSelect = itemContainer.querySelector('.sro-item-select');

            if (sroItemSelect && sroItemsData.length > 0) {
                // Clear existing options and add default
                sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';

                // Add options from API response and store in global Map
                sroItemsData.forEach(item => {
                    const itemId = item.srO_ITEM_ID || item.sro_item_id;
                    const itemDesc = item.srO_ITEM_DESC || item.sro_item_desc || item.description;

                    if (itemId && itemDesc) {
                        // Store in global Map for label conversion
                        sroItems.set(String(itemId), itemDesc);

                        const option = document.createElement('option');
                        option.value = itemId;
                        option.textContent = itemDesc;
                        sroItemSelect.appendChild(option);
                    }
                });

                // Visual feedback that options are loaded
                sroItemSelect.classList.remove('bg-blue-50', 'bg-gray-50');
                sroItemSelect.classList.add('bg-green-50');
                sroItemSelect.title = `${sroItemsData.length} SRO item(s) available`;

                // Update required indicators now that SRO items are available
                updateSroRequiredIndicators(sroItemSelect);

                // Notify Select2 about new options
                if ($(sroItemSelect).hasClass('select2-hidden-accessible')) {
                    $(sroItemSelect).trigger('change');
                }
            }
        }

        // Clear SRO Items dropdown
        function clearSroItems(itemContainer) {
            const sroItemSelect = itemContainer.querySelector('.sro-item-select');
            if (sroItemSelect) {
                sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';
                sroItemSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
                sroItemSelect.classList.add('bg-gray-50');
                sroItemSelect.title = 'SRO items will be loaded when SRO schedule is selected';

                // Update required indicators since SRO items are no longer available
                updateSroRequiredIndicators(sroItemSelect);

                // Notify Select2 about cleared options
                if ($(sroItemSelect).hasClass('select2-hidden-accessible')) {
                    $(sroItemSelect).trigger('change');
                }
            }
        }

                // Clear SRO Schedule and Items when HS code changes
        function clearSroScheduleForItem(hsCodeSelect) {
            const itemContainer = hsCodeSelect.closest('div.bg-white');
            const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
            const sroItemSelect = itemContainer.querySelector('.sro-item-select');

            // Clear SRO Schedule
            if (sroScheduleSelect) {
                sroScheduleSelect.innerHTML = '<option value="">Select SRO Schedule</option>';
                sroScheduleSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
                sroScheduleSelect.classList.add('bg-gray-50');
                sroScheduleSelect.title = 'SRO schedules will be loaded when rate is selected';
            }

            // Clear SRO Items
            if (sroItemSelect) {
                sroItemSelect.innerHTML = '<option value="">Select SRO Item</option>';
                sroItemSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
                sroItemSelect.classList.add('bg-gray-50');
                sroItemSelect.title = 'SRO items will be loaded when SRO schedule is selected';
            }

            // Update required indicators
            updateSroRequiredIndicators(hsCodeSelect);
        }

        // Update required indicators for SRO fields
        function updateSroRequiredIndicators(changedElement) {
            const itemContainer = changedElement.closest('div.bg-white') || changedElement.closest('#addItemModal');
            const sroScheduleSelect = itemContainer.querySelector('.sro-schedule-select');
            const sroItemSelect = itemContainer.querySelector('.sro-item-select');
            const sroScheduleRequired = itemContainer.querySelector('.sro-schedule-required');
            const sroItemRequired = itemContainer.querySelector('.sro-item-required');

            // Get current values
            const sroScheduleValue = sroScheduleSelect ? sroScheduleSelect.value : '';
            const sroItemValue = sroItemSelect ? sroItemSelect.value : '';

            // Check if SRO Schedule dropdown has actual options (not just the default "Select SRO Schedule")
            const sroScheduleHasOptions = sroScheduleSelect ? sroScheduleSelect.options.length > 1 : false;

            // Check if SRO Item dropdown has actual options (not just the default "Select SRO Item")
            const sroItemHasOptions = sroItemSelect ? sroItemSelect.options.length > 1 : false;

            // Update SRO Schedule required indicator
            // SRO Schedule is required when there are options available
            if (sroScheduleRequired) {
                if (sroScheduleHasOptions) {
                    sroScheduleRequired.classList.remove('hidden');
                } else {
                    sroScheduleRequired.classList.add('hidden');
                }
            }

            // Update SRO Item required indicator
            // SRO Item is only required when:
            // 1. SRO Schedule is selected AND
            // 2. SRO items were actually loaded (dropdown has options)
            if (sroItemRequired) {
                if (sroScheduleValue && sroScheduleValue.trim() !== '' && sroItemHasOptions) {
                    sroItemRequired.classList.remove('hidden');
                } else if (sroItemValue && sroItemValue.trim() !== '') {
                    // Also show required if user has selected an SRO item
                    sroItemRequired.classList.remove('hidden');
                } else {
                    sroItemRequired.classList.add('hidden');
                }
            }

            // Add visual feedback to fields
            if (sroScheduleSelect) {
                // SRO Schedule is required when options are available
                if (sroScheduleHasOptions) {
                    sroScheduleSelect.classList.add('border-red-300');
                    sroScheduleSelect.setAttribute('required', 'required');
                } else {
                    sroScheduleSelect.classList.remove('border-red-300');
                    sroScheduleSelect.removeAttribute('required');
                }
            }

            if (sroItemSelect) {
                // SRO Item becomes required only when:
                // 1. SRO Schedule is selected AND SRO items are available (dropdown has options)
                // 2. OR user has already selected an SRO item
                if ((sroScheduleValue && sroScheduleValue.trim() !== '' && sroItemHasOptions) ||
                    (sroItemValue && sroItemValue.trim() !== '')) {
                    sroItemSelect.classList.add('border-red-300');
                    sroItemSelect.setAttribute('required', 'required');
                } else {
                    sroItemSelect.classList.remove('border-red-300');
                    sroItemSelect.removeAttribute('required');
                }
            }
        }

        // Global variable to store items data
        let itemsData = [];
        let editingItemIndex = -1;

        // Open add item modal
        function openAddItemModal() {
            // Check if buyer requirements are met
            if (!validateBuyerRequirements()) {
                showMessage('Please select buyer province and registration type before adding items', 'warning');
                return;
            }

            // Clear the form
            clearModalForm();
            editingItemIndex = -1;

            // Reset modal title and button text for adding new item
            document.querySelector('#addItemModal h3').textContent = 'Add Invoice Item';
            document.getElementById('addItemFromModalBtn').innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Item
            `;

            // Show modal
            document.getElementById('addItemModal').classList.remove('hidden');

            // Populate modal selects with data FIRST (before Select2 init)
            populateModalSelects();

            // Initialize Select2 for modal fields AFTER options are populated
            initializeModalSelect2();
        }


        // Close add item modal
        function closeAddItemModal() {
            document.getElementById('addItemModal').classList.add('hidden');
            clearModalForm();
        }

        // Clear modal form
        function clearModalForm() {
            hideProductDescriptionAutocomplete();
            const form = document.getElementById('itemForm');
            form.reset();

            // Clear Select2 selections
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                if ($(select).hasClass('select2-hidden-accessible')) {
                    $(select).val('').trigger('change');
                }
            });

            // Reset UoM field
            const uomSelect = document.getElementById('modalUoM');
            uomSelect.innerHTML = '<option value="">Select HS Code first</option>';
            uomSelect.disabled = true;
            uomSelect.classList.remove('bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50');
            uomSelect.classList.add('bg-gray-50');

            // Reset sales tax field
            const salesTaxField = document.getElementById('modalSalesTaxApplicable');
            if (salesTaxField) {
                salesTaxField.classList.remove('bg-green-50', 'bg-red-50');
                salesTaxField.classList.add('bg-gray-50');
                salesTaxField.title = 'Enter rate and value sales to calculate';
            }
        }

        // Initialize Select2 for modal fields
        function initializeModalSelect2() {
            // Initialize Select2 for modal sale type
            $('#modalSaleType').select2({
                placeholder: 'Select Sale Type',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });

            // Sale type change handled by delegated listener at line 969

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

            // Initialize Select2 for modal UoM
            $('#modalUoM').select2({
                placeholder: 'Select Unit of Measure',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addItemModal')
            });

            // Initialize Select2 for modal rate
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

        // Add item from modal to table
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

            // Handle discount type - if percentage, calculate actual amount
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
                // Update existing item
                itemsData[editingItemIndex] = itemData;
                showMessage('Item updated successfully!', 'success');
            } else {
                // Add new item
                itemsData.push(itemData);
                showMessage('Item added successfully!', 'success');
            }

            // Update table display
            updateItemsTable();

            // Update hidden form inputs
            updateHiddenFormInputs();

            // Save product description for future autocomplete suggestions
            saveNewProductDescription(itemData.productDescription);
            hideProductDescriptionAutocomplete();

            // Close modal
            closeAddItemModal();
        }

        // Get selected text from select element
        function getSelectText(selectId) {
            const select = document.getElementById(selectId);
            if (select && select.selectedIndex >= 0) {
                return select.options[select.selectedIndex].text;
            }
            return '';
        }

        // Calculate and update table totals
        function updateTableTotals() {
            const tableFooter = document.getElementById('itemsTableFooter');
            const totalQuantityEl = document.getElementById('totalQuantity');
            const totalValueSalesEl = document.getElementById('totalValueSales');
            const totalSalesTaxEl = document.getElementById('totalSalesTax');

            if (itemsData.length === 0) {
                // Hide footer if no items
                tableFooter.classList.add('hidden');
                return;
            }

            // Show footer
            tableFooter.classList.remove('hidden');

            // Calculate totals
            let totalQuantity = 0;
            let totalValueSales = 0;
            let totalSalesTax = 0;

            itemsData.forEach(item => {
                totalQuantity += parseFloat(item.quantity || 0);
                totalValueSales += parseFloat(item.valueSalesExcludingST || 0);
                totalSalesTax += parseFloat(item.salesTaxApplicable || 0);
            });

            // Update display with appropriate formatting
            // For quantity: show decimals only if needed, for monetary values: always show 2 decimals
            totalQuantityEl.textContent = totalQuantity % 1 === 0 ? totalQuantity.toFixed(0) : totalQuantity.toFixed(2);
            totalValueSalesEl.textContent = totalValueSales.toFixed(2);
            totalSalesTaxEl.textContent = totalSalesTax.toFixed(2);

         
        }

        // Update items table display
        function updateItemsTable() {
            const tableBody = document.getElementById('itemsTableBody');
            const noItemsRow = document.getElementById('noItemsRow');

            // Always clear existing rows first (except no items row)
            const existingRows = tableBody.querySelectorAll('tr:not(#noItemsRow)');
            existingRows.forEach(row => row.remove());

            if (itemsData.length === 0) {
                noItemsRow.style.display = '';
                updateTableTotals(); // This will hide the footer
                return;
            }

            noItemsRow.style.display = 'none';

            // Add rows for each item
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

            // Update totals after updating table
            updateTableTotals();
        }

        // Edit item
        function editItem(index) {
            if (index < 0 || index >= itemsData.length) return;

            // Check if buyer requirements are met before allowing edit
            if (!validateBuyerRequirements()) {
                showMessage('Please select buyer province and registration type before editing items', 'warning');
                return;
            }

            editingItemIndex = index;
            const item = itemsData[index];

            // Open modal
            document.getElementById('addItemModal').classList.remove('hidden');

            // Initialize Select2 for modal fields
            initializeModalSelect2();

            // Populate modal selects with data
            populateModalSelects();

                // Fill form with item data
            setTimeout(async () => {
                // First, populate non-dependent fields
                Object.keys(item).forEach(key => {
                    const element = document.getElementById('modal' + key.charAt(0).toUpperCase() + key.slice(1));
                    if (element && key !== 'uoM') { // Skip UoM for now, we'll handle it separately
                        if (element.tagName === 'SELECT') {
                            $(element).val(item[key]).trigger('change');
                        } else {
                            element.value = item[key];
                        }
                    }
                });

                // Restore discount percent value for editing
                if (item.discountType === 'percent' && item.discountPercentInput !== undefined) {
                    document.getElementById('modalDiscount').value = item.discountPercentInput;
                }

                // Check if this is a 3rd Schedule item
                const saleTypeText = item.saleTypeText || '';
                if (saleTypeText.toLowerCase().includes('3rd schedule') || saleTypeText.toLowerCase().includes('3rd party')) {
                    // Restore 3rd Schedule percentage fields
                    if (item.ghPercent !== undefined) {
                        document.getElementById('modalGhPercent').value = item.ghPercent;
                    }
                    if (item.discountPercent !== undefined) {
                        document.getElementById('modalDiscountPercent').value = item.discountPercent;
                    }
                    // Trigger recalculation for 3rd Schedule fields
                    recalculate3rdSchedule();
                }

                // Handle HS Code and UoM dependency
                const hsCodeElement = document.getElementById('modalHsCode');
                const uomElement = document.getElementById('modalUoM');

                if (hsCodeElement && item.hsCode && uomElement && item.uoM) {
                    // Set HS Code first
                    $(hsCodeElement).val(item.hsCode).trigger('change');

                    // Wait a bit for HS Code to be set, then fetch UoM options
                    setTimeout(async () => {
                        try {
                           
                            // Fetch UoM options for this HS code
                            await fetchUomByHsCode(hsCodeElement);

                            // Wait a bit more for UoM options to be populated, then set the value
                            setTimeout(() => {
                                $(uomElement).val(item.uoM).trigger('change');
                               

                                // After UoM is set, handle SRO Schedule and SRO Item
                                setTimeout(async () => {
                                    // Set SRO Schedule No. if available
                                    if (item.sroScheduleNo) {
                                       

                                        // Get the rate data to fetch SRO schedules
                                        const rateSelect = document.getElementById('modalRate');
                                        const invoiceDate = document.getElementById('invoiceDate').value;
                                        const buyerProvince = $('#buyerProvince').val();

                                        if (rateSelect.value) {
                                            try {
                                                const rateData = JSON.parse(rateSelect.value);
                                                const rateId = rateData.rate_id;

                                                // Fetch SRO schedules first
                                                await fetchSroSchedule(rateId, invoiceDate, buyerProvince, document.getElementById('addItemModal'));

                                                // Wait for SRO schedules to be populated
                                                setTimeout(() => {
                                                    $('#modalSroScheduleNo').val(item.sroScheduleNo).trigger('change');
                                                  

                                                    // Wait for SRO Items to load after SRO Schedule is set
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
                                    }
                                }, 300);
                            }, 200);
                        } catch (error) {
                            console.error('Error loading UoM for edit mode:', error);
                        }
                    }, 300);
                } else if (uomElement) {
                    // If no HS code or UoM, reset UoM field
                    uomElement.innerHTML = '<option value="">Select HS Code first</option>';
                    uomElement.disabled = true;
                }

                // Update modal title
                document.querySelector('#addItemModal h3').textContent = 'Edit Invoice Item';
                document.getElementById('addItemFromModalBtn').innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.828-2.828z" />
                    </svg>
                    Update Item
                `;
            }, 100);
        }

        // Delete item
        function deleteItem(index) {
            if (index < 0 || index >= itemsData.length) return;

            if (confirm('Are you sure you want to delete this item?')) {
                itemsData.splice(index, 1);
                updateItemsTable(); // This calls updateTableTotals() automatically
                updateHiddenFormInputs();
                showMessage('Item deleted successfully!', 'success');
            }
        }

        // Update hidden form inputs for submission
        function updateHiddenFormInputs() {
            const container = document.getElementById('hiddenItemsContainer');
            container.innerHTML = '';

            itemsData.forEach((item, index) => {
                Object.keys(item).forEach(key => {
                    // Skip display text keys
                    if (key.endsWith('Text') || key === 'rateValue') return;

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${index}][${key}]`;
                    input.value = item[key];
                    container.appendChild(input);
                });
            });
        }



        // Helper function to convert IDs to labels for console display ONLY
        // WARNING: This should NOT be used for actual form submission - only for debugging/logging
        function convertIdsToLabels(data) {
            const convertedData = JSON.parse(JSON.stringify(data)); // Deep clone

            // Convert province codes to descriptions
            if (convertedData.sellerProvince && provinces) {
                const sellerProvince = provinces.find(p => p.stateProvinceCode == convertedData.sellerProvince);
                if (sellerProvince) {
                    convertedData.sellerProvince = sellerProvince.stateProvinceDesc;
                }
            }

            if (convertedData.buyerProvince && provinces) {
                const buyerProvince = provinces.find(p => p.stateProvinceCode == convertedData.buyerProvince);
                if (buyerProvince) {
                    convertedData.buyerProvince = buyerProvince.stateProvinceDesc;
                }
            }

            // Convert item UoMs and sale types
            if (convertedData.items && Array.isArray(convertedData.items)) {
                convertedData.items.forEach(item => {
                    // Convert UoM ID to description
                    if (item.uoM && uoMs) {
                        const uom = uoMs.find(u => (u.uoM_ID || u.id) == item.uoM);
                        if (uom) {
                            item.uoM = uom.uoM_DESC || uom.description || item.uoM;
                        }
                    }

                    // Convert sale type ID to description
                    if (item.saleType && transactionTypes) {
                        const saleType = transactionTypes.find(t => t.transactioN_TYPE_ID == item.saleType);
                        if (saleType) {
                            item.saleType = saleType.transactioN_DESC || item.saleType;
                        }
                    }

                                        // Rate is already in correct format from FBR API (no processing needed)

                    // Convert SRO Schedule ID to description
                    if (item.sroScheduleNo && sroSchedules.has(String(item.sroScheduleNo))) {
                        item.sroScheduleNo = sroSchedules.get(String(item.sroScheduleNo));
                    }

                    // Convert SRO Item ID to description
                    if (item.sroItemSerialNo && sroItems.has(String(item.sroItemSerialNo))) {
                        item.sroItemSerialNo = sroItems.get(String(item.sroItemSerialNo));
                    }
                });
            }

            return convertedData;
        }

        // Clear form and reset to default state
        function clearForm() {
            // Show confirmation dialog
            if (!confirm('Are you sure you want to clear all form data? This action cannot be undone.')) {
                return;
            }

           

            // Clear seller information and reset to defaults
            // document.getElementById('sellerNTNCNIC').value = userProfile.cinc_ntn || '';
            // document.getElementById('sellerBusinessName').value = userProfile.business_name || '';
            // document.getElementById('sellerAddress').value = userProfile.address || '';

            // Reset seller province to user's default
            // if (userProfile.province) {
            //     // $('#sellerProvince').val(userProfile.province).trigger('change');
            // } else {
            // $('#sellerProvince').val('').trigger('change');
            // }

            // Clear invoice information
            // $('#invoiceType').val('').trigger('change');
            // const today = new Date().toISOString().split('T')[0];
            // document.getElementById('invoiceDate').value = today;
            // document.getElementById('invoiceRefNo').value = '';

            // Clear scenario ID if it exists
            // const scenarioIdField = document.getElementById('scenarioId');
            // if (scenarioIdField) {
            //     scenarioIdField.value = 'SN000';
            // }

            // Clear buyer information
            document.getElementById('buyerNTNCNIC').value = '';
            document.getElementById('buyerBusinessName').value = '';
            document.getElementById('buyerAddress').value = '';
            $('#buyerProvince').val('').trigger('change');
            $('#buyerRegistrationType').val('').trigger('change');

            // Reset readonly states and styling
            const ntnCnicField = document.getElementById('buyerNTNCNIC');
            const businessNameField = document.getElementById('buyerBusinessName');
            const addressField = document.getElementById('buyerAddress');

            [ntnCnicField, businessNameField, addressField].forEach(field => {
                field.readOnly = false;
                field.classList.remove('bg-gray-100', 'text-gray-500');
            });

            // Clear all items
            itemsData = [];
            updateItemsTable();
            updateHiddenFormInputs();

            // Clear any SRO data cache
            sroSchedules.clear();
            sroItems.clear();

            // Reset validation state for add item button
            setTimeout(() => {
                validateBuyerRequirements();
            }, 100);

            // Show success message
            showMessage('Form cleared successfully. All fields have been reset to their default values.', 'success');
        }

        // Validate invoice
        async function validateInvoice() {
             const formData = new FormData(document.getElementById('invoiceForm'));
    const data = formDataToObjectWithLabels(formData); // <-- use labels
    const apiUrl = `${API_BASE}/invoicing/validate`;

            // Create display version for console logging (with labels)
            const displayData = convertIdsToLabels(data);

          

            try {
                showMessage('Validating invoice...', 'info');

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data) // Send original data with IDs, not converted labels
                });

                const text = await response.text();
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success) {
                    showMessage('Invoice validation successful!', 'success');
                } else {
                    showMessage('Validation failed: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Validation error:', error);
                showMessage('Validation failed: ' + error.message, 'error');
            }
        }

        // Submit invoice
        async function submitInvoice(e) {
             e.preventDefault();
    const formData = new FormData(e.target);
    const data = formDataToObjectWithLabels(formData); // <-- use labels
    const apiUrl = `${API_BASE}/premiertax/invoicing/submit`;

            // Create display version for console logging (with labels)
            const displayData = convertIdsToLabels(data);

           

             try {
        showMessage('Submitting invoice to FBR...', 'info');
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
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success) {
                    showMessage('Invoice submitted successfully to FBR!', 'success');
                    if (result.data && result.data.invoiceNumber) {
                        showMessage('Invoice Number: ' + result.data.invoiceNumber, 'info');
                    }
                    setTimeout(() => {
                        window.location.href = `${API_BASE}/premiertax/invoicing`;
                    }, 1000);
                } else {
                    var errMsg = typeof result.message === 'object' ? JSON.stringify(result.message) : result.message;
                    showMessage('Submission failed: ' + errMsg, 'error');
                    if (result.errors) {
                        console.error('Validation errors:', result.errors);
                    }
                }
            } catch (error) {
                console.error('Submission error:', error);
                showMessage('Submission failed: ' + error.message, 'error');
            } finally {
                document.getElementById('submitBtn').disabled = false;
            }
        }


//For displaySroInformation
// Submit invoice
        async function submitDraft(e) {
             e.preventDefault();
    const formData = new FormData(document.getElementById('invoiceForm'));

    // Build top-level fields from formData (non-items)
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (!key.startsWith('items[')) {
            data[key] = value;
        }
    }

    // Use raw itemsData for items — keeps original IDs (saleType, rate JSON, uoM)
    // so that the Edit Draft page can correctly restore all dropdown selections.
    data.items = itemsData.map(item => {
        const clean = { ...item };
        // Remove transient UI-only fields
        Object.keys(clean).forEach(k => {
            if (k.endsWith('Text') || k === 'rateValue' || k === 'rowId' || k === 'index') {
                delete clean[k];
            }
        });
        return clean;
    });

    const apiUrl = `${API_BASE}/premiertax/invoicing/save-draft`;

            // Create display version for console logging (with labels)
            const displayData = data;

           

             try {
        showMessage('Submitting invoice to FBR...', 'info');
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
console.log('RAW RATE RESPONSE:', text);

const cleanText = text.trim().startsWith('{')
    ? text
    : text.substring(text.indexOf('{'));

const result = JSON.parse(cleanText);

                if (result.success) {
                    showMessage('Invoice Saved in Draft Successfully !', 'success');
                    if (result.data && result.data.invoiceNumber) {
                        showMessage('Draft Invoice Number: ' + result.data.invoiceNumber, 'info');
                    }

                    // Form values are preserved after successful submission
                    // Users can modify and resubmit if needed
                } else {
                    var errMsg = typeof result.message === 'object' ? JSON.stringify(result.message) : result.message;
                    showMessage('Submission failed: ' + errMsg, 'error');
                    if (result.errors) {
                        console.error( result);
                    }
                }
            } catch (error) {
                console.error('Submission error:', error);
                showMessage('Submission failed: ' + error.message, 'error');
            } finally {
                document.getElementById('submitBtn').disabled = false;
            }
        }



        // Convert FormData to object
        function formDataToObject(formData) {
            const obj = {};
            const items = {};

            for (let [key, value] of formData.entries()) {
                if (key.startsWith('items[')) {
                    const matches = key.match(/items\[(\d+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const itemIndex = matches[1];
                        const fieldName = matches[2];

                        if (!items[itemIndex]) {
                            items[itemIndex] = {};
                        }

                                                // Handle rate field specifically (extract rate_desc from JSON, fallback to rate_value)
                        if (fieldName === 'rate') {
                            if (value) {
                                try {
                                    const rateData = JSON.parse(value);
                                    let rate;

                                                                        // Use rate_desc if available, otherwise fallback to rate_value with % sign
                                    if (rateData.rate_desc && rateData.rate_desc.trim() !== '') {
                                        rate = rateData.rate_desc;
                                    } else {
                                        rate = (parseFloat(rateData.rate_value) || 0) + '%';
                                    }

                                    const rateValue = parseFloat(rateData.rate_value) || 0;

                                    // Convert 0 or 0% to "Exempt"
                                    if (rateValue === 0 || rate === '0%' || rate.toLowerCase().includes('exempt')) {
                                        rate = 'Exempt';
                                    }
                                    // For non-zero numeric values, ensure % is present if not already there
                                    else if (rateValue > 0 && /^\d+(\.\d+)?$/.test(rate.toString().trim())) {
                                        rate = rate + '%';
                                    }

                                    items[itemIndex][fieldName] = rate;
                                } catch (e) {
                                    console.warn('Could not parse rate data:', e);
                                    items[itemIndex][fieldName] = 'Exempt';
                                }
                            } else {
                                items[itemIndex][fieldName] = 'Exempt';
                            }
                        }
                        // Convert other numeric fields
                        else if (['quantity', 'totalValues', 'valueSalesExcludingST', 'salesTaxApplicable',
                             'fixedNotifiedValueOrRetailPrice', 'salesTaxWithheldAtSource', 'furtherTax',
                             'fedPayable', 'discount'].includes(fieldName)) {
                            items[itemIndex][fieldName] = parseFloat(value) || 0;
                        } else {
                            items[itemIndex][fieldName] = value;
                        }
                    }
                } else {
                    // Exclude scenarioId when use_sandbox is false
                    if (key === 'scenarioId' && !userProfile.use_sandbox) {
                      
                        continue;
                    }
                    obj[key] = value;
                }
            }

            // If no items found in form data, check if we need to validate items exist
            if (Object.keys(items).length === 0 && itemsData.length === 0) {
                showMessage('Please add at least one item to the invoice!', 'error');
                return null;
            }

            obj.items = Object.values(items);
            return obj;
        }

        // Toggle seller accordion
        function toggleSellerAccordion() {
            const content = document.getElementById('sellerAccordionContent');
            const icon = document.getElementById('sellerAccordionIcon');

            if (content.classList.contains('hidden')) {
                // Show content
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                // Hide content
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        // Show status message
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

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageEl.parentElement) {
                    messageEl.remove();
                }
            }, 5000);
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

                // Convert fields to descriptions
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
            // Convert province codes to descriptions
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

// Upload Excel Handler (Restricted to CID 53)
let uploadedExcelData = [];

document.addEventListener('DOMContentLoaded', function() {
    const uploadExcelBtn = document.getElementById('uploadExcelBtn');
    const excelFileInput = document.getElementById('excelFileInput');

    if (uploadExcelBtn && excelFileInput) {
        uploadExcelBtn.addEventListener('click', function() {
            excelFileInput.click();
        });

        excelFileInput.addEventListener('change', handleExcelUpload);
    }
});

function handleExcelUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(evt) {
        try {
            const data = evt.target.result;
            const workbook = XLSX.read(data, { type: 'binary' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const rawRows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            if (!rawRows || rawRows.length < 2) {
                alert('No data found in uploaded Excel file.');
                return;
            }

            uploadedExcelData = [];
            // Skip header row (row index 0)
            for (let i = 1; i < rawRows.length; i++) {
                const r = rawRows[i];
                if (!r || r.length === 0 || (!r[1] && !r[2])) continue; // skip empty rows

                uploadedExcelData.push({
                    srNo: r[0] || i,
                    cnic: String(r[1] || '').trim(),
                    name: String(r[2] || '').trim(),
                    invoiceDate: String(r[3] || '').trim(),
                    price: parseFloat(r[4]) || 0,
                    quantity: parseFloat(r[5]) || 0,
                    hsCode: String(r[6] || '').trim(),
                    productName: String(r[7] || '').trim(),
                    retailValue: parseFloat(r[8]) || 0,
                    discPct: parseFloat(r[9]) || 0,
                    discount: parseFloat(r[10]) || 0,
                    exValue: parseFloat(r[11]) || 0,
                    salesTax: parseFloat(r[12]) || 0,
                    inclusiveValue: parseFloat(r[13]) || 0,
                    status: 'ready',
                    error: null,
                    fbrInvoiceNo: null
                });
            }

            renderUploadedExcelTable();
            document.getElementById('excelFileInput').value = '';
        } catch (err) {
            console.error('Excel parse error:', err);
            alert('Failed to parse Excel file: ' + err.message);
        }
    };
    reader.readAsBinaryString(file);
}

function renderUploadedExcelTable() {
    const container = document.getElementById('uploadedExcelContainer');
    const tbody = document.getElementById('uploadedExcelTableBody');
    const badge = document.getElementById('uploadedItemCountBadge');

    if (!container || !tbody) return;

    if (uploadedExcelData.length === 0) {
        container.classList.add('hidden');
        tbody.innerHTML = '';
        return;
    }

    container.classList.remove('hidden');
    badge.textContent = `${uploadedExcelData.length} rows`;

    let html = '';
    uploadedExcelData.forEach((row, index) => {
        let statusHtml = '';
        if (row.status === 'submitting') {
            statusHtml = `<span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-semibold animate-pulse">Submitting...</span>`;
        } else if (row.status === 'success') {
            statusHtml = `<span class="px-2 py-0.5 rounded bg-green-100 text-green-800 font-semibold">Generated</span>`;
            if (row.fbrInvoiceNo) {
                statusHtml += `<div class="text-xs text-green-700 font-mono mt-0.5">${row.fbrInvoiceNo}</div>`;
            }
        } else if (row.status === 'error') {
            statusHtml = `<span class="px-2 py-0.5 rounded bg-red-100 text-red-800 font-semibold">Failed</span>`;
            if (row.error) {
                statusHtml += `<div class="text-xs text-red-600 mt-1 max-w-xs break-words">${row.error}</div>`;
            }
        } else {
            statusHtml = `<span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-semibold">Ready</span>`;
        }

        html += `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2">${index + 1}</td>
                <td class="px-3 py-2 font-mono">${row.cnic}</td>
                <td class="px-3 py-2 font-medium text-gray-900">${row.name}</td>
                <td class="px-3 py-2 whitespace-nowrap">${row.invoiceDate}</td>
                <td class="px-3 py-2">${row.productName}</td>
                <td class="px-3 py-2 font-mono">${row.hsCode}</td>
                <td class="px-3 py-2 text-right">${row.price.toLocaleString()}</td>
                <td class="px-3 py-2 text-right">${row.quantity}</td>
                <td class="px-3 py-2 text-right">${row.retailValue.toLocaleString()}</td>
                <td class="px-3 py-2 text-right">${row.discPct}%</td>
                <td class="px-3 py-2 text-right">${row.discount.toLocaleString()}</td>
                <td class="px-3 py-2 text-right">${row.exValue.toLocaleString()}</td>
                <td class="px-3 py-2 text-right">${row.salesTax.toLocaleString()}</td>
                <td class="px-3 py-2 text-right font-semibold text-gray-900">${row.inclusiveValue.toLocaleString()}</td>
                <td class="px-3 py-2">${statusHtml}</td>
                <td class="px-3 py-2 text-center whitespace-nowrap space-x-1">
                    <button type="button" onclick="openEditExcelModal(${index})" class="px-2 py-1 rounded font-medium text-xs transition" style="background-color: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important;" title="Edit row">
                        ✏️ Edit
                    </button>
                    <button type="button" onclick="generateExcelInvoiceFbr(${index})" ${row.status === 'submitting' ? 'disabled' : ''} class="px-2 py-1 rounded font-medium text-xs transition disabled:opacity-50" style="background-color: #059669 !important; color: #ffffff !important; border: 1px solid #047857 !important;" title="Generate on FBR">
                        🚀 Generate
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

function openEditExcelModal(index) {
    const row = uploadedExcelData[index];
    if (!row) return;

    document.getElementById('editExcelRowIndex').value = index;
    document.getElementById('editExcelCnic').value = row.cnic;
    document.getElementById('editExcelName').value = row.name;
    document.getElementById('editExcelDate').value = row.invoiceDate;
    document.getElementById('editExcelProductName').value = row.productName;
    document.getElementById('editExcelHsCode').value = row.hsCode;
    document.getElementById('editExcelPrice').value = row.price;
    document.getElementById('editExcelQty').value = row.quantity;
    document.getElementById('editExcelRetailValue').value = row.retailValue;
    document.getElementById('editExcelDiscPct').value = row.discPct;
    document.getElementById('editExcelDiscount').value = row.discount;
    document.getElementById('editExcelExValue').value = row.exValue;
    document.getElementById('editExcelSalesTax').value = row.salesTax;
    document.getElementById('editExcelInclusiveValue').value = row.inclusiveValue;

    document.getElementById('editExcelModal').classList.remove('hidden');
}

function closeEditExcelModal() {
    document.getElementById('editExcelModal').classList.add('hidden');
}

function saveEditedExcelRow() {
    const index = parseInt(document.getElementById('editExcelRowIndex').value);
    if (isNaN(index) || !uploadedExcelData[index]) return;

    uploadedExcelData[index].cnic = document.getElementById('editExcelCnic').value;
    uploadedExcelData[index].name = document.getElementById('editExcelName').value;
    uploadedExcelData[index].invoiceDate = document.getElementById('editExcelDate').value;
    uploadedExcelData[index].productName = document.getElementById('editExcelProductName').value;
    uploadedExcelData[index].hsCode = document.getElementById('editExcelHsCode').value;
    uploadedExcelData[index].price = parseFloat(document.getElementById('editExcelPrice').value) || 0;
    uploadedExcelData[index].quantity = parseFloat(document.getElementById('editExcelQty').value) || 0;
    uploadedExcelData[index].retailValue = parseFloat(document.getElementById('editExcelRetailValue').value) || 0;
    uploadedExcelData[index].discPct = parseFloat(document.getElementById('editExcelDiscPct').value) || 0;
    uploadedExcelData[index].discount = parseFloat(document.getElementById('editExcelDiscount').value) || 0;
    uploadedExcelData[index].exValue = parseFloat(document.getElementById('editExcelExValue').value) || 0;
    uploadedExcelData[index].salesTax = parseFloat(document.getElementById('editExcelSalesTax').value) || 0;
    uploadedExcelData[index].inclusiveValue = parseFloat(document.getElementById('editExcelInclusiveValue').value) || 0;

    closeEditExcelModal();
    renderUploadedExcelTable();
}

function clearUploadedExcelData() {
    if (confirm('Are you sure you want to clear all uploaded Excel rows?')) {
        uploadedExcelData = [];
        renderUploadedExcelTable();
    }
}

async function generateExcelInvoiceFbr(index) {
    const row = uploadedExcelData[index];
    if (!row) return;

    row.status = 'submitting';
    row.error = null;
    renderUploadedExcelTable();

    const sellerNTN = document.getElementById('sellerNTNCNIC')?.value || window.appData?.user?.cinc_ntn || '';
    const sellerName = document.getElementById('sellerBusinessName')?.value || window.appData?.user?.business_name || '';
    const sellerProvince = document.getElementById('sellerProvince')?.value || window.appData?.user?.province || 'Punjab';
    const sellerAddress = document.getElementById('sellerAddress')?.value || window.appData?.user?.address || 'N/A';

    const payload = {
        sellerNTNCNIC: sellerNTN,
        sellerBusinessName: sellerName,
        sellerProvince: sellerProvince,
        sellerAddress: sellerAddress,
        buyerNTNCNIC: row.cnic,
        buyerBusinessName: row.name,
        buyerProvince: sellerProvince,
        buyerAddress: 'N/A',
        buyerRegistrationType: row.cnic ? 'Registered' : 'Unregistered',
        invoiceType: 'Sale Invoice',
        invoiceDate: row.invoiceDate || new Date().toISOString().split('T')[0],
        scenarioId: '1',
        items: [
            {
                productDescription: row.productName || 'Plastic Articles',
                hsCode: row.hsCode || '3926.1000',
                quantity: row.quantity,
                uoM: 'Numbers, Count, Pcs, Units',
                saleType: 'Third Schedule Goods',
                rate: '18%',
                valueSalesExcludingST: row.exValue,
                salesTaxApplicable: row.salesTax,
                fixedNotifiedValueOrRetailPrice: row.retailValue,
                discount: row.discount,
                furtherTax: 0,
                extraTax: '',
                totalValues: row.inclusiveValue
            }
        ]
    };

    try {
        const response = await fetch(`${API_BASE}/premiertax/invoicing/submit`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const text = await response.text();
        const cleanText = text.trim().startsWith('{') ? text : text.substring(text.indexOf('{'));
        const result = JSON.parse(cleanText);

        if (result.success) {
            row.status = 'success';
            row.fbrInvoiceNo = result.data?.invoiceNumber || result.invoiceNumber || 'Submitted';
        } else {
            row.status = 'error';
            row.error = typeof result.message === 'object' ? JSON.stringify(result.message) : (result.message || 'FBR Submission Failed');
        }
    } catch (err) {
        row.status = 'error';
        row.error = err.message || 'Network error';
    }

    renderUploadedExcelTable();
}

</script>
@endsection