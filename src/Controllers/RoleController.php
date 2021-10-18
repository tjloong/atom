<?php

namespace Jiannius\Atom\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\Ability;
use App\Requests\RoleStoreRequest;
use App\Http\Controllers\Controller;
use Jiannius\Atom\Traits\MultiTenant;

class RoleController extends Controller
{
    /**
     * Create role
     * 
     * @return Response
     */
    public function create()
    {
        $data['clonables'] = Role::clonable()->get();

        if (in_array(MultiTenant::class, class_uses_recursive(Role::class)) && request()->query('tenant_id')) {
            $data['tenant'] = Tenant::find(request()->query('tenant_id'));
        }

        return inertia('role/create', $data);
    }

    /**
     * Update role
     * 
     * @return Response
     */
    public function update()
    {
        $tab = request()->tab ?? 'abilities';
        $tabs = [
            ['value' => 'abilities', 'label' => 'Permissions'],
            ['value' => 'users', 'label' => 'Users'],
        ];

        $role = Role::findOrFail(request()->id);

        $data = [
            'tabs' => $tabs,
            'role' => $role,
        ];

        if ($tab === 'users') $data['users'] = $role->users()->fetch();
        if ($tab === 'abilities') {
            $data['abilities'] = Ability::all()->map(function($ability) use ($role) {
                $ability->enabled = $role->abilities()->where('abilities.id', $ability->id)->count() > 0
                    || $role->is_admin;

                return $ability;
            });
        }

        return inertia('role/update/' . $tab, $data);
    }

    /**
     * Role list
     *
     * @return Response
     */
    public function list()
    {
        $roles = Role::assignable()->fetch();

        return inertia('role/list', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store role
     *
     * @param RoleStoreRequest $request
     * @return RoleResource
     */
    public function store(RoleStoreRequest $request)
    {
        $request->validated();

        $role = Role::findOrNew($request->id);

        $role->fill($request->input('role'))->save();

        if ($request->has('role.clone_from_id') && !$request->has('id')) {
            if ($source = Role::find($request->input('role.clone_from_id'))) {
                $role->abilities()->sync($source->abilities->pluck('id')->toArray());
            }
        }

        if ($request->has('role.abilities')) {
            $role->abilities()->sync($request->input('role.abilities'));
        }

        return redirect($request->query('redirect') ?? route('role.update', ['id' => $role->id]))
            ->with('toast', $request->query('toast') ?? 'Role saved::success');
    }

    /**
     * Delete role
     *
     * @return Response
     */
    public function delete()
    {
        if (Role::whereIn('id', explode(',', request()->id))->has('users')->count() > 0) {
            return back()->with('alert', 'There are users assigned under this role::alert');
        }

        Role::whereIn('id', explode(',', request()->id))->get()->each(fn($role) => $role->delete());

        return redirect(request()->query('redirect') ?? route('role.list'))
            ->with('toast', request()->query('toast') ?? 'Role deleted');
    }
}
