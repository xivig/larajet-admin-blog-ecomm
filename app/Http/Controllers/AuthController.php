<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\View\View;

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
     * @return View|Application|Factory ()
     */
    public function profile(): Factory|View|Application
    {
        return view('auth.profile');
    }

    /**
     * Write code on Method
     *
     * @return View|Application|Factory ()
     */
    public function inbox(): Factory|View|Application
    {
        return view('auth.inbox');
    }

    /**
     * Write code on Method
     *
     * @return View|Application|Factory ()
     */
    public function setting(): Factory|View|Application
    {
        return view('auth.setting');
    }

}
