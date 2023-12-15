<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\helper\UsesUuid as uniqueId;

class Comment extends Model
{
    use uniqueId;

    protected $fillable = [
        'post_id',
        'slug',
        'comment',
        'comment_by'
    ];

    public function posts(){
        return $this->belongsTo(Post::class);
    }
//    public function replies(){
//        return $this->hasMany(Replie::class);
//    }
}
