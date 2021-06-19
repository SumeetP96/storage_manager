<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use Illuminate\Http\Request;
use App\GodownProductsStock;
use App\StockTransferProduct;
use App\Traits\PurchaseTrait;
use App\TransferType;
use Illuminate\Support\Facades\DB;

class PurchaseRepository
{
    use PurchaseTrait;

    /**
     * Fetch all records
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fetchAll(Request $request)
    {
        return [
            'total' => $this->allRecords($request)->count(),

            'records' => $this->allRecords($request)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }

    public function fetchOne($id)
    {
        return DB::table('stock_transfers as st')->where('st.id', $id)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->selectRaw('
                st.*,
                DATE_FORMAT(st.date, "%d-%m-%Y") as dateRaw,
                fg.name as fromName,
                fg.address as fromAddress,
                fg.contact_1 as fromContact1,
                fg.contact_2 as fromContact2,
                tg.name as toName,
                tg.address as toAddress,
                tg.contact_1 as toContact1,
                tg.contact_2 as toContact2,
                ag.name as agentName
            ')
            ->first();
    }

    public function fetchShowTransferProducts($purchaseId)
    {
        return DB::table('stock_transfer_products as stp')
            ->where('stp.stock_transfer_id', $purchaseId)
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->selectRaw('
                stp.id,
                stp.lot_number as lotNumber,
                stp.quantity,
                stp.quantity div 100 as quantityRaw,
                stp.compound_quantity as compoundQuantity,
                stp.compound_quantity div 100 as compoundQuantityRaw,
                stp.rent,
                stp.rent div 100 as rentRaw,
                stp.labour,
                stp.labour div 100 as labourRaw,
                pr.compound_unit as compoundUnit,
                pr.packing,
                pr.id as productId,
                pr.name as name,
                pr.unit as unit
            ')
            ->get();
    }

    /**
     * Create Purchase Entry
     */
    public function create(Request $request, $purchaseService)
    {
        $id = StockTransfer::create([
            'purchase_no'       => $request->purchase_no,
            'transfer_type_id'  => StockTransfer::PURCHASE,
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'invoice_no'        => strtoupper($request->invoice_no),
            'transport_details' => strtoupper($request->transport_details),
            'agent_id'          => $request->agent_id,
            'remarks'           => $request->remarks
        ])->id;

        foreach($request->products as $product) {
            StockTransferProduct::create([
                'stock_transfer_id' => $id,
                'product_id'        => $product['id'],
                'lot_number'        => $product['lot_number'] ?? NULL,
                'rent'              => (int) $product['rent'],
                'loading'           => (int) $product['loading'],
                'unloading'         => (int) $product['unloading'],
                'quantity'          => (int) $product['quantity']
            ]);

            if ($existingGPS = $purchaseService->checkExistingGPS($request, $product)) {
                $this->updateGPS($existingGPS, $product);
            } else {
                $this->createGPS($request, $product);
            }
        }
    }

    /**
     * Update Purchase Entry
     */
    public function update(Request $request, $id, $purchaseService)
    {
        $this->undoPreviousGPSChanges($id);

        StockTransfer::find($id)->update([
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'invoice_no'        => strtoupper($request->invoice_no),
            'transport_details' => strtoupper($request->transport_details),
            'agent_id'          => $request->agent_id,
            'remarks'           => $request->remarks
        ]);

        StockTransferProduct::where('stock_transfer_id', $id)->delete();

        foreach($request->products as $product) {
            StockTransferProduct::create([
                'stock_transfer_id' => $id,
                'product_id'        => $product['id'],
                'lot_number'        => $product['lot_number'] ?? NULL,
                'rent'              => (int) $product['rent'],
                'loading'           => (int) $product['loading'],
                'unloading'         => (int) $product['unloading'],
                'quantity'          => (int) $product['quantity']
            ]);

            if ($existingGPS = $purchaseService->checkExistingGPS($request, $product['id'])) {
                $this->updateGPS($existingGPS, $product);
            } else {
                $this->createGPS($request, $product);
            }
        }
    }

    /**
     * Delete record
     */
    public function destroy($id)
    {
        $this->undoPreviousGPSChanges($id);

        StockTransfer::find($id)->delete();
    }

    /**
     * New purchase
     */
    public function new()
    {
        $purchaseNo = StockTransfer::max('purchase_no');
        return [
            'purchase_no'   => is_null($purchaseNo) ? 1 : $purchaseNo + 1,
            'dateRaw'       => date('d-m-Y'),
            'date'          => date('Y-m-d')
        ];
    }

    /**
     * Undo given changes
     */
    public function undoPreviousGPSChanges($id)
    {
        $stockTransfer = StockTransfer::find($id);
        $products = StockTransferProduct::where('stock_transfer_id', $id)->get();

        foreach($products as $product) {
            $oldGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->to_godown_id)
                ->where('lot_number', $product['lot_number'])
                ->where('product_id', $product->product_id)
                ->first();

            $oldGodownStock->current_stock -= $product->quantity;
            $oldGodownStock->save();
        }
    }

    public function createGPS(Request $request, $product)
    {
        GodownProductsStock::create([
            'product_id'    => $product['id'],
            'godown_id'     => $request->to_godown_id,
            'lot_number'    => $product['lot_number'],
            'current_stock' => (int) $product['quantity']
        ]);
    }

    public function updateGPS(GodownProductsStock $godownStock, $product)
    {
        $godownStock->current_stock += (int) $product['quantity'];
        $godownStock->lot_number = $product['lot_number'];
        $godownStock->save();
    }
}
