<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\helper\UsesUuid as uniqueId;

class Message extends Model
{
    use uniqueId;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message'
    ];
}
