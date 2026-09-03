<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ClientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_clients', only: ['index', 'show']),
            new Middleware('can:create_clients', only: ['create', 'store']),
            new Middleware('can:edit_clients', only: ['edit', 'update']),
            new Middleware('can:delete_clients', only: ['destroy']),
        ];
    }

    public function index()
    {
        // Mengambil data client terbaru, ditambah pagination
        $clients = Client::latest()->paginate(10);

        return Inertia::render('Clients/Index', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(Request $request)
    {
        // Validasi data yang masuk
        $validated = $request->validate([
            'client_code' => 'required|string|unique:clients,client_code|max:255',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('message', 'Client created successfully.');
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'client_code' => 'required|string|max:255|unique:clients,client_code,'.$client->id,
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('message', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        // Fitur Soft Delete otomatis jalan karena ada trait di Model
        $client->delete();

        return redirect()->route('clients.index')->with('message', 'Client deleted successfully.');
    }
}
