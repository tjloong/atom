<?php

namespace Jiannius\Atom\JsonResources;

class Ability extends JsonResource
{
    /**
     * Get the resource
     *
     * @return array
     */
    public function getResource($request)
    {
        return [
            'module' => $this->module,
            'name' => $this->name,

            // relationship
            'pivot' => $this->pivot,
        ];
    }
}
