<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    use Notifiable, HasApiTokens; 


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
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

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * Mutator for first name attribute
     * Before saving it to database first letter will be changed to upper case
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['firstname'] = ucfirst($value);
    }

    /**
     * Mutator for last name attribute
     * Before saving it to database first letter will be changed to upper case
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['lastname'] = ucfirst($value);
    }

    /**
     * Function to get user details by email
     * Passing the email as parameter
     * 
     * @return array
     */
    public static function getUserByEmail($email){
        $user = User::where('email', $email)->first();
        return $user;
    }

    public function collaborators()
    {
        return $this->hasMany('App\Models\Collaborator');
    }

    public function notes()
    {
        return $this->hasMany('App\Models\Notes');
    }  
    public function labels()
    {
        return $this->hasmany('App\Models\Labels');
    }
    public function label_notes()
    {
        return $this->hasmany('App\Models\LabelNotes');
    }
    
}
