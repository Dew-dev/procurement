<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderWipStatus;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total contracts and purchase orders
        $totalContracts = Contract::count();
        $totalPurchaseOrders = PurchaseOrder::count();
        $totalDelivered = PurchaseOrder::whereNotNull('delivered_date')->count();
        
        // Total item metrics
        $totalContractItems = (int) DB::table('contract_items')->sum('qty');
        $totalPoItems = (int) DB::table('purchase_order_items')->sum('qty');
        $totalDeliveredItems = (int) DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->whereNotNull('purchase_orders.delivered_date')
            ->sum('purchase_order_items.qty');

        // Average WIP progress - get latest percentage for each PO
        $avgProgress = PurchaseOrder::join('purchase_order_wip_statuses as w', 'purchase_orders.id', '=', 'w.purchase_order_id')
            ->whereIn('w.id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
            ->avg('w.percentage') ?? 0;

        // Items delivered per contract (sum of delivered purchase_order_items.qty)
        $deliveredByContract = DB::table('purchase_orders')
            ->whereNotNull('purchase_orders.delivered_date')
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('contracts', 'purchase_orders.contract_id', '=', 'contracts.id')
            ->selectRaw(
                'contracts.id,
                contracts.contract_number,
                contracts.buyer_name,
                COALESCE(SUM(purchase_order_items.qty), 0) as delivered_items'
            )
            ->groupBy('contracts.id', 'contracts.contract_number', 'contracts.buyer_name')
            ->orderByDesc('delivered_items')
            ->limit(10)
            ->get();

        // Aggregate totals per contract (used to compute item-based metrics)
        $totalItemsPerContract = DB::table('contract_items')
            ->select('contract_id', DB::raw('COALESCE(SUM(qty),0) as total_items'))
            ->groupBy('contract_id')
            ->pluck('total_items', 'contract_id');

        $itemsInPoPerContract = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->select('purchase_orders.contract_id', DB::raw('COALESCE(SUM(purchase_order_items.qty),0) as items_in_po'))
            ->groupBy('purchase_orders.contract_id')
            ->pluck('items_in_po', 'contract_id');

        $deliveredItemsPerContract = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->whereNotNull('purchase_orders.delivered_date')
            ->select('purchase_orders.contract_id', DB::raw('COALESCE(SUM(purchase_order_items.qty),0) as delivered_items'))
            ->groupBy('purchase_orders.contract_id')
            ->pluck('delivered_items', 'contract_id');

        // Contract progress distribution (for insights) based on items in PO
        $contractProgress = Contract::get()->map(function ($contract) use ($totalItemsPerContract, $itemsInPoPerContract, $deliveredItemsPerContract) {
            $totalItems = (int) ($totalItemsPerContract[$contract->id] ?? 0);
            $itemsInPo = (int) ($itemsInPoPerContract[$contract->id] ?? 0);
            $deliveredItems = (int) ($deliveredItemsPerContract[$contract->id] ?? 0);

            $itemsInPoPercent = $totalItems > 0 ? round(($itemsInPo / $totalItems) * 100, 1) : 0;
            $deliveredPercent = $totalItems > 0 ? round(($deliveredItems / $totalItems) * 100, 1) : 0;

            return [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'buyer_name' => $contract->buyer_name,
                'items_in_po' => $itemsInPo,
                'items_in_po_percent' => $itemsInPoPercent,
                'delivered_items' => $deliveredItems,
                'delivered_percent' => $deliveredPercent,
                'total_items' => $totalItems,
            ];
        })->sortByDesc('items_in_po_percent')->values();

        // High and low based on items that are already PO (percent)
        $highProgress = $contractProgress->first();
        $lowProgress = $contractProgress->last();

        // Paginated contract list ordered by delivered progress percent (item delivered / total)
        $contractsQuery = DB::table('contracts')
            ->leftJoinSub(DB::table('contract_items')
                ->select('contract_id', DB::raw('COALESCE(SUM(qty),0) as total_items'))
                ->groupBy('contract_id'), 'ci', 'ci.contract_id', '=', 'contracts.id')
            ->leftJoinSub(DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->select('purchase_orders.contract_id', DB::raw('COALESCE(SUM(purchase_order_items.qty),0) as items_in_po'))
                ->groupBy('purchase_orders.contract_id'), 'poit', 'poit.contract_id', '=', 'contracts.id')
            ->leftJoinSub(DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->whereNotNull('purchase_orders.delivered_date')
                ->select('purchase_orders.contract_id', DB::raw('COALESCE(SUM(purchase_order_items.qty),0) as delivered_items'))
                ->groupBy('purchase_orders.contract_id'), 'dit', 'dit.contract_id', '=', 'contracts.id')
            ->select('contracts.*', DB::raw('COALESCE(ci.total_items,0) as total_items'), DB::raw('COALESCE(poit.items_in_po,0) as items_in_po'), DB::raw('COALESCE(dit.delivered_items,0) as delivered_items'))
            ->selectRaw('CASE WHEN COALESCE(ci.total_items,0) > 0 THEN ROUND((COALESCE(dit.delivered_items,0) / ci.total_items) * 100,1) ELSE 0 END as progress_percent')
            ->orderByDesc('progress_percent');

        $contracts = $contractsQuery->paginate(15);

        // Delivery status breakdown (based on item quantities in POs)
        $deliveryStatus = [
            'delivered' => (int) DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->whereNotNull('purchase_orders.delivered_date')
                ->sum('purchase_order_items.qty'),
            'on_track' => (int) DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->whereNull('purchase_orders.delivered_date')
                ->where(DB::raw('DATE(purchase_orders.exact_delivery_date)'), '>=', DB::raw('CURDATE()'))
                ->sum('purchase_order_items.qty'),
            'at_risk' => (int) DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->whereNull('purchase_orders.delivered_date')
                ->where(DB::raw('DATE(purchase_orders.exact_delivery_date)'), '<', DB::raw('CURDATE()'))
                ->sum('purchase_order_items.qty'),
        ];

        // Progress distribution - using subquery for latest WIP status
        $progressBuckets = [
            '0-20%' => PurchaseOrderWipStatus::whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
                ->whereBetween('percentage', [0, 20])
                ->count(),
            '21-40%' => PurchaseOrderWipStatus::whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
                ->whereBetween('percentage', [21, 40])
                ->count(),
            '41-60%' => PurchaseOrderWipStatus::whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
                ->whereBetween('percentage', [41, 60])
                ->count(),
            '61-80%' => PurchaseOrderWipStatus::whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
                ->whereBetween('percentage', [61, 80])
                ->count(),
            '81-100%' => PurchaseOrderWipStatus::whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
                ->whereBetween('percentage', [81, 100])
                ->count(),
        ];

        // Top POs by progress
        $topPosByProgress = PurchaseOrder::join('purchase_order_wip_statuses as w', 'purchase_orders.id', '=', 'w.purchase_order_id')
            ->join('contracts', 'purchase_orders.contract_id', '=', 'contracts.id')
            ->selectRaw(
                'purchase_orders.id,
                purchase_orders.po_number,
                contracts.contract_number,
                w.percentage'
            )
            ->whereIn('w.id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('purchase_order_wip_statuses')
                    ->groupBy('purchase_order_id');
            })
            ->orderByDesc('w.percentage')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalContracts',
            'totalPurchaseOrders',
            'totalDelivered',
            'totalContractItems',
            'totalPoItems',
            'totalDeliveredItems',
            'avgProgress',
            'deliveredByContract',
            'contractProgress',
            'highProgress',
            'lowProgress',
            'deliveryStatus',
            'progressBuckets',
            'topPosByProgress',
            'contracts'
        ));
    }
}
