<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ContactSource;
use App\Models\SocialNetwork;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::withCount('leads')->paginate(10);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contact_sources = ContactSource::all();
        $social_networks = SocialNetwork::all();

        return view('clients.create', compact('contact_sources', 'social_networks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cpf' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required|max:2',
            'contact_source' => 'required',
        ]);

        $cpf = str_replace(['.','-'], "", $request->cpf);
        $phone = str_replace([' ', '(', ')', '-'], "", $request->phone);
        $social_networks = $request->social_networks;
        dd($social_networks);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
