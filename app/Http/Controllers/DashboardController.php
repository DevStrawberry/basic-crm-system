<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role?->name);

        if ($role == 'administrador') {
            return view('admin.dashboard.index');
        } elseif (in_array($role, ['gestor', 'assessor'])) {
            return view('dashboard.index');
        }

        return redirect()->route('auth.login');
    }
}
