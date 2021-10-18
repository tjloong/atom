<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Providers\RouteServiceProvider;
use Jiannius\Atom\Traits\AuthManager;

class AuthController extends Controller
{
    use AuthManager;
    
    /**
     * Custom action after login process is done
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function authenticated($user)
    {
        //
    }

    /**
     * Validate and create a newly registered user
     * 
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function createUser($request)
    {
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'agree_tnc' => 'accepted',
        ])->validate();

        $role = Role::name('administrator')->first();

        return User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role_id' => $role ? $role->id : null,
        ]);
    }

    /**
     * Custom action after register process is done
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function registered($user)
    {
        //
    }

    /**
     * Custom action after user is logged out
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function loggedOut($user)
    {
        //
    }

    /**
     * Custom action after email verification link is sent
     *
     * @return \Illuminate\Http\Response
     */
    public function verificationLinkSent($user)
    {
        //
    }

    /**
     * Custom action after email is verified
     *
     * @return \Illuminate\Http\Response
     */
    public function emailVerified($user)
    {
        //
    }
}