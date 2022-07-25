<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\RegisteredTenantController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {

    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.post');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('loginView')
        ;

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->name('password.confirm.post');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});



Route::middleware(InitializeTenancyByDomain::class)->prefix('{tenant}')->group(function (){
    Route::get('/', function () {
        return view('welcome');
    })->name('tenant.welcome');
    Route::get('/dashboard', function () {
        return \App\Models\tenant\User::all();
    })->middleware(['auth']);

    Route::middleware('guest')->group(function () {

        Route::get('register', function (){
            return redirect()->route('register');
        })->name('tenant.register');

        Route::post('register', function (){
            return redirect()->route('register.post');
        })->name('tenant.register.post');


        Route::get('login', function(){
            return redirect()->route('loginView');
        });

        Route::post('login', function(){
            return redirect()->route('login');
        })->name('tenant.login');

        Route::get('forgot-password', function(){
            return redirect()->route('password.request');
        })->name('tenant.password.request');

        Route::post('forgot-password', function(){
            return redirect()->route('password.email');
        })->name('tenant.password.email');

        Route::get('reset-password/{token}', function(){
            return redirect()->route('password.reset');
        })->name('tenant.password.reset');

        Route::post('reset-password', function(){
            return redirect()->route('password.update');
        })->name('tenant.password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::get('verify-email', function(){
            return redirect()->route('verification.notice');
        })->name('tenant.verification.notice');

        Route::get('verify-email/{id}/{hash}', function(){
            return redirect()->route('verification.verify');
        })->middleware(['signed', 'throttle:6,1'])
            ->name('tenant.verification.verify');

        Route::post('email/verification-notification', function(){
            return redirect()->route('verification.send');
        })->middleware('throttle:6,1')
            ->name('tenant.verification.send');

        Route::get('confirm-password', function(){
            return redirect()->route('password.confirm');
        })->name('tenant.password.confirm');

        Route::post('confirm-password', function(){
            return redirect()->route('password.confirm.post');
        })->name('tenant.password.confirm.post');

        Route::post('logout', function(){
            return redirect()->route('logout');
        })->name('tenant.logout');
    });
    Route::get('register', [RegisteredTenantController::class, 'create']);
    Route::post('register', [RegisteredTenantController::class, 'store'])->name('register.tenant');
});
