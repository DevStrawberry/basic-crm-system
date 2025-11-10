<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = strtolower($user->role?->name ?? '');

            if ($role == 'administrador') {
                return redirect()->route('admin.dashboard.index');
            }
            if ($role == 'gestor' || $role == 'assessor') {
                return redirect()->route('dashboard.index');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas',
        ])->onlyInput('email');
    }

    public function logout(Request $request) {
        Auth::logout();

        // Invalida a sessão e o token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    public function resetPassword(Request $request) {

    }

    public function showChangePasswordForm()
    {
        return view('auth.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user['password'] = Hash::make($request->password);
        $user['must_change_password'] = false;
        $user['status'] = true;
        $user->save();

        $role = strtolower($user->role?->name ?? '');

        if($role == 'gestor' || $role == 'assessor'){
            return redirect()->route('dashboard.index')
                ->with('success', 'Senha alterada com sucesso!');
        } elseif ($role == 'administrador') {
            return redirect()->route('admin.dashboard.index')
                ->with('success', 'Senha alterada com sucesso!');
        }

        return redirect()->route('auth.login');
    }


    public function sendResetPasswordEmail(Request $request) {

    }
}
