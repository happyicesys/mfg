<?php

namespace App\Http\Livewire;

use App\Models\Bom;
use App\Models\BomContent;
use App\Models\BomItem;
use App\Models\Country;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementItem;
use App\Models\Supplier;
use DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class BomMovement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $selected = [];
    public $filters = [
        'batch' => '',
        'action' => '',
        'status' => '',
        'created_at' => '',
    ];
    public Bom $bom;
    public BomItem $selectedBomItem;
    public InventoryMovement $inventoryMovementForm;
    public InventoryMovementItem $inventoryMovementItemForm;
    public Supplier $supplier;
    public $bomItems;
    public $selectBomItemId;
    public $supplierQuotedPrice;
    public $supplierQuotedPriceStr;
    public $inventoryMovementItems = [];
    public $totalAmount = 0.00;
    public $countries;
    public $attachments;
    public $showGenerateByBomArea = false;
    public $negativeAvailableQtyAlert = false;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function rules()
    {
        return [
            'inventoryMovementForm.id' => 'sometimes',
            'inventoryMovementForm.action' => 'required',
            'inventoryMovementForm.batch' => 'required',
            'inventoryMovementForm.remarks' => 'sometimes',
            'inventoryMovementForm.status' => 'sometimes',
            'inventoryMovementForm.total_amount' => 'sometimes',
            'inventoryMovementForm.country_id' => 'sometimes',
            'inventoryMovementForm.bom_id' => 'sometimes',
            'inventoryMovementItemForm.bom_item_id' => 'sometimes',
            'inventoryMovementItemForm.unit_price' => 'sometimes|numeric',
            'inventoryMovementItemForm.qty' => 'sometimes|numeric',
            'inventoryMovementItemForm.amount' => 'sometimes|numeric',
            'inventoryMovementItemForm.supplier_quote_price_id' => 'sometimes',
            'inventoryMovementItemForm.remarks' => 'sometimes',
            'inventoryMovementItemForm.bom_qty' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->countries = Country::orderBy('currency_name')->get();
        $this->boms = Bom::latest()->get();
    }

    public function render()
    {
        $inventoryMovements = InventoryMovement::with([
                                    'inventoryMovementItems',
                                    'bom',
                                    'createdBy',
                                    'updatedBy',
                            ]);

        $inventoryMovements = $inventoryMovements
                ->when($this->filters['batch'], fn($query, $input) => $query->searchLike('batch', $input))
                ->when($this->filters['action'], fn($query, $input) => $query->search('action', $input))
                ->when($this->filters['status'], fn($query, $input) => $query->search('status', $input))
                ->when($this->filters['created_at'], fn($query, $input) => $query->searchDate('created_at', $input));

        if($sortKey = $this->sortKey) {
            $inventoryMovements = $inventoryMovements->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $inventoryMovements = $inventoryMovements->latest();
        }

        $inventoryMovements = $inventoryMovements->paginate($this->itemPerPage);

        return view('livewire.bom-movement', [
            'inventoryMovements' => $inventoryMovements,
        ]);
    }

    public function sortBy($key)
    {
        if($this->sortKey === $key) {
            $this->sortAscending = !$this->sortAscending;
        }else {
            $this->sortAscending = true;
        }

        $this->sortKey = $key;
    }

    public function edit(InventoryMovement $inventoryMovement)
    {
        $this->inventoryMovementForm = $inventoryMovement;

    }

    public function save()
    {
        $this->validate();
        $this->inventoryMovementForm->save();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete()
    {
        $this->inventoryMovementForm->delete();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been deleted');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function createInventoryMovement()
    {
        $this->inventoryMovementForm = new InventoryMovement();
    }

    public function updatedInventoryMovementFormAction($value)
    {
        $countryId = '';

        if($this->inventoryMovementForm->country_id) {
            $countryId = $this->inventoryMovementForm->country_id;
        }
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->inventoryMovementItems = [];
        $this->inventoryMovementForm->action = $value;
        $this->inventoryMovementForm->country_id = $countryId;
    }

    public function updatedInventoryMovementFormCountryId($value)
    {
        $action = '';

        if($this->inventoryMovementForm->action) {
            $action = $this->inventoryMovementForm->action;
        }
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->inventoryMovementItems = [];
        $this->inventoryMovementForm->country_id = $value;
        $this->inventoryMovementForm->action = $action;
    }

    public function updatedInventoryMovementFormBomId($value)
    {
        $this->bom = Bom::findOrFail($value);
    }

    public function updated()
    {
        if($this->inventoryMovementForm->action and $this->inventoryMovementForm->country_id) {
            // $this->inventoryMovementItemForm = new InventoryMovementItem();
            // $this->inventoryMovementItems = [];
            $countryId = $this->inventoryMovementForm->country_id;
            $this->bomItems = BomItem::where(function($query) use ($countryId) {
                $query->doesntHave('supplierQuotePrices')->orWhereHas('supplierQuotePrices', function($query) use ($countryId) {
                    $query->where('country_id', $countryId);
                });
            })->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
            // dd($countryId, $this->bomItems->toArray());
        }
    }

    public function updatedInventoryMovementItemFormBomItemId($value)
    {
        if($value) {
            $this->inventoryMovementItemForm->unit_price = 0.00;
            $this->inventoryMovementItemForm->amount = 0.00;
            $this->inventoryMovementItemForm->qty = 0;

            $this->selectedBomItem = BomItem::findOrFail($value);
            $this->supplier = $this->selectedBomItem->supplierQuotePrices()->latest()->first() ? $this->selectedBomItem->supplierQuotePrices()->latest()->first()->supplier : new Supplier();

            $this->inventoryMovementItemForm->supplier_quote_price_id = $this->supplier->id ? $this->selectedBomItem->supplierQuotePrices()->latest()->first()->id : '';

            $this->supplierQuotedPrice = $this->supplier->id ?
                                            $this->selectedBomItem->supplierQuotePrices()->latest()->first()->unit_price :
                                            null;
            $this->supplierQuotedPriceStr = $this->supplier->id ?
                                            $this->selectedBomItem->supplierQuotePrices()->latest()->first()->unit_price.' ('.$this->selectedBomItem->supplierQuotePrices()->latest()->first()->country->currency_name.')' :
                                            null;
                                            // dd($this->supplierQuotedPriceStr);
            $this->inventoryMovementItemForm->unit_price = $this->supplierQuotedPrice;
        }
    }

    public function calculateAmount()
    {
        if($this->inventoryMovementItemForm->qty and $this->inventoryMovementItemForm->unit_price) {
            $this->inventoryMovementItemForm->amount = round($this->inventoryMovementItemForm->qty * $this->inventoryMovementItemForm->unit_price, 2);
        }else {
            $this->inventoryMovementItemForm->amount = 0.00;
        }
    }

    public function addInventoryMovementItem()
    {
        $bomItem = BomItem::findOrFail($this->inventoryMovementItemForm->bom_item_id);

        $data = [
            'bom_item_id' => $bomItem->id,
            'bom_item_code' => $bomItem->code,
            'bom_item_name' => $bomItem->name,
            'unit_price' => $this->inventoryMovementItemForm->unit_price,
            'amount' => $this->inventoryMovementItemForm->amount,
            'qty' => $this->inventoryMovementItemForm->qty,
            'supplier_quote_price_id' => $this->inventoryMovementItemForm->supplier_quote_price_id,
            'status' => null,
        ];
        // dd($this->inventoryMovementItems);
        array_push($this->inventoryMovementItems, $data);

        $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
    }

    public function deleteSingleInventoryMovementItem($index)
    {
        unset($this->inventoryMovementItems[$index]);

        $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
    }

    public function saveInventoryMovementForm($statusStr)
    {
        $this->validate([
            'inventoryMovementForm.action' => 'required',
            'inventoryMovementForm.batch' => 'required',
        ], [
            'inventoryMovementForm.action.required' => 'Please select an Action',
            'inventoryMovementForm.batch.required' => 'Please fill in the Batch',
        ]);

        $status = array_search($statusStr, InventoryMovement::STATUSES);

        $action = $this->inventoryMovementForm->action;

        switch($action) {
            case array_search('Receiving', InventoryMovement::ACTIONS):
                $this->validate([
                    'inventoryMovementForm.country_id' => 'required',
                ], [
                    'inventoryMovementForm.country_id.required' => 'Please select a Currency',
                ]);
                break;
            case array_search('Outgoing', InventoryMovement::ACTIONS):
                $this->validate([
                    'inventoryMovementForm.bom_id' => 'required',
                ], [
                    'inventoryMovementForm.bom_id.required' => 'Please select a BOM',
                ]);
                break;
        }

        if(!$this->inventoryMovementForm->id) {
            $inventoryMovement = InventoryMovement::create([
                'batch' => $this->inventoryMovementForm->batch,
                'remarks' => $this->inventoryMovementForm->remarks,
                'action' => $this->inventoryMovementForm->action,
                'bom_id' => $this->inventoryMovementForm->bom_id ? $this->inventoryMovementForm->bom_id : null,
                'country_id' => $this->inventoryMovementForm->country_id ? $this->inventoryMovementForm->country_id : null,
                'status' => $status,
                'total_amount' => $this->inventoryMovementForm->total_amount,
                'created_by' => auth()->user()->id,
            ]);
        }else {
            $this->inventoryMovementForm->update([
                'batch' => $this->inventoryMovementForm->batch,
                'remarks' => $this->inventoryMovementForm->remarks,
                'action' => $this->inventoryMovementForm->action,
                'bom_id' => $this->inventoryMovementForm->bom_id ? $this->inventoryMovementForm->bom_id : null,
                'country_id' => $this->inventoryMovementForm->country_id ? $this->inventoryMovementForm->country_id : null,
                'status' => $status,
                'total_amount' => $this->inventoryMovementForm->total_amount,
                'updated_by' => auth()->user()->id,
            ]);
            $inventoryMovement = $this->inventoryMovementForm;
        }

        if($action == array_search('Receiving', InventoryMovement::ACTIONS)) {
            if($this->inventoryMovementItems) {
                $previousInventoryMovementItems = InventoryMovementItem::where('inventory_movement_id', $inventoryMovement->id)->delete();
                foreach($this->inventoryMovementItems as $inventoryMovementItem) {
                    $bomItem = BomItem::findOrFail($inventoryMovementItem['bom_item_id']);
                    InventoryMovementItem::create([
                        'bom_item_id' => $bomItem->id,
                        'inventory_movement_id' => $inventoryMovement->id,
                        'supplier_quote_price_id' => $inventoryMovementItem['supplier_quote_price_id'],
                        'status' => 1,
                        'qty' => $inventoryMovementItem['qty'],
                        'amount' => $inventoryMovementItem['amount'],
                        'unit_price'  => $inventoryMovementItem['unit_price'],
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }
        }elseif($action == array_search('Outgoing', InventoryMovement::ACTIONS)) {
            if($this->inventoryMovementItems) {
                $previousInventoryMovementItems = InventoryMovementItem::where('inventory_movement_id', $inventoryMovement->id)->delete();
                foreach($this->inventoryMovementItems as $inventoryMovementItem) {
                    $bomItem = BomItem::findOrFail($inventoryMovementItem['bom_item_id']);
                    InventoryMovementItem::create([
                        'bom_item_id' => $bomItem->id,
                        'inventory_movement_id' => $inventoryMovement->id,
                        'status' => $status,
                        'qty' => $inventoryMovementItem['qty'],
                        'amount' => 0,
                        'unit_price'  => 0,
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }
        }
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been added');
    }

    public function editInventoryMovement(InventoryMovement $inventoryMovement)
    {
        // dd($inventoryMovement->toArray());
        $this->inventoryMovementForm = $inventoryMovement;
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->reloadInventoryItems($inventoryMovement);
        $this->bomItems = BomItem::where(function($query) use ($inventoryMovement) {
            $query->doesntHave('supplierQuotePrices')->orWhereHas('supplierQuotePrices', function($query) use ($inventoryMovement) {
                $query->where('country_id', $inventoryMovement->country_id);
            });
        })->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
    }

    public function deleteInventoryMovement()
    {
        if($this->inventoryMovementForm->inventoryMovementItems()->exists()) {
            $this->inventoryMovementForm->inventoryMovementItems()->delete();
        }
        $this->inventoryMovementForm->delete();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been deleted');
    }

    public function onPrevNextDateClicked($direction, $model)
    {
        $date = Carbon::now();
        if($model) {
            $date = Carbon::parse($this->filters[$model]);
        }
        if($direction > 0) {
            $this->filters[$model] = $date->addDay()->toDateString();
        }else {
            $this->filters[$model] = $date->subDay()->toDateString();
        }
    }

    public function changeInventoryMovementItemRemarks($index)
    {
        // dd($this->inventoryMovementItems[$index]);
        $inventoryMovementItem = InventoryMovementItem::findOrFail($this->inventoryMovementItems[$index]['id']);
        $inventoryMovementItem->remarks = $this->inventoryMovementItems[$index]['remarks'];
        $inventoryMovementItem->save();
    }

    public function receivedSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        $inventoryMovementItem->status = array_search('Confirmed', InventoryMovementItem::STATUSES);
        $inventoryMovementItem->save();
        $this->addBomItemQtyAvailable($inventoryMovementItem->bom_item_id, $inventoryMovementItem->qty);
        $this->reloadInventoryItems($inventoryMovementItem->inventoryMovement);
        $this->emit('refresh');
    }

    public function voidSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        if($inventoryMovementItem->status == array_search('Confirmed', InventoryMovementItem::STATUSES)) {
            $this->reduceBomItemQtyAvailable($inventoryMovementItem->bom_item_id, $inventoryMovementItem->qty);
        }
        $inventoryMovementItem->status = array_search('Void', InventoryMovementItem::STATUSES);
        $inventoryMovementItem->save();
        $this->reloadInventoryItems($inventoryMovementItem->inventoryMovement);
        $this->emit('refresh');
    }

    public function reloadInventoryItems($inventoryMovement)
    {
        $this->inventoryMovementItems = [];
        if($inventoryMovement->inventoryMovementItems) {
            foreach($inventoryMovement->inventoryMovementItems as $inventoryMovementItem) {
                $data = [
                    'id' => $inventoryMovementItem->id,
                    'bom_item_id' => $inventoryMovementItem->bomItem->id,
                    'bom_item_code' => $inventoryMovementItem->bomItem->code,
                    'bom_item_name' => $inventoryMovementItem->bomItem->name,
                    'unit_price' => $inventoryMovementItem->unit_price,
                    'amount' => $inventoryMovementItem->amount,
                    'qty' => $inventoryMovementItem->qty + 0,
                    'supplier_quote_price_id' => $inventoryMovementItem->supplier_quote_price_id,
                    'remarks' => $inventoryMovementItem->remarks,
                    'status' => $inventoryMovementItem->status,
                ];
                // dd($this->inventoryMovementItems);
                array_push($this->inventoryMovementItems, $data);
                $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
            }
        }
    }

    public function viewAttachmentsByBomItem(BomItem $bomItem)
    {
        $this->reset('attachments');
        $this->attachments = $bomItem->attachments;
    }

    public function onGenerateOutgoingClicked()
    {
        $bomId = $this->bom->id;
        $bomQty = $this->inventoryMovementItemForm->bom_qty;
        $bomContents = BomContent::whereHas('bomHeader', function($query) use ($bomId) {
            $query->where('bom_id', $bomId);
        })->whereHas('bomItem', function($query) {
            $query->where('is_part', 1)->where('is_inventory', 1);
        })->get();
        // dd($bomContents->toArray());
        $inventoryMovementItemArr = [];
        foreach($bomContents as $bomContent) {
            $inventoryMovementItemArr[$bomContent->bom_item_id] = [
                'bom_item_id' => $bomContent->bomItem->id,
                'bom_item_code' => $bomContent->bomItem->code,
                'bom_item_name' => $bomContent->bomItem->name,
                'unit_price' => 0,
                'amount' => 0,
                'available_qty' => $bomContent->bomItem->available_qty,
            ];
            if(isset($inventoryMovementItemArr[$bomContent->bom_item_id]['qty'])) {
                $inventoryMovementItemArr[$bomContent->bom_item_id]['qty'] += $bomContent->qty * $bomQty;
            }else {
                $inventoryMovementItemArr[$bomContent->bom_item_id]['qty'] = $bomContent->qty * $bomQty;
            }
            $inventoryMovementItemArr[$bomContent->bom_item_id]['balance_qty'] = $inventoryMovementItemArr[$bomContent->bom_item_id]['available_qty'] - $inventoryMovementItemArr[$bomContent->bom_item_id]['qty'];
        }
        $this->inventoryMovementItems = $inventoryMovementItemArr;
        $this->showGenerateByBomArea = false;
    }

    private function calculateTotalAmount($inventoryMovementItemArr)
    {
        $totalAmount = 0.00;

        if($inventoryMovementItemArr) {
            foreach($inventoryMovementItemArr as $inventoryMovementItem) {
                $totalAmount += $inventoryMovementItem['amount'];
            }
        }

        return $totalAmount;
    }

    private function addBomItemQtyAvailable($bomItemId, $qty)
    {
        $bomItem = BomItem::findOrFail($bomItemId);
        $bomItem->available_qty += $qty;
        $bomItem->save();
    }

    private function reduceBomItemQtyAvailable($bomItemId, $qty)
    {
        $bomItem = BomItem::findOrFail($bomItemId);
        $bomItem->available_qty -= $qty;
        $bomItem->save();
    }
}
