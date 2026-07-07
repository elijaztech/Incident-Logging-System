<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AccountController;



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
    return view('account.login');
});

Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');

// display login page UI...?
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route calling the login validation and authentication function...?
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

//register
Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');
//manage account
Route::middleware(['auth'])->group(function () {
    Route::get('/account/manage', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/manage', [AccountController::class, 'update'])->name('account.update');
    //ticket storage
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');
    //fetch ticket
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    //status update
    Route::post('/tickets/{id}/update-status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    //manage/update ticket
    Route::get('/tickets/{id}/manage', [TicketController::class, 'manage'])->name('tickets.manage');
    Route::post('/tickets/{id}/update', [TicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{id}/updaterating', [TicketController::class, 'updaterating'])->name('tickets.updaterating');
    //management analytics dashboard
    Route::get('/manager/mad', [TicketController::class, 'showMAD'])->name('mad');
});

//notification stuff
use App\Models\User;

Broadcast::channel('user.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});

Route::get('/manager/print-report', function() {
    return view('manager.madprint', [
        'totalCount' => \App\Models\Incident::count(),
        'pendingCount' => \App\Models\Incident::where('status', 'pending')->count(),
        'resolvedCount' => \App\Models\Incident::where('status', 'resolved')->count(),
        'incidents' => \App\Models\Incident::latest()->get()
    ]);
})->middleware(['auth'])->name('/manager/report');

Route::get('/manager/printticket/{id}', function($id) {
    // Find the specific incident or throw a 404 error if it doesn't exist
    $incident = \App\Models\Incident::findOrFail($id);

    return view('manager.printticket', compact('incident'));
})->middleware(['auth'])->name('manager.printticket');