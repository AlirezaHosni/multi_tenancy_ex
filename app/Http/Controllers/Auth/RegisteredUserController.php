<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\tenant\Tenant;
use App\Models\tenant\User;
use App\Models\User as BaseUser;
use App\Providers\AppServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $tenants = Tenant::all();
        return view('auth.register', compact('tenants'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tenant' => ['nullable', 'string', 'exists:tenants,id']
        ]);

        $user = BaseUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($request->tenant)
        {
            Tenant::where('id', $request->tenant)->first()->run(function() use ($user){
                User::create([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                ]);

            });

            $user->tenant = $request->tenant;
            $user->save();

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('tenant.welcome', ['tenant'=>$request->tenant]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route(RouteServiceProvider::HOME);
    }
}
