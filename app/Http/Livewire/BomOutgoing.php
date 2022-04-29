<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\Bom;
use App\Models\BomContent;
use App\Models\BomHeader;
use App\Models\BomItem;
use App\Models\BomItemType;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementItem;
use App\Models\InventoryMovementItemQuantity;
use App\Models\Supplier;
use App\Models\SupplierQuotePrice;
use DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;

class BomOutgoing extends Component
{
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
        'name' => '',
        'bom_item_type_id' => '',
        'is_consumable' => '',
        'is_inventory' => '',
        'supplier_id' => '',
    ];
    public $inventoryMovementItemFormFilters = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'supplier_id' => ''
    ];
    public Bom $bom;
    public Bom $bomForm;
    public BomItem $selectedBomItem;
    public InventoryMovement $inventoryMovementForm;
    public InventoryMovementItem $inventoryMovementItemForm;
    public InventoryMovementItem $editInventoryMovementItemForm;
    public Supplier $supplier;
    public $bomItems;
    public $selectBomItemId;
    public $supplierQuotedPrice;
    public $supplierQuotedPriceCountry;
    public $supplierQuotedPriceStr;
    public $inventoryMovementItems = [];
    public $totalAmount = 0.00;
    public $countries;
    public $showGenerateByBomArea = false;
    public $showGenerateLooseArea = false;
    public $negativeAvailableQtyAlert = false;
    public $attachments;
    public $file;
    public $selectBomHeader = [];
    public $selectBomContent = [];
    public $selectAll = false;
    public $selectedValue;
    public $selectedContentValue;
    public $bomItemTypes = [];
    public $suppliers = [];

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
            'inventoryMovementForm.order_date' => 'sometimes',
            'inventoryMovementForm.delivery_date' => 'sometimes',
            'inventoryMovementItemForm.bom_item_id' => 'sometimes',
            'inventoryMovementItemForm.unit_price' => 'sometimes|numeric',
            'inventoryMovementItemForm.qty' => 'sometimes|numeric',
            'inventoryMovementItemForm.amount' => 'sometimes|numeric',
            'inventoryMovementItemForm.supplier_quote_price_id' => 'sometimes',
            'inventoryMovementItemForm.remarks' => 'sometimes',
            'inventoryMovementItemForm.bom_qty' => 'sometimes',
            'inventoryMovementItemForm.supplier_unit_price' => 'sometimes',
            'inventoryMovementItemForm.rate' => 'sometimes',
            'inventoryMovementItemForm.date' => 'sometimes',
            'editInventoryMovementItemForm.date' => 'sometimes',
            'editInventoryMovementItemForm.remarks' => 'sometimes',
            'inventoryMovementItemFormFilters.code' => 'sometimes',
            'inventoryMovementItemFormFilters.name' => 'sometimes',
            'inventoryMovementItemFormFilters.bom_item_type_id' => 'sometimes',
            'inventoryMovementItemFormFilters.supplier_id' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->countries = Country::orderBy('currency_name')->get();
        $this->boms = Bom::latest()->get();
        $this->bomItems = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('company_name')->get();
    }

    public function render()
    {
        $inventoryMovements = InventoryMovement::with([
                                    'inventoryMovementItems' => function($query) {
                                        $query->leftJoin('bom_items', 'bom_items.id', '=', 'inventory_movement_items.bom_item_id')->orderBy('bom_items.code', 'asc');
                                    },
                                    'inventoryMovementItems.bomItem',
                                    'inventoryMovementItems.bomItem.bomItemType',
                                    'inventoryMovementItems.bomItem.supplierQuotePrices',
                                    'bom',
                                    'createdBy',
                                    'updatedBy',
                            ]);

        $inventoryMovements = $inventoryMovements
                ->when($this->filters['batch'], fn($query, $input) => $query->searchLike('batch', $input))
                ->when($this->filters['status'], fn($query, $input) => $query->search('status', $input))
                ->when($this->filters['created_at'], fn($query, $input) => $query->searchDate('created_at', $input));

        if($filtersName = $this->filters['name']) {
            $inventoryMovements = $inventoryMovements->whereHas('inventoryMovementItems.bomItem', function($query) use ($filtersName) {
                $query->where('name', 'LIKE', '%'.$filtersName.'%');
            });
        }

        if($filtersBomItemTypeId = $this->filters['bom_item_type_id']) {
            $inventoryMovements = $inventoryMovements->whereHas('inventoryMovementItems.bomItem.bomItemType', function($query) use ($filtersBomItemTypeId) {
                $query->where('id', $filtersBomItemTypeId);
            });
        }

        if($this->filters['is_consumable'] != '') {
            $filtersIsConsumable = $this->filters['is_consumable'];
            $inventoryMovements = $inventoryMovements->whereHas('inventoryMovementItems.bomItem.bomItemType', function($query) use ($filtersIsConsumable) {
                if($filtersIsConsumable) {
                    $query->whereIn('name', ['C', 'CB']);
                }else {
                    $query->whereNotIn('name', ['C', 'CB']);
                }
            });
        }

        if($this->filters['is_inventory'] != '') {
            $filtersIsInventory = $this->filters['is_inventory'];
            $inventoryMovements = $inventoryMovements->whereHas('inventoryMovementItems.bomItem', function($query) use ($filtersIsInventory) {
                $query->where('is_inventory', $filtersIsInventory);
            });
        }

        if($filtersSupplierId = $this->filters['supplier_id']) {
            $inventoryMovements = $inventoryMovements->whereHas('inventoryMovementItems.bomItem.supplierQuotePrices', function($query) use ($filtersSupplierId) {
                $query->where('supplier_id', $filtersSupplierId);
            });
        }

        $inventoryMovements = $inventoryMovements->where('action', array_search('Outgoing', \App\Models\InventoryMovement::ACTIONS));

        if($sortKey = $this->sortKey) {
            $inventoryMovements = $inventoryMovements->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $inventoryMovements = $inventoryMovements->latest();
        }

        $inventoryMovements = $inventoryMovements->paginate($this->itemPerPage);

        return view('livewire.bom-outgoing', [
            'inventoryMovements' => $inventoryMovements,
        ]);
    }

    public function updated($name, $value)
    {
        if($name == 'inventoryMovementItemFormFilters.code' or $name == 'inventoryMovementItemFormFilters.name' or $name == 'inventoryMovementItemFormFilters.bom_item_type_id' or $name == 'inventoryMovementItemFormFilters.supplier_id') {
            $bomItems = BomItem::when($this->inventoryMovementItemFormFilters['code'], fn($query, $input) => $query->searchLike('code', $input))
                                    ->when($this->inventoryMovementItemFormFilters['name'], fn($query, $input) => $query->searchLike('name', $input))
                                    ->when($this->inventoryMovementItemFormFilters['bom_item_type_id'], fn($query, $input) => $query->whereHas('bomItemType', function($query) use ($input) { $query->search('id', $input); }));

            if($supplierId = $this->inventoryMovementItemFormFilters['supplier_id']) {
                $bomItems = $bomItems->where(function($query) use ($supplierId) {
                    $query->whereHas('supplierQuotePrices', function($query) use ($supplierId) {
                        $query->where('supplier_id', $supplierId);
                    });
                });
            }


            $this->bomItems = $bomItems->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        }

        if($name == 'showGenerateByBomArea' and $value == 'true') {
            $this->showGenerateLooseArea = false;
        }

        if($name == 'showGenerateLooseArea' and $value == 'true') {
            $this->showGenerateByBomArea = false;
        }
    }

    public function updatedSelectAll($value)
    {
        if($value) {
            $selectedBomId = $this->inventoryMovementForm->bom_id;
            $this->selectBomHeader = BomHeader::where('bom_id', $selectedBomId)->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $this->selectBomContent = BomContent::whereHas('bomHeader', function($query) use ($selectedBomId) {
                $query->where('bom_id', $selectedBomId);
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }else {
            $this->selectBomHeader = [];
            $this->selectBomContent = [];
        }
    }

    public function updatedInventoryMovementFormBomId($value)
    {
        $this->bomForm = Bom::findOrFail($value);
    }

    public function selectedHeader($value)
    {
        if($value) {
            $subBomHeaderIds = BomContent::whereHas('bomHeader', function($query) use ($value) {
                $query->where('id', $value);
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();

            if(in_array($value, $this->selectBomHeader)) {
                $this->selectBomContent = array_unique(array_merge($this->selectBomContent, $subBomHeaderIds));
            }else {
                if($subBomHeaderIds) {
                    foreach($this->selectBomContent as $selectBomContentIndex => $selectBomContentValue) {
                        foreach($subBomHeaderIds as $idValue) {
                            if($selectBomContentValue == $idValue) {
                                unset($this->selectBomContent[$selectBomContentIndex]);
                            }
                        }
                    }
                }
            }
        }
        $this->selectBomContent = array_values($this->selectBomContent);
    }

    public function selectedContent($value)
    {
        if($value) {
            $selectedBomContent = BomContent::findOrFail($value);

            if($selectedBomContent->is_group) {
                $subBomContentIds = array_map('strval', BomContent::where('sequence', 'LIKE', $selectedBomContent->sequence.'%')->pluck('id')->map(fn($id) => (string) $id)->toArray());

                if(in_array($value, $this->selectBomContent)) {
                    $this->selectBomContent = array_unique(array_merge($this->selectBomContent, $subBomContentIds));
                }else {
                    if($subBomContentIds) {
                        foreach($this->selectBomContent as $selectBomContentIndex => $selectBomContentValue) {
                            foreach($subBomContentIds as $idValue) {
                                if($selectBomContentValue == $idValue) {
                                    unset($this->selectBomContent[$selectBomContentIndex]);
                                }
                            }
                        }
                    }
                }
                $this->selectBomContent = array_values($this->selectBomContent);
            }
        }
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
        $this->reset('file');
    }

    public function createInventoryMovement()
    {
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementForm->order_date = Carbon::today()->toDateString();
        $this->selectedContent = [];
        $this->inventoryMovementItems = [];
        $this->reset('inventoryMovementItemFormFilters');
    }

    public function editInventoryMovement(InventoryMovement $inventoryMovement)
    {
        $this->inventoryMovementForm = $inventoryMovement;
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->reloadInventoryItems($inventoryMovement);
        $this->bomItems = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        $this->bomForm = Bom::findOrFail($inventoryMovement->bom_id);
    }

    public function editSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $this->inventoryMovementItemForm = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        $this->attachments = $this->inventoryMovementItemForm->attachments;
        $this->bomItems = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
    }

    public function onGenerateOutgoingClicked()
    {
        $bomId = $this->inventoryMovementForm->bom_id;
        $bomQty = $this->inventoryMovementItemForm->bom_qty;
        $bomContents = BomContent::whereIn('id', $this->selectBomContent)->where('is_group', false)->get();

        $inventoryMovementItemArr = [];
        foreach($bomContents as $bomContent) {
            if($bomContent->qty > 0) {
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
        }
        $this->inventoryMovementItems = $inventoryMovementItemArr;
        $this->showGenerateByBomArea = false;
        $this->showGenerateLooseArea = false;
    }

    public function onGenerateLooseClicked()
    {
        $looseBomItem = BomItem::findOrFail($this->inventoryMovementItemForm->bom_item_id);
        $this->inventoryMovementItems[$looseBomItem->id] = [
            'bom_item_id' => $looseBomItem->id,
            'bom_item_code' => $looseBomItem->code,
            'bom_item_name' => $looseBomItem->name,
            'unit_price' => 0,
            'amount' => 0,
            'available_qty' => $looseBomItem->available_qty,
            'qty' => $this->inventoryMovementItemForm->qty,
            'balance_qty' => $looseBomItem->available_qty - $this->inventoryMovementItemForm->qty,
        ];
        $this->showGenerateByBomArea = false;
        $this->showGenerateLooseArea = false;
    }

    public function saveInventoryMovementForm($statusStr = null)
    {
        $this->validate([
            'inventoryMovementForm.bom_id' => 'required',
            'inventoryMovementForm.batch' => 'required',
        ], [
            'inventoryMovementForm.bom_id.required' => 'Please select a BOM',
            'inventoryMovementForm.batch.required' => 'Please fill in the Batch',
        ]);

        $status = $statusStr ? array_search($statusStr, InventoryMovement::STATUSES) : $this->inventoryMovementForm->status;

        $action = array_search('Outgoing', InventoryMovement::ACTIONS);

        if(!$this->inventoryMovementForm->id) {
            $inventoryMovement = InventoryMovement::create([
                'batch' => $this->inventoryMovementForm->batch,
                'remarks' => $this->inventoryMovementForm->remarks,
                'action' => $action,
                'bom_id' => $this->inventoryMovementForm->bom_id ? $this->inventoryMovementForm->bom_id : null,
                'country_id' => $this->inventoryMovementForm->country_id ? $this->inventoryMovementForm->country_id : null,
                'status' => $status,
                'total_amount' => $this->inventoryMovementForm->total_amount,
                'created_by' => auth()->user()->id,
                'order_date' => $this->inventoryMovementForm->order_date,
                'supplier_id' => $this->inventoryMovementForm->supplier_id ? $this->inventoryMovementForm->supplier_id : null,
                'created_at' => $this->inventoryMovementForm->created_at,
            ]);
        }else {
            $this->inventoryMovementForm->update([
                'batch' => $this->inventoryMovementForm->batch,
                'remarks' => $this->inventoryMovementForm->remarks,
                'action' => $action,
                'bom_id' => $this->inventoryMovementForm->bom_id ? $this->inventoryMovementForm->bom_id : null,
                'country_id' => $this->inventoryMovementForm->country_id ? $this->inventoryMovementForm->country_id : null,
                'status' => $status,
                'total_amount' => $this->inventoryMovementForm->total_amount,
                'updated_by' => auth()->user()->id,
                'order_date' => $this->inventoryMovementForm->order_date,
                'supplier_id' => $this->inventoryMovementForm->supplier_id ? $this->inventoryMovementForm->supplier_id : null,
                'created_at' => $this->inventoryMovementForm->created_at,
            ]);

            $inventoryMovement = $this->inventoryMovementForm;
        }

        // dd($inventoryMovement->status, isset($statusStr), $statusStr);

        if($action == array_search('Outgoing', InventoryMovement::ACTIONS)) {
            if($inventoryMovement->inventoryMovementItems()->exists()) {
                // if(($inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES))) {
                if($inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES) and $statusStr == null) {
                    foreach($inventoryMovement->inventoryMovementItems as $inventoryMovementItem) {
                        $this->addBomItemQtyAvailable($inventoryMovementItem->bomItem->id, $inventoryMovementItem['qty']);
                    }
                }
                $inventoryMovement->inventoryMovementItems()->delete();
            }
            if($this->inventoryMovementItems) {
                foreach($this->inventoryMovementItems as $inventoryMovementItem) {
                    $bomItem = BomItem::findOrFail($inventoryMovementItem['bom_item_id']);

                    $createdInventoryMovementItem = InventoryMovementItem::create([
                        'bom_item_id' => $bomItem->id,
                        'inventory_movement_id' => $inventoryMovement->id,
                        'status' => $status,
                        'qty' => $inventoryMovementItem['qty'],
                        'amount' => 0,
                        'unit_price'  => 0,
                        'created_by' => auth()->user()->id,
                        'date' => $inventoryMovement->order_date,
                    ]);

                    if(($inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES)) or (isset($statusStr) and $statusStr == 'Completed')) {
                        $this->reduceBomItemQtyAvailable($createdInventoryMovementItem->bom_item_id, $createdInventoryMovementItem->qty);
                    }

                    $this->syncBomItemQty($bomItem->id);
                }
            }
        }

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been added');
    }

    public function replicateInventoryMovementForm()
    {
        $replicationStatus = array_search('Confirmed', \App\Models\InventoryMovement::STATUSES);

        $replicatedInventoryMovement = $this->inventoryMovementForm->replicate()->fill([
            'batch' => $this->inventoryMovementForm->batch.'-replicated',
            'status' => $replicationStatus,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ]);
        $replicatedInventoryMovement->save();

        if($this->inventoryMovementForm->inventoryMovementItems()->exists()) {
            foreach($this->inventoryMovementForm->inventoryMovementItems as $inventoryMovementItem) {
                $replicatedInventoryMovementItem = $inventoryMovementItem->replicate()->fill([
                    'inventory_movement_id' => $replicatedInventoryMovement->id,
                    'status' => $replicationStatus,
                ]);
                $replicatedInventoryMovementItem->save();

                $this->syncBomItemQty($replicatedInventoryMovementItem->bomItem->id);
            }
        }

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Entry has been created');
    }

    public function updateSingleInventoryMovementItem()
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($this->inventoryMovementItemForm->id);

        $inventoryMovementItem->update([
            'qty' => $this->inventoryMovementItemForm->qty,
            'amount' => 0,
            'unit_price' => 0,
            'updated_by' => auth()->user()->id,
        ]);
        $this->syncInventoryMovementItemStatus($inventoryMovementItem);
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function onPrevNextDateInventoryMovementFormClicked($direction, $model)
    {
        $date = Carbon::now();
        if($model) {
            $date = Carbon::parse($this->inventoryMovementForm[$model]);
        }
        if($direction > 0) {
            $this->inventoryMovementForm[$model] = $date->addDay()->toDateString();
        }else {
            $this->inventoryMovementForm[$model] = $date->subDay()->toDateString();
        }
    }

    public function reloadInventoryItems($inventoryMovement)
    {
        $this->inventoryMovementItems = [];
        if($inventoryMovement->inventoryMovementItems) {
            // foreach($inventoryMovement->inventoryMovementItems as $inventoryMovementItem) {
            foreach($inventoryMovement->inventoryMovementItems()->leftJoin('bom_items', 'bom_items.id', '=', 'inventory_movement_items.bom_item_id')->orderBy('bom_items.code', 'asc')->get() as $inventoryMovementItem) {

            // foreach($inventoryMovement->inventoryMovementItems()->with(['bomItem' => function($query) {
            //     $query->orderBy('code', 'asc');
            // }])->get() as $inventoryMovementItem) {
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
                    'date' => $inventoryMovementItem->date,
                    'inventoryMovementItemQuantities' => $inventoryMovementItem->inventoryMovementItemQuantities()->with(['attachments', 'inventoryMovementItem', 'inventoryMovementItem.inventoryMovement', 'createdBy'])->get(),
                    'inventoryMovement' => $inventoryMovement,
                    'attachment_url' => $inventoryMovementItem->attachments()->latest()->first() ? $inventoryMovementItem->attachments()->latest()->first()->full_url : '',
                    'attachment' => $inventoryMovementItem->attachments()->latest()->first(),
                    'attachments' => $inventoryMovementItem->attachments,
                ];
                // dd($this->inventoryMovementItems);
                array_push($this->inventoryMovementItems, $data);
            }
        }
    }

    public function deleteAttachment(Attachment $attachment)
    {
        if(Attachment::where('full_url', $attachment->full_url)->count() === 1) {
            Storage::disk('digitaloceanspaces')->delete($attachment->url);
        }
        $attachment->delete();

        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
    }

    public function deleteSingleInventoryMovementItemIndex($index)
    {
        unset($this->inventoryMovementItems[$index]);
    }

    public function deleteSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        $inventoryMovement = $inventoryMovementItem->inventoryMovement;
        $bomItemId = $inventoryMovementItem->bomItem->id;
        if($inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES)) {
            $this->addBomItemQtyAvailable($bomItemId, $inventoryMovementItem->qty);
        }
        if($inventoryMovementItem->attachments()->exists()) {
            foreach($inventoryMovementItem->attachments as $attachment) {
                $this->deleteAttachment($attachment);
            }
        }
        $inventoryMovementItem->delete();
        $this->syncBomItemQty($bomItemId);
        $this->reloadInventoryItems($inventoryMovementItem->inventoryMovement);
        $this->inventoryMovement = new InventoryMovement();
        $this->inventoryMovementItem = new InventoryMovementItem();
        $this->inventoryMovementItemQuantity = new InventoryMovementItemQuantity();
        $this->emit('refresh');
    }

    public function deleteInventoryMovement()
    {
        if($this->inventoryMovementForm->inventoryMovementItems()->exists()) {
            foreach($this->inventoryMovementForm->inventoryMovementItems as $inventoryMovementItem) {
                $bomItemId = $inventoryMovementItem->bomItem->id;
                if($inventoryMovementItem->inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES)) {
                    $this->addBomItemQtyAvailable($bomItemId, $inventoryMovementItem->qty);
                }
                $inventoryMovementItem->delete();
                $this->syncBomItemQty($bomItemId);
            }
        }
        if($this->inventoryMovementForm->exists()) {
            $this->inventoryMovementForm->delete();
        }

        $this->inventoryMovementItem = new InventoryMovementItem();
        $this->inventoryMovementForm = new InventoryMovement();
        $this->bomItemId = null;

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been deleted');
    }

    private function syncBomItemQty($bomItemId)
    {
        $bomItem = BomItem::findOrFail($bomItemId);

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

    private function syncInventoryMovementItemStatus(InventoryMovementItem $inventoryMovementItem)
    {
        $isDelivered = false;
        if($inventoryMovementItem->inventoryMovement->status == array_search('Completed', InventoryMovement::STATUSES)) {
            $isDelivered = true;
        }

        if($isDelivered) {
            $inventoryMovementItem->status = array_search('Delivered', InventoryMovementItem::OUTGOING_STATUSES);
        }else {
            $inventoryMovementItem->status = array_search('Planned', InventoryMovementItem::OUTGOING_STATUSES);
        }
        $inventoryMovementItem->save();
    }

    private function addBomItemQtyAvailable($bomItemId, $qty)
    {
        $bomItem = BomItem::findOrFail($bomItemId);
        $bomItem->available_qty += $qty;
        $bomItem->save();
        $this->syncBomItemQty($bomItemId);
        // dd($bomItemId, $qty, $bomItem->toArray());
    }

    private function reduceBomItemQtyAvailable($bomItemId, $qty)
    {
        $bomItem = BomItem::findOrFail($bomItemId);
        $bomItem->available_qty -= $qty;
        $bomItem->save();
        $this->syncBomItemQty($bomItemId);
    }
}
