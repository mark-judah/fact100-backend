<?php

namespace App\Models;

use App\Models\helper\UsesUuid as uniqueId;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use uniqueId;

    protected $fillable = [
        'category',
    ];

}
