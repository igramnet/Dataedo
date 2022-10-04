<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class Users2Controller extends Controller

{
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUsers(Request $request)
    {
        foreach ($request as $user) {

            $data = User::findOrFail($user);
            $userID = $data->id;

            $user->validate([
                'name' => 'required|min:10',
                'login' => 'required|min:2',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|max:200',
            ]);

            User::whereId($userID)->update([
                'name' => $user->name,
                'login' => $user->login,
                'email' => $user->email,
                'password' => Hash::make($user->password)
            ]);

        }
        return Redirect::back()->with(['success', 'All users updated.']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeUsers(Request $request)
    {

        foreach ($request as $user) {

            $validator = $user->validate([
                'name' => 'required|min:10',
                'login' => 'required|min:2',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|max:200',
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $this->sendEmail($user);
        }
        return Redirect::back()->with(['success', 'All users created.']);
    }

    private function sendEmail(array $user): void
    {
        $message = 'Account has beed created. You can log in as <b>' . $user['login'] . '</b>';
        if ($user['email']) {

            Mail::raw($message, function ($message) use ($user) {
                $message->from('no-reply@laravel.com', 'Laravel Project');
                $message->to($user['email'])->cc('support@company.com');
            });

        }
    }
}
