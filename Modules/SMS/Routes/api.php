<?php

use Illuminate\Support\Facades\Route;
use Modules\SMS\Http\Controllers\SMSController;
use Modules\SMS\Http\Controllers\SMSTemplateController;

Route::prefix('v1')->group(function () {

    // SMS Template Routes
    Route::prefix('sms_templates')->group(function () {
        Route::get('/', [SMSTemplateController::class, 'index'])->name('api.sms_templates.index');
        Route::get('show/{id}', [SMSTemplateController::class, 'show'])->name('api.sms_templates.show');
        Route::delete('delete/{id}', [SMSTemplateController::class, 'delete'])->name('api.sms_templates.delete');
        Route::put('update/{id}', [SMSTemplateController::class, 'update'])->name('api.sms_templates.update');
        Route::post('create', [SMSTemplateController::class, 'create'])->name('api.sms_templates.create');
    });

    Route::prefix('sms')->group(function () {
        Route::post('send', [SMSController::class, 'send'])->name('api.sms.send');
        Route::post('send_bulk', [SMSController::class, 'send_bulk'])->name('api.sms.send_bulk');
        Route::get('logs', [SMSController::class, 'logs'])->name('api.sms.logs');
    });
});
