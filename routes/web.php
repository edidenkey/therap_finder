<?php

use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DisciplinesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard',[HomeController::class, 'home'])->name('dashboard');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('mon-profil', [InfoUserController::class, 'create'])->name('mon-profil');

	Route::post('update-profil', [InfoUserController::class, 'store']);

	Route::get('rtl', function () {
		return view('rtl');
	})->name('rtl');

	Route::get('gestion-des-utilisateurs', [UserManagementController::class, 'show'])->name('gestion-des-utilisateurs');

	Route::get('liste-des-categories', [CategoriesController::class, 'show'])->name('liste-des-categories');

	Route::post('add-categories', [CategoriesController::class, 'store']);

	Route::get('liste-des-disciplines', [DisciplinesController::class, 'show'])->name('liste-des-disciplines');

	Route::post('add-disciplines', [DisciplinesController::class, 'store']);

	Route::get('liste-des-abonnements', [AbonnementController::class, 'show'])->name('liste-des-abonnements');

	Route::post('add-abonnements', [AbonnementController::class, 'store']);

	Route::get('liste-des-produits', [ProductController::class, 'show'])->name('liste-des-produits');

	Route::get('liste-des-consultations', [MeetingController::class, 'show'])->name('liste-des-consultations');

	Route::post('add-product', [ProductController::class, 'store']);

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');

    Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');