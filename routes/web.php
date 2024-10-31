<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;

Route::get('tender/{tender}/documents', [TenderController::class, 'getDocuments'])->name('tender.documents');
Route::get('category/data', [CategoryController::class, 'getData'])->name('category.data');
Route::post('partner/check', [PartnerController::class, 'checkName'])->name('partner.check');
Route::get('partner/{partner}/upload', [PartnerController::class, 'upload'])->name('partner.upload');
Route::post('partner/{partner}/save', [PartnerController::class, 'save'])->name('partner.save');
Route::delete('partner/{partner}/remove', [PartnerController::class, 'remove'])->name('partner.remove');
Route::patch('partner/{partner}/verify', [PartnerController::class, 'verify'])->name('partner.verify');
Route::delete('partner/delete/{fileId}', [PartnerController::class, 'fileDelete'])->name('partner.file-delete');
Route::get('type/data', [TypeController::class, 'getData'])->name('type.data');


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function(){
    Route::resource('category', CategoryController::class);
    Route::resource('partner', PartnerController::class);
    Route::resource('tender', TenderController::class);
    Route::resource('type', TypeController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
