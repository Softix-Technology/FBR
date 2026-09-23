<?php
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SaleDetails;
use App\Http\Controllers\PurchaseDetail;
use App\Http\Controllers\Backup;
use App\Http\Controllers\CashController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemUnit;
use App\Http\Controllers\PartyMemberController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LedgerDetailController;
use App\Http\Controllers\UserManagement;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\ErpParamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankReciptController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BankPaymentController; 
use App\Http\Controllers\OfficeCashController; 
use App\Http\Controllers\GateExController; 
use App\Http\Controllers\account\BillController;
use App\Http\Controllers\account\LoanController;
use App\Http\Controllers\GluePurchaseController;
use App\Http\Controllers\GlueReturnController;
use App\Http\Controllers\InkPurchaseController;
use App\Http\Controllers\InkReturnController;
use App\Http\Controllers\account\GroupController;
use App\Http\Controllers\account\PartyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PlatePurchaseController; 
use App\Http\Controllers\PlateReturnController; 
use App\Http\Controllers\account\Level1Controller;
use App\Http\Controllers\account\Level2Controller;
use App\Http\Controllers\account\Level3Controller;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\OpenBalController;
use App\Http\Controllers\PaymentInvoiceController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\DeliveryChallanController;
use App\Http\Controllers\ConfectioneryController;
use App\Http\Controllers\RecieveableableController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\LeminationPurchaseController;
use App\Http\Controllers\LaminationReturnController;
use App\Http\Controllers\CorrugationPurchaseController;
use App\Http\Controllers\CorrugationReturnController;
use App\Http\Controllers\ShipperPurchasesController;
use App\Http\Controllers\ShipperReturnController;
use App\Http\Controllers\account\AccountMasterController;
use App\Http\Controllers\WastageSaleController;
use App\Http\Controllers\WastageController;
use App\Http\Controllers\SaleInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ConfectBillingController;
use App\Http\Controllers\GatePassInController;
use App\Http\Controllers\GatePassOutController;
use App\Http\Controllers\RegistrationFormController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StorageLinkController;
use App\Http\Controllers\PhpIniController;
use App\Http\Controllers\PhpInfoController;
use App\Http\Controllers\ProductLogController;
use App\Http\Controllers\ChequeReceiptsController;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\CreateAccountController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SReportsController;
use App\Http\Controllers\DailyStatementController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportStockController;
use App\Http\Controllers\JobSheetController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\DepartmentSectionController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ExtraTimeController;
use App\Http\Controllers\DyeSectionController;
use App\Http\Controllers\PasteSectionController;
use App\Http\Controllers\ProcessSectionController;
use App\Http\Controllers\AttendenceFormController;
use App\Http\Controllers\DyePurchaseController;
use App\Http\Controllers\DyeReturnController;
use App\Http\Controllers\GeneralJobSheetController;
use App\Http\Controllers\GeneralDeliveryChallanController;
use App\Http\Controllers\GeneralBillingController;
use App\Http\Controllers\WageBoxboardController;
use App\Http\Controllers\SalaryCalculatorController;
use App\Http\Controllers\BankCashReportController;
use App\Http\Controllers\CompaniesController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\ThirdScheduleDraftController;


