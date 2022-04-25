<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;

class PresaleReservation extends Model
{
    use GeneratesUuid;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'salt',
        'quantity',
    ];
}
