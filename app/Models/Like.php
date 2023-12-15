<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\helper\UsesUuid as uniqueId;

class Like extends Model
{
    use uniqueId;

    protected $fillable = [
        'post_id',
        'liked_by',
        'blog_slug'
    ];

    public function posts(){
        return $this->belongsTo(Post::class);
    }
}
