<?php

namespace App\Console\Commands;

use App\Models\BomItem;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementItem;
use Illuminate\Console\Command;

class syncInventoryMovementStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:inventory-movement-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inventoryMovements = InventoryMovement::all();

        if($inventoryMovements) {
            foreach($inventoryMovements as $inventoryMovement) {
                if($inventoryMovement->inventoryMovementItems()->exists()) {
                    $isCompleted = true;
                    foreach($inventoryMovement->inventoryMovementItems as $inventoryMovementItem) {
                        $isReceived = false;
                        if($inventoryMovementItem->inventoryMovementItemQuantities()->exists()) {
                            if(($inventoryMovementItem->inventoryMovementItemQuantities()->sum('qty') == $inventoryMovementItem->qty) or $inventoryMovementItem->is_incomplete_qty) {
                                $isReceived = true;
                            }else {
                                $isReceived = false;
                            }
                        }
                        if($isReceived) {
                            $inventoryMovementItem->status =  array_search('Received', InventoryMovementItem::RECEIVING_STATUSES);
                        }else {
                            $inventoryMovementItem->status =  array_search('Ordered', InventoryMovementItem::RECEIVING_STATUSES);
                        }

                        $inventoryMovementItem->save();

                        if($inventoryMovementItem->status != array_search('Received', InventoryMovementItem::RECEIVING_STATUSES)) {
                            $isCompleted = false;
                        }

                        $bomItem = BomItem::findOrFail($inventoryMovementItem->bomItem->id);

                        $orderedQty = $bomItem
                                        ->inventoryMovementItems()
                                        ->where('status', array_search('Ordered', InventoryMovementItem::RECEIVING_STATUSES))
                                        ->whereHas('inventoryMovement', function($query) {
                                            $query->where('action', array_search('Receiving', InventoryMovement::ACTIONS));
                                        })->sum('qty');
                        $bomItem->ordered_qty = $orderedQty;

                        $plannedQty = $bomItem
                                        ->inventoryMovementItems()
                                        ->where('status', array_search('Planned', InventoryMovementItem::OUTGOING_STATUSES))
                                        ->whereHas('inventoryMovement', function($query) {
                                            $query->where('action', array_search('Outgoing', InventoryMovement::ACTIONS));
                                        })->sum('qty');
                        $bomItem->planned_qty = $plannedQty;

                        $bomItem->save();
                    }
                    if($isCompleted) {
                        $inventoryMovement->status = array_search('Completed', InventoryMovement::STATUSES);
                    }else {
                        $inventoryMovement->status = array_search('Confirmed', InventoryMovement::STATUSES);
                    }
                    $inventoryMovement->save();
                }
            }
        }


        return Command::SUCCESS;
    }
}
