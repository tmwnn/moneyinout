<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\Users\UsersService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ProfileController extends Controller
{
    protected $usersService;

    /**
     * Create a new controller instance.
     *
     * @param UsersService $usersService
     */
    public function __construct(
        UsersService $usersService
    )
    {
        $this->usersService = $usersService;
    }

    public function edit()
    {
        $userId = \Auth::user()->id ?? 0;
        if (!$userId) {
            abort(404);
        }
        $userInfo = $this->usersService->find($userId);
        return view('auth.profile', $userInfo);
    }

    public function update(Request $request)
    {
        $userId = \Auth::user()->id ?? 0;
        if (!$userId) {
            abort(404);
        }

        $validateArr = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
        ];

        if ($request->get('password') || $request->get('password_confirmation')) {
            $validateArr['password'] = 'required|string|min:8|confirmed';
        }
        $this->validate($request, $validateArr);

        $userInfo = $request->toArray();
        if (!empty($userInfo['password'])) {
            if ($userInfo['password'] == $userInfo['password_confirmation']) {
                unset($userInfo['password_confirmation']);
                $userInfo['password'] = Hash::make($userInfo['password']);
            }
        } else {
            unset($userInfo['password']);
            unset($userInfo['password_confirmation']);
        }
        $userInfo = $this->usersService->update($userId, $userInfo);
        return view('auth.profile', $userInfo);
    }
}
