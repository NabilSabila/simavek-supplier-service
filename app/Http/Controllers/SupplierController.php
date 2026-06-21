<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Resources\SupplierResource;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        $supplier = Supplier::all();

        return new SupplierResource(
            $supplier,
            'Success',
            'List Supplier'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()){
            return new SupplierResource(
                null,
                'Failed',
                $validator->errors()
            );
        }

        $supplier = Supplier::create($request->all());

        return new SupplierResource(
            $supplier,
            'Success',
            'Supplier created successfully'
        );
    }

    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        if($supplier){
            return new SupplierResource(
                $supplier,
                'Success',
                'Supplier found'
            );
        }

        return new SupplierResource(
            null,
            'Failed',
            'Supplier not found'
        );
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);

        if($supplier){

            $supplier->update($request->all());

            return new SupplierResource(
                $supplier,
                'Success',
                'Supplier updated successfully'
            );
        }

        return new SupplierResource(
            null,
            'Failed',
            'Supplier not found'
        );
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);

        if($supplier){

            $supplier->delete();

            return new SupplierResource(
                $supplier,
                'Success',
                'Supplier deleted successfully'
            );
        }

        return new SupplierResource(
            null,
            'Failed',
            'Supplier not found'
        );
    }
}
