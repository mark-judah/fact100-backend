<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\helper\UsesUuid as uniqueId;

class Podcast extends Model
{
    use uniqueId;

    protected $fillable = [
        'title',
        'about',
        'season',
        'episode',
        'category',
        'status',
        'cover_photo',
        'audio',
        'posted_by',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
