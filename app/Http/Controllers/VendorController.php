<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(15);

        return Inertia::render('Vendors/Index', [
            'vendors' => $vendors
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_code' => 'required|string|unique:vendors,vendor_code|max:255',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        Vendor::create($validated);

        return redirect()->back()->with('message', 'Vendor added successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->back()->with('message', 'Vendor deleted successfully.');
    }

    public function show(Vendor $vendor)
    {
        return Inertia::render('Vendors/Show', [
            'vendor' => $vendor
        ]);
    }

    public function edit(Vendor $vendor)
    {
        return Inertia::render('Vendors/Edit', [
            'vendor' => $vendor
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'vendor_code' => 'required|string|max:255|unique:vendors,vendor_code,' . $vendor->id,
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $vendor->update($validated);

        return redirect()->back()->with('message', 'Vendor updated successfully.');
    }
}
