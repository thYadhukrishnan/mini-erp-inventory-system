<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    /**
     * GET /api/reports/supplier-spend
     *
     * Returns total spend per supplier for RECEIVED purchase orders.
     * Aggregation is done entirely at the database level for performance.
     */
    public function supplierSpend(): JsonResponse
    {
        $results = DB::table('purchase_orders')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->where('purchase_orders.status', 'RECEIVED')
            ->select(
                'suppliers.id as supplier_id',
                'suppliers.name as supplier_name',
                'suppliers.email as supplier_email',
                DB::raw('COUNT(purchase_orders.id) as order_count'),
                DB::raw('SUM(purchase_orders.total_amount) as total_spend')
            )
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email')
            ->orderByDesc('total_spend')
            ->get();

        return response()->json([
            'data' => $results->map(fn ($row) => [
                'supplier_id'    => $row->supplier_id,
                'supplier_name'  => $row->supplier_name,
                'supplier_email' => $row->supplier_email,
                'order_count'    => (int) $row->order_count,
                'total_spend'    => round((float) $row->total_spend, 2),
            ]),
        ]);
    }
}
