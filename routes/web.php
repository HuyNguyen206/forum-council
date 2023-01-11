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

Route::get('', fn() => view('welcome'));
Route::get('profile/{user}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('users.profile');
Route::post('threads/{thread}/subscribe', [ThreadController::class, 'subscribe'])->name('threads.subscribe');
Route::get('channels/threads/{channel?}', [ThreadController::class, 'index'])->name('channels.threads.index');
Route::resource('threads', ThreadController::class)->except('index', 'show', 'destroy');
Route::get('threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');
Route::delete('threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');

Route::prefix('admin')->middleware(['auth', 'can:create,App\Models\\Channel'])->group(function (){
    Route::get('channels', [\App\Http\Controllers\ChannelController::class, 'index'])->name('channels.index');
    Route::get('channels/edit/{channel}', [\App\Http\Controllers\ChannelController::class, 'edit'])->name('channels.edit');
    Route::view('channels/create', 'channels.create')->name('channels.create');
    Route::patch('channels/archive/{channel}', [\App\Http\Controllers\ChannelController::class, 'toggleArchive'])->name('channels.toggle-archive');
});

Route::redirect('dashboard','channels/threads')->name('dashboard');

require __DIR__.'/auth.php';
