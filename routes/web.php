<?php

use App\Http\Controllers\ThreadController;
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
//Route::post('avatar/upload', function () {
//    $path = request()->file('photo')->store('photos');
//    request()->user()->update(['image_path' => $path]);
//    return back();
//})->name('avatar.upload');
Route::get('test', function (){
    RedisAlias::zmscore('key', 'member');
});

//Route::view('test', 'test');Route::view('test', 'test');

Route::get('dashboard', fn() => view('dashboard'))->name('dashboard');

Route::get('', fn() => view('welcome'))->name('dashboard');
Route::get('profile/{user}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('users.profile');
Route::post('threads/{thread}/subscribe', [ThreadController::class, 'subscribe'])->name('threads.subscribe');
Route::get('channels/threads/{channel?}', [ThreadController::class, 'index'])->name('channels.threads.index');
Route::resource('threads', ThreadController::class)->except('index', 'show', 'destroy');
Route::get('threads/{thread}/{slug}', [ThreadController::class, 'show'])->name('threads.show');
Route::delete('threads/{thread}/{slug}', [ThreadController::class, 'destroy'])->name('threads.destroy');

require __DIR__.'/auth.php';
