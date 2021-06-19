<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return self::toUser($this);
    }

    public static function toUser($u){

        $data = [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'email_verified' => $u->email_verified,
            'created_at' => $u->created_at->format('g:i A, d M Y'),
            'updated_at' => $u->updated_at->format('g:i A, d M Y'),
        ];

        if(isset($u->token)){
            $data['access_token'] = $u->token;
        }

        return $data;
    }
}
