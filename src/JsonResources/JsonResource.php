<?php

namespace Jiannius\Atom\JsonResources;

use App\JsonResources\Tenant;
use Jiannius\Atom\Traits\MultiTenant;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

class JsonResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->getResource($request);

        if (isset($this->id)) $resource['id'] = $this->id;
        if (isset($this->created_at)) $resource['created_at'] = $this->created_at;
        if (isset($this->updated_at)) $resource['updated_at'] = $this->updated_at;

        // attach tenant if class is using MultiTenant trait
        if (
            in_array(MultiTenant::class, class_uses_recursive($this->getModel()))
            && isset($this->tenant_id)
        ) {
            if (class_exists(Tenant::class)) $resource['tenant'] = new Tenant($this->tenant);
            else $resource['tenant'] = $this->tenant;
        }

        return $resource;
    }
}