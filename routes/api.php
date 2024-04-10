<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['middleware' => ['cors', 'forceJSON']], function () {
        
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
        
    Route::post('/reset-password-request', [App\Http\Controllers\PasswordResetRequestController::class, 'sendPasswordResetEmail']);
    Route::post('/change-password', [App\Http\Controllers\ChangePasswordController::class, 'passwordResetProcess']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout']);
        Route::get('/user', [App\Http\Controllers\Auth\AuthController::class, 'userProfile']);
        Route::post('refresh', [App\Http\Controllers\Auth\AuthController::class, 'refresh']);
    });

    Route::group(['prefix' => 'game', 'namespace' => 'App\Http\Controllers\Api\Game'], function() {
        Route::apiResource('all-worlds', WorldController::class);
        Route::apiResource('all-users', UserController::class);
        Route::apiResource('all-battles', BattleController::class);
        Route::apiResource('all-fleets', FleetController::class);
        Route::apiResource('all-ships', ShipController::class);
        Route::apiResource('all-crews', CrewController::class);
        Route::apiResource('all-items', ItemController::class);
        Route::apiResource('all-equipments', EquipmentController::class);
        Route::apiResource('all-islands', IslandController::class);
        Route::apiResource('all-queries', QueriesController::class);
        Route::apiResource('user-played-worlds', LoginWorldsController::class);
        Route::apiResource('user-not-played-worlds', NotLoginWorldsController::class);
        Route::apiResource('user-battles', UserBattlesController::class);
        Route::apiResource('user-crews', UserCrewsController::class);
        Route::apiResource('user-equipment', UserEquipmentController::class);
        Route::apiResource('user-fleets', UserFleetsController::class);
        Route::apiResource('user-queries', UserQueriesController::class);
        Route::apiResource('world-played-users', WorldUsersController::class);
        Route::apiResource('world-seas', WorldSeasController::class);
        Route::apiResource('world-islands', WorldIslandsController::class);
        Route::apiResource('equipment-items', EquipmentItemsController::class);
        Route::apiResource('fleet-ships', FleetShipsController::class);
        Route::apiResource('ship-crew', ShipCrewController::class);
        Route::apiResource('store-new-world', StoreNewWorldController::class);
        Route::apiResource('store-user-data', SaveUserDataController::class);
        Route::apiResource('store-user-skills', SaveUserSkillsController::class);
        Route::post('/online-game-data/{id}', [App\Http\Controllers\Api\Game\OnlineGameController::class, 'store']);
        Route::put('/store-positions/{id}', [App\Http\Controllers\Api\Game\OnlineGameController::class, 'storePositions']);
        Route::put('/store-moving/{id}', [App\Http\Controllers\Api\Game\OnlineGameController::class, 'storeMove']);
        Route::put('/update-game-status/{id}', [App\Http\Controllers\Api\Game\OnlineGameController::class, 'updateStatus']);
        Route::put('/finish-game/{id}', [App\Http\Controllers\Api\Game\OnlineGameController::class, 'gameOver']);
        Route::get('/get-data-game/{id}', [App\Http\Controllers\Api\Game\OnlineGameController::class, 'show']);
        Route::get('/get-user-data/{world}/{user}', [App\Http\Controllers\Api\Game\SaveUserDataController::class, 'show']);
        Route::get('/get-user-skills/{world}/{user}', [App\Http\Controllers\Api\Game\SaveUserSkillsController::class, 'show']);
        Route::get('/get-all-ships', [App\Http\Controllers\Api\Game\GetShipController::class, 'index']);
        Route::get('/get-all-islands', [App\Http\Controllers\Api\Game\GetIslandsController::class, 'index']);
        Route::get('/get-all-worlds', [App\Http\Controllers\Api\Game\GetAllWorldsController::class, 'index']);
        Route::put('/update-user/{id}', [App\Http\Controllers\Api\Game\UserController::class, 'update']);
        Route::put('/update-ship/{id}', [App\Http\Controllers\Api\Game\ShipController::class, 'update']);
        Route::put('/ship-use-item/{id}', [App\Http\Controllers\Api\Game\ShipController::class, 'updateShip']);
        Route::put('/delete-item-equipment/{id}', [App\Http\Controllers\Api\Game\ItemController::class, 'deleteItem']);
    });

    Route::group(['prefix' => 'forum', 'namespace' => 'App\Http\Controllers\Api\Forum'], function() {
        Route::apiResource('all-topics', TopicController::class);
        Route::apiResource('all-posts', PostController::class);
        Route::get('/get-topic-posts/{topic}', [App\Http\Controllers\Api\Forum\TopicPostsController::class, 'show']);
    });

});