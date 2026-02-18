<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class SupplierController extends Controller
{
    // Mostrar lista de proveedores
    public function index(Request $request)
    {
        $search = $request->input('search');

        $suppliers = Supplier::query()
            ->when($search, function ($query, $search) {
                $query->where('prvnombre', 'like', "%{$search}%")
                      ->orWhere('contrato', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%")
                      ->orWhere('enlace', 'like', "%{$search}%");
            })
            ->orderBy('prvnombre')
            ->paginate(9)
            ->withQueryString();

        if ($request->ajax()) {
            return view('suppliers.partials.list', compact('suppliers'))->render();
        }

        return view('suppliers.index', compact('suppliers'));
    }

    // Mostrar detalles de un proveedor
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    // Mostrar formulario para crear nuevo proveedor
    public function create()
    {
        return view('suppliers.create');
    }

    // Almacenar nuevo proveedor
    public function store(Request $request)
    {
        $data = $request->validate([
            'prvnombre' => 'required|string|max:255',
            'contrato'  => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:50',
            'enlace'    => 'nullable|string|max:255',
            'prvstatus' => 'boolean',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // solo tipos permitidos
        ]);


        if ($request->hasFile('logo')) {
            
            $data['logo_path'] = $request->file('logo')->store('suppliers', 'public');
        }

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'prvnombre' => 'required|string|max:255',
            'contrato'  => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:50',
            'enlace'    => 'nullable|string|max:255',
            'prvstatus' => 'boolean',
            'logo'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Borrar imagen anterior si existe
            if ($supplier->logo_path) {
                Storage::disk('public')->delete($supplier->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('suppliers', 'public');
        }

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }
    /**
     * Eliminar proveedor.
     */
    public function destroy(Supplier $supplier)
    {
        // Borrar la imagen si existe
        if ($supplier->logo_path) {
            Storage::disk('public')->delete($supplier->logo_path);
        }

        // Borrar el proveedor
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }

}
