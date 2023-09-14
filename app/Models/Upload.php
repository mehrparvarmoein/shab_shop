<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Upload extends Model
{
    use HasFactory;
    protected $guarded = [];

    const TYPE_ORIGINAL = 1;
    const TYPE_RESIZE   = 2;


    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }
}
