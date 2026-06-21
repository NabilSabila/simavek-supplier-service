<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $supplier = Supplier::all();

        if ($supplier->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'List Supplier',
            'data' => $supplier
        ]);
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($request->all());

        return response()->json([
            'message' => 'Data berhasil ditambahkan',
            'data' => $supplier
        ]);
    }

    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $supplier
        ]);
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $supplier->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $supplier
        ]);
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
