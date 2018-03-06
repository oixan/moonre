<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id','message','message2','category_id'
    ];

     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'reports_count'
    ];
    

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function likes()
    {
        return $this->belongsToMany('App\User','user_like_mood')->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

}
