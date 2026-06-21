<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $po = PurchaseOrder::all();

        if ($po->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'List Purchase Order',
            'data' => $po
        ]);
    }

    public function store(Request $request)
    {
        $po = PurchaseOrder::create($request->all());

        return response()->json([
            'message' => 'Data berhasil ditambahkan',
            'data' => $po
        ]);
    }

    public function show(int $id)
    {
        $po = PurchaseOrder::find($id);

        if (!$po) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $po
        ]);
    }

    public function update(Request $request, int $id)
    {
        $po = PurchaseOrder::find($id);

        if (!$po) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $po->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $po
        ]);
    }
}
