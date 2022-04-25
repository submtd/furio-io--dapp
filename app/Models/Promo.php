<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use GeneratesUuid;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'max',
        'price',
        'value',
        'available',
    ];
}
