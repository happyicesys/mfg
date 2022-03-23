<?php

namespace App\Console\Commands;

use App\Models\BomItem;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementItem;
use Illuminate\Console\Command;

class syncInventoryQty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:inventory-qty';

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
        $bomItems = BomItem::all();

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

        return Command::SUCCESS;
    }
}
