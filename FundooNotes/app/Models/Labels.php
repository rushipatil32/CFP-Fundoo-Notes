<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;



class Labels extends Model implements JWTSubject
{
    use HasFactory;

    protected $table="labels";
    protected $fillable = [
        'labelname',
        'user_id',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Function to get label by the label_id and user_id
     * passing label_id and user_id as parameters
     * 
     * @return array
     */
    public static function getLabelByLabelIdandUserId($label_id, $user_id)
    {
        $label = Labels::where('id', $label_id)->where('user_id', $user_id)->first();
        return $label;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
