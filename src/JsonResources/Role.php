<?php

namespace Jiannius\Atom\JsonResources;

class Role extends JsonResource
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
            'access' => $this->access,
            'is_system' => $this->is_system,

            // relationship
            'abilities' => Ability::collection($this->abilities),
        ];
    }
}
