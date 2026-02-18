<?php

namespace App\Http\Controllers;

use App\Models\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeviceTypeController extends Controller
{
    public function index(Request $request)
    {
        $deviceTypes = DeviceType::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where('equipo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderBy('equipo')
            ->paginate(6)
            ->withQueryString();

        return view('device_types.index', compact('deviceTypes'));
    }

    public function create()
    {
        return view('device_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/dispositivos'), $filename);
            $data['image_path'] = 'images/dispositivos/' . $filename;
        }

        DeviceType::create($data);

        return redirect()->route('device_types.index')
            ->with('success', 'Tipo de dispositivo creado correctamente.');
    }

    public function edit(DeviceType $deviceType)
    {
        return view('device_types.edit', compact('deviceType'));
    }

    public function update(Request $request, DeviceType $deviceType)
    {
        $data = $request->validate([
            'equipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($deviceType->image_path && file_exists(public_path($deviceType->image_path))) {
                unlink(public_path($deviceType->image_path));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/dispositivos'), $filename);
            $data['image_path'] = 'images/dispositivos/' . $filename;
        }

        $deviceType->update($data);

        return redirect()->route('device_types.index')
            ->with('success', 'Tipo de dispositivo actualizado correctamente.');
    }

    public function destroy(DeviceType $deviceType)
    {
        if ($deviceType->image_path && file_exists(public_path($deviceType->image_path))) {
            unlink(public_path($deviceType->image_path));
        }

        $deviceType->delete();

        return redirect()->route('device_types.index')
            ->with('success', 'Tipo de dispositivo eliminado correctamente.');
    }
}
