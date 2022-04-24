<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use GeneratesUuid;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
    ];
}
