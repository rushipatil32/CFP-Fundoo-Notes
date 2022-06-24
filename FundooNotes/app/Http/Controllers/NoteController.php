<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notes;
use App\Models\Labels;
use App\Models\LabelNotes;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


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
     *   @OA\Response(response=200, description="notes created successfully"),
     *   @OA\Response(response=400, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    public function createNote(Request $request)
    {
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
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Autherization token'
            ]);
        } else {
            $note = Notes::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => $user_id,
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Note successfully created',
                'note' => $note
            ]);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/readNote",
     *      summary="Read Note",
     *      description="Read Note",
     * 
     *      @OA\Response(response=200, description="Notes found successfully"),
     *      @OA\Response(response=404, description="Invalid Autherization token")
     * )
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function readNote(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Invalid Autherization token'
            ]);
        } 
        $note = Notes::where('user_id', $user->id)->get();
        
        if(!$note){
            return response()->json([
                'status'=>400,
                'messge'=>'No note created by current user',
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'note' => $note
            ]);
        }
    }

        /** @OA\Get(
        * path="/api/readNoteById",
        * summary="Read Note",
        * description="Read Notes For an Particular User",
        * @OA\RequestBody(),
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

        $validator = Validator::make($request->only('id'), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->tojson(), 400);
        }

        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Authorization Token',
            ]);
        }

        $userid = $user->id;
        $note = Notes::where('user_id', $userid)->where('id', $request->id)->first();

        if (!$note) {
            return response()->json([
                'status' => 404,
                'message' => 'Notes not found'
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'note' => $note,
                'messege'=>'All notes found successfully'
            ]);
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,30',
            'description' => 'required|string|between:3,1000',
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Authorization Token',
            ]);
        }

        $note = Notes::where('user_id', $user->id)->where('id', $request->id)->first();

        if (!$note) {

            return response()->json([
                'status' => 404,
                'message' => 'Notes not found'
            ]);
        }

        $note->update([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => 400,
            'note' => $note,
            'mesaage' => 'Note Successfully updated',
        ]);
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
     *   @OA\Response(response=201, description="Enter Valid ID"),
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
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Authorization Token',
            ]);
        }

        $note = Notes::where('id', $request->id)->first();

        if (!$note) {
            return response()->json([
                'status' => 404,
                'mesaage' => 'Enter valid id',
            ]);
        } else {
            $note->delete($note->id);
            return response()->json([
                'status' => 200,
                'mesaage' => 'Note Successfully deleted',
            ]);
        }
    }


    function addNoteLabel(Request $request){

        $validator = Validator::make($request->all(), [
            'label_id' => 'required|integer',
            'note_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if(!$user){
            return response()->json([
                'message' => 'Invalid Token',
            ],400);
        }

        $notes = Notes::getNotesByNoteIdandUserId($request->note_id, $user->id);
        $label = Labels::getLabelByLabelIdandUserId($request->label_id, $user->id);

        if (!$notes || !$label) {
            return response()->json([
                'status'=>401,
                'message' => 'Note or label not found for user',
            ]);
        }

        else{
            $labelnote = LabelNotes::getLabelNotesbyLabelIdNoteIdandUserId($request->label_id, $request->note_id, $user->id);
            if ($labelnote) {
                return response()->json([
                    'status'=>402,
                    'message' => 'Note already have this label',
                ]);
            }
            else{

                $notelabel = LabelNotes::createNoteLabel($request, $user->id);

                return response()->json([
                    'status'=>200,
                    'message' => 'Label note added Successfully',
                    'notelabel' => $notelabel,
                ]);
            }
        }
    }

    function deleteNoteLabel(Request $request){

        $validator = Validator::make($request->all(), [
            'label_id' => 'required|integer',
            'note_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'status'=>400,
                'message' => 'Invalid Token',
            ]);
        }

            $labelnotes = LabelNotes::where('note_id', $request->note_id)->where('label_id', $request->label_id)->where('user_id', $user->id)->first();    
            if (!$labelnotes) {
                return response()->json([
                    'status'=>400,
                    'message' => 'Label and Note not found ',
                ]);
            }
            $labelnotes->delete($labelnotes->id);
            return response()->json([
                'status'=>200,
                'message' => 'label Note deleted Successfully',
            ]);
    }
}