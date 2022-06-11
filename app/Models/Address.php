<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model implements AuthenticatableContract
{
    use Authenticatable, GeneratesUuid;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'name',
    ];

    /**
     * Avatar.
     *
     * @return HasOne
     */
    public function avatar(): HasOne
    {
        return $this->hasOne(Avatar::class);
    }
}
