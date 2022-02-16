<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\Bom;
use App\Models\BomContent;
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

class BomReceiving extends Component
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
    public $inventoryMovementItemFormFilters = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
    ];
    public $inventoryMovementItems = [];
    public $totalAmount = 0.00;
    public $attachments;
    public $bomItems;
    public $bomItemTypes;
    public $file;
    public InventoryMovement $inventoryMovementForm;
    public InventoryMovementItem $inventoryMovementItemForm;
    public InventoryMovementItem $editInventoryMovementItemForm;
    public InventoryMovementItemQuantity $inventoryMovementItemQuantityForm;
    public InventoryMovementItemQuantity $inventoryMovementItemQuantity;

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
            'inventoryMovementItemQuantityForm.date' => 'required',
            'inventoryMovementItemQuantityForm.qty' => 'required',
            'inventoryMovementItemQuantityForm.remarks' => 'sometimes',
            'inventoryMovementItemFormFilters.code' => 'sometimes',
            'inventoryMovementItemFormFilters.name' => 'sometimes',
            'inventoryMovementItemFormFilters.bom_item_type_id' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->inventoryMovementItemQuantityForm = new InventoryMovementItemQuantity();
        $this->countries = Country::orderBy('currency_name')->get();
        $this->boms = Bom::latest()->get();
        $this->bomItems = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
    }

    public function render()
    {
        $inventoryMovements = InventoryMovement::with([
                                    'inventoryMovementItems',
                                    'inventoryMovementItems.inventoryMovementItemQuantities',
                                    'bom',
                                    'createdBy',
                                    'updatedBy',
                            ]);

        $inventoryMovements = $inventoryMovements
                ->when($this->filters['batch'], fn($query, $input) => $query->searchLike('batch', $input))
                ->when($this->filters['status'], fn($query, $input) => $query->search('status', $input))
                ->when($this->filters['created_at'], fn($query, $input) => $query->searchDate('created_at', $input));

        $inventoryMovements = $inventoryMovements->where('action', array_search('Receiving', \App\Models\InventoryMovement::ACTIONS));

        if($sortKey = $this->sortKey) {
            $inventoryMovements = $inventoryMovements->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $inventoryMovements = $inventoryMovements->latest();
        }

        $inventoryMovements = $inventoryMovements->paginate($this->itemPerPage);

        return view('livewire.bom-receiving', [
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

    public function updated($name, $value)
    {
        if($name == 'inventoryMovementItemFormFilters.code' or $name == 'inventoryMovementItemFormFilters.name' or $name == 'inventoryMovementItemFormFilters.bom_item_type_id') {
            $bomItems = BomItem::when($this->inventoryMovementItemFormFilters['code'], fn($query, $input) => $query->searchLike('code', $input))
                                    ->when($this->inventoryMovementItemFormFilters['name'], fn($query, $input) => $query->searchLike('name', $input))
                                    ->when($this->inventoryMovementItemFormFilters['bom_item_type_id'], fn($query, $input) => $query->whereHas('bomItemType', function($query) use ($input) { $query->search('id', $input); }));

            // if($bomItemTypeId = $this->inventoryMovementItemFormFilters['bom_item_type_id']) {
            //     $bomItems = $bomItems->whereHas('bomItemType', function($query) use ($bomItemTypeId) {
            //         $query->search('id', $bomItemTypeId);
            //     });
            // }

            $this->bomItems = $bomItems->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        }
    }

    public function updatedInventoryMovementFormCountryId($value)
    {
        $this->inventoryMovementForm = new InventoryMovement();
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->inventoryMovementItems = [];
        $this->inventoryMovementForm->country_id = $value;
        $this->bomItems = BomItem::where(function($query) use ($value) {
            $query->doesntHave('supplierQuotePrices')->orWhereHas('supplierQuotePrices', function($query) use ($value) {
                $query->where('country_id', $value);
            });
        })->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
    }

    public function updatedInventoryMovementItemFormSupplierUnitPrice($value)
    {
        $this->inventoryMovementItemForm->unit_price = is_numeric($value) ? $value : 0;
    }

    public function updatedInventoryMovementItemFormBomItemId($value)
    {
        if($value) {
            $this->inventoryMovementItemForm->unit_price = 0.00;
            $this->inventoryMovementItemForm->amount = 0.00;

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

    public function deleteInventoryMovement()
    {
        if($this->inventoryMovementForm->inventoryMovementItems()->exists()) {
            foreach($this->inventoryMovementForm->inventoryMovementItems as $inventoryMovementItem) {
                $bomItemId = $inventoryMovementItem->bomItem->id;
                if($inventoryMovementItem->inventoryMovementItemQuantities()->exists()) {
                    foreach($inventoryMovementItem->inventoryMovementItemQuantities as $inventoryMovementItemQuantity) {
                        $this->reduceBomItemQtyAvailable($inventoryMovementItemQuantity->inventoryMovementItem->bomItem->id, $inventoryMovementItemQuantity->qty);
                        if($inventoryMovementItemQuantity->attachments()->exists()) {
                            foreach($inventoryMovementItemQuantity->attachments as $attachment) {
                                $this->deleteAttachment($attachment);
                            }
                        }
                        $inventoryMovementItemQuantity->delete();
                    }
                }
                $inventoryMovementItem->delete();
                $this->syncBomItemQty($bomItemId);
            }
        }
        if($this->inventoryMovementForm->exists()) {
            $this->inventoryMovementForm->delete();
        }

        $this->inventoryMovementItemQuantity = new InventoryMovementItemQuantity();
        $this->inventoryMovementItem = new InventoryMovementItem();
        $this->inventoryMovementForm = new InventoryMovement();

        $inventoryMovementItemQuantity = new InventoryMovementItemQuantity();
        $inventoryMovementItem = new InventoryMovementItem();

        $this->bomItemId = null;
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

    public function editInventoryMovement(InventoryMovement $inventoryMovement)
    {
        $this->inventoryMovementForm = $inventoryMovement;
        $this->inventoryMovementItemForm = new InventoryMovementItem();
        $this->reloadInventoryItems($inventoryMovement);
        $this->bomItems = BomItem::where(function($query) use ($inventoryMovement) {
            $query->doesntHave('supplierQuotePrices')->orWhereHas('supplierQuotePrices', function($query) use ($inventoryMovement) {
                $query->where('country_id', $inventoryMovement->country_id);
            });
        })->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
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
                    'inventoryMovementItemQuantities' => $inventoryMovementItem->inventoryMovementItemQuantities()->with(['attachments', 'inventoryMovementItem', 'inventoryMovementItem.inventoryMovement'])->get(),
                    'inventoryMovement' => $inventoryMovement,
                ];
                // dd($this->inventoryMovementItems);
                array_push($this->inventoryMovementItems, $data);
                $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
            }
        }
    }

    public function saveInventoryMovementForm($statusStr = null)
    {
        $this->validate([
            'inventoryMovementForm.country_id' => 'required',
            'inventoryMovementForm.batch' => 'required',
        ], [
            'inventoryMovementForm.country_id.required' => 'Please select a Currency',
            'inventoryMovementForm.batch.required' => 'Please fill in the Batch',
        ]);
        // dd($this->inventoryMovementForm->toArray());

        $status = $statusStr ? array_search($statusStr, InventoryMovement::STATUSES) : $this->inventoryMovementForm->status;

        $action = array_search('Receiving', InventoryMovement::ACTIONS);

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
            ]);
        }else {
            // if($status) {
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
                ]);

            $inventoryMovement = $this->inventoryMovementForm;
        }

        // InventoryMovement
        //     1 => 'Pending',
        //     2 => 'Confirmed',
        //     3 => 'Partially',
        //     4 => 'Completed',
        //     99 => 'Cancelled',

        // InventoryMovementItem
        // 1 => 'New',
        // 2 => 'Ordered',
        // 4 => 'Received',
        // 99 => 'Void',

        // When IM confirmed, IMI ordered
        // When IM completed, IMIQ not able to create
        // When IM completed, IMI received
        // When IMIQ created, qty_available added
        // When IMIQ deleted, qty_available reduced
        // When IMI voided, qty_available reduced

        // sync everytime when update (delete all, then create again)
        // sync only the newly added (remain old, add new)


        if($action == array_search('Receiving', InventoryMovement::ACTIONS) and $statusStr) {
            if($this->inventoryMovementItems) {
                foreach($this->inventoryMovementItems as $inventoryMovementItem) {
                    // dd($inventoryMovementItem->toArray());
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
                    if(isset($inventoryMovementItem['id'])) {
                        InventoryMovementItem::findOrFail($inventoryMovementItem['id'])->update([
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
                    }else {
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
                    }

                    $this->syncBomItemQty($bomItem->id);
                }
            }
        }
        $this->syncTotalAmount($inventoryMovement->id);

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been added');
    }

    public function editReceiveInventoryMovementItem(InventoryMovementItem $inventoryMovementItem)
    {
        $this->inventoryMovementItemForm = $inventoryMovementItem;
        $this->inventoryMovementItemQuantityForm = new InventoryMovementItemQuantity();
        $this->inventoryMovementItemQuantityForm->date = Carbon::today()->toDateString();
    }

    public function deleteSingleInventoryMovementItemIndex($index)
    {
        unset($this->inventoryMovementItems[$index]);

        $this->inventoryMovementForm->total_amount = $this->calculateTotalAmount($this->inventoryMovementItems);
    }

    public function deleteSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        $inventoryMovement = $inventoryMovementItem->inventoryMovement;
        $bomItemId = $inventoryMovementItem->bomItem->id;
        if($inventoryMovementItem->inventoryMovementItemQuantities()->exists()) {
            foreach($inventoryMovementItem->inventoryMovementItemQuantities as $inventoryMovementItemQuantity) {
                $this->reduceBomItemQtyAvailable($inventoryMovementItem->bom_item_id, $inventoryMovementItemQuantity->qty);
                if($inventoryMovementItemQuantity->attachments()->exists()) {
                    foreach($inventoryMovementItemQuantity->attachments as $attachment) {
                        $this->deleteAttachment($attachment);
                    }
                }
                $inventoryMovementItemQuantity->delete();
            }
        }
        $inventoryMovementItem->delete();
        $this->syncBomItemQty($bomItemId);
        $this->syncTotalAmount($inventoryMovement->id);
        $this->reloadInventoryItems($inventoryMovementItem->inventoryMovement);
        $this->inventoryMovement = new InventoryMovement();
        $this->inventoryMovementItem = new InventoryMovementItem();
        $this->inventoryMovementItemQuantity = new InventoryMovementItemQuantity();
        $this->emit('refresh');
    }

    public function saveInventoryMovementItemQuantityForm()
    {
        $inventoryMovementItemQuantity = $this->inventoryMovementItemForm->inventoryMovementItemQuantities()->save($this->inventoryMovementItemQuantityForm);
        if($this->file) {
            $url = $this->file->storePublicly('receiving', 'digitaloceanspaces');
            $fullUrl = Storage::url($url);
            $inventoryMovementItemQuantity->attachments()->create([
                'url' => $url,
                'full_url' => $fullUrl,
            ]);
        }
        $this->addBomItemQtyAvailable($this->inventoryMovementItemForm->bomItem->id, $inventoryMovementItemQuantity->qty);
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function removeQuantity(InventoryMovementItemQuantity $inventoryMovementItemQuantity)
    {
        $inventoryMovement = $inventoryMovementItemQuantity->inventoryMovementItem->inventoryMovement;
        $this->reduceBomItemQtyAvailable($inventoryMovementItemQuantity->inventoryMovementItem->bomItem->id, $inventoryMovementItemQuantity->qty);
        if($inventoryMovementItemQuantity->attachments()->exists()) {
            foreach($inventoryMovementItemQuantity->attachments as $attachment) {
                $this->deleteAttachment($attachment);
            }
        }
        $inventoryMovementItemQuantity->delete();
        $this->reloadInventoryItems($inventoryMovement);
        $this->inventoryMovement = new InventoryMovement();
        $this->inventoryMovementItemQuantity = new InventoryMovementItemQuantity();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been removed');
    }

    public function removeQuantityByInventoryMovementItemQuantityId($id)
    {
        $inventoryMovementItemQuantityCollection = InventoryMovementItemQuantity::findOrFail($id);
        $inventoryMovement = $inventoryMovementItemQuantityCollection->inventoryMovementItem->inventoryMovement;
        $this->reduceBomItemQtyAvailable($inventoryMovementItemQuantityCollection->inventoryMovementItem->bomItem->id, $inventoryMovementItemQuantityCollection->qty);
        if($inventoryMovementItemQuantityCollection->attachments()->exists()) {
            foreach($inventoryMovementItemQuantityCollection->attachments as $attachment) {
                $this->deleteAttachment($attachment);
            }
        }
        $inventoryMovementItemQuantityCollection->delete();
        $this->reloadInventoryItems($inventoryMovement);
        $this->inventoryMovement = null;
        $this->inventoryMovementItemQuantityCollection = null;
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been removed');
    }

    public function viewBomItemAttachments(BomItem $bomItem)
    {
        $this->reset('attachments');
        $this->attachments = $bomItem->attachments;
    }

    public function viewQuantityAttachments(InventoryMovementItemQuantity $inventoryMovementItemQuantity)
    {
        $this->reset('attachments');
        $this->attachments = $inventoryMovementItemQuantity->attachments;
        $this->inventoryMovement = $inventoryMovementItemQuantity->inventoryMovementItem->inventoryMovement;
        $this->inventoryMovementItemQuantity = $inventoryMovementItemQuantity;
    }

    public function viewQuantityAttachmentsByInventoryMovementItemQuantityId($id)
    {
        $inventoryMovementItemQuantity = InventoryMovementItemQuantity::findOrFail($id);
        $this->reset('attachments');
        $this->attachments = $inventoryMovementItemQuantity->attachments;
        $this->inventoryMovementItemQuantity = $inventoryMovementItemQuantity;
    }

    public function deleteAttachment(Attachment $attachment)
    {
        $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
        if($deleteFile){
            $attachment->delete();
        }
        $this->attachment = new Attachment();
        $this->deleteFile = null;
        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
    }

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
    }

    public function createInventoryMovement()
    {
        $this->inventoryMovementForm = new InventoryMovement();
        $this->reset('inventoryMovementItemFormFilters');
    }

    public function onPrevNextDateClicked($direction, $model)
    {
        $date = Carbon::now();
        if($this[$model]) {
            $date = Carbon::parse($this[$model]);
        }
        if($direction > 0) {
            $this[$model] = $date->addDay()->toDateString();
        }else {
            $this[$model] = $date->subDay()->toDateString();
        }
    }

    public function onInventoryMovementItemQuantityFormPrevNextDateClicked($direction, $model)
    {
        $date = Carbon::now();
        if($model) {
            $date = Carbon::parse($this->inventoryMovementItemQuantityForm[$model]);
        }
        if($direction > 0) {
            $this->inventoryMovementItemQuantityForm[$model] = $date->addDay()->toDateString();
        }else {
            $this->inventoryMovementItemQuantityForm[$model] = $date->subDay()->toDateString();
        }
    }

    public function addInventoryMovementItem()
    {
        $bomItem = BomItem::findOrFail($this->inventoryMovementItemForm->bom_item_id);
        if(isset($this->inventoryMovementForm->id)) {
            $inventoryMovementItem = InventoryMovementItem::create([
                'bom_item_id' => $bomItem->id,
                'inventory_movement_id' => $this->inventoryMovementForm->id,
                'supplier_quote_price_id' => $this->inventoryMovementItemForm->supplier_quote_price_id,
                'status' => $this->inventoryMovementForm->status,
                'qty' => $this->inventoryMovementItemForm->qty,
                'amount' => $this->inventoryMovementItemForm->amount,
                'unit_price' => $this->inventoryMovementItemForm->unit_price,
                'created_by' => auth()->user()->id,
                'date' => $this->inventoryMovementItemForm->date,
                'remarks' => $this->inventoryMovementItemForm->remarks,
            ]);

            $this->syncBomItemQty($bomItem->id);

            $data = [
                'id' => $inventoryMovementItem->id,
                'bom_item_id' => $inventoryMovementItem->bom_item_id,
                'bom_item_code' => $inventoryMovementItem->bomItem->code,
                'bom_item_name' => $inventoryMovementItem->bomItem->name,
                'unit_price' => $inventoryMovementItem->unit_price,
                'amount' => $inventoryMovementItem->amount,
                'qty' => $inventoryMovementItem->qty,
                'supplier_quote_price_id' => $inventoryMovementItem->supplier_quote_price_id,
                'status' => $inventoryMovementItem->status,
                'date' => $inventoryMovementItem->date,
                'remarks' => $inventoryMovementItem->remarks,
                'inventoryMovement' => $inventoryMovementItem->inventoryMovement,
            ];
        }else {
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
        }

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

    public function editSingleInventoryMovementItem($inventoryMovementItemId)
    {
        $this->inventoryMovementItemForm = InventoryMovementItem::findOrFail($inventoryMovementItemId);
        $this->updatedInventoryMovementItemFormBomItemId($this->inventoryMovementItemForm->bomItem->id);
        $inventoryMovementCollection = $this->inventoryMovementItemForm->inventoryMovement;
        $this->attachments = $this->inventoryMovementItemForm->attachments;
        $this->bomItems = BomItem::where(function($query) use ($inventoryMovementCollection) {
            $query->doesntHave('supplierQuotePrices')->orWhereHas('supplierQuotePrices', function($query) use ($inventoryMovementCollection) {
                $query->where('country_id', $inventoryMovementCollection->country_id);
            });
        })->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
    }

    public function updateSingleInventoryMovementItem()
    {
        $inventoryMovementItem = InventoryMovementItem::findOrFail($this->inventoryMovementItemForm->id);
        $inventoryMovementItem->update([
            'qty' => $this->inventoryMovementItemForm->qty,
            'amount' => $this->inventoryMovementItemForm->amount,
            'unit_price' => $this->inventoryMovementItemForm->unit_price,
            'updated_by' => auth()->user()->id,
            'date' => $this->inventoryMovementItemForm->date,
            'remarks' => $this->inventoryMovementItemForm->remarks,
        ]);
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function calculateAmount()
    {
        if($this->inventoryMovementItemForm->qty and $this->inventoryMovementItemForm->unit_price) {
            $this->inventoryMovementItemForm->amount = round($this->inventoryMovementItemForm->qty * $this->inventoryMovementItemForm->unit_price, 2);
        }else {
            $this->inventoryMovementItemForm->amount = 0.00;
        }
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

    private function syncTotalAmount($inventoryMovementId)
    {
        $totalAmount = 0.00;
        $inventoryMovement = InventoryMovement::findOrFail($inventoryMovementId);
        if($inventoryMovement->inventoryMovementItems()->exists()) {
            foreach($inventoryMovement->inventoryMovementItems as $inventoryMovementItem) {
                $totalAmount += $inventoryMovementItem->amount;
            }
        }
        $inventoryMovement->total_amount = $totalAmount;
        $inventoryMovement->save();
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
