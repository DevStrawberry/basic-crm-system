<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query()->with('role');
        $roles = Role::all();

        // Filtro pelo status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro pelo perfil
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtro por nome ou email
        if ($request->filled('search')) {
            $search = $request->get('search', "");
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
        }
        $users = $query->orderBy('name', 'asc')->paginate(10);;

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Gera senha aleatória
        $password = Str::random(10);
        $params['password'] = Hash::make($password);

        // Verifica se já existe (inclusive soft deleted)
        $user = User::withTrashed()->where('email', $params['email'])->first();

        if ($user) {
            // Usuário existe e está deletado -> restaurar
            if ($user->trashed()) {
                $params['must_change_password'] = true;

                $user->restore();
                $user->update($params);
            }
        } else {
            // Criar novo usuário
            $user = User::create($params);
        }

        // Envia e-mail
        try {
            Mail::to($user->email)->send(new UserCreatedMail($user, $password));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return back()->withErrors(['error' => 'Falha no envio do e-mail ao usuário']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::query()->findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::query()->findOrFail($id);

        $params = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        if($user->update($params)) {
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário atualizado com sucesso');
        };

        return redirect()->route('admin.users.index')
            ->withErrors(['error' => 'Erro ao atualizar usuário']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::query()->findOrFail($id);

        if($user->delete()){
            return redirect()
                ->route('admin.users.index')
                ->with(['success' => 'Usuário removido com sucesso']);
        };

        return redirect()->route('admin.users.index')
            ->withErrors(['error' => 'Erro ao remover usuário']);
    }
}
