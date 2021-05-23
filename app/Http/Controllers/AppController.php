<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('app');
    }

    /**
     * Show change password view
     *
     * @return void
     */
    public function changePassword()
    {
        return view('change_password');
    }

    /**
     * Update user password
     *
     * @param  mixed $request
     * @return void
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'  => 'required',
            'new_password'  => 'required|same:confirm_password|different:old_password',
            'confirm_password'  => 'required|same:new_password',
        ]);

        $validator->after(function ($validator) use ($request) {
            $oldPassword = auth()->user()->password;
            if (!Hash::check($request->old_password, $oldPassword)) {
                $validator->errors()->add('old_password', 'Invalid password!');
            }
        });

        $user = $validator->validate();
        User::find(auth()->user()->id)->update([
            'password'  => Hash::make($user['new_password'])
        ]);

        Auth::logout();

        return redirect('/');
    }
}
