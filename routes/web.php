<?php

use App\Http\Livewire\Chat\CreateChat;
use App\Http\Livewire\Chat\Main;
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

Route::get('/', function () {
    return view('welcome');
});

//livewire routes
Route::middleware(['auth'])->group(function () {
Route::get('/users',CreateChat::class)->name('users');
Route::get('/chat',Main::class)->name('chat');
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
});
require __DIR__.'/auth.php';
