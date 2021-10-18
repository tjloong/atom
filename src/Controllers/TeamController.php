<?php

namespace Jiannius\Atom\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Tenant;
use App\Requests\TeamStoreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facade\Validator;
use Jiannius\Atom\Traits\MultiTenant;

class TeamController extends Controller
{
    /**
     * Create team
     * 
     * @return Response
     */
    public function create()
    {
        $data = [];

        if (in_array(MultiTenant::class, class_uses_recursive(Team::class)) && request()->query('tenant_id')) {
            $data['tenant'] = Tenant::find(request()->query('tenant_id'));
        }

        return inertia('team/create', $data);
    }

    /**
     * Update team
     * 
     * @return Response
     */
    public function update()
    {
        $team = Team::findOrFail(request()->id);
        $users = User::teamId($team->id)->fetch();

        return inertia('team/update', [
            'team' => $team,
            'users' => $users,
        ]);
    }

    /**
     * List teams
     *
     * @return Response
     */
    public function list()
    {
        $teams = Team::fetch();

        if (request()->isMethod('post')) return back()->with('async', $teams);

        return inertia('team/list', [
            'teams' => $teams,
        ]);
    }

    /**
     * Store team
     *
     * @param TeamStoreRequest $request
     * @return Response
     */
    public function store(TeamStoreRequest $request)
    {
        $request->validated();

        $team = Team::findOrNew($request->id);

        $team->fill($request->input('team'))->save();

        return redirect($request->query('redirect') ?? route('team.update', ['id' => $team->id]))
            ->with('toast', $request->query('toast') ?? 'Team saved::success');
    }

    /**
     * Delete team
     *
     * @return Response
     */
    public function delete()
    {
        if (Team::whereIn('id', explode(',', request()->id))->has('users')->count() > 0) {
            return back()->with('alert', 'There are users assigned under this team::alert');
        }

        Team::whereIn('id', explode(',', request()->id))->get()->each(fn($team) => $team->delete());

        return redirect(request()->query('redirect') ?? route('team.list'))
            ->with('toast', request()->query('toast') ?? 'Team deleted');
    }
}
