<?php

namespace App\Traits;
use App\Models\BomItem;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementItem;

trait HasInventoryQty
{
    public function syncQtyAvailable($bomItemId = null)
    {
        if($bomItemId) {
            $bomItems = BomItem::where('id', $bomItemId)->get();
        }else {
            $bomItems = BomItem::all();
        }

        if($bomItems) {
            foreach($bomItems as $bomItem) {
                $availableQty = 0;
                if($bomItem->inventoryMovementItems()->exists()) {
                    foreach($bomItem->inventoryMovementItems as $inventoryMovementItem) {
                        if($inventoryMovementItem->inventoryMovement->action == array_search('Outgoing', InventoryMovement::ACTIONS)) {
                            if($inventoryMovementItem->inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES)) {
                                $availableQty -= $inventoryMovementItem->qty;
                            }
                        }else {
                            if($inventoryMovementItem->inventoryMovementItemQuantities()->exists()) {
                                foreach($inventoryMovementItem->inventoryMovementItemQuantities as $inventoryMovementItemQuantity) {
                                    $availableQty += $inventoryMovementItemQuantity->qty;
                                }
                            }
                        }
                    }
                }
                $bomItem->available_qty = $availableQty;
                $bomItem->save();
            }
        }
    }

    public function syncQtyOrdered($bomItemId = null)
    {
        if($bomItemId) {
            $bomItems = BomItem::where('id', $bomItemId)->get();
        }else {
            $bomItems = BomItem::all();
        }

        if($bomItems) {
            foreach($bomItems as $bomItem) {
                $orderedQty = $bomItem
                                ->inventoryMovementItems()
                                ->where('status', array_search('Ordered', InventoryMovementItem::RECEIVING_STATUSES))
                                ->whereHas('inventoryMovement', function($query) {
                                    $query->where('action', array_search('Receiving', InventoryMovement::ACTIONS));
                                })->sum('qty');
                $bomItem->ordered_qty = $orderedQty;
                $bomItem->save();
            }
        }
    }

    public function syncQtyPlanned($bomItemId = null)
    {
        if($bomItemId) {
            $bomItems = BomItem::where('id', $bomItemId)->get();
        }else {
            $bomItems = BomItem::all();
        }

        if($bomItems) {
            foreach($bomItems as $bomItem) {
                $plannedQty = $bomItem
                                ->inventoryMovementItems()
                                ->where('status', array_search('Planned', InventoryMovementItem::OUTGOING_STATUSES))
                                ->whereHas('inventoryMovement', function($query) {
                                    $query->where('action', array_search('Outgoing', InventoryMovement::ACTIONS));
                                })->sum('qty');
                $bomItem->planned_qty = $plannedQty;
                $bomItem->save();
            }
        }
    }

    public function syncQtyOrderedPlanned($bomItemId = null)
    {
        $this->syncQtyOrdered($bomItemId);
        $this->syncQtyPlanned($bomItemId);
    }
}
