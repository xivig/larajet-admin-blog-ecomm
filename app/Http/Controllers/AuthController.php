<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AuthController
{
    /**
     * Write code on Method
     *
     * @return RedirectResponse ()
     */
    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    /**
     * Write code on Method
     *
     * @return View ()
     */
    public function profile(): View
    {
        return view('auth.profile');
    }

    /**
     * Write code on Method
     *
     * @return \Illuminate\Contracts\View\View|Application|Factory ()
     */
    public function inbox(): Factory|\Illuminate\Contracts\View\View|Application
    {
        return view('auth.inbox');
    }

    /**
     * Write code on Method
     *
     * @return \Illuminate\Contracts\View\View|Application|Factory ()
     */
    public function setting(): Factory|\Illuminate\Contracts\View\View|Application
    {
        return view('auth.setting');
    }

}