Route::get('premiertax/migrate-sale-parties-id', function () {
   
         Schema::create('sale_invoice_fbr', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->string('fbr_invoice_no');
            // Seller info
            $table->string('seller_ntn_cnic')->nullable();
            $table->string('seller_business_name')->nullable();
            $table->string('seller_province')->nullable();
            $table->string('seller_address')->nullable();

            // Invoice info
            $table->string('invoice_type')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('invoice_ref_no')->nullable();
            $table->string('scenario_id')->nullable();

            // Buyer info
            $table->string('buyer_ntn_cnic')->nullable();
            $table->string('buyer_business_name')->nullable();
            $table->string('buyer_province')->nullable();
            $table->string('buyer_registration_type')->nullable();
            $table->string('buyer_address')->nullable();

            // Items as JSON
            $table->json('items')->nullable();

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    

    
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return redirect()->route('login');
});

Route::get('premiertax/run-cid-migration', function () {
    try {
        // Run only pending migrations
        Artisan::call('migrate', [
            '--force' => true, // needed when running in production
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Migration executed successfully',
            'output' => Artisan::output(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});
Route::get('premiertax/link', function () {
    try {
        // Run the storage:link command
        Artisan::call('storage:link', [
            '--force' => true, // optional: overwrite existing symlink
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Storage link created successfully',
            'output' => Artisan::output(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});
Route::get('premiertax/migrate', function() {
    try {
        // Clear caches first
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        // Cache routes and config for better performance
        Artisan::call('route:cache');
        Artisan::call('config:cache');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Migration completed ',
            'output' => Artisan::output()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Migration failed',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth'); // Add authentication middleware for security
Route::get('/premiertax', function () {
    return redirect()->route('login');
});
// routes/web.php
Route::get('premiertax/reports/party', [ReportController::class, 'partyReport'])->name('reports.party')->middleware('auth');
Route::get('premiertax/reports/Sales', [ReportController::class, 'SaleReport'])->name('reports.sales')->middleware('auth');
Route::delete('premiertax/reports/sales/delete/{id}', [ReportController::class, 'deleteSaleInvoice'])->name('reports.sales.delete')->middleware('auth');
Route::get('premiertax/reports/Purchase', [ReportController::class, 'PurchaseReport'])->name('reports.purchase')->middleware('auth');
Route::delete('premiertax/reports/purchase/delete/{id}', [ReportController::class, 'deletePurchaseInvoice'])->name('reports.purchase.delete')->middleware('auth');
Route::get('premiertax/purchase/invoice/{id}', [PurchaseDetail::class, 'invoice'])
     ->name('premiertax.purchase.invoice');
Route::get('premiertax/sale/invoice/{id}', [SaleDetails::class, 'invoice'])
     ->name('premiertax.sale.invoice');
Route::get('premiertax/sale/invoice/{id}/third-schedule', [SaleDetails::class, 'thirdSchedule'])
     ->name('premiertax.sale.third-schedule');
Route::get('premiertax/sale/invoice/{id}/standard', [SaleDetails::class, 'standardInvoice'])
     ->name('premiertax.sale.standard-invoice');
Route::get('premiertax/sale/invoice/{id}/commercial-print', [SaleDetails::class, 'commercialPrint'])
     ->name('premiertax.sale.commercial-print');
Route::get('/premiertax/create-storage-link', [StorageLinkController::class, 'createLink']);

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/premiertax/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard.admin');
    // Route::get('/premiertax/create_account', [CreateAccountController::class, 'index'])->name('create_account.list');
    // Route::get('/premiertax/create_account/reports', [CreateAccountController::class, 'reports'])->name('create_account.reports');
    Route::post('/premiertax/create_account', [CreateAccountController::class, 'store'])->name('create_account.store');
    // Route::get('/premiertax/create_account/delete/{id}', [CreateAccountController::class, 'delete'])->name('create_account.delete');
    // Route::get('/premiertax/create_account/edit/{id}', [CreateAccountController::class, 'edit'])->name('create_account.edit');
    // Route::put('/premiertax/create_account/update/{id}', [CreateAccountController::class, 'update'])->name('create_account.update');
    
    Route::resource('/premiertax/Supplier', PartyMemberController::class)->names('parties');

});


Route::resource('/premiertax/Customers', CustomerController::class)->names('custommer');
Route::middleware('auth')->group(function () {
    Route::resource('/premiertax/users',UserManagement::class)->names('users');
    Route::resource('/premiertax/companies', CompaniesController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->names('premiertax.companies');
    
    
    Route::resource('/premiertax/Customers', CustomerController::class)->names('custommer');
    Route::get('/premiertax/salary_calc', [SalaryCalculatorController::class, 'index'])->name('salary_calc.list');
    Route::get('/premiertax/cust', [CustomerController::class, 'index'])->name('cust.index');
    Route::post('/premiertax/salary_calc/get-data', [SalaryCalculatorController::class, 'getSalaryData'])->name('salary_calc.get_data');
    
    
    Route::get('/premiertax/attendence_form', [AttendenceFormController::class, 'index'])->name('attendence_form.list');
    Route::post('/premiertax/attendence_form', [AttendenceFormController::class, 'store'])->name('attendence_form.store');
    Route::get('/premiertax/attendence_form/reports', [AttendenceFormController::class, 'reports'])->name('attendence_form.reports');
    Route::delete('/premiertax/attendence_form/{id}', [AttendenceFormController::class, 'destroy'])->name('attendence_form.destroy');
    Route::get('/premiertax/attendence_form/{id}/edit', [AttendenceFormController::class, 'edit'])->name('attendence_form.edit');
    Route::put('/premiertax/attendence_form/{id}', [AttendenceFormController::class, 'update'])->name('attendence_form.update');
    
    Route::get('/premiertax/check-attendance-status', [AttendenceFormController::class, 'checkAttendanceStatus'])->name('check.attendance.status');
    
    
    Route::get('/premiertax/get-boxboard-details/{item_id}', [JobSheetController::class, 'getBoxboardDetails']);
    Route::get('/premiertax/get-ink-details/{item_id}', [JobSheetController::class, 'getinkDetails'])->name('getinkDetails');
    
    Route::get('/premiertax/get-lamination-details', [JobSheetController::class, 'getLaminationDetails']);
    Route::get('/premiertax/get-glue-details/{item_id}', [JobSheetController::class, 'getglueDetails']);
    Route::get('/premiertax/get-shipper-details/{item_id}', [JobSheetController::class, 'getshipperDetails']);
    Route::get('/premiertax/job_sheet', [JobSheetController::class, 'index'])->name('job.index');
    Route::post('/premiertax/job_sheet/store', [JobSheetController::class, 'store'])->name('job.store');
    Route::get('/premiertax/job_sheet/report', [JobSheetController::class, 'report'])->name('job.report');
    Route::delete('/premiertax/job-details', [JobSheetController::class, 'destroy'])->name('job-details.destroy');
    Route::get('/premiertax/job-details/{v_no}/edit', [JobSheetController::class, 'edit'])->name('job-details.edit');
    Route::put('/premiertax/job-details/{v_no}', [JobSheetController::class, 'update'])->name('job-details.update');
    Route::get('/premiertax/get-product-details', [JobSheetController::class, 'getProductDetails'])->name('get.product.details');
    
    Route::get('/premiertax/get-products/{customerId}', [JobSheetController::class, 'getProducts']);
    Route::get('/premiertax/fetch-custom-rate', [JobSheetController::class, 'fetchRate'])->name('fetch.custom.rate');
    Route::get('/premiertax/fetch-shipper-stock', [JobSheetController::class, 'fetchShipperStock'])->name('fetch.shipper.stock');
    Route::get('/premiertax/fetch-corrugation-stock', [JobSheetController::class, 'fetchCorrugationStock'])->name('fetch.corrugation.stock');
    
    
    Route::get('/premiertax/daily_statement/reports', [DailyStatementController::class, 'reports'])->name('daily_statement.reports');
    
    Route::get('/premiertax/expense/reports', [ExpenseController::class, 'reports'])->name('expense.reports');
    
    
    
    Route::get('/premiertax/general/job/sheet', [GeneralJobSheetController::class, 'index'])->name('general_job_sheet.list');
    Route::post('/premiertax/general-job-sheet', [GeneralJobSheetController::class, 'store'])->name('general-job-sheet.store');
    Route::get('/premiertax/general-job-sheet/report', [GeneralJobSheetController::class, 'report'])->name('general_job_sheet.report');
    Route::delete('/premiertax/general-job-sheet/{id}', [GeneralJobSheetController::class, 'destroy'])->name('general_job_sheet.destroy');
    Route::get('/premiertax/get-purchase-items', [GeneralJobSheetController::class, 'getPurchaseItems']);
    Route::get('/premiertax/get-purchase-item-details', [GeneralJobSheetController::class, 'getPurchaseItemDetails']);
    Route::get('/premiertax/general-job-sheet/{id}/edit', [GeneralJobSheetController::class, 'edit'])->name('general_job_sheet.edit');
    Route::put('/premiertax/general-job-sheet/{id}', [GeneralJobSheetController::class, 'update'])->name('general_job_sheet.update');
    
    
    Route::get('/premiertax/general/delivery/challan', [GeneralDeliveryChallanController::class, 'index'])->name('general_delivery_challan.list');
    Route::get('/premiertax/get-general-job-sheet-data', [GeneralDeliveryChallanController::class, 'getGeneralJobSheetData']);
    Route::post('/premiertax/general/delivery/challan/store', [GeneralDeliveryChallanController::class, 'store'])->name('general_delivery_challan.store');
    Route::get('/premiertax/general/delivery/challan/report', [GeneralDeliveryChallanController::class, 'report'])->name('general_delivery_challan.report');
    Route::delete('/premiertax/general-delivery-challan/{id}', [GeneralDeliveryChallanController::class, 'destroy'])->name('general_delivery_challan.destroy'); 
    Route::get('/premiertax/general-delivery-challan/{id}/edit', [GeneralDeliveryChallanController::class, 'edit'])->name('general_delivery_challan.edit'); 
    Route::put('/premiertax/general-delivery-challan//{id}', [GeneralDeliveryChallanController::class, 'update'])->name('general_delivery_challan.update'); 
     
     
    Route::get('/premiertax/general/billing', [GeneralBillingController::class, 'index'])->name('general_billing.list');
    Route::post('/premiertax/general/billing/store', [GeneralBillingController::class, 'store'])->name('general_billing.store');  
    Route::get('/premiertax/general/billing/report', [GeneralBillingController::class, 'report'])->name('general_billing.report');  
        Route::delete('/premiertax/general/billing/{id}', [GeneralBillingController::class, 'destroy'])->name('general_billing.destroy'); 
    Route::get('/premiertax/get-voucher-numbers/{partyId}', [GeneralBillingController::class, 'getVoucherNumbers'])->name('get.voucher.numbers');
    Route::get('/premiertax/get-voucher-details/{voucherNo}', [GeneralBillingController::class, 'getVoucherDetails']);
    Route::get('/premiertax/check-existing-billings', [GeneralBillingController::class, 'checkExistingBillings']);
     
     
     
    Route::get('/premiertax/boxboard/wage', [WageBoxboardController::class, 'index'])->name('boxboard_wage.list'); 
    Route::get('/premiertax/boxboard/wage/report', [WageBoxboardController::class, 'report'])->name('boxboard_wage.report'); 
    Route::get('/premiertax/boxboard/wage/vouchers/{employee_id}', [WageBoxboardController::class, 'getVouchersByEmployee'])->name('boxboard_wage.vouchers'); 
    Route::get('/premiertax/boxboard/wage/details/{employee_id}/{v_no}', [WageBoxboardController::class, 'getVoucherDetails'])->name('boxboard_wage.details'); 
    Route::post('/premiertax/boxboard/wage/store', [WageBoxboardController::class, 'store'])->name('boxboard_wage.store');  
    
     Route::delete('/premiertax/boxboard/wage/store/{id}', [WageBoxboardController::class, 'destroy'])->name('boxboard_wage.destroy');  
     
    Route::get('/premiertax/reports/stock', [ReportStockController::class, 'reports'])->name('report.stock');
    
    Route::get('/premiertax/purchase/reports', [ReportsController::class, 'reports'])->name('purchase.reports');
    
    
    Route::get('/premiertax/sale/reports', [SReportsController::class, 'reports'])->name('sale.reports');
    Route::get('/premiertax/bank_cash/reports', [BankCashReportController::class, 'reports'])->name('bank_cash.reports');
    
    Route::get('/premiertax/get-item-details/{id}', [PaymentInvoiceController::class, 'getItemDetails'])->name('getItemDetails');
    Route::post('/premiertax/update-status/{id}', [CashController::class, 'updateStatus'])->name('cash.updateStatus');
   
    Route::get('/premiertax/invoice/dashboard', [DashboardController::class, 'user_index'])->name('dashboard.user');
    Route::get('/premiertax/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    
    Route::get('/premiertax/department', [DepartmentController::class, 'index'])->name('department.list');
    Route::get('/premiertax/department/create', [DepartmentController::class, 'create'])->name('department.create');
    Route::post('/premiertax/department', [DepartmentController::class, 'store'])->name('department.store');
    Route::get('/premiertax/department/{id}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::post('/premiertax/department/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/premiertax/department/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
    
    Route::get('/premiertax/employee', [EmployeeController::class, 'index'])->name('employee.list');
    
    Route::get('/premiertax/employees', [EmployeesController::class, 'index'])->name('employees.list');
    Route::post('/premiertax/employees', [EmployeesController::class, 'store'])->name('employees.store');
    Route::get('/premiertax/employees/reports', [EmployeesController::class, 'reports'])->name('employees.reports');
    Route::delete('/premiertax/employees/{id}', [EmployeesController::class, 'destroy'])->name('employees.destroy');
    Route::get('/premiertax/employees/{id}/edit', [EmployeesController::class, 'edit'])->name('employees.edit');
    Route::put('/premiertax/employees/{id}', [EmployeesController::class, 'update'])->name('employees.update');
   Route::get('/premiertax/extra-times/{id}', [EmployeesController::class, 'getRate']);
    
    Route::get('/premiertax/employee_type', [EmployeeTypeController::class, 'index'])->name('employee_type.list');
    Route::post('/premiertax/employee_type', [EmployeeTypeController::class, 'store'])->name('employee_type.store');
    Route::get('/premiertax/get-employee-details/{id}', [EmployeeTypeController::class, 'getEmployeeDetails']);
    
    
    Route::get('/premiertax/employee_type/reports', [EmployeeTypeController::class, 'reports'])->name('employee_type.reports');
    Route::get('/premiertax/employee_type/{id}/edit', [EmployeeTypeController::class, 'edit'])->name('employee_type.edit');
    Route::put('/premiertax/employee_type/{id}', [EmployeeTypeController::class, 'update'])->name('employee_type.update');
    Route::delete('/premiertax/employee_type/{id}', [EmployeeTypeController::class, 'destroy'])->name('employee_type.destroy');
    
    Route::get('/premiertax/category', [CategoryController::class, 'index'])->name('category.list');
    Route::post('/premiertax/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/premiertax/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/premiertax/category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/premiertax/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::get('/premiertax/category/create', [CategoryController::class, 'create'])->name('category.create');
    
    Route::get('/premiertax/bank', [BankController::class, 'index'])->name('bank.list');
    Route::post('/premiertax/bank', [BankController::class, 'store'])->name('bank.store');
    
    Route::get('/premiertax/erp_param', [ErpParamController::class, 'index'])->name('erp_param.list');
    Route::get('/premiertax/erp_param/create', [ErpParamController::class, 'create'])->name('erp_param.create');
    Route::post('/premiertax/erp_param', [ErpParamController::class, 'store'])->name('erp_param.store');
    Route::get('/premiertax/erp_param/{id}/edit', [ErpParamController::class, 'edit'])->name('erp_param.edit');
    Route::put('/premiertax/erp_param/{id}', [ErpParamController::class, 'update'])->name('erp_param.update');
    Route::delete('/premiertax/erp_param/{id}', [ErpParamController::class, 'destroy'])->name('erp_param.destroy');

    Route::get('/premiertax/cash', [CashController::class, 'index'])->name('cash.list');
    Route::get('/premiertax/cash/reports', [CashController::class, 'reports'])->name('cash.reports');
    Route::post('/premiertax/cash', [CashController::class, 'store'])->name('cash.store');
    Route::put('/premiertax/cash/{v_no}/update', [CashController::class, 'update'])->name('cash.update'); // Use PUT for update
    Route::get('/premiertax/cash/{v_no}/edit', [CashController::class, 'edit'])->name('cash.edit');
    Route::get('/premiertax/cash/{id}', [CashController::class, 'destroy'])->name('cash.destroy');
    Route::delete('/premiertax/cash-delete/{id}', [CashController::class, 'delete'])->name('cash.delete');
    Route::get('/premiertax/cash/create', [CashController::class, 'create'])->name('cash.create');

    Route::get('/premiertax/payment', [PaymentController::class, 'index'])->name('payment.list');
    Route::get('/premiertax/paymentreports', [PaymentController::class, 'reports'])->name('payment.reports');
    Route::post('/premiertax/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/premiertax/payment/{v_no}/edit', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::put('/premiertax/payment/{v_no}/update', [PaymentController::class, 'update'])->name('payment.update');
    Route::get('/premiertax/payment/{id}', [PaymentController::class, 'destroy'])->name('payment.destroy');
    Route::delete('/premiertax/payment-delete/{id}', [PaymentController::class, 'delete'])->name('payment.delete');
    Route::get('/premiertax/payment/create', [PaymentController::class, 'create'])->name('payment.create');

   
    Route::get('/premiertax/bank_payment', [BankPaymentController::class, 'index'])->name('bank_payment.list');
    Route::post('/premiertax/bank_payment', [BankPaymentController::class, 'store'])->name('bank_payment.store');
    Route::get('/premiertax/bank_payment/reports', [BankPaymentController::class, 'reports'])->name('bank_payment.reports');
    Route::get('/premiertax/bank_payment/{v_no}/edit', [BankPaymentController::class, 'edit'])->name('bank_payment.edit');
    Route::put('/premiertax/bank_payment/{v_no}/update', [BankPaymentController::class, 'update'])->name('bank_payment.update');
    Route::get('/premiertax/bank_payment/{id}', [BankPaymentController::class, 'destroy'])->name('bank_payment.destroy');
    Route::delete('/premiertax/bank_payment-delete/{id}', [BankPaymentController::class, 'delete'])->name('bank_payment.delete');

    Route::get('/premiertax/gate_ex', [GateExController::class, 'index'])->name('gate_ex.list');
    Route::post('/premiertax/gate_ex', [GateExController::class, 'store'])->name('gate_ex.store');
    Route::get('/premiertax/gate_ex/reports', [GateExController::class, 'reports'])->name('gate_ex.reports');
    Route::get('/premiertax/gate_ex/{v_no}/edit', [GateExController::class, 'edit'])->name('gate_ex.edit');
    Route::put('/premiertax/gate_ex/{v_no}/update', [GateExController::class, 'update'])->name('gate_ex.update');
    Route::get('/premiertax/gate_ex/{id}', [GateExController::class, 'destroy'])->name('gate_ex.destroy');
    Route::delete('/premiertax/gate_ex-delete/{id}', [GateExController::class, 'delete'])->name('gate_ex.delete');
    
    
    Route::get('/premiertax/office_cash', [OfficeCashController::class, 'index'])->name('office_cash.list');
    Route::post('/premiertax/office_cash', [OfficeCashController::class, 'store'])->name('office_cash.store');
    Route::get('/premiertax/office_cash/reports', [OfficeCashController::class, 'reports'])->name('office_cash.reports');
    Route::get('/premiertax/office_cash/{v_no}/edit', [OfficeCashController::class, 'edit'])->name('office_cash.edit');
    Route::put('/premiertax/office_cash/{v_no}/update', [OfficeCashController::class, 'update'])->name('office_cash.update');
    Route::get('/premiertax/office_cash/{id}', [OfficeCashController::class, 'destroy'])->name('office_cash.destroy');
    Route::delete('/premiertax/office_cash-delete/{id}', [OfficeCashController::class, 'delete'])->name('office_cash.delete');
    
    Route::get('/premiertax/bank_recipt', [BankReciptController::class, 'index'])->name('bank_recipt.list');
    Route::post('/premiertax/bank_recipt', [BankReciptController::class, 'store'])->name('bank_recipt.store');
    Route::get('/premiertax/bank_recipt/reports', [BankReciptController::class, 'reports'])->name('bank_recipt.reports');
    Route::get('/premiertax/bank_recipt/{id}/edit', [BankReciptController::class, 'edit'])->name('bank_recipt.edit');
    Route::put('/premiertax/bank_recipt/{v_no}/update', [BankReciptController::class, 'update'])->name('bank_recipt.update');
    Route::get('/premiertax/bank_recipt/{id}', [BankReciptController::class, 'destroy'])->name('bank_recipt.destroy');
    Route::delete('/premiertax/bank_recipt-delete/{id}', [BankReciptController::class, 'delete'])->name('bank_recipt.delete');

    Route::get('/premiertax/ledger', [LedgerController::class, 'index'])->name('ledger.list');
    Route::get('/premiertax/ledger_detail', [LedgerDetailController::class, 'index'])->name('ledger_detail.list');

    Route::get('/premiertax/payables', [PayableController::class, 'index'])->name('payables.list');

    Route::get('/premiertax/recieveables', [RecieveableableController::class, 'index'])->name('recieveables.list');


    Route::get('/premiertax/journal_voucher', [JournalVoucherController::class, 'index'])->name('journal_voucher.list');
    Route::post('/premiertax/journal_voucher', [JournalVoucherController::class, 'store'])->name('journal_voucher.store');
    Route::get('/premiertax/journal_voucher/reports', [JournalVoucherController::class, 'reports'])->name('journal_voucher.reports');
    Route::get('/premiertax/journal_voucher/{v_no}/edit', [JournalVoucherController::class, 'edit'])->name('journal_voucher.edit');
    Route::get('/premiertax/journal_voucher/delete/{id}', [JournalVoucherController::class, 'delete'])->name('journal_voucher.delete');
    Route::put('/premiertax/journal_voucher/{v_no}/update', [JournalVoucherController::class, 'update'])->name('journal_voucher.update');
    Route::delete('/premiertax/journal_voucher/{id}', [JournalVoucherController::class, 'destroy'])->name('journal_voucher.destroy');

    Route::get('/premiertax/open_bal', [OpenBalController::class, 'index'])->name('open_bal.list');
    Route::post('/premiertax/open_bal', [OpenBalController::class, 'store'])->name('open_bal.store');
    Route::get('/premiertax/open_bal/reports', [OpenBalController::class, 'reports'])->name('open_bal.reports');
    Route::get('/premiertax/open_bal/{v_no}/edit', [OpenBalController::class, 'edit'])->name('open_bal.edit');
    Route::get('/premiertax/open_bal/delete/{id}', [OpenBalController::class, 'delete'])->name('open_bal.delete');
    Route::put('/premiertax/open_bal/{v_no}/update', [OpenBalController::class, 'update'])->name('open_bal.update');
    Route::delete('/premiertax/open_bal/{id}', [OpenBalController::class, 'destroy'])->name('open_bal.destroy');
    
    
    Route::get('/premiertax/employee/create', [EmployeeController::class, 'create'])->name('employee.create');
    Route::get('/premiertax/employee/create1', [EmployeeController::class, 'create1'])->name('employee.create1');
    Route::post('/premiertax/employee', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/premiertax/employee/{id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('/premiertax/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/premiertax/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    Route::post('/premiertax/account/level1', [Level1Controller::class, 'store'])->name('level1.store');
    Route::get('/premiertax/account/level1', [Level1Controller::class, 'index'])->name('level1.list');
    Route::get('/premiertax/account/level1/create', [Level1Controller::class, 'create'])->name('level1.create');
    Route::get('/premiertax/account/level1/{id}/edit', [Level1Controller::class, 'edit'])->name('level1.edit');
    Route::post('/premiertax/account/level1/{id}', [Level1Controller::class, 'update'])->name('level1.update');
    Route::delete('/premiertax/account/level1/{id}', [Level1Controller::class, 'destroy'])->name('level1.destroy');

    Route::post('/premiertax/account/level2', [Level2Controller::class, 'store'])->name('level2.store');
    Route::get('/premiertax/account/level2', [Level2Controller::class, 'index'])->name('level2.list');
    Route::get('/premiertax/account/level2/create', [Level2Controller::class, 'create'])->name('level2.create');
    Route::get('/premiertax/account/level2/{id}/edit', [Level2Controller::class, 'edit'])->name('level2.edit');
    Route::post('/premiertax/account/level2/{id}', [Level2Controller::class, 'update'])->name('level2.update');
    Route::delete('/premiertax/account/level2/{id}', [Level2Controller::class, 'destroy'])->name('level2.destroy');

    Route::post('/premiertax/account/level3', [Level3Controller::class, 'store'])->name('level3.store');
    Route::get('/premiertax/account/level3', [Level3Controller::class, 'index'])->name('level3.list');
    Route::get('/premiertax/account/level3/create', [Level3Controller::class, 'create'])->name('level3.create');

    Route::post('/premiertax/account/group', [GroupController::class, 'store'])->name('group.store');
    Route::get('/premiertax/account/group', [GroupController::class, 'index'])->name('group.list');
    Route::get('/premiertax/account/group/create', [GroupController::class, 'create'])->name('group.create');

    Route::post('/premiertax/account/a_master', [AccountMasterController::class, 'store'])->name('amaster.store');
    Route::get('/premiertax/account/a_master', [AccountMasterController::class, 'index'])->name('amaster.list');
    Route::get('/premiertax/account/reports', [AccountMasterController::class, 'reports'])->name('account.reports');
    Route::get('/premiertax/account/a_master/create', [AccountMasterController::class, 'create'])->name('amaster.create');
    Route::get('/premiertax/account/a_master/{id}/edit', [AccountMasterController::class, 'edit'])->name('amaster.edit');
    Route::post('/premiertax/account/a_master/{id}', [AccountMasterController::class, 'update'])->name('amaster.update');
    Route::delete('/premiertax/account/a_master/{id}', [AccountMasterController::class, 'destroy'])->name('amaster.destroy');

    Route::post('/premiertax/amount/party', [PartyController::class, 'store'])->name('party.store');
    Route::get('/premiertax/account/party', [PartyController::class, 'index'])->name('party.list');
    Route::get('/premiertax/account/party/create', [PartyController::class, 'create'])->name('party.create');
    Route::get('/premiertax/account/bill', [BillController::class, 'index'])->name('bill.list');
    Route::post('/premiertax/account/bill', [BillController::class, 'store'])->name('bill.store');
    Route::get('/premiertax/account/bill/create', [BillController::class, 'create'])->name('bill.create');
    Route::get('/premiertax/account/loan', [LoanController::class, 'index'])->name('loan.list');
    Route::post('/premiertax/account/loan', [LoanController::class, 'store'])->name('loan.store');
    Route::get('/premiertax/account/loan/create', [LoanController::class, 'create'])->name('loan.create');

    Route::get('/premiertax/inventory/itemmaster', [InventoryController::class, 'index_itemmaster'])->name('inventory.itemmaster.list');
    Route::get('/premiertax/inventory/itemmaster/{id}/edit', [InventoryController::class, 'itemmasteredit'])->name('inventory.itemmaster.edit');
    Route::post('/premiertax/inventory/itemmaster/{id}', [InventoryController::class, 'itemmasterupdate'])->name('inventory.itemmaster.update');
    Route::delete('/premiertax/inventory/itemmaster/{id}', [InventoryController::class, 'itemmasterdestroy'])->name('inventory.itemmaster.destroy');
   
    Route::get('/premiertax/inventory/itemtype', [InventoryController::class, 'index_itemtype'])->name('inventory.itemtype.list');
    Route::post('/premiertax/inventory/itemmaster', [InventoryController::class, 'itemmaster'])->name('inventory.itemmaster');
    Route::post('/premiertax/inventory/itemtype', [InventoryController::class, 'itemtype'])->name('inventory.itemtype');
    Route::get('/premiertax/inventory/itemtype/{id}/edit', [InventoryController::class, 'itemtypeedit'])->name('inventory.itemtype.edit');
    Route::post('/premiertax/inventory/itemtype/{id}', [InventoryController::class, 'itemtypeupdate'])->name('inventory.itemtype.update');
    Route::delete('/premiertax/inventory/itemtype/{id}', [InventoryController::class, 'itemtypedestroy'])->name('inventory.itemtype.destroy');
    Route::get('/premiertax/inventory/create/itemmaster', [InventoryController::class, 'createitemmaster'])->name('inventory.create.itemmaster');
    Route::get('/premiertax/inventory/itemLog', [InventoryController::class, 'itemlogList'])->name('inventory.item_log');


    Route::get('/premiertax/inventory/create/itemtype', [InventoryController::class, 'createitemtype'])->name('inventory.create.itemtype');
    Route::post('/premiertax/inventory/boxboard', [InventoryController::class, 'boxboard'])->name('inventory.boxboard');
    Route::post('/premiertax/inventory/lamination', [InventoryController::class, 'lamination'])->name('inventory.lamination');
    Route::post('/premiertax/inventory/corrugation', [InventoryController::class, 'corrugation'])->name('inventory.corrugation');
    Route::post('/premiertax/inventory/plates', [InventoryController::class, 'plates'])->name('inventory.plates');
    Route::post('/premiertax/inventory/dye', [InventoryController::class, 'dye'])->name('inventory.dye');
    Route::post('/premiertax/inventory/ink', [InventoryController::class, 'ink'])->name('inventory.ink');

    
    Route::get('/premiertax/stock_report', [StockReportController::class, 'index'])->name('stock_report.list');
    Route::post('/premiertax/stock_report/store', [StockReportController::class, 'store'])->name('stock_report.store');
    Route::get('/premiertax/stock_report/reports', [StockReportController::class, 'reports'])->name('stock_report.reports');


    Route::get('/premiertax/delivery_challan', [DeliveryChallanController::class, 'index'])->name('delivery_challan.list');
    Route::get('/premiertax/get-products/{partyId}', [DeliveryChallanController::class, 'getProducts']);

    Route::get('/premiertax/delivery_challan/reports', [DeliveryChallanController::class, 'reports'])->name('delivery_challan.reports');
    Route::post('/premiertax/delivery_challan', [DeliveryChallanController::class, 'store'])->name('delivery_challan.store');
    Route::get('/premiertax/delivery_challan/edit/{v_no}', [DeliveryChallanController::class, 'edit'])->name('delivery_challan.edit');
    Route::put('/premiertax/delivery_challan/update/{id}', [DeliveryChallanController::class, 'update'])->name('delivery_challan.update');
    Route::get('/premiertax/delivery_challan/{v_no}/delete', [DeliveryChallanController::class, 'destroy'])->name('delivery_challan.destroy');
    Route::delete('/premiertax/delivery_challan/{id}/del', [DeliveryChallanController::class, 'delete'])->name('delivery_challan.delete');

    Route::get('/premiertax/delivery_challan/editCon/{v_no}', [DeliveryChallanController::class, 'editCon'])->name('delivery_challan.editDel');
    Route::put('/premiertax/delivery_challan/{v_no}/updateCon', [DeliveryChallanController::class, 'updateCon'])->name('delivery_challan.updateDel');

    Route::get('/premiertax/get-aid/{accountId}', [ConfectioneryController::class, 'getAid']);
    Route::get('/premiertax/confectionery', [ConfectioneryController::class, 'index'])->name('confectionery.list');
    Route::post('/premiertax/confectionery', [ConfectioneryController::class, 'store'])->name('confectionery.store');
    Route::get('/premiertax/confectionery/reports', [ConfectioneryController::class, 'reports'])->name('confectionery.reports');
    Route::get('/premiertax/confectionery/edit/{v_no}', [ConfectioneryController::class, 'edit'])->name('confectionery.edit');
    Route::put('/premiertax/confectionery/update/{id}', [ConfectioneryController::class, 'update'])->name('confectionery.update');
    Route::get('/premiertax/confectionery/{v_no}/delete', [ConfectioneryController::class, 'destroy'])->name('confectionery.destroy');
    Route::delete('/premiertax/confectionery/{id}/del', [ConfectioneryController::class, 'delete'])->name('confectionery.delete');

    

    Route::get('/premiertax/confectionery/editCon/{v_no}', [ConfectioneryController::class, 'editCon'])->name('confectionery.editCon');
    Route::put('/premiertax/confectionery/{v_no}/updateCon', [ConfectioneryController::class, 'updateCon'])->name('confectionery.updateCon');
    
    Route::get('/premiertax/wastage_sale', [WastageSaleController::class, 'index'])->name('wastage_sale.list');
    Route::get('/premiertax/wastage_sale/reports', [WastageSaleController::class, 'reports'])->name('wastage_sale.reports');
    Route::post('/premiertax/wastage_sale', [WastageSaleController::class, 'store'])->name('wastage_sale.store');
    Route::get('/premiertax/wastage_sale/{v_no}/delete', [WastageSaleController::class, 'destroy'])->name('wastage_sale.destroy');
    Route::delete('/premiertax/wastage_sale/{id}/delete', [WastageSaleController::class, 'delete'])->name('wastage_sale.delete');
    Route::get('/premiertax/wastage_sale/edit/{v_no}', [WastageSaleController::class, 'edit'])->name('wastage_sale.edit');
    Route::put('/premiertax/wastage_sale/update/{id}', [WastageSaleController::class, 'update'])->name('wastage_sale.update');
    
    
    
    Route::get('/premiertax/dye_purchase', [DyePurchaseController::class, 'index'])->name('dye_purchase.list');
    Route::get('/premiertax/dye_purchase/reports', [DyePurchaseController::class, 'reports'])->name('dye_purchases.reports');
    Route::post('/premiertax/dye_purchase', [DyePurchaseController::class, 'store'])->name('dye_purchase.store');
    Route::get('/premiertax/dye_purchase/{v_no}/delete', [DyePurchaseController::class, 'destroy'])->name('dye_purchase.destroy');
    Route::delete('/premiertax/dye_purchase/{id}/delete', [DyePurchaseController::class, 'delete'])->name('dye_purchase.delete');
    Route::get('/premiertax/dye_purchase/edit/{v_no}', [DyePurchaseController::class, 'edit'])->name('dye_purchase.edit');
    Route::put('/premiertax/dye_purchase/update/{id}', [DyePurchaseController::class, 'update'])->name('dye_purchase.update');
    
    Route::get('/premiertax/dye_purchase/editDye/{v_no}', [DyePurchaseController::class, 'editDye'])->name('dye_purchase.editDye');
    Route::put('/premiertax/dye_purchase/{v_no}/updateDye', [DyePurchaseController::class, 'updateDye'])->name('dye_purchase.updateDye');
    
    
    Route::get('/premiertax/wastage/reports', [WastageController::class, 'reports'])->name('wastage.reports');

    Route::get('/premiertax/get-vnoss/{accountId}', [SaleInvoiceController::class, 'getVnoss']);
    Route::get('/premiertax/get-entry-detailss/{vno}', [SaleInvoiceController::class, 'getEntryDetailss']);

    Route::get('/premiertax/pharma_billing', [SaleInvoiceController::class, 'index'])->name('pharma_billing.list');
    Route::get('/premiertax/pharma_billing/reports', [SaleInvoiceController::class, 'reports'])->name('pharma_billing.reports');
    Route::post('/premiertax/pharma_billing', [SaleInvoiceController::class, 'store'])->name('pharma_billing.store');
    Route::delete('/premiertax/pharma-billing/{billing_no}/del', [SaleInvoiceController::class, 'destroy'])->name('pharma_billing.destroy');


    Route::get('/premiertax/get-vnos/{accountId}', [ConfectBillingController::class, 'getVnos']);
    Route::get('/premiertax/get-entry-details/{vno}', [ConfectBillingController::class, 'getEntryDetails']);

    Route::get('/premiertax/confect_billing', [ConfectBillingController::class, 'index'])->name('confect_billing.list');
    Route::get('/premiertax/confect_billing/reports', [ConfectBillingController::class, 'reports'])->name('confect_billing.reports');
    Route::post('/premiertax/confect_billing', [ConfectBillingController::class, 'store'])->name('confect_billing.store');
    Route::delete('/premiertax/confect-billing/{billing_no}/del', [ConfectBillingController::class, 'destroy'])
    ->name('confect_billing.destroy');



    Route::get('/premiertax/gate_pass_in', [GatePassInController::class, 'index'])->name('gate_pass_in.list');
    Route::get('/premiertax/gate_pass_in/reports', [GatePassInController::class, 'reports'])->name('gate_pass_in.reports');
    Route::post('/premiertax/gate_pass_in', [GatePassInController::class, 'store'])->name('gate_pass_in.store');
    Route::get('/premiertax/gate_pass_in/{v_no}/delete', [GatePassInController::class, 'destroy'])->name('gate_pass_in.destroy');
    Route::delete('/premiertax/gate_pass_in/{id}/delete', [GatePassInController::class, 'delete'])->name('gate_pass_in.delete');
    Route::get('/premiertax/gate_pass_in/edit/{v_no}', [GatePassInController::class, 'edit'])->name('gate_pass_in.edit');
    Route::put('/premiertax/gate_pass_in/update/{id}', [GatePassInController::class, 'update'])->name('gate_pass_in.update');

    Route::get('/premiertax/gate_pass_out', [GatePassOutController::class, 'index'])->name('gate_pass_out.list');
    Route::get('/premiertax/gate_pass_out/reports', [GatePassOutController::class, 'reports'])->name('gate_pass_out.reports');
    Route::post('/premiertax/gate_pass_out', [GatePassOutController::class, 'store'])->name('gate_pass_out.store');
    Route::get('/premiertax/gate_pass_out/{v_no}/delete', [GatePassOutController::class, 'destroy'])->name('gate_pass_out.destroy');
    Route::delete('/premiertax/gate_pass_out/{id}/delete', [GatePassOutController::class, 'delete'])->name('gate_pass_out.delete');
    Route::get('/premiertax/gate_pass_out/edit/{v_no}', [GatePassOutController::class, 'edit'])->name('gate_pass_out.edit');
    Route::put('/premiertax/gate_pass_out/update/{id}', [GatePassOutController::class, 'update'])->name('gate_pass_out.update');

    Route::get('/premiertax/cheque_receipts', [ChequeReceiptsController::class, 'index'])->name('cheque.index');
    Route::get('/premiertax/cheque_receipts/reports', [ChequeReceiptsController::class, 'reports'])->name('cheque_receipts.reports');
    Route::post('/premiertax/cheque_receipts', [ChequeReceiptsController::class, 'store'])->name('cheque_receipts.store');
    Route::delete('/premiertax/cheque-receipts/{id}', [ChequeReceiptsController::class, 'destroy'])->name('chequeReceipts.destroy');
    Route::get('/premiertax/cheque_receipts/edit/{v_no}', [ChequeReceiptsController::class, 'edit'])->name('cheque_receipts.edit');
    Route::put('/premiertax/cheque_receipts/update/{id}', [ChequeReceiptsController::class, 'update'])->name('cheque_receipts.update');
    Route::get('/premiertax/cheque_receipt/{v_no}/delete', [ChequeReceiptsController::class, 'del'])->name('cheque_receipts.del');

    Route::get('/premiertax/country', [CountryController::class, 'index'])->name('country.index');
    Route::post('/premiertax/country', [CountryController::class, 'store'])->name('country.store');
    Route::get('/premiertax/country/add_country', [CountryController::class, 'list'])->name('country.list');
    Route::delete('/premiertax/country/{id}', [CountryController::class, 'destroy'])->name('country.destroy');
    
    Route::get('/premiertax/custom', [CustomController::class, 'index'])->name('custom.index');
    Route::post('/premiertax/custom', [CustomController::class, 'store'])->name('custom.store');
    Route::get('/premiertax/custom/add_country', [CustomController::class, 'list'])->name('custom.list');
    Route::delete('/premiertax/custom/{id}', [CustomController::class, 'destroy'])->name('custom.destroy');

    Route::get('/premiertax/printing', [DepartmentSectionController::class, 'index'])->name('print.index');
    Route::post('/premiertax/printing', [DepartmentSectionController::class, 'store'])->name('print.store');
    Route::get('/premiertax/printing/edit/{id}', [DepartmentSectionController::class, 'edit'])->name('print.edit');
    Route::put('/premiertax/printing/{id}', [DepartmentSectionController::class, 'update'])->name('print.update');
    Route::get('/premiertax/printing/add_printing', [DepartmentSectionController::class, 'list'])->name('print.list');
    Route::delete('/premiertax/printing/{id}', [DepartmentSectionController::class, 'destroy'])->name('print.destroy');
    
    
    Route::get('/premiertax/designation', [DesignationController::class, 'index'])->name('designation.index');
    Route::post('/premiertax/designation', [DesignationController::class, 'store'])->name('designation.store');
    Route::get('/premiertax/designation/edit/{id}', [DesignationController::class, 'edit'])->name('designation.edit');
    Route::put('/premiertax/designation/{id}', [DesignationController::class, 'update'])->name('designation.update');
    Route::get('/premiertax/designation/add_printing', [DesignationController::class, 'list'])->name('designation.list');
    Route::delete('/premiertax/designation/{id}', [DesignationController::class, 'destroy'])->name('designation.destroy');
    
    
    Route::get('/premiertax/extra_time', [ExtraTimeController::class, 'index'])->name('extra_time.index');
    Route::post('/premiertax/extra_time', [ExtraTimeController::class, 'store'])->name('extra_time.store');
    Route::get('/premiertax/extra_time/edit/{id}', [ExtraTimeController::class, 'edit'])->name('extra_time.edit');
    Route::put('/premiertax/extra_time/{id}', [ExtraTimeController::class, 'update'])->name('extra_time.update');
    Route::get('/premiertax/extra_time/add_printing', [ExtraTimeController::class, 'list'])->name('extra_time.list');
    Route::delete('/premiertax/extra_time/{id}', [ExtraTimeController::class, 'destroy'])->name('extra_time.destroy');
    
    
    Route::get('/premiertax/process', [ProcessSectionController::class, 'index'])->name('process.index');
    Route::post('/premiertax/process', [ProcessSectionController::class, 'store'])->name('process.store');
    Route::get('/premiertax/process/add_process', [ProcessSectionController::class, 'list'])->name('process.list');
    Route::delete('/premiertax/process/{id}', [ProcessSectionController::class, 'destroy'])->name('process.destroy');
    
    Route::get('/premiertax/paste', [PasteSectionController::class, 'index'])->name('paste.index');
    Route::post('/premiertax/paste', [PasteSectionController::class, 'store'])->name('paste.store');
    Route::get('/premiertax/paste/add_paste', [PasteSectionController::class, 'list'])->name('paste.list');
    Route::delete('/premiertax/paste/{id}', [PasteSectionController::class, 'destroy'])->name('paste.destroy');
    
    
    Route::get('/premiertax/registration_form/add_product', [RegistrationFormController::class, 'index'])->name('registration_form.list');
    Route::post('/premiertax/registration_form/add_product', [RegistrationFormController::class, 'store'])->name('registration_form.store');
    Route::get('/premiertax/registration_form/reports', [RegistrationFormController::class, 'reports'])->name('registration_form.reports');
    Route::get('/premiertax/registration_form/edit/{id}', [RegistrationFormController::class, 'edit'])->name('registration_form.edit');
    Route::put('/premiertax/registration_form/update/{id}', [RegistrationFormController::class, 'update'])->name('registration_form.update');
    Route::delete('/premiertax/registration_form/{id}', [RegistrationFormController::class, 'destroy'])->name('registration_form.destroy');
    Route::delete('/premiertax/registration_form/remove-image/{id}', [RegistrationFormController::class, 'removeImage'])->name('remove.image');

    Route::get('/premiertax/product-log', [ProductLogController::class, 'index'])->name('product_log.index');
    Route::get('/premiertax/product-log/report', [ProductLogController::class, 'report'])->name('product_log.report');

    Route::get('/premiertax/profile', [UserController::class, 'profile'])->name('profile');
    
    
    Route::get('/premiertax/payment_invoice', [PaymentInvoiceController::class, 'index'])->name('payment_invoice.list');
    Route::get('/premiertax/payment_invoice/reports', [PaymentInvoiceController::class, 'reports'])->name('payment_invoice.reports');
    Route::post('/premiertax/payment_invoice', [PaymentInvoiceController::class, 'store'])->name('payment_invoice.store');
    Route::get('/premiertax/purchase-details/edit/{v_no}', [PaymentInvoiceController::class, 'edit'])->name('purchase_details.edit');
    Route::get('/premiertax/purchase-details/editBoxboard/{v_no}', [PaymentInvoiceController::class, 'editBoxboard'])->name('purchase_details.editBoxboard');
    Route::put('/premiertax/purchase-details/{v_no}/updateBoxboard', [PaymentInvoiceController::class, 'updateBoxboard'])->name('purchase_details.updateBoxboard');
    Route::put('/premiertax/purchase-details/{v_no}/update', [PaymentInvoiceController::class, 'update'])->name('purchase_details.update');
    Route::delete('/premiertax/purchase-details/{id}/delete', [PaymentInvoiceController::class, 'destroy'])->name('purchase_details.delete');
    Route::get('/premiertax/purchase-details/{id}/del', [PaymentInvoiceController::class, 'delete'])->name('purchase_details.destroy');

    Route::get('/premiertax/purchase_return', [PurchaseReturnController::class, 'index'])->name('purchase_return.list');
    Route::post('/premiertax/purchase_return', [PurchaseReturnController::class, 'store'])->name('purchase_return.store');
    Route::get('/premiertax/purchase_return/reports', [PurchaseReturnController::class, 'reports'])->name('purchase_return.reports');
    Route::get('/premiertax/purchase_return/{id}/delete', [PurchaseReturnController::class, 'destroy'])->name('purchase_return.destroy');
    Route::delete('/premiertax/purchase_return/{id}/del', [PurchaseReturnController::class, 'delete'])->name('purchase_return.delete');
    Route::get('/premiertax/purchase_return/edit/{v_no}', [PurchaseReturnController::class, 'edit'])->name('purchase_return.edit');
    Route::put('/premiertax/purchase_return/update/{id}', [PurchaseReturnController::class, 'update'])->name('purchase_return.update');

    Route::get('/premiertax/plate_purchase', [PlatePurchaseController::class, 'index'])->name('plate_purchase.list');
    Route::get('/premiertax/plate_purchase/reports', [PlatePurchaseController::class, 'reports'])->name('plate_purchase.reports');
    Route::get('/premiertax/get-products-by-country', [PlatePurchaseController::class, 'getProductsByCountry']);
    Route::post('/premiertax/plate_purchase', [PlatePurchaseController::class, 'store'])->name('plate_purchase.store');
    Route::get('/premiertax/plate_purchase/edit/{v_no}', [PlatePurchaseController::class, 'edit'])->name('plate_purchase.edit');
    Route::put('/premiertax/plate_purchase/update/{id}', [PlatePurchaseController::class, 'update'])->name('plate_purchase.update');
    Route::get('/premiertax/plate_purchase/{v_no}/delete', [PlatePurchaseController::class, 'destroy'])->name('plate_purchase.destroy');
    Route::delete('/premiertax/plate_purchase/{id}/del', [PlatePurchaseController::class, 'delete'])->name('plate_purchase.delete');
    
    
    
    Route::get('/premiertax/plate_return', [PlateReturnController::class, 'index'])->name('plate_return.list');
    Route::get('/premiertax/plate_return/reports', [PlateReturnController::class, 'reports'])->name('plate_return.reports');
    Route::post('/premiertax/plate_return', [PlateReturnController::class, 'store'])->name('plate_return.store');
    Route::delete('/premiertax/plate_return/{id}', [PlateReturnController::class, 'destroy'])->name('plate_return.destroy');

    Route::get('/premiertax/glue_purchase', [GluePurchaseController::class, 'index'])->name('glue_purchase.list');
    Route::get('/premiertax/glue_purchase/reports', [GluePurchaseController::class, 'reports'])->name('glue_purchase.reports');
    Route::post('/premiertax/glue_purchase', [GluePurchaseController::class, 'store'])->name('glue_purchase.store');
    Route::get('/premiertax/glue_purchase/edit/{v_no}', [GluePurchaseController::class, 'edit'])->name('glue_purchase.edit');
    Route::put('/premiertax/glue_purchase/update/{id}', [GluePurchaseController::class, 'update'])->name('glue_purchase.update');
    Route::get('/premiertax/glue_purchase/{v_no}/delete', [GluePurchaseController::class, 'destroy'])->name('glue_purchase.destroy');
    Route::delete('/premiertax/glue_purchase/{id}/del', [GluePurchaseController::class, 'delete'])->name('glue_purchase.delete');
     Route::get('/premiertax/glue_purchase/editBoxboard/{v_no}', [GluePurchaseController::class, 'editBoxboard'])->name('glue_purchase.editBoxboard');
    Route::put('/premiertax/glue_purchase/{v_no}/updateBoxboard', [GluePurchaseController::class, 'updateBoxboard'])->name('glue_purchase.updateBoxboard');

    
    Route::get('/premiertax/glue_return', [GlueReturnController::class, 'index'])->name('glue_return.list');
    Route::get('/premiertax/glue_return/reports', [GlueReturnController::class, 'reports'])->name('glue_return.reports');
    Route::post('/premiertax/glue_return', [GlueReturnController::class, 'store'])->name('glue_return.store');
    Route::delete('/premiertax/glue_return/{id}', [GlueReturnController::class, 'destroy'])->name('glue_return.destroy');
    
    
    
    
    Route::get('/premiertax/ink_purchase', [InkPurchaseController::class, 'index'])->name('ink_purchase.list');
    Route::get('/premiertax/ink_purchase/reports', [InkPurchaseController::class, 'reports'])->name('ink_purchase.reports');
    Route::post('/premiertax/ink_purchase', [InkPurchaseController::class, 'store'])->name('ink_purchase.store');
    Route::get('/premiertax/ink_purchase/edit/{v_no}', [InkPurchaseController::class, 'edit'])->name('ink_purchase.edit');
    Route::put('/premiertax/ink_purchase/update/{id}', [InkPurchaseController::class, 'update'])->name('ink_purchase.update');
    Route::get('/premiertax/ink_purchase/{v_no}/delete', [InkPurchaseController::class, 'destroy'])->name('ink_purchase.destroy');
    Route::delete('/premiertax/ink_purchase/{id}/del', [InkPurchaseController::class, 'delete'])->name('ink_purchase.delete');
     Route::get('/premiertax/ink_purchase/editBoxboard/{v_no}', [InkPurchaseController::class, 'editBoxboard'])->name('ink_purchase.editBoxboard');
    Route::put('/premiertax/ink_purchase/{v_no}/updateBoxboard', [InkPurchaseController::class, 'updateBoxboard'])->name('ink_purchase.updateBoxboard');


    
    Route::get('/premiertax/ink_return', [InkReturnController::class, 'index'])->name('ink_return.list');
    Route::get('/premiertax/ink_return/reports', [InkReturnController::class, 'reports'])->name('ink_return.reports');
    Route::post('/premiertax/ink_return', [InkReturnController::class, 'store'])->name('ink_return.store');
    Route::delete('/premiertax/ink_return/{id}', [InkReturnController::class, 'destroy'])->name('ink_return.destroy');
    
    
    
    Route::get('/premiertax/shippers_purchase', [ShipperPurchasesController::class, 'index'])->name('shipper_purchases.list');
    Route::get('/premiertax/shippers_purchase/reports', [ShipperPurchasesController::class, 'reports'])->name('shipper_purchases.reports');
    Route::post('/premiertax/shippers_purchase', [ShipperPurchasesController::class, 'store'])->name('shipper_purchases.store');
    Route::get('/premiertax/shippers_purchase/edit/{v_no}', [ShipperPurchasesController::class, 'edit'])->name('shipper_purchases.edit');
    Route::put('/premiertax/shippers_purchase/update/{id}', [ShipperPurchasesController::class, 'update'])->name('shipper_purchases.update');
    Route::get('/premiertax/shippers_purchase/{v_no}/delete', [ShipperPurchasesController::class, 'destroy'])->name('shipper_purchases.destroy');
    Route::delete('/premiertax/shippers_purchase/{id}/del', [ShipperPurchasesController::class, 'delete'])->name('shipper_purchases.delete');
    Route::get('/premiertax/shippers_purchase/editBoxboard/{v_no}', [ShipperPurchasesController::class, 'editBoxboard'])->name('shipper_purchases.editBoxboard');
    Route::put('/premiertax/shippers_purchase/{v_no}/updateBoxboard', [ShipperPurchasesController::class, 'updateBoxboard'])->name('shipper_purchases.updateBoxboard');


    Route::get('/premiertax/shipper_return', [ShipperReturnController::class, 'index'])->name('shipper_return.list');
    Route::get('/premiertax/shipper_return/reports', [ShipperReturnController::class, 'reports'])->name('shipper_return.reports');
    Route::post('/premiertax/shipper_return', [ShipperReturnController::class, 'store'])->name('shipper_return.store');
    Route::delete('/premiertax/shipper_return/{id}', [ShipperReturnController::class, 'destroy'])->name('shipper_return.destroy');
    
    
    Route::get('/premiertax/dye_return', [DyeReturnController::class, 'index'])->name('dye_return.list');
    Route::get('/premiertax/dye_return/reports', [DyeReturnController::class, 'reports'])->name('dye_return.reports');
    Route::post('/premiertax/dye_return', [DyeReturnController::class, 'store'])->name('dye_return.store');
    Route::delete('/premiertax/dye_return/{id}', [DyeReturnController::class, 'destroy'])->name('dye_return.destroy');



    Route::get('/premiertax/lemination_purchase', [LeminationPurchaseController::class, 'index'])->name('lemination_purchase.list');
    Route::get('/premiertax/lemination_purchase/reports', [LeminationPurchaseController::class, 'reports'])->name('lemination_purchase.reports');
    Route::post('/premiertax/lemination_purchase', [LeminationPurchaseController::class, 'store'])->name('lemination_purchase.store');
    Route::get('/premiertax/lemination_purchase/edit/{v_no}', [LeminationPurchaseController::class, 'edit'])->name('lemination_purchase.edit');
    Route::put('/premiertax/lemination_purchase/update/{id}', [LeminationPurchaseController::class, 'update'])->name('lemination_purchase.update');
    Route::get('/premiertax/lemination_purchase/{v_no}/delete', [LeminationPurchaseController::class, 'destroy'])->name('lemination_purchase.destroy');
    Route::delete('/premiertax/lemination_purchase/{id}/del', [LeminationPurchaseController::class, 'delete'])->name('lemination_purchase.delete');
    Route::get('/premiertax/lemination_purchase/editBoxboard/{v_no}', [LeminationPurchaseController::class, 'editBoxboard'])->name('lemination_purchase.editBoxboard');
    Route::put('/premiertax/lemination_purchase/{v_no}/updateBoxboard', [LeminationPurchaseController::class, 'updateBoxboard'])->name('lemination_purchase.updateBoxboard');
    




    
    
    Route::get('/premiertax/lamination_return', [LaminationReturnController::class, 'index'])->name('lamination_return.list');
    Route::get('/premiertax/lamination_return/reports', [LaminationReturnController::class, 'reports'])->name('lamination_return.reports');
    Route::post('/premiertax/lamination_return', [LaminationReturnController::class, 'store'])->name('lamination_return.store');
    Route::delete('/premiertax/lamination_return/{id}', [LaminationReturnController::class, 'destroy'])->name('lamination_return.destroy');
    
    



     Route::get('/premiertax/corrugation_return', [CorrugationReturnController::class, 'index'])->name('corrugation_return.list');
    Route::get('/premiertax/corrugation_return/reports', [CorrugationReturnController::class, 'reports'])->name('corrugation_return.reports');
    Route::post('/premiertax/corrugation_return', [CorrugationReturnController::class, 'store'])->name('corrugation_return.store');
    Route::delete('/premiertax/corrugation_return/{id}', [CorrugationReturnController::class, 'destroy'])->name('corrugation_return.destroy');
    
    
    

    Route::get('/premiertax/corrugation_purchase', [CorrugationPurchaseController::class, 'index'])->name('corrugation_purchase.list');
    Route::get('/premiertax/corrugation_purchase/reports', [CorrugationPurchaseController::class, 'reports'])->name('corrugation_purchase.reports');
    Route::post('/premiertax/corrugation_purchase', [CorrugationPurchaseController::class, 'store'])->name('corrugation_purchase.store');
    Route::get('/premiertax/corrugation_purchase/edit/{v_no}', [CorrugationPurchaseController::class, 'edit'])->name('corrugation_purchase.edit');
    Route::put('/premiertax/corrugation_purchase/update/{id}', [CorrugationPurchaseController::class, 'update'])->name('corrugation_purchase.update');
    Route::get('/premiertax/corrugation_purchase/{v_no}/delete', [CorrugationPurchaseController::class, 'destroy'])->name('corrugation_purchase.destroy');
    Route::delete('/premiertax/corrugation_purchase/{id}/del', [CorrugationPurchaseController::class, 'delete'])->name('corrugation_purchase.delete');
    Route::get('/premiertax/corrugation_purchase/editBoxboard/{v_no}', [CorrugationPurchaseController::class, 'editBoxboard'])->name('corrugation_purchase.editBoxboard');
    Route::put('/premiertax/corrugation_purchase/{v_no}/updateBoxboard', [CorrugationPurchaseController::class, 'updateBoxboard'])->name('corrugation_purchase.updateBoxboard');
    // Data Backup 
Route::resource('premiertax/sales', SaleDetails::class)->names('premiertax.sales');
Route::resource('premiertax/purchase', PurchaseDetail::class)->names('premiertax.purchase');
Route::resource('premiertax/Units', ItemUnit::class)->names('unit');
    Route::resource('premiertax/data-backup', Backup::class );
});


Route::post('premiertax/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('premiertax/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('premiertax/login', [LoginController::class, 'login']);


// Auth::routes(['login' => false, 'register' => false, 'logout' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/premiertax/test',function(){
    return "Hello World";
});
Route::get('premiertax/update-company-id/{id}', function ($id) {
    (new \App\Http\Controllers\Addvalue)->updateCompanyIdForAllTables($id);
    return "Updated c_id with company id = $id for all tables.";
});

    
//latest code for Invoicing
Route::get('premiertax/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('premiertax/invoicing', [App\Http\Controllers\InvoicingController::class, 'index'])->name('invoicing.index');
    Route::post('premiertax/invoicing/submit', [App\Http\Controllers\InvoicingController::class, 'submitInvoice'])->name('invoicing.submit');
    Route::post('premiertax/invoicing/validate', [App\Http\Controllers\InvoicingController::class, 'validateInvoice'])->name('invoicing.validate');

    // Purchase Invoicing Routes (DB only, no FBR API)
    Route::get('premiertax/purchase-invoicing', [App\Http\Controllers\PurchaseInvoicingController::class, 'index'])->name('purchase.invoicing.index');
    Route::get('premiertax/purchase-invoicing/edit/{id}', [App\Http\Controllers\PurchaseInvoicingController::class, 'edit'])->name('purchase.invoicing.edit');
    Route::post('premiertax/purchase-invoicing/store', [App\Http\Controllers\PurchaseInvoicingController::class, 'store'])->name('purchase.invoicing.store');
    Route::post('premiertax/purchase-invoicing/update/{id}', [App\Http\Controllers\PurchaseInvoicingController::class, 'update'])->name('purchase.invoicing.update');
    Route::get('premiertax/purchase-invoicing/list', [App\Http\Controllers\PurchaseInvoicingController::class, 'list'])->name('purchase.invoicing.list');
    Route::get('premiertax/purchase-invoicing/{id}', [App\Http\Controllers\PurchaseInvoicingController::class, 'show'])->name('purchase.invoicing.show');
    Route::delete('premiertax/purchase-invoicing/{id}', [App\Http\Controllers\PurchaseInvoicingController::class, 'destroy'])->name('purchase.invoicing.destroy');

    // FBR API endpoints for reference data
    Route::get('premiertax/api/fbr/provinces', [App\Http\Controllers\InvoicingController::class, 'getProvinceCodes'])->name('api.fbr.provinces');
    Route::get('premiertax/api/fbr/hs-codes', [App\Http\Controllers\InvoicingController::class, 'getHsCodes'])->name('api.fbr.hs-codes');
    Route::get('premiertax/api/fbr/item-description-codes', [App\Http\Controllers\InvoicingController::class, 'getItemDescriptionCodes'])->name('api.fbr.item-description-codes');
    Route::get('premiertax/api/fbr/uom', [App\Http\Controllers\InvoicingController::class, 'getUnitsOfMeasurement'])->name('api.fbr.uom');
    Route::get('premiertax/api/fbr/transaction-types', [App\Http\Controllers\InvoicingController::class, 'getTransactionTypeCodes'])->name('api.fbr.transaction-types');
    Route::get('premiertax/api/fbr/tax-rates', [App\Http\Controllers\InvoicingController::class, 'getTaxRates'])->name('api.fbr.tax-rates');
    Route::get('premiertax/api/fbr/doctypecode', [App\Http\Controllers\InvoicingController::class, 'getDocumentTypeCodes'])->name('api.fbr.doctypecode');
    Route::get('premiertax/drafts/{id}/edit', [DraftController::class, 'edit'])->name('drafts.edit');
    Route::get('premiertax/drafts', [DraftController::class, 'index'])->name('drafts.index');
    Route::get('premiertax/standard-drafts', function () {
        return redirect()->route('drafts.index');
    })->name('standard.drafts.index');
    Route::put('premiertax/drafts/{id}', [DraftController::class, 'update'])->name('drafts.update');
    Route::post('premiertax/drafts/{id}/submit', [DraftController::class, 'submit'])->name('drafts.submit');
    Route::post('/premiertax/invoicing/save-draft', [DraftController::class, 'saveDraft'])->name('invoicing.saveDraft');
    Route::delete('/premiertax/draftinvoices/{id}', [DraftController::class, 'destroy'])
    ->name('drafts.destroy');

    Route::get('premiertax/third-schedule-drafts', function () {
        return redirect()->route('drafts.index');
    })->name('third.schedule.drafts.index');
    Route::get('premiertax/third-schedule-drafts/{id}/edit', [ThirdScheduleDraftController::class, 'edit'])->name('third.schedule.drafts.edit');
    Route::put('premiertax/third-schedule-drafts/{id}', [ThirdScheduleDraftController::class, 'update'])->name('third.schedule.drafts.update');
    Route::post('premiertax/third-schedule-drafts/{id}/submit', [ThirdScheduleDraftController::class, 'submit'])->name('third.schedule.drafts.submit');
    Route::post('premiertax/invoicing/save-third-schedule-draft', [ThirdScheduleDraftController::class, 'saveDraft'])->name('invoicing.saveThirdScheduleDraft');
    Route::get('premiertax/commercial-drafts', function () {
        return redirect()->route('drafts.index');
    })->name('commercial.drafts.index');
    Route::get('premiertax/commercial-drafts/{id}/edit', [ThirdScheduleDraftController::class, 'editCommercial'])->name('commercial.drafts.edit');
    Route::put('premiertax/commercial-drafts/{id}', [ThirdScheduleDraftController::class, 'updateCommercial'])->name('commercial.drafts.update');
    Route::post('premiertax/commercial-drafts/{id}/submit', [ThirdScheduleDraftController::class, 'submitCommercial'])->name('commercial.drafts.submit');
    Route::post('premiertax/invoicing/save-standard-draft', [ThirdScheduleDraftController::class, 'saveStandardDraft'])->name('invoicing.saveStandardDraft');
    Route::post('premiertax/invoicing/save-commercial-draft', [ThirdScheduleDraftController::class, 'saveCommercialDraft'])->name('invoicing.saveCommercialDraft');
    Route::delete('/premiertax/third-schedule-drafts/{id}', [ThirdScheduleDraftController::class, 'destroy'])->name('third.schedule.drafts.destroy');
    Route::delete('/premiertax/commercial-drafts/{id}', [ThirdScheduleDraftController::class, 'destroyCommercial'])->name('commercial.drafts.destroy');

    // Bulk Submit Drafts
    Route::post('premiertax/draftinvoices/bulk-submit', [DraftController::class, 'bulkSubmit'])->name('draftinvoices.bulk-submit');
    Route::post('premiertax/draftinvoices/bulk-submit-all', [DraftController::class, 'bulkSubmitAll'])->name('draftinvoices.bulk-submit-all');

    // 72-Hour Safe Edit and Resubmit for FBR Sale Invoices
    Route::get('premiertax/sales/invoice/{id}/edit', [App\Http\Controllers\InvoicingController::class, 'editSaleInvoice'])->name('premiertax.sale.edit');
    Route::post('premiertax/sales/invoice/{id}/resubmit', [App\Http\Controllers\InvoicingController::class, 'resubmitSaleInvoice'])->name('premiertax.sale.resubmit');

});




Route::get('premiertax/route-list', function() {
    // Execute the route:list command and capture output
    Artisan::call('route:list');
    
    // Get the command output
    $output = Artisan::output();
    
    return $output;
;
})->middleware('auth'); // Optional: protect this route








