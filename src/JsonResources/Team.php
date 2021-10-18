<?php

namespace Jiannius\Atom\JsonResources;

class Team extends JsonResource
{
    /**
     * Get the resource
     * 
     * @return array
     */
    public function getResource($request)
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'counter' => $this->counter,
        ];
    }
}
