<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
        }
        $users = $query->orderBy('id', 'asc')->paginate(10);

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
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Gera uma senha de 10 caracteres aleatórios
        $password = Str::random(10);
        $params['password'] = Hash::make($password);

        // Insere no banco
        $user = User::create($params);

        if($user) {
            // Envia email com usuário e senha aleatória
            Mail::to($user->email)->send(new UserCreatedMail($user, $password));

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário cadastrado com sucesso');
        }

        return redirect()->route('admin.users.index')
            ->withErrors(['error' => 'Erro ao cadastrar usuário']);
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
