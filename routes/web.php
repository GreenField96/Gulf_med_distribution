<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Member;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/members/{member}/document', function (Member $member) {
    if (!auth()->check()) {
        abort(403, 'Unauthorized');
    }

    $filePath = $member->reference_to_pdf;

    if (!$filePath || !Storage::disk('local')->exists($filePath)) {
        abort(404, 'File not found');
    }

    return response()->file(Storage::disk('local')->path($filePath), [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        'Cache-Control'       => 'no-cache, no-store, must-revalidate, max-age=0',
        'Pragma'              => 'no-cache',
        'Expires'             => 'Sat, 01 Jan 2000 00:00:00 GMT',
    ]);
})->name('members.document.view')->middleware(['web', 'auth']);
