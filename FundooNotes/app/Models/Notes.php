<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Notes extends Model implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'notes';
    protected $fillable = [
        'id',
        'title',
        'description',
        'user_id',
        'pin',
        'archive',
        'colour',
        'label',
        
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Function to get Notes by Note_Id and User_Id
     * Passing the Note_id and User_id as the parameter
     * 
     * @return array
     */
    public static function getNotesByNoteIdandUserId($id, $user_id)
    {
        $notes = Notes::where('id', $id)->where('user_id', $user_id)->first();
        return $notes;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function noteId($id) {
        return Notes::where('id', $id)->first();
    }
    /**
     * Function to get the pinned notes
     * Passing the user as a parameter
     * 
     * @return array
     */
    public static function getPinnedNotes($user)
    {
        $notes = Notes::where('user_id', $user->id)->where('pin',1)->get();

        return $notes;
    }



    /**
     * Function to get the Archived Notes
     * Passing the user as a parameter
     * 
     * @return array
     */
    public static function getArchivedNotes($user)
    {
        $notes = Notes::where('user_id', $user->id)->where('archive', 1)->get();

        return $notes;
    }

    /**
     * Function to get a searched Note 
     * Passing the Current User Data and Search Key as parameters
     * 
     * @return array
     */
    public static function getSearchedNote($searchKey, $currentUser){
        $usernotes = Notes::leftJoin('collaborators', 'collaborators.note_id', '=', 'notes.id')
        ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
        ->leftJoin('labels', 'labels.id', '=', 'label_notes.label_id')
        ->select('notes.id', 'notes.title', 'notes.description', 'notes.pin', 'notes.archive', 'notes.colour', 'collaborators.email as Collaborator', 'labels.labelname')
        ->where('notes.user_id', '=', $currentUser->id)->Where('notes.title', 'like', '%' . $searchKey . '%')
        ->orWhere('notes.user_id', '=', $currentUser->id)->Where('notes.description', 'like', '%' . $searchKey . '%')
        ->orWhere('notes.user_id', '=', $currentUser->id)->Where('labels.labelname', 'like', '%' . $searchKey . '%')
        ->orWhere('collaborators.email', '=', $currentUser->email)->Where('notes.title', 'like', '%' . $searchKey . '%')
        ->orWhere('collaborators.email', '=', $currentUser->email)->Where('notes.description', 'like', '%' . $searchKey . '%')
        ->orWhere('collaborators.email', '=', $currentUser->email)->Where('labels.labelname', 'like', '%' . $searchKey . '%')
        ->get();

        return $usernotes;
    }


}