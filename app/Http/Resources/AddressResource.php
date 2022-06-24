<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $return = [
            'type' => 'addresses',
            'id' => $this->uuid,
            'attributes' => [
                'address' => $this->address,
                'nonce' => $this->nonce,
                'name' => $this->name,
                'direct_referrals' => $this->direct_referrals,
                'airdrops_sent' => $this->airdrops_sent,
                'twitter' => $this->twitter,
                'telegram' => $this->telegram,
                'discord' => $this->discord,
                'medium' => $this->medium,
                'facebook' => $this->facebook,
                'instagram' => $this->instagram,
                'admin' => (bool) $this->admin,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
        if($this->avatar) {
            $return['related']['avatar'] = AvatarResource::make($this->avatar);
        }

        return $return;
    }
}
