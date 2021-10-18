<?php

namespace Jiannius\Atom\JsonResources;

class File extends JsonResource
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
            'mime' => $this->mime,
            'type' => $this->type,
            'size' => $this->size,
            'url' => $this->url,
            'data' => $this->data,

            // relationship
        ];
    }
}
