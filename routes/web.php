<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\TimeOffController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProjectsDashboardController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminLoginController;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

if (! request()->getRequestUri() == '/login') {
    Route::redirect('/login', '/admin/login')
        ->name('login');
}

// Redirect /admin root to custom dashboard (for authenticated users)
Route::get('/admin', function () {
    return redirect('/app/dashboard');
})->middleware('web');

// Public landing page
Route::get('/', [LandingController::class, 'index'])->name('home');

// Custom visual dashboard
Route::get('/app/dashboard', [DashboardController::class, 'index'])
    ->middleware(['web'])
    ->name('app.dashboard');

// Custom Projects dashboard
Route::get('/app/projects', [ProjectsDashboardController::class, 'index'])
    ->middleware(['web'])
    ->name('app.projects');

// SaaS self-serve tenant registration
Route::prefix('onboard')->name('saas.')->group(function () {
    Route::get('/register', [TenantRegistrationController::class, 'show'])->name('register');
    Route::post('/register', [TenantRegistrationController::class, 'store'])->name('register.store');
    Route::get('/success/{company}', [TenantRegistrationController::class, 'success'])->name('register.success');
});

// Contacts
Route::get('/app/contacts', [ContactsController::class, 'index'])->name('contacts.index');
Route::get('/app/contacts/create', [ContactsController::class, 'create'])->name('contacts.create');
Route::post('/app/contacts', [ContactsController::class, 'store'])->name('contacts.store');
Route::get('/app/contacts/{id}', [ContactsController::class, 'show'])->name('contacts.show');
Route::get('/app/contacts/{id}/edit', [ContactsController::class, 'edit'])->name('contacts.edit');
Route::put('/app/contacts/{id}', [ContactsController::class, 'update'])->name('contacts.update');
Route::delete('/app/contacts/{id}', [ContactsController::class, 'destroy'])->name('contacts.destroy');

