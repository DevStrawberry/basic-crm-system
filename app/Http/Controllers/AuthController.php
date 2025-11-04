<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request) {

    }

    public function logout(Request $request) {

    }

    public function resetPassword(Request $request) {

    }

    public function sendResetPasswordEmail(Request $request) {

    }
}
