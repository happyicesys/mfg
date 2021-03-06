<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\Bom;
use App\Models\BomContent;
use App\Models\BomItem;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementItem;
use App\Models\Supplier;
use App\Models\SupplierQuotePrice;
use DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;

class BomMovement extends Component
{
    use WithFileUploads, WithPagination;

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
    public $negativeAvailableQtyAlert = false;
    public $attachments;
    public $file;

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
            $this->supplierQuotedPriceCountry = $this->supplier->id ?
                                            $this->selectedBomItem->supplierQuotePrices()->latest()->first()->country :
                                            null;
            $this->supplierQuotedPriceStr = $this->supplier->id ?
                                            $this->supplierQuotedPrice.' ('.$this->supplierQuotedPriceCountry->currency_name.')' :
                                            null;
                                            // dd($this->supplierQuotedPriceStr);
            $this->inventoryMovementItemForm->unit_price = $this->supplierQuotedPrice;
            $this->inventoryMovementItemForm->supplier_unit_price = $this->supplierQuotedPrice;
            $this->inventoryMovementItemForm->rate = $this->supplier->transactedCurrency ? $this->supplier->transactedCurrency->currencyRates()->latest()->first()->rate : null;
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
            'date' => $this->inventoryMovementItemForm->date,
            'remarks' => $this->inventoryMovementItemForm->remarks,
        ];

        if($this->inventoryMovementItemForm->supplier_unit_price) {
            if($this->supplierQuotedPrice != $this->inventoryMovementItemForm->supplier_unit_price) {
                $data['supplier_unit_price'] = $this->inventoryMovementItemForm->supplier_unit_price;
                $data['supplier_quote_price_id'] = null;
            }
        }

        if($this->inventoryMovementItemForm->rate) {
            if($this->supplier->transactedCurrency->currencyRates()->latest()->first()->rate != $this->inventoryMovementItemForm->rate) {
                $data['country_id'] = $this->supplier->transactedCurrency->id;
                $data['rate'] = $this->inventoryMovementItemForm->rate;
            }
        }

        array_push($this->inventoryMovementItems, $data);
        // dd($this->inventoryMovementItems);

        $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
    }

    public function deleteSingleInventoryMovementItem($index)
    {
        unset($this->inventoryMovementItems[$index]);

        $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
    }

    public function saveInventoryMovementForm($statusStr = null)
    {
        $this->validate([
            'inventoryMovementForm.action' => 'required',
            'inventoryMovementForm.batch' => 'required',
        ], [
            'inventoryMovementForm.action.required' => 'Please select an Action',
            'inventoryMovementForm.batch.required' => 'Please fill in the Batch',
        ]);

        $status = $statusStr ? array_search($statusStr, InventoryMovement::STATUSES) : $this->inventoryMovementForm->status;

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
                'order_date' => $this->inventoryMovementForm->order_date,
            ]);
        }else {
            // if($status) {
                $this->inventoryMovementForm->update([
                    'batch' => $this->inventoryMovementForm->batch,
                    'remarks' => $this->inventoryMovementForm->remarks,
                    'action' => $this->inventoryMovementForm->action,
                    'bom_id' => $this->inventoryMovementForm->bom_id ? $this->inventoryMovementForm->bom_id : null,
                    'country_id' => $this->inventoryMovementForm->country_id ? $this->inventoryMovementForm->country_id : null,
                    'status' => $status,
                    'total_amount' => $this->inventoryMovementForm->total_amount,
                    'updated_by' => auth()->user()->id,
                    'order_date' => $this->inventoryMovementForm->order_date,
                ]);
            // }else {
            //     $preInventoryMovement = InventoryMovement::findOrFail($this->inventoryMovementForm->id);
            //     $this->inventoryMovementForm->update([
            //         'batch' => $this->inventoryMovementForm->batch,
            //         'remarks' => $this->inventoryMovementForm->remarks,
            //         'action' => $this->inventoryMovementForm->action,
            //         'bom_id' => $this->inventoryMovementForm->bom_id ? $this->inventoryMovementForm->bom_id : null,
            //         'country_id' => $this->inventoryMovementForm->country_id ? $this->inventoryMovementForm->country_id : null,
            //         'status' => $preInventoryMovement->status,
            //         'total_amount' => $this->inventoryMovementForm->total_amount,
            //         'updated_by' => auth()->user()->id,
            //         'order_date' => $this->inventoryMovementForm->order_date,
            //     ]);
            // }

            $inventoryMovement = $this->inventoryMovementForm;
        }

        if($statusStr) {
            if($action == array_search('Receiving', InventoryMovement::ACTIONS)) {
                if($this->inventoryMovementItems) {
                    $previousInventoryMovementItems = InventoryMovementItem::where('inventory_movement_id', $inventoryMovement->id)->delete();
                    foreach($this->inventoryMovementItems as $inventoryMovementItem) {
                        $bomItem = BomItem::findOrFail($inventoryMovementItem['bom_item_id']);

                        if(isset($inventoryMovementItem['rate'])) {
                            $currencyRate = CurrencyRate::create([
                                'rate' => $inventoryMovementItem['rate'],
                                'country_id' => $inventoryMovementItem['country_id'],
                            ]);
                        }

                        if(isset($inventoryMovementItem['supplier_unit_price'])) {
                            $supplierQuotePrice = SupplierQuotePrice::create([
                                'bom_item_id' => $bomItem->id,
                                'country_id' => $currencyRate->country_id,
                                'currency_rate_id' => $currencyRate->id,
                                'supplier_id' => $this->supplier->id,
                                'unit_price' => $inventoryMovementItem['supplier_unit_price'],
                                'base_price' => $inventoryMovementItem['supplier_unit_price']/ ($currencyRate->rate ? $currencyRate->rate : 1),
                            ]);
                            $inventoryMovementItem['supplier_quote_price_id'] = $supplierQuotePrice->id;
                        }
                        InventoryMovementItem::create([
                            'bom_item_id' => $bomItem->id,
                            'inventory_movement_id' => $inventoryMovement->id,
                            'supplier_quote_price_id' => $inventoryMovementItem['supplier_quote_price_id'],
                            'status' => $status,
                            'qty' => $inventoryMovementItem['qty'],
                            'amount' => $inventoryMovementItem['amount'],
                            'unit_price'  => $inventoryMovementItem['unit_price'],
                            'created_by' => auth()->user()->id,
                            'date' => $inventoryMovementItem['date'],
                            'remarks' => $inventoryMovementItem['remarks'],
                        ]);

                        $this->syncBomItemQty($bomItem->id);
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
                            'date' => $inventoryMovement->order_date,
                        ]);
                        $this->syncBomItemQty($bomItem->id);
                    }
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

    public function onPrevNextDateinventoryMovementFormClicked($direction, $model)
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

    public function onPrevNextDateinventoryMovementItemFormClicked($direction, $model)
    {
        $date = Carbon::now();
        if($model) {
            $date = Carbon::parse($this->inventoryMovementItemForm[$model]);
        }
        if($direction > 0) {
            $this->inventoryMovementItemForm[$model] = $date->addDay()->toDateString();
        }else {
            $this->inventoryMovementItemForm[$model] = $date->subDay()->toDateString();
        }
    }

    public function onPrevNextDateEditInventoryMovementItemFormClicked($direction, $model)
    {
        $date = Carbon::now();
        if($model) {
            $date = Carbon::parse($this->editInventoryMovementItemForm[$model]);
        }
        if($direction > 0) {
            $this->editInventoryMovementItemForm[$model] = $date->addDay()->toDateString();
        }else {
            $this->editInventoryMovementItemForm[$model] = $date->subDay()->toDateString();
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
        $inventoryMovementItem->status = array_search('Received', InventoryMovementItem::RECEIVING_STATUSES);
        $inventoryMovementItem->save();
        $this->addBomItemQtyAvailable($inventoryMovementItem->bom_item_id, $inventoryMovementItem->qty);
        $this->reloadInventoryItems($inventoryMovementItem->inventoryMovement);
        $this->emit('refresh');
    }

    public function voidSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        if($inventoryMovementItem->status == array_search('Received', InventoryMovementItem::RECEIVING_STATUSES)) {
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
                    'date' => $inventoryMovementItem->date,
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

    public function updatedInventoryMovementItemFormSupplierUnitPrice($value)
    {
        $this->inventoryMovementItemForm->unit_price = is_numeric($value) ? $value : 0;
    }

    public function editSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $this->editInventoryMovementItemForm = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        $this->attachments = $this->editInventoryMovementItemForm->attachments;
    }

    public function saveInventoryMovementItemForm()
    {
        $this->inventoryMovementItemForm->save();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been deleted');
    }

    public function saveEditInventoryMovementItemForm()
    {
        $this->editInventoryMovementItemForm->save();

        if($this->file) {
            $url = $this->file->storePublicly('receiving', 'digitaloceanspaces');
            $fullUrl = Storage::url($url);
            $this->editInventoryMovementItemForm->attachments()->create([
                'url' => $url,
                'full_url' => $fullUrl,
            ]);
        }

        $this->reloadInventoryItems($this->editInventoryMovementItemForm->inventoryMovement);
        $this->editInventoryMovementItemForm = InventoryMovementItem::findOrFail($this->editInventoryMovementItemForm->id);
        $this->attachments = $this->editInventoryMovementItemForm->attachments;
        $this->emit('refresh');
        session()->flash('success', 'Your entry has been deleted');
    }

    // public function deleteAttachment(Attachment $attachment)
    // {
    //     $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
    //     if($deleteFile){
    //         $attachment->delete();
    //     }
    //     $this->emit('updated');
    //     session()->flash('success', 'Entry has been removed');
    // }

    public function deleteAttachment(Attachment $attachment)
    {
        if(Attachment::where('full_url', $attachment->full_url)->count() === 1) {
            Storage::disk('digitaloceanspaces')->delete($attachment->url);
        }
        $attachment->delete();

        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
    }

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
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
        $this->syncBomItemQty($bomItemId);
    }

    private function reduceBomItemQtyAvailable($bomItemId, $qty)
    {
        $bomItem = BomItem::findOrFail($bomItemId);
        $bomItem->available_qty -= $qty;
        $bomItem->save();
        $this->syncBomItemQty($bomItemId);
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
}
