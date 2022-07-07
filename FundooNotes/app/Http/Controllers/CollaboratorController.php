<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Exceptions\FundoNotesException;
use App\Models\Collaborator;
use App\Models\Notes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Notifications\mailtocollab;



class CollaboratorController extends Controller
{
    /**    /**
     * @OA\Post(
     *   path="/api/addCollaboratorByNoteId",
     *   summary="Add Colaborator to a specific Note",
     *   description="Add Colaborator a to specific Note",
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
     *   @OA\Response(response=201, description="Collaborator Created Sucessfully"),
     *   @OA\Response(response=202, description="Collab Not Added"),
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
     * it will successfully creates a collaborator.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function addCollaboratorByNoteId(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'note_id' => 'required|integer',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }

            $regMail = User::where('email', $request->email)->first();
            if (!$regMail) {
                Log::info('Email to be Collaborate with is not Registered');
                throw new FundoNotesException('Email to be Collaborate with is not Registered', 404);
            }

            $note = Notes::where('id', $request->note_id)->where('user_id', $currentUser->id)->first();
            if (!$note) {
                Log::error('Notes Not Found For User:: ' . $currentUser->id);
                throw new FundoNotesException('Notes Not Found', 404);
            }

            $collab = Collaborator::where('email', $request->email)->where('note_id', $request->note_id)->first();
            if ($collab) {
                Log::info('Collaborator Already Created');
                throw new FundoNotesException('Collaborator Already Created', 409);
            }

            $collab = new Collaborator();
            $collab->note_id = $request->get('note_id');
            $collab->email = $request->get('email');
            $collaborator = Notes::select('id', 'title', 'description')->where('id', $request->note_id)->first();
            $collab->user_id = $currentUser->id;
            $collab->save();

            $userTo = User::where('email', $request->email)->first();

            // Mail::send('collabmail', $collabNote, function ($message) use ($sendTo, $sendName) {
            //     $message->to($sendTo, $sendName)->subject('Sharing Note');
            //     $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            // });

            $delay = now()->addSeconds(300);
            $userTo->notify((new MailTocollab($currentUser->email, $collaborator))->delay($delay));

            Log::info('Collaborator created Sucessfully');
            return response()->json([
                'message' => 'Collaborator Created Sucessfully',
            ], 201);
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
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
     *   @OA\Response(response=201, description="Collaborator updated Sucessfully"),
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

    function updateCollaboratorById(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'note_id' => 'required|integer',
                'updated_title' => 'string|between:3,30',
                'updated_description' => 'string|between:3,1000'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);
            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            }

            $collab1 = Collaborator::where('user_id', $currentUser->id)->where('note_id', $request->note_id)->first();
            $collab2 = Collaborator::where('email', $currentUser->email)->where('note_id', $request->note_id)->first();
            if ($collab1 || $collab2) {

                $collabNote = Notes::where('id', $request->note_id)->first();
                if (!$collabNote) {
                    Log::error('Notes Not Found');
                    throw new FundoNotesException('Notes Not Found', 404);
                }

                $collabNote->update([
                    'title' => $request->updated_title,
                    'description' => $request->updated_description
                ]);
                Log::info('Note updated Successfully');
                return response()->json([
                    'message' => 'Note updated Successfully',
                ], 200);
            }

            Log::error('Note Not Updated');
            throw new FundoNotesException('Note Not Updated', 400);
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
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