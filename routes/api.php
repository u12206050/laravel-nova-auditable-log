<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Day4\AuditableLog\Http\Controllers\AuditController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. You're free to add
| as many additional routes to this file as your tool may require.
|
*/

Route::get('/audits/{resourceName}/{resourceId}', AuditController::class.'@show');
Route::delete('/audits/{resourceName}/{resourceId}/{auditId}', AuditController::class.'@delete');
Route::post('/audits/{resourceName}/{resourceId}/{auditId}', AuditController::class.'@restore');
