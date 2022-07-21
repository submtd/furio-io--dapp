<?php

namespace App\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vault extends Model
{
    use GeneratesUuid;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'address_id',
        'start_time',
        'balance',
        'deposited',
        'compounded',
        'claimed',
        'taxed',
        'awarded',
        'negative',
        'penalized',
        'maxed',
        'banned',
        'team_wallet',
        'complete',
        'maxed_rate',
        'direct_referrals',
        'airdrop_sent',
        'airdrop_received',
    ];

    /**
     * Belongs to address.
     *
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
