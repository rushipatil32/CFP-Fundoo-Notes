<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\CollaboratorController;


// use App\Http\Controllers\SendMailController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register',[usercontroller::class,'register']);
Route::post('login',[usercontroller::class,'login']);
Route::post('forgotPassword', [usercontroller::class, 'forgotPassword']);
Route::get('paginationNote', [NoteController::class, 'paginationNote']);

Route::group(['middleware' => ['jwt.verify']], function() {
Route::get('logout', [usercontroller::class, 'logout']);
Route::get('get_user', [usercontroller::class, 'get_user']);
Route::post('resetPassword', [usercontroller::class, 'resetPassword']);

Route::post('createNote', [NoteController::class, 'createNote']);
Route::get('readAllNotes', [NoteController::class, 'readAllNotes']);
Route::post('readNoteById', [NoteController::class, 'readNoteById']);
Route::post('updateNoteById', [NoteController::class, 'updateNoteById']);
Route::post('deleteNoteById', [NoteController::class, 'deleteNoteById']);
Route::post('addNoteLabel', [NoteController::class, 'addNoteLabel']);
Route::post('deleteNoteLabel', [NoteController::class, 'deleteNoteLabel']);
Route::post('pinNoteById',[NoteController::class,'pinNoteById']);
Route::post('unpinNoteById',[NoteController::class,'unpinNoteById']);
Route::post('archiveNoteById',[NoteController::class,'archiveNoteById']);
Route::post('unarchiveNoteById',[NoteController::class,'unarchiveNoteById']);
Route::post('colourNoteById', [NoteController::class, 'colourNoteById']);
Route::get('getAllPinnedNotes', [NoteController::class, 'getAllPinnedNotes']);
Route::get('getAllArchivedNotes', [NoteController::class, 'getAllArchivedNotes']);
Route::post('searchNotes', [NoteController::class, 'searchNotes']);


Route::post('createLabel', [LabelController::class, 'createLabel']);
Route::get('readLabel', [LabelController::class, 'readLabel']);
Route::get('readLabelById', [LabelController::class, 'readLabelById']);
Route::post('updateLabelById', [LabelController::class, 'updateLabelById']);
Route::post('deleteLabelById', [LabelController::class, 'deleteLabelById']);

Route::post('addCollaboratorByNoteId',[CollaboratorController::class,'addCollaboratorByNoteId']);
Route::post('updateCollaboratorById',[CollaboratorController::class,'updateCollaboratorById']);
Route::post('deleteCollaboratorById',[CollaboratorController::class,'deleteCollaboratorById']);

});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

