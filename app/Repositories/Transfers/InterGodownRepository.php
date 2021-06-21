<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use App\GodownProductsStock;
use App\Product;
use App\StockTransferProduct;
use App\Traits\InterGodownTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterGodownRepository
{
    use InterGodownTrait;

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
        $record = DB::table('stock_transfers as st')->where('st.id', $id)
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

        $productIds = GodownProductsStock::where('godown_id', $record->from_godown_id)->pluck('product_id')->toArray();

        $record->autofillProducts = Product::whereIn('id', array_unique($productIds))
            ->selectRaw('id, CONCAT_WS(" - ", name, CONCAT("(", alias, ")")) as name')
            ->get();

        $record->inputProducts = StockTransferProduct::where('stock_transfer_id', $id)
            ->selectRaw('
                product_id as id,
                lot_number,
                rent, ROUND(rent / 100, 1) as rentRaw,
                loading, ROUND(loading / 100, 1) as loadingRaw,
                unloading, ROUND(unloading / 100, 1) as unloadingRaw,
                quantity, ROUND(quantity / 100, 2) as quantityRaw
            ')
            ->get();

        foreach ($record->inputProducts as $product) {
            $product->details = Product::where('id', $product->id)
                ->selectRaw('
                    remarks,
                    unit,
                    packing, ROUND(packing / 100, 0) as packingRaw
                ')
                ->first();

            $product->stock = GodownProductsStock::where('godown_id', $record->from_godown_id)
                ->where('product_id', $product->id)
                ->where('lot_number', $product['lot_number'])
                ->first()->current_stock;

            $product->details->lotNumbers = GodownProductsStock::where('godown_id', $record->from_godown_id)
                ->where('product_id', $product->id)
                ->select('lot_number')
                ->get();

            $product->lotDetails = DB::table('stock_transfers as st')
                ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
                ->where('st.transfer_type_id', StockTransfer::INTER_GODOWN)
                ->where('st.from_godown_id', $record->from_godown_id)
                ->where('stp.product_id', $product->id)
                ->where('stp.lot_number', $product['lot_number'])
                ->orderBy('st.date', 'desc')
                ->select('rent', 'loading', 'unloading')
                ->first();
        }

        return $record;
    }

    public function fetchShowTransferProducts($transferId)
    {
        return DB::table('stock_transfer_products as stp')
            ->where('stp.stock_transfer_id', $transferId)
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->selectRaw('
                stp.id,
                stp.lot_number as lotNumber,
                stp.quantity,
                ROUND(stp.quantity / 100, 2) as quantityRaw,
                stp.rent,
                ROUND(stp.rent / 100, 1) as rentRaw,
                stp.loading, ROUND(stp.loading / 100, 1) as loadingRaw,
                stp.unloading, ROUND(stp.unloading / 100, 1) as unloadingRaw,
                pr.packing,
                (stp.quantity * pr.packing) / 100 as quantityKgs,
                pr.id as productId,
                pr.name as name,
                pr.unit
            ')
            ->get();
    }

    /**
     * Create Purchase Entry
     */
    public function create(Request $request, $interGodownService)
    {
        $id = StockTransfer::create([
            'inter_godown_no'   => $request->inter_godown_no,
            'transfer_type_id'  => StockTransfer::INTER_GODOWN,
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'transport_details' => strtoupper($request->transport_details),
            'remarks'           => $request->remarks
        ])->id;

        if (count($request->products) > 0) {
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

                if ($existingGPS = $interGodownService->checkExistingGPS($request, $product)) {
                    $this->updateGPS($existingGPS, $request, $product);
                } else {
                    $this->createGPS($request, $product);
                }
            }
        }
    }

    /**
     * Update Purchase Entry
     */
    public function update(Request $request, $id, $interGodownService)
    {
        $this->undoPreviousGPSChanges($id);
        $this->removeUnusedProducts($request, $id);

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

        if (count($request->products) > 0) {
            foreach($request->products as $product) {
                $purchaseItem = StockTransferProduct::where('stock_transfer_id', $id)
                    ->where('product_id', $product['id'])
                    ->where('lot_number', $product['lot_number'])
                    ->first();

                if (!is_null($purchaseItem)) {
                    $purchaseItem->update([
                        'rent'              => (int) $product['rent'],
                        'loading'           => (int) $product['loading'],
                        'unloading'         => (int) $product['unloading'],
                        'quantity'          => (int) $product['quantity']
                    ]);
                } else {
                    StockTransferProduct::create([
                        'stock_transfer_id' => $id,
                        'product_id'        => $product['id'],
                        'lot_number'        => $product['lot_number'] ?? NULL,
                        'rent'              => (int) $product['rent'],
                        'loading'           => (int) $product['loading'],
                        'unloading'         => (int) $product['unloading'],
                        'quantity'          => (int) $product['quantity']
                    ]);
                }

                if ($existingGPS = $interGodownService->checkExistingGPS($request, $product)) {
                    $this->updateGPS($existingGPS, $request, $product);
                } else {
                    $this->createGPS($request, $product);
                }
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
     * New transfer
     */
    public function new()
    {
        $transferNo = StockTransfer::max('inter_godown_no');
        return [
            'inter_godown_no'   => is_null($transferNo) ? 1 : $transferNo + 1,
            'dateRaw'           => date('d-m-Y'),
            'date'              => date('Y-m-d')
        ];
    }

    public function removeUnusedProducts(Request $request, $id)
    {
        $inputIds = [];
        foreach ($request->products as $product) {
            $purchaseItem = StockTransferProduct::where('stock_transfer_id', $id)
                ->where('product_id', $product['id'])
                ->where('lot_number', $product['lot_number'])
                ->first();
            if (!is_null($purchaseItem)) array_push($inputIds, $purchaseItem->id);
        }
        StockTransferProduct::where('stock_transfer_id', $id)->whereNotIn('id', $inputIds)->delete();
    }

    /**
     * Undo given changes
     */
    public function undoPreviousGPSChanges($id)
    {
        $stockTransfer = StockTransfer::find($id);
        $products = StockTransferProduct::where('stock_transfer_id', $id)->get();

        foreach ($products as $product) {
            $fromGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->from_godown_id)
                ->where('lot_number', $product['lot_number'])
                ->where('product_id', $product->product_id)
                ->first();

            $toGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->to_godown_id)
                ->where('product_id', $product->product_id)
                ->where('lot_number', $product['lot_number'])
                ->first();

            $fromGodownStock->current_stock += $product->quantity;
            $fromGodownStock->save();

            $toGodownStock->current_stock -= $product->quantity;
            $toGodownStock->save();
        }
    }

    public function createGPS(Request $request, $product)
    {
        GodownProductsStock::create([
            'product_id'    => $product['id'],
            'godown_id'     => $request->to_godown_id,
            'lot_number'    => $product['lot_number'],
            'current_stock' => $product['quantity']
        ]);

        $from = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $product['id'])
            ->where('lot_number', $product['lot_number'])
            ->first();
        $from->current_stock = $from->current_stock - $product['quantity'];
        $from->save();
    }

    public function updateGPS(GodownProductsStock $godownStock, Request $request, $product)
    {
        $godownStock->current_stock += $product['quantity'];
        $godownStock->save();

        $from = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('lot_number', $product['lot_number'])
            ->where('product_id', $product['id'])
            ->first();
        $from->current_stock -= $product['quantity'];
        $from->save();
    }
}
