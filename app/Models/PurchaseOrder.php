<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PurchaseOrder extends Model
{
    use HasUuids;
    protected $fillable = [
        'po_number',
        'supplier_id',
        'medicine_id',
        'quantity',
        'price',
        'status'
    ];
}