// Products
Route::get('/app/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/app/products/create', [ProductsController::class, 'create'])->name('products.create');
Route::post('/app/products', [ProductsController::class, 'store'])->name('products.store');
Route::get('/app/products/{id}', [ProductsController::class, 'show'])->name('products.show');
Route::get('/app/products/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
Route::put('/app/products/{id}', [ProductsController::class, 'update'])->name('products.update');
Route::delete('/app/products/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');

// Employees
Route::get('/app/employees', [EmployeesController::class, 'index'])->name('employees.index');
Route::get('/app/employees/create', [EmployeesController::class, 'create'])->name('employees.create');
Route::post('/app/employees', [EmployeesController::class, 'store'])->name('employees.store');
Route::get('/app/employees/{id}', [EmployeesController::class, 'show'])->name('employees.show');
Route::get('/app/employees/{id}/edit', [EmployeesController::class, 'edit'])->name('employees.edit');
Route::put('/app/employees/{id}', [EmployeesController::class, 'update'])->name('employees.update');
Route::delete('/app/employees/{id}', [EmployeesController::class, 'destroy'])->name('employees.destroy');


// Sales
Route::get('/app/sales', [SalesController::class, 'index'])->name('sales.index');
Route::get('/app/sales/create', [SalesController::class, 'create'])->name('sales.create');
Route::post('/app/sales', [SalesController::class, 'store'])->name('sales.store');
Route::get('/app/sales/{id}', [SalesController::class, 'show'])->name('sales.show');
Route::get('/app/sales/{id}/edit', [SalesController::class, 'edit'])->name('sales.edit');
Route::put('/app/sales/{id}', [SalesController::class, 'update'])->name('sales.update');

// Purchases
Route::get('/app/purchases', [PurchasesController::class, 'index'])->name('purchases.index');
Route::get('/app/purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
Route::post('/app/purchases', [PurchasesController::class, 'store'])->name('purchases.store');
Route::get('/app/purchases/{id}', [PurchasesController::class, 'show'])->name('purchases.show');
Route::get('/app/purchases/{id}/edit', [PurchasesController::class, 'edit'])->name('purchases.edit');
Route::put('/app/purchases/{id}', [PurchasesController::class, 'update'])->name('purchases.update');

// Inventory
Route::get('/app/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/app/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');

// Accounting
Route::get('/app/accounting', [AccountingController::class, 'index'])->name('accounting.index');
Route::get('/app/accounting/{id}', [AccountingController::class, 'show'])->name('accounting.show');

// Manufacturing
Route::get('/app/manufacturing/work-orders', [ManufacturingController::class, 'workOrders'])->name('manufacturing.work-orders');
Route::get('/app/manufacturing/bom', [ManufacturingController::class, 'billsOfMaterials'])->name('manufacturing.bom');
Route::get('/app/manufacturing', [ManufacturingController::class, 'index'])->name('manufacturing.index');
Route::get('/app/manufacturing/{id}', [ManufacturingController::class, 'show'])->name('manufacturing.show');

// Recruitment
Route::get('/app/recruitment', [RecruitmentController::class, 'index'])->name('recruitment.index');
Route::get('/app/recruitment/create', [RecruitmentController::class, 'create'])->name('recruitment.create');
Route::post('/app/recruitment', [RecruitmentController::class, 'store'])->name('recruitment.store');
Route::get('/app/recruitment/{id}', [RecruitmentController::class, 'show'])->name('recruitment.show');
Route::get('/app/recruitment/{id}/edit', [RecruitmentController::class, 'edit'])->name('recruitment.edit');
Route::put('/app/recruitment/{id}', [RecruitmentController::class, 'update'])->name('recruitment.update');
Route::delete('/app/recruitment/{id}', [RecruitmentController::class, 'destroy'])->name('recruitment.destroy');

// Time Off
Route::get('/app/time-off', [TimeOffController::class, 'index'])->name('time-off.index');
Route::get('/app/time-off/create', [TimeOffController::class, 'create'])->name('time-off.create');
Route::post('/app/time-off', [TimeOffController::class, 'store'])->name('time-off.store');
Route::get('/app/time-off/{id}', [TimeOffController::class, 'show'])->name('time-off.show');
Route::post('/app/time-off/{id}/approve', [TimeOffController::class, 'approve'])->name('time-off.approve');
Route::post('/app/time-off/{id}/refuse', [TimeOffController::class, 'refuse'])->name('time-off.refuse');
Route::delete('/app/time-off/{id}', [TimeOffController::class, 'destroy'])->name('time-off.destroy');

// Settings
Route::get('/app/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/app/settings', [SettingsController::class, 'updateGeneral'])->name('settings.update');
Route::get('/app/settings/users', [SettingsController::class, 'users'])->name('settings.users');
Route::get('/app/settings/users/create', [SettingsController::class, 'createUser'])->name('settings.users.create');
Route::post('/app/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
Route::post('/app/settings/users/invite', [SettingsController::class, 'inviteUser'])->name('settings.users.invite');
Route::get('/app/settings/users/{id}/edit', [SettingsController::class, 'editUser'])->name('settings.users.edit');
Route::put('/app/settings/users/{id}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
Route::delete('/app/settings/users/{id}', [SettingsController::class, 'destroyUser'])->name('settings.users.destroy');
Route::get('/app/settings/roles', [SettingsController::class, 'roles'])->name('settings.roles');
Route::get('/app/settings/activity-types', [SettingsController::class, 'activityTypes'])->name('settings.activity-types');
Route::get('/app/settings/currencies', [SettingsController::class, 'currencies'])->name('settings.currencies');
Route::get('/app/settings/currencies/{id}/edit', [SettingsController::class, 'editCurrency'])->name('settings.currencies.edit');
Route::post('/app/settings/currencies/{id}', [SettingsController::class, 'updateCurrency'])->name('settings.currencies.update');

// Website & Blog
Route::get('/app/website', [WebsiteController::class, 'index'])->name('website.index');
Route::get('/app/website/create', [WebsiteController::class, 'create'])->name('website.create');
Route::post('/app/website', [WebsiteController::class, 'store'])->name('website.store');
Route::get('/app/website/{id}', [WebsiteController::class, 'show'])->name('website.show');
Route::post('/app/website/{id}/toggle-publish', [WebsiteController::class, 'togglePublish'])->name('website.toggle-publish');
Route::post('/app/website/{id}/delete', [WebsiteController::class, 'destroy'])->name('website.destroy');

Route::get('/app/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/app/blog/create', [BlogController::class, 'create'])->name('blog.create');
Route::post('/app/blog', [BlogController::class, 'store'])->name('blog.store');
Route::get('/app/blog/{id}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/app/blog/{id}/toggle-publish', [BlogController::class, 'togglePublish'])->name('blog.toggle-publish');
Route::post('/app/blog/{id}/delete', [BlogController::class, 'destroy'])->name('blog.destroy');

// Payroll
Route::get('/app/payroll', [PayrollController::class, 'index'])->name('payroll.index');
Route::post('/app/payroll/run', [PayrollController::class, 'runPayroll'])->name('payroll.run');
Route::get('/app/payroll/create', [PayrollController::class, 'createPayslip'])->name('payroll.create');
Route::post('/app/payroll', [PayrollController::class, 'storePayslip'])->name('payroll.store');

Route::get('/app/payroll/structures', [PayrollController::class, 'structures'])->name('payroll.structures');
Route::post('/app/payroll/structures', [PayrollController::class, 'storeStructure'])->name('payroll.structures.store');
Route::get('/app/payroll/structures/{id}/edit', [PayrollController::class, 'editStructure'])->name('payroll.structures.edit');
Route::put('/app/payroll/structures/{id}', [PayrollController::class, 'updateStructure'])->name('payroll.structures.update');
Route::delete('/app/payroll/structures/{id}', [PayrollController::class, 'deleteStructure'])->name('payroll.structures.delete');
Route::post('/app/payroll/structures/{id}/rules', [PayrollController::class, 'storeRule'])->name('payroll.structures.rules.store');
Route::delete('/app/payroll/structures/{structureId}/rules/{ruleId}', [PayrollController::class, 'deleteRule'])->name('payroll.structures.rules.delete');

Route::get('/app/payroll/{id}', [PayrollController::class, 'showPayslip'])->name('payroll.show');
Route::post('/app/payroll/{id}/confirm', [PayrollController::class, 'confirmPayslip'])->name('payroll.confirm');
Route::post('/app/payroll/{id}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.mark-paid');
Route::delete('/app/payroll/{id}', [PayrollController::class, 'deletePayslip'])->name('payroll.delete');

// Helpdesk
Route::get('/app/helpdesk', [HelpdeskController::class, 'index'])->name('helpdesk.index');
Route::get('/app/helpdesk/create', [HelpdeskController::class, 'createTicket'])->name('helpdesk.create');
Route::post('/app/helpdesk', [HelpdeskController::class, 'storeTicket'])->name('helpdesk.store');

Route::get('/app/helpdesk/teams', [HelpdeskController::class, 'teams'])->name('helpdesk.teams');
Route::post('/app/helpdesk/teams', [HelpdeskController::class, 'storeTeam'])->name('helpdesk.teams.store');
Route::get('/app/helpdesk/teams/{id}/edit', [HelpdeskController::class, 'editTeam'])->name('helpdesk.teams.edit');
Route::put('/app/helpdesk/teams/{id}', [HelpdeskController::class, 'updateTeam'])->name('helpdesk.teams.update');
Route::delete('/app/helpdesk/teams/{id}', [HelpdeskController::class, 'deleteTeam'])->name('helpdesk.teams.delete');
Route::post('/app/helpdesk/teams/{id}/members', [HelpdeskController::class, 'addTeamMember'])->name('helpdesk.teams.members.add');
Route::delete('/app/helpdesk/teams/{teamId}/members/{userId}', [HelpdeskController::class, 'removeTeamMember'])->name('helpdesk.teams.members.remove');

Route::get('/app/helpdesk/{id}', [HelpdeskController::class, 'showTicket'])->name('helpdesk.show');
Route::put('/app/helpdesk/{id}', [HelpdeskController::class, 'updateTicket'])->name('helpdesk.update');
Route::delete('/app/helpdesk/{id}', [HelpdeskController::class, 'deleteTicket'])->name('helpdesk.delete');
Route::post('/app/helpdesk/{id}/messages', [HelpdeskController::class, 'storeMessage'])->name('helpdesk.messages.store');

// AI Assistant
Route::post('/app/ai/chat', [AiController::class, 'chat'])->name('ai.chat');
Route::get('/app/ai/insights', [AiController::class, 'insights'])->name('ai.insights');

// Notifications
Route::get('/app/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
Route::get('/app/notifications/unread', [NotificationsController::class, 'unread'])->name('notifications.unread');
Route::post('/app/notifications/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('notifications.markAllRead');
Route::delete('/app/notifications/clear-all', [NotificationsController::class, 'clearAll'])->name('notifications.clearAll');
Route::post('/app/notifications/{id}/read', [NotificationsController::class, 'markRead'])->name('notifications.markRead');
Route::delete('/app/notifications/{id}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');

// Super Admin Console (SaaS owner panel — separate from tenant ERP)
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    // Public: login / logout
    Route::get('/login',  [SuperAdminLoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [SuperAdminLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout',[SuperAdminLoginController::class, 'logout'])->name('logout');

    // Protected: require superadmin guard
    Route::middleware('superadmin.auth')->group(function () {
        Route::get('/',                             [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/organizations',                [SuperAdminController::class, 'organizations'])->name('organizations');
        Route::get('/organizations/{id}',           [SuperAdminController::class, 'showOrganization'])->name('organizations.show');
        Route::post('/organizations/{id}/suspend',  [SuperAdminController::class, 'suspendOrganization'])->name('organizations.suspend');
        Route::post('/organizations/{id}/unsuspend',[SuperAdminController::class, 'unsuspendOrganization'])->name('organizations.unsuspend');
        Route::post('/organizations/{id}/activate', [SuperAdminController::class, 'activateOrganization'])->name('organizations.activate');
        Route::get('/subscriptions',                [SuperAdminController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/plans',                        [SuperAdminController::class, 'plans'])->name('plans');
        Route::get('/users',                        [SuperAdminController::class, 'users'])->name('users');
        Route::get('/analytics',                    [SuperAdminController::class, 'analytics'])->name('analytics');
    });
});
