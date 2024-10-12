<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{

    public static $wrap = false;

    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'minmark' => $this->minmark
        ];
    }
}
