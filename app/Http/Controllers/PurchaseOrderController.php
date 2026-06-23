<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Http\Resources\PurchaseOrderResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\RabbitmqService;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $po = PurchaseOrder::all();
        return new PurchaseOrderResource($po, 'Success', 'List Purchase Order');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'po_number'   => 'required|unique:purchase_orders',
            'supplier_id' => 'required',
            'medicine_id' => 'required',
            'quantity'    => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'status'      => 'required|in:pending,approved,received,cancelled',
        ]);

        if ($validator->fails()) {
            return new PurchaseOrderResource(null, 'Failed', $validator->errors());
        }

        $po = PurchaseOrder::create($request->all());
        return new PurchaseOrderResource($po, 'Success', 'Purchase Order created successfully');
    }

    public function show(string $id)
    {
        $po = PurchaseOrder::find($id);
        if ($po) {
            return new PurchaseOrderResource($po, 'Success', 'Purchase Order found');
        }
        return new PurchaseOrderResource(null, 'Failed', 'Purchase Order not found');
    }

    public function update(Request $request, string $id)
    {
        $po = PurchaseOrder::find($id);

        if (!$po) {
            return new PurchaseOrderResource(null, 'Failed', 'Purchase Order not found');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,approved,received,cancelled',
        ]);

        if ($validator->fails()) {
            return new PurchaseOrderResource(null, 'Failed', $validator->errors());
        }

        $statusLama = $po->status;
        $po->update($request->all());

        if ($request->status === 'received' && $statusLama !== 'received') {
            // Kirim ke Transaction Service
            $this->kirimKeTransactionService($po);

            // Publish event ke RabbitMQ
            RabbitmqService::publish('notification.po_received', [
                'poNumber'   => $po->po_number,
                'supplierId' => $po->supplier_id,
                'medicineId' => $po->medicine_id,
                'quantity'   => (int) $po->quantity,
                'message'    => "Purchase Order {$po->po_number} telah diterima",
                'timestamp'  => now()->toISOString(),
            ]);
        }

        return new PurchaseOrderResource($po, 'Success', 'Purchase Order updated successfully');
    }

    private function kirimKeTransactionService(PurchaseOrder $po): void
    {
        $transactionServiceUrl = env('TRANSACTION_SERVICE_URL', 'http://simavek-transaction-service:3001');

        $mutation = <<<'GQL'
        mutation CreateTransaction($input: CreateTransactionInput!) {
            createTransaction(input: $input) {
                id
                trxNumber
                type
                totalAmount
            }
        }
        GQL;

        $variables = [
            'input' => [
                'type'  => 'purchase',
                'items' => [
                    [
                        'medicineId' => $po->medicine_id,
                        'quantity'   => (int) $po->quantity,
                    ],
                ],
            ],
        ];

        try {
            $response = Http::timeout(10)
                ->post("{$transactionServiceUrl}/graphql", [
                    'query'     => $mutation,
                    'variables' => $variables,
                ]);

            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['errors'])) {
                    Log::error('Transaction Service error', ['po_id' => $po->id, 'errors' => $body['errors']]);
                } else {
                    Log::info('Berhasil kirim ke Transaction Service', ['po_id' => $po->id]);
                }
            } else {
                Log::error('Transaction Service HTTP error', ['po_id' => $po->id, 'status' => $response->status()]);
            }
        } catch (\Exception $e) {
            Log::error('Gagal menghubungi Transaction Service', ['po_id' => $po->id, 'message' => $e->getMessage()]);
        }
    }
}