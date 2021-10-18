<?php

namespace Jiannius\Atom\Controllers;

use App\Models\User;
use App\Requests\UserStoreRequest;
use App\Http\Controllers\Controller;
use Jiannius\Atom\Traits\MultiTenant;

class UserController extends Controller
{
    /**
     * User account
     * 
     * @return Response
     */
    public function account()
    {
        if (request()->isMethod('post')) {
            $user = request()->user();

            if (request()->filled('password')) request()->merge(['password' => bcrypt(request()->input('password'))]);

            $user->fill(request()->all())->save();

            return back()->with('toast', 'Account Updated::success');
        }

        return inertia('user/account');
    }

    /**
     * User list
     * 
     * @return Response
     */
    public function list()
    {
        $users = User::fetch();
        
        if (request()->isMethod('post')) return back()->with('async', $users);

        return inertia('user/list', [
            'users' => $users,
        ]);
    }

    /**
     * Store user
     * 
     * @param UserStoreRequest $request
     * @return Response
     */
    public function store(UserStoreRequest $request)
    {
        $request->validated();

        $user = User::findOrNew($request->id);

        $user->fill($request->input('user'))->push();

        if ($request->has('user.abilities')) {
            $user->abilities()->sync($request->input('user.abilities'));
        }

        if ($request->has('user.join_team')) {
            $user->teams()->sync(
                $user->teams->pluck('id')->push($request->input('user.join_team'))->toArray()
            );
        }

        if ($request->has('user.leave_team')) {
            $user->teams()->sync(
                $user->teams()->where('teams.id', '<>', $request->input('user.leave_team'))->get()
                    ->pluck('id')
                    ->toArray()
            );
        }

        if (!$request->id) $user->invite();

        return redirect($request->query('redirect') ?? route('user.update', [$user->id]))
            ->with('toast', $request->query('toast') ?? 'User saved::success');
    }

    /**
     * Send account activation invitation
     * 
     * @return Response
     */
    public function invite()
    {
        $user = User::findOrFail(request()->id);

        $user->invite();

        return back()->with('toast', 'Email Sent::success');
    }

    /**
     * Delete user
     * 
     * @return Response
     */
    public function delete()
    {
        User::where('id', '<>', request()->user()->id)
            ->whereIn('id', explode(',', request()->id))
            ->get()
            ->each(fn($user) => $user->delete());

        return redirect(request()->query('redirect') ?? route('user.list'))
            ->with('toast', request()->query('toast') ?? 'User Deleted');
    }
}