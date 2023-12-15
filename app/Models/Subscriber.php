<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\helper\UsesUuid as uniqueId;

class Subscriber extends Model
{
    use uniqueId;

    protected $fillable = [
        'email',
    ];
}
