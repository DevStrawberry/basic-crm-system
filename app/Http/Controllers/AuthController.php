<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        if (!User::where('email', $credentials['email'])->exists()) {
            return back()->withErrors([
                'login' => 'Usuário não encontrado'
            ])->onlyInput('email');
        }

        if (User::where('email', $credentials['email'])->where('status', 'inactive')->exists()) {
            return back()->withErrors([
                'login' => 'Usuário não autorizado'
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = strtolower($user->role?->name ?? '');

            if ($role == 'administrador') {
                return redirect()->route('admin.dashboard.index');
            }
            if (in_array($role,['gestor', 'assessor'])) {
                return redirect()->route('dashboard.index');
            }
        }

        return back()->withErrors([
            'login' => 'Credenciais inválidas'
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
        return view('auth.reset-password');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->status = 'active';
        $user->save();

        $role = strtolower($user->role?->name ?? '');

        if(in_array($role, ['gestor', 'assessor'])){
            return redirect()->route('dashboard.index')
                ->with('success', 'Senha alterada com sucesso!');
        } elseif ($role == 'administrador') {
            return redirect()->route('admin.dashboard.index')
                ->with('success', 'Senha alterada com sucesso!');
        }

        return redirect()->route('auth.login')
            ->withErrors(['error' => 'Erro ao alterar a senha!']);
    }


    public function sendResetPasswordEmail(Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return back()->withErrors(['email' => 'Usuário não encontrado']);
        }

        if($user->status == 'inactive') {
            return back()->withErrors(['email' => 'Usário inativo']);
        }

        $tempPassword = Str::random(10);
        $user->password = Hash::make($tempPassword);
        $user->must_change_password = true;
        $user->save();

        // Envia e-mail ao usuário
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $tempPassword));
        } catch (\Exception $exception) {
            Log::log('Error', $exception->getMessage());
            return back()->withErrors(['email' => 'Falha no envio do e-mail']);
        }

        return redirect()->route('auth.login')
            ->with('success', 'Senha temporária enviada para seu e-mail');
    }
}
