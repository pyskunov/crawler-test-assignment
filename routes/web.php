<?php

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

/**
 * When Spatie gave us routing via annotations
 * I found it a great thing as we can remove routing files
 * and use web.php for our FEs which are usually returning of an SPA view.
 *
 * I also cleaned Routing Service Provider from trash inside so it do less work on bootstrapping
 */

Route::view('/', 'welcome');
