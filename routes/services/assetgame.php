<?php

use App\Http\Controllers\Client\AuthenticationController;
use App\Http\Controllers\Client\CharacterAppearanceController;
use App\Http\Controllers\Client\GameJoinControlller;
use App\Http\Controllers\Client\LuaWebServiceController;
use App\Http\Controllers\Client\StudioJoinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\rbxAPIs;

Route::group(['as' => 'client-routes.'], function(){
    Route::get('/asset', [rbxAPIs::class, 'asset'])->name('asset');
    Route::get('/Asset', [rbxAPIs::class, 'asset']);
    Route::group(['prefix' => 'asset'], function() {
        Route::get('/CharacterFetch.ashx', [CharacterAppearanceController::class, 'characterFetch'])->name('character-fetch');
        Route::get('/BodyColors.ashx', [CharacterAppearanceController::class, 'bodyColors'])->name('body-colors');
    });

    Route::group(['prefix'=> 'game', 'as'=> 'game.'], function() { 
        Route::match(['get','post'],'PlaceLauncher.ashx', [GameJoinControlller::class, 'placeLauncher'])->name('place-launcher');
        Route::get('Join.ashx', [GameJoinControlller::class,'joinScript'])->name('join-script');
        Route::get('visit.ashx', [StudioJoinController::class,'visit'])->name('visit-script');
        Route::get('GetCurrentUser.ashx', [AuthenticationController::class,'getCurrentUser'])->name('get-current-user');
        Route::get('LuaWebService/HandleSocialRequest.ashx', [LuaWebServiceController::class,'handleSocialRequest'])->name('lua-web-service');
    });

    // game server (RCC) callbacks, authenticated with FINOBE_CLOUD_AUTHORIZATION
    Route::group(['prefix' => 'api/gameserver'], function() {
        Route::get('register/{jobId}', [rbxAPIs::class, 'registerJobId']);
        Route::get('visit/{jobId}', [rbxAPIs::class, 'visitJobId']);
        Route::get('alive/{jobId}', [rbxAPIs::class, 'aliveJobId']);
        Route::get('shutdown/{jobId}', [rbxAPIs::class, 'shutdownJobId']);
        Route::post('update', [rbxAPIs::class, 'update']);
        Route::any('sysstats-report', fn() => response('', 200));
    });

    Route::get('/penelope/login/negotiate', [AuthenticationController::class,'authenticateClient'])->name('negotiate');
    Route::get('login/RequestAuth.ashx', [AuthenticationController::class,'requestAuth'])->name('request-auth');

});

?>