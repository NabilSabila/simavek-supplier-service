<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Http\Resources\PurchaseOrderResource;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $po = PurchaseOrder::all();

        return new PurchaseOrderResource(
            $po,
            'Success',
            'List Purchase Order'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'po_number' => 'required|unique:purchase_orders',
            'supplier_id' => 'required',
            'medicine_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return new PurchaseOrderResource(
                null,
                'Failed',
                $validator->errors()
            );
        }

        $po = PurchaseOrder::create($request->all());

        return new PurchaseOrderResource(
            $po,
            'Success',
            'Purchase Order created successfully'
        );
    }

    public function show(string $id)
    {
        $po = PurchaseOrder::find($id);

        if($po){
            return new PurchaseOrderResource(
                $po,
                'Success',
                'Purchase Order found'
            );
        }

        return new PurchaseOrderResource(
            null,
            'Failed',
            'Purchase Order not found'
        );
    }

    public function update(Request $request, string $id)
    {
        $po = PurchaseOrder::find($id);

        if($po){

            $po->update($request->all());

            return new PurchaseOrderResource(
                $po,
                'Success',
                'Purchase Order updated successfully'
            );
        }

        return new PurchaseOrderResource(
            null,
            'Failed',
            'Purchase Order not found'
        );
    }
}
