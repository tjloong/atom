<?php

namespace Jiannius\Atom\JsonResources;

class User extends JsonResource
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
            'email' => $this->email,
            'role_id' => $this->role_id,
            'is_admin' => $this->isRole('admin'),
            'is_root' => $this->when($request->user()->isRole('root'), true),
            'is_pending_activate' => $this->is_pending_activate,
            'email_verified_at' => $this->email_verified_at,

            // relationships
            'role' => $this->role,
            'abilities' => $this->abilities,
        ];
    }
}
