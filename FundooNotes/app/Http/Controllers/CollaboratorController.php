<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Models\Collaborator;
use App\Models\Notes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Mail;


class CollaboratorController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/addCollaboratorByNoteId",
     *   summary="Add Colaborator ",
     *   description=" Add Colaborator a to specific Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email" , "note_id"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="note_id", type="integer")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Collaborator Created Sucessfully"),
     *   @OA\Response(response=202, description="Collab Not Added"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=404, description="Not a Registered Email"),
     *   @OA\Response(response=403, description="Collaborator Already Created"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes User access token and 
     * checks if it is authorised or not and 
     * takes note_id, email if those parameters are valid 
     * it will successfully creates a collaborator.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function addCollaboratorByNoteId(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::authenticate($request->token);
        if(!$user){
            Log::error('Invalid Authorisation Token');
            return response()->json([
                'status'=>401,
                'message' => 'Invalid Authorisation token',
            ]);
        }

        $regMail = User::where('email',$request->email)->first();
        if(!$regMail){
            Log::warning('Email is not registered');
            return response()->json([
                'status'=>400,
                'message' => 'Email is not registered',
            ]);
        }
        $note = Notes::where('id',$request->note_id)->where('user_id',$user->id)->first();
        Log::error('No note found for this user');
        if(!$note){
            return response()->json([
                'status'=>404,
                'message' => 'No note found for this user',
            ]);
        }

        $collab = Collaborator::where('email',$request->email)->where('note_id',$request->note_id)->first();
        if($collab){
            Log::info('Collaborator already created for this email and note');
            return response()->json([
                'status'=>403,
                'message' => 'Collaborator Already created for this note and email',
            ], 403);
        }
        $collaborator = Collaborator::create([
            'status'=>200,
            'user_id' => $user->id,
            'note_id' => $request->note_id,
            'email' => $request->email,
        ]);

        $collabNote = array('id'=>$note->id,'title'=>$note->title,'description'=>$note->description);
        $sendTo = $request->email;
        $userTo = User::where('email',$request->email)->first();
        $sendName = $userTo->firstname;
        

        Mail::send('collabmail', $collabNote, function ($message) use ($sendTo,$sendName) {
            $message->to($sendTo,$sendName)->subject('Sharing Note');
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
        Log::info('Collaborator created Sucessfully');
        return response()->json([
            'status'=>200,
            'message' => 'Collaborator Created Sucessfully',
            'collaborator' => $collaborator,
        ]);

    }

    /**
     * @OA\Post(
     *   path="/api/updateCollaboratorById",
     *   summary="update Colaborator",
     *   description=" Update Colaborator Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"note_id","updated_title","updated_description"},
     *               @OA\Property(property="note_id", type="integer"),
     *               @OA\Property(property="updated_title", type="string"),
     *               @OA\Property(property="updated_description", type="string")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Collaborator updated Sucessfully"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=404, description="Not a Registered Email"),
     *   @OA\Response(response=409, description="Collaborator Already Created"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes User access token and 
     * checks if it is authorised or not and 
     * takes note_id, email if those parameters are valid 
     * it will successfully updates a collaborator.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function updateCollaboratorById(Request $request){

        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
            'updated_title' => 'string|between:3,30',
            'updated_description' => 'string|between:3,1000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::authenticate($request->token);
        if(!$user){
            Log::error('Invalid Authorisation Token');
            return response()->json([
                'staus'=>401,
                'message' => 'Invalid Authorisation token',
            ]);
        }

        $collab = Collaborator::where('user_id',$user->id)->where('note_id',$request->note_id)->first();
        if(!$collab){
            Log::error('Invalid Note id');
            return response()->json([
                'staus'=>400,
                'message' => 'Invalid Note id',
            ]);
        }

        $collabNote = Notes::where('id',$request->note_id)->where('user_id',$collab->user_id)->first();
        if(!$collabNote){
            Log::error('Note not found');
            return response()->json([
                'staus'=>404,
                'message' => 'Note not found',
            ]);
        }

        $collabNote->update([
            'title' => $request->updated_title,
            'description' => $request->updated_description
        ]);
        Log::info('Note updated Successfully');
            return response()->json([
                'staus'=>200,
                'message' => 'Note updated Successfully',
            ]);  
    }

     /**
     * @OA\Post(
     *   path="/api/deleteCollaboratorById",
     *   summary="delete Colaborator",
     *   description=" Delete Colaborator Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"note_id","email"},
     *               @OA\Property(property="note_id", type="integer"),
     *               @OA\Property(property="email", type="email"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Collaborator deleted Sucessfully"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=404, description="Not a Registered Email"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes User access token and 
     * checks if it is authorised or not and 
     * takes note_id, email if those parameters are valid 
     * it will successfully updates a collaborator.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function deleteCollaboratorById(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = JWTAuth::authenticate($request->token);
        if(!$user){
            Log::error('Invalid Authorisation Token');
            return response()->json([
                'status'=>401,
                'message' => 'Invalid Authorisation token',
            ]);
        }

        $collab = Collaborator::where('user_id',$user->id)->where('note_id',$request->note_id)->first();
        if(!$collab){
            Log::error('Invalid Note id');
            return response()->json([
                'status'=>400,
                'message' => 'Invalid Note id',
            ]);
        }
        $collab->delete();
        Log::info('collaborator deleted successfully');
            return response()->json([
                'status'=>200,
                'message' => 'collaborator deleted successfully',
            ]);  
    }

}