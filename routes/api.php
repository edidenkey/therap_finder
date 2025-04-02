<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('theraplib/v1')->group(function () {
    Route::post('/login', [ApiController::class, 'apiLogin']);
    Route::post('/register', [ApiController::class, 'apiRegister']);

    Route::post('/checks/email', [ApiController::class, 'apiCheckEmailUnique']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        //Connected user
        Route::get('/logout', [ApiController::class, 'apiLogOut']);
        Route::get('/users/me', [ApiController::class, 'apiGetConnectedUser']);
        Route::put('/users/me', [ApiController::class, 'apiUpdateUser']);
        Route::post('/users/me/password', [ApiController::class, 'apiUpdateUserPassword']);
        Route::post('/users/me/location', [ApiController::class, 'apiUpdateUserLocation']);
        Route::post('/users/me/profile-image', [ApiController::class, 'apiUpdateUserImageProfil']);
        Route::delete('/users/me/profile-image', [ApiController::class, 'apiDeleteUserImageProfil']);

        // Pay Informations
        Route::get('/information-pays/me', [ApiController::class, 'apiGetUserInformationPays']);
        Route::post('/information-pays/me', [ApiController::class, 'apiAddUserInformationPay']);
        Route::put('/information-pays/me/{information_pay_id}', [ApiController::class, 'apiEditUserInformationPay']);
        Route::delete('/information-pays/me/{information_pay_id}', [ApiController::class, 'apiDeleteUserInformationPay']);

        // Categories
        Route::get('/disciplines', [ApiController::class, 'apiListDisciplines']);

        // Products
        Route::get('/products', [ApiController::class, 'apiListProdutcs']);
        Route::get('/products/category/{categorie_id}', [ApiController::class, 'apiListProductsByCategory']);
        Route::get('/products/therapeute/{therapeute_id}', [ApiController::class, 'apiListProductsByTherapeute']);

        // Therapeutes
        Route::get('/therapeutes', [ApiController::class, 'apiListTherapeutes']); // /therapeutes?discipline_id=?
        Route::get('/therapeutes/stats/{therapeute_id}', [ApiController::class, 'apiTherapeuteStats']);
        Route::get('/therapeutes/{therapeute_id}', [ApiController::class, 'apiGetTherapeute']);
        Route::get('/therapeutes/near/{lon}/{lat}', [ApiController::class, 'apiTherapeutesNearUser']); // /therapeutes/near/{lon}/{lat}?discipline_id=?

        // Client
        Route::get('/clients/{client_id}', [ApiController::class, 'apiGetClient']);

        // Commandes
        Route::get('/orders/me', [ApiController::class, 'apiGetUserOrder']);
        Route::post('/orders', [ApiController::class, 'apiSaveOrder']);

        // meetings
        Route::get('/meetings/me', [ApiController::class, 'apiGetMeetings']);
        Route::post('/meetings', [ApiController::class, 'apiSaveMeeting']);
        Route::put('/meetings/{meeting_id}', [ApiController::class, 'apiUpdateMeeting']);

        // Services
        Route::get('/services', [ApiController::class, 'apiGetServices']);
        Route::get('/services/therapeute/{therapeute_id}', [ApiController::class, 'apiGetServicesByTherapeute']);
    });
});
