<?php

namespace App\Http\Controllers;

use App\Exceptions\FundoNotesException;
use Illuminate\Http\Request;
use App\Models\Notes;
use App\Models\Labels;
use App\Models\LabelNotes;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{

    /**
     * This function takes User access token and checks if it is
     * authorised or not if so and it procees for the note creation 
     * and created it successfully.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/createNote",
     *   summary="Create Note",
     *   description=" Create Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"title", "description"},
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="notes created successfully"),
     *   @OA\Response(response=400, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    public function createNote(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|between:2,50',
                'description' => 'required|string|between:3,500',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->tojson(), 400);
            }
            $user = JWTAuth::authenticate($request->token);
            $user_id = $user->id;

            if (!$user) {
                throw new FundoNotesException('Invalid Authorization Token', 401);
            } else {
                $note = Notes::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'user_id' => $user_id,
                ]);
                return response()->json([
                    'message' => 'Note successfully created',
                    'note' => $note
                ],200);
            }
            Cache::remember('notes', 3600, function () {
                return DB::table('notes')->get();
            });

        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/readAllNotes",
     *      summary="Read Note",
     *      description="Read Note",
     * 
     *      @OA\Response(response=200, description="Notes found successfully"),
     *      @OA\Response(response=404, description="Invalid Autherization token"),
     * 
     *      security={
     *          {"Bearer":{}}
     * }
     * )
     * 
     * @return \Illuminate\Http\JsonResponse
     */


    function readAllNotes(Request $request)
    {
        try {

            $user = JWTAuth::authenticate($request->token);

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid Authorization Token',
                ], 401);
            }
            $note = Notes::getAllNotes($user);

            if (!$note) {
                return response()->json([
                    'message' => 'No note created by this user',
                ], 401);
            } else {
                // $paginate = $note->paginate(3);
                return response()->json([
                    'notes' => $note,
                    'message'=>'Notes Found Successfully'
                ], 200);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /** @OA\Post(
     * path="/api/readNoteById",
     * summary="Read Note",
     * description="Read Notes For an Particular User",
     * @OA\RequestBody(
     *    @OA\JsonContent(),
     *    @OA\mediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *           type="object",
     *           required={"id"},
     *           @OA\Property(property="id",type="integer"),
     *      ),
     *    ),
     *  ),
     *   @OA\Response(response=200, description="All Notes are found Successfully"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *   }
     * )
     * This function takes access token and note id and finds
     * if there is any note existing on that User id and note id if so
     * it successfully returns that note id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function readNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->only('id'), [
                'id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->tojson(), 400);
            }

            $user = JWTAuth::authenticate($request->token);

            if (!$user) {
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }
            Cache::remember('notes', 3600, function () {
                return DB::table('notes')->get();
            });

            $userid = $user->id;
            $note = Notes::where('user_id', $userid)->where('id', $request->id)->first();

            if (!$note) {
                throw new FundoNotesException('Notes Not Found', 404);
            } else {
                return response()->json([
                    'status' => 200,
                    'note' => $note,
                    'messege' => 'All notes found successfully'
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /**
     *   @OA\Post(
     *   path="/api/updateNoteById",
     *   summary="update note",
     *   description="update note",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id","title","description"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Updated Successfully"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and
     * Note Id which user wants to update and 
     * finds the note id if it is existed or not. 
     * If it is existed then, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|between:2,30',
                'description' => 'required|string|between:3,1000',
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = JWTAuth::authenticate($request->token);
            Cache::remember('notes', 3600, function () {
                return DB::table('notes')->get();
            });

            if (!$user) {
                Log::channel('customLog')->error('Invalid User');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }

            $note = Notes::where('user_id', $user->id)->where('id', $request->id)->first();

            if (!$note) {
                throw new FundoNotesException('Notes Not Found', 404);
            }

            $note->update([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => $user->id,
            ]);

            Log::channel('customLog')->info('Note updated', ['user_id' => $user->id]);
            return response()->json([
                'status' => 200,
                'note' => $note,
                'mesaage' => 'Note Successfully updated',
            ]);
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     *   @OA\post(
     *   path="/api/deleteNoteById",
     *   summary="Delete Note",
     *   description="Delete exit Note",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Enter Valid ID"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes User access token and note id.
     * Finds which user wants to delete and 
     * Finds the note id if it is existed or not.
     * If Exists deletes it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = JWTAuth::authenticate($request->token);
            Cache::remember('notes', 3600, function () {
                return DB::table('notes')->get();
            });

            if (!$user) {
                Log::channel('customLog')->error('Invalid User');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }

            $note = Notes::where('id', $request->id)->first();

            if (!$note) {
                Log::channel('customLog')->error('Notes Not Found', ['id' => $request->id]);
                throw new FundoNotesException('Notes Not Found', 404);
            } else {
                $note->delete($note->id);
                Log::channel('customLog')->info('notes deleted', ['user_id' => $user->id, 'note_id' => $request->id]);
                return response()->json([
                    'status' => 201,
                    'mesaage' => 'Note Successfully deleted',
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }


    /**
     *   @OA\Post(
     *   path="/api/addNoteLabel",
     *   summary="Add note label",
     *   description="Adiing note label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"label_id","note_id"},
     *               @OA\Property(property="label_id", type="string"),
     *               @OA\Property(property="note_id", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note successfully updated"),
     *   @OA\Response(response=402, description="Labels or Notes not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and note id which
     * user wants to update and finds the note id if it is existed
     * or not if so, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function addNoteLabel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'label_id' => 'required|integer',
                'note_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                log::error('Invalid Authorisation Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }

            $notes = Notes::getNotesByNoteIdandUserId($request->note_id, $user->id);
            $label = Labels::getLabelByLabelIdandUserId($request->label_id, $user->id);

            if (!$notes || !$label) {
                log::error('Note or label not found');
                throw new FundoNotesException('Note or Label Not Found', 404);
            } else {
                $labelnote = LabelNotes::getLabelNotesbyLabelIdNoteIdandUserId($request->label_id, $request->note_id, $user->id);
                if ($labelnote) {
                    log::info('Label already exists');
                    throw new FundoNotesException('Note Already Having This Label', 409);
                } else {

                    $notelabel = LabelNotes::createNoteLabel($request, $user->id);
                    log::info('Label created Successfully');
                    return response()->json([
                        'status' => 200,
                        'message' => 'Label and note added Successfully',
                        'notelabel' => $notelabel,
                    ]);
                }
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     *   @OA\Post(
     *   path="/api/deleteNoteLabel",
     *   summary="Delete note label",
     *   description="Deleting note label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"label_id","note_id"},
     *               @OA\Property(property="label_id", type="string"),
     *               @OA\Property(property="note_id", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note and label successfully deleted"),
     *   @OA\Response(response=402, description="Notelabel not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and note id which
     * user wants to update and finds the note id if it is existed
     * or not if so, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function deleteNoteLabel(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'label_id' => 'required|integer',
                'note_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                log::warning('Invalid Authorisation Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }

            $labelnote = LabelNotes::where('note_id', $request->note_id)->where('label_id', $request->label_id)->where('user_id', $user->id)->first();
            if (!$labelnote) {
                log::error('Label note not found');
                throw new FundoNotesException('LabelNotes Not Found With These Credentials', 404);
            }
            $labelnote->delete($labelnote->id);
            log::info('label and note deleted successfully');
            return response()->json([
                'status' => 200,
                'message' => 'label Note deleted Successfully',
            ]);
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Post(
     *   path="/api/pinNoteById",
     *   summary="Pin Note",
     *   description=" Pin Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Pinned Sucessfully"),
     *   @OA\Response(response=404, description="Notes not Found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * This function takes the User access token and checks if it
     * authorised or not and it takes the note_id and pins  it
     * successfully if notes is exist.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function pinNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $noteObject = new Notes();
            $user = JWTAuth::authenticate($request->id);
            $note = $noteObject->noteId($request->id);

            if (!$note) {
                Log::error('Notes Not Found', ['user' => $user, 'id' => $request->id]);
                throw new FundoNotesException('Notes Not Found', 404);
            }

            if ($note->pin == 0) {
                if ($note->archive == 1) {
                    $note->archive = 0;
                    $note->save();
                }
                $note->pin = 1;
                $note->save();

                log::info('Note pinned successfully');
                return response()->json([
                    'status' => 200,
                    'message' => 'Note pinned Successfully',
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Post(
     *   path="/api/unpinNoteById",
     *   summary="Unpin Note",
     *   description=" Unpin Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Unpinned Sucessfully"),
     *   @OA\Response(response=404, description="Notes not Found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * This function takes the User access token and checks if it
     * authorised or not and it takes the note_id and unpin  it
     * successfully if notes is exist.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function unpinNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $noteObject = new Notes();
            $user = JWTAuth::parseToken()->authenticate();
            $note = $noteObject->noteId($request->id);

            if (!$note) {
                Log::error('Notes Not Found', ['user' => $user, 'id' => $request->id],);
                throw new FundoNotesException('Notes Not Found', 404);
            }

            if ($note->pin == 1) {
                $note->pin = 0;
                $note->save();

                Log::info('note unpin', ['user_id' => $user, 'note_id' => $request->id]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Note Unpinned Sucessfully'
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
        
        /**
     * @OA\Get(
     *   path="/api/getAllPinnedNotes",
     *   summary="Display All Pinned Notes",
     *   description=" Display All Pinned Notes",
     *   @OA\RequestBody(
     *
     *    ),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=200, description="Fetched All Pinned Notes Successfully"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes the User access token and 
     * checks if it authorised or not. 
     * If Authorized, it returns all the pinned notes successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPinnedNotes()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            } else {
                $userNotes = Notes::getPinnedNotes($user);
                if (!$userNotes) {
                    Log::error('Notes Not Found');
                    throw new FundoNotesException('Notes Not Found', 404);
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Found All Pinned Notes Successfully',
                    'notes' => $userNotes
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }



    /**
     * @OA\Post(
     *   path="/api/archiveNoteById",
     *   summary="Archive Note",
     *   description=" Archive Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Archived Sucessfully"),
     *   @OA\Response(response=404, description="Notes not Found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     *
     * This function takes the User access token and checks if it
     * authorised or not and it takes the note_id and Archives it
     * successfully if notes exist.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function archiveNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $noteObject = new Notes();
            $user = JWTAuth::parseToken()->authenticate();
            $note = $noteObject->noteId($request->id);

            if (!$note) {
                Log::error('Notes Not Found', ['user' => $user, 'id' => $request->id]);
                throw new FundoNotesException('Notes Not Found', 404);
            }

            if ($note->archive == 0) {
                if ($note->pin == 1) {
                    $note->pin = 0;
                    $note->save();
                }
                $note->archive = 1;
                $note->save();

                Log::info('notes Archived', ['user_id' => $user, 'note_id' => $request->id]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Note Archived Sucessfully'
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Post(
     *   path="/api/unarchiveNoteById",
     *   summary="Unarchive Note",
     *   description=" Unarchive Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Unarchived Sucessfully"),
     *   @OA\Response(response=404, description="Notes not Found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * This function takes the User access token and checks if it
     * authorised or not and it takes the note_id and Unarchives it
     * successfully if notes exist.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function unarchiveNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $noteObject = new Notes();
            $user = JWTAuth::parseToken()->authenticate();
            $note = $noteObject->noteId($request->id);

            if (!$note) {
                Log::error('Notes Not Found', ['user' => $user, 'id' => $request->id]);
                throw new FundoNotesException('Notes Not Found', 404);
            }

            if ($note->archive == 1) {
                $note->archive = 0;
                $note->save();

                Log::info('notes UnArchived', ['user_id' => $user, 'note_id' => $request->id]);
                return response()->json([
                    'status' => 20,
                    'message' => 'Note UnArchived Sucessfully'
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Get(
     *   path="/api/getAllArchivedNotes",
     *   summary="Display All Archived Notes",
     *   description=" Display All Archived Notes",
     *   @OA\RequestBody(
     *
     *    ),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=200, description="Found All Archived Notes Successfully"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes the User access token and 
     * checks if it authorised or not. 
     * If Authorized, it returns all the archived notes successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllArchivedNotes()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            } else {
                $userNotes = Notes::getArchivedNotesandItsLabels($user);
                if (!$userNotes) {
                    Log::error('Notes Not Found For User:: ' . $user->id);
                    throw new FundoNotesException('Notes Not Found', 404);
                }

                Log::info('Fetched All Archived Notes Successfully');
                return response()->json([
                    'message' => 'Fetched All Archived Notes Successfully',
                    'notes' => $userNotes
                ], 200);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Post(
     *   path="/api/colourNoteById",
     *   summary="Colour Note",
     *   description=" Colour Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id" , "colour"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="colour", type="string")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Coloured Sucessfully"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=406, description="Colour Not Specified in the List"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes the User access token and 
     * checks if it authorised or not and it takes the note_id and 
     * colours it successfully if notes exist.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function colourNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'colour' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $noteObject = new Notes();
            $user = JWTAuth::authenticate($request->token);
            $note = $noteObject->noteId($request->id);


            if (!$note) {
                Log::error('Notes Not Found', ['user' => $user, 'id' => $request->id]);
                return response()->json([
                    'status' => 404,
                    'message' => 'Notes not Found'
                ], 404);
            }

            $colours  =  array(
                'green' => 'rgb(0,255,0)',
                'red' => 'rgb(255,0,0)',
                'blue' => 'rgb(0,0,255)',
                'yellow' => 'rgb(255,255,0)',
                'grey' => 'rgb(128,128,128)',
                'purple' => 'rgb(128,0,128)',
                'brown' => 'rgb(165,42,42)',
                'orange' => 'rgb(255,165,0)',
                'pink' => 'rgb(255,192,203)',
                'black' => 'rgb(0,0,0)',
                'silver' => 'rgb(192,192,192)',
                'teal' => 'rgb(0,128,128)',
                'white' => 'rgb(255,255,255)',
            );

            $colour_name = strtolower($request->colour);

            if (isset($colours[$colour_name])) {
                $note->colour = $colours[$colour_name];
                $note->save();

                Log::info('notes coloured', ['user_id' => $user, 'note_id' => $request->id]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Note coloured Sucessfully'
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Colour Not Specified in the List'
                ]);
                throw new FundoNotesException('Colour Not Specified in the List', 406);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }


    // /** * @OA\Get(
    //  *   path="/api/paginationNote",
    //  *   summary="Pagination",
    //  *   description="Pagination of Notes",
    //  *   @OA\RequestBody(),
    //  *   @OA\Response(response=200, description="Pagination Applied to all Notes")
    //  * )
    //  * 
    //  * Function to view all notes,
    //  * 4 notes per page will be displayed.
    //  * 
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function paginationNote()
    // {
    //     $allNotes = Notes::paginate(4);
    //     return response()->json([
    //         'status'=>200,
    //         'message' => 'Pagination aplied to all Notes',
    //         'notes' =>  $allNotes,
    //     ]);
    // }

        /**
     * @OA\Post(
     *   path="/api/searchNotes",
     *   summary="Search Note",
     *   description=" Search Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"search"},
     *               @OA\Property(property="search", type="string")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Fetched Sucessfully"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes the User access token and search key 
     * if the access token is valid, it returns all the notes 
     * which has given search key for that particular user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchNotes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $searchKey = $request->input('search');
            $user = JWTAuth::parseToken()->authenticate();

            if ($user) {
                $usernotes = Notes::getSearchedNote($searchKey, $user);
                
                if ($usernotes == '[]') {
                    return response()->json([
                        'message' => 'Notes Not Found'
                    ], 404);
                }
                return response()->json([
                    'message' => 'Found Notes Successfully',
                    'notes' => $usernotes
                ], 200);
            }
            Log::error('Invalid Authorization Token');
            throw new FundoNotesException('Invalid Authorization Token', 401);
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    
}