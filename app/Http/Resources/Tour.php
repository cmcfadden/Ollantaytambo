<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Tour extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
            'title' => $this->title,
            'tour_content' => $this->tour_content,
            'start_location' =>$this->start_location?["lat"=>$this->start_location->getLat(), "lng"=>$this->start_location->getLng()]:null,
            'stops' => $this->stops
        ];
    }
}
