<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return self::todo($this);
    }

    public static function todo($t){

        $data = [
            'id' => $t->id,
            'title' => $t->title,
            'description'=> $t->description,
            'user' => $t->user,
            'created_at' => $t->created_at->format('g:i A, d M Y'),
            'updated_at' => $t->updated_at->format('g:i A, d M Y'),
        ];

        return $data;
    }
}
