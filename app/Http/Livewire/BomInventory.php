<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\Bom;
use App\Models\BomContent;
use App\Models\BomItem;
use App\Models\BomItemType;
use App\Models\Profile;
use App\Models\Supplier;
use App\Models\SupplierQuotePrice;
use DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;

class BomInventory extends Component
{
    use WithFileUploads, WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 'All';
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $showFilters = false;
    public $selected = [];
    public $filters = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'is_inventory' => '1',
        'is_consumable' => '0',
        'supplier_id' => '',
        'qty_status' => '',
    ];
    public $planner = [
        'bom_id' => '',
        'qty' => '',
    ];
    // public $bomItemForm = [
    //     'code' => '',
    //     'name' => '',
    //     'bom_item_type_id' => '',
    //     'is_inventory' => '',
    // ];
    public $bomItemFormFilters = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'supplier_id' => '',
    ];
    public $supplierQuotePriceForm = [
        'supplier_id' => '',
        'unit_price' => '',
    ];
    public $bomItemTypes;
    public $suppliers;
    public $supplier;
    public $realTimeConversionPrice = 0;
    public $supplierCurrencyName;
    public $supplierQuotePrices = [];
    public $attachments;
    public $file;
    public $showPlannerArea = false;
    public $showChildrenArea = false;
    public $bomItemsFilters;

    public BomItem $bomItemForm;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function rules()
    {
        return [
            'bomItemForm.code' => 'required',
            'bomItemForm.name' => 'required',
            'bomItemForm.bom_item_type_id' => 'sometimes',
            'bomItemForm.is_inventory' => 'sometimes',
            'bomItemForm.bom_item_parent_id' => 'sometimes',
            'supplierQuotePriceForm.supplier_id' => 'sometimes',
            'supplierQuotePriceForm.unit_price' => 'sometimes',
            'bomItemFormFilters.code' => 'sometimes',
            'bomItemFormFilters.name' => 'sometimes',
            'bomItemFormFilters.bom_item_type_id' => 'sometimes',
            'bomItemFormFilters.supplier_id' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->bomItemForm = new BomItem();
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('company_name')->get();
        $this->boms = Bom::latest()->get();
        $this->bomItemsFilters = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
    }

    public function render()
    {
        $bomItems = BomItem::with([
            'attachments',
            'bomItemType',
            'bomHeaders',
            'bomContents',
            'supplierQuotePrices',
            'supplierQuotePrices.country',
            'supplierQuotePrices.supplier',
            'children',
            'parent'
        ]);

        // advance search
        $bomItems = $bomItems
                ->when($this->filters['code'], fn($query, $input) => $query->searchLike('code', $input))
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input));

        if($bomItemTypeId = $this->filters['bom_item_type_id']) {
            $bomItems = $bomItems->whereHas('bomItemType', function($query) use ($bomItemTypeId) {
                $query->search('id', $bomItemTypeId);
            });
        }

        if($this->filters['is_consumable'] != '') {
            $isConsumable = $this->filters['is_consumable'];
            $bomItems = $bomItems->whereHas('bomItemType', function($query) use ($isConsumable) {
                if($isConsumable) {
                    $query->whereIn('name', ['C', 'CB']);
                }else {
                    $query->whereNotIn('name', ['C', 'CB']);
                }
            });
        }

        if($this->filters['is_inventory'] != '') {
            $isInventory = $this->filters['is_inventory'];
            $bomItems = $bomItems->where('is_inventory', $isInventory);
        }

        if($supplierId = $this->filters['supplier_id']) {
            $bomItems = $bomItems->whereHas('supplierQuotePrices', function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            });
        }

        if($qtyStatus = $this->filters['qty_status']) {
            $bomItems = $bomItems->where('planned_qty', '>', 'available_qty');
        }


        $bomItems = $bomItems->where('is_part', 1);

        if($sortKey = $this->sortKey) {
            $bomItems = $bomItems->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $bomItems = $bomItems->orderBy('code');
        }

        if($this->itemPerPage == 'All') {
            $bomItems = $bomItems->get();
        }else {
            $bomItems = $bomItems->paginate($this->itemPerPage);
        }

        return view('livewire.bom-inventory', ['bomItems' => $bomItems]);
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

    public function edit(BomItem $bomItem)
    {
        $this->bomItemForm = $bomItem;
        $this->supplierQuotePrices = $bomItem->supplierQuotePrices()->latest()->get();
        $this->attachments = $bomItem->attachments;
    }

    public function save()
    {
        $this->validate();
        // dd($this->bomItemForm->toArray());
        if($this->bomItemForm->is_inventory) {
            $this->bomItemForm->bom_item_parent_id = null;
        }
        $this->bomItemForm->save();

        if($this->file) {
            $oriFileName = $this->file->getClientOriginalName();
            $url = $this->file->storePubliclyAs('bom', $oriFileName, 'digitaloceanspaces');
            $fullUrl = Storage::url($url);
            $this->bomItemForm->attachments()->create([
                'url' => $url,
                'full_url' => $fullUrl,
            ]);
        }

        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete()
    {
        if($this->bomItemForm->attachments()->exists()) {
            foreach($this->bomItemForm->attachments as $attachment) {
                $this->deleteAttachment($attachment);
            }
        }
        $this->bomItemForm->delete();

        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function updatedFiltersQtyStatus($value)
    {
        if($value) {
            $this->filters['code'] = '';
            $this->filters['name'] = '';
            $this->filters['bom_item_type_id'] = '';
            $this->filters['is_inventory'] = '';
            $this->filters['is_consumable'] = '';
            $this->filters['supplier_id'] = '';
        }
    }

    public function updated($name, $value)
    {
        if($name == 'bomItemFormFilters.code' or $name == 'bomItemFormFilters.name' or $name == 'bomItemFormFilters.bom_item_type_id' or $name == 'bomItemFormFilters.supplier_id') {
            $bomItemsFilters = BomItem::when($this->bomItemFormFilters['code'], fn($query, $input) => $query->searchLike('code', $input))
                                    ->when($this->bomItemFormFilters['name'], fn($query, $input) => $query->searchLike('name', $input))
                                    ->when($this->bomItemFormFilters['bom_item_type_id'], fn($query, $input) => $query->whereHas('bomItemType', function($query) use ($input) { $query->search('id', $input); }));

            if($supplierId = $this->bomItemFormFilters['supplier_id']) {
                $bomItemsFilters = $bomItemsFilters->where(function($query) use ($supplierId) {
                    $query->whereHas('supplierQuotePrices', function($query) use ($supplierId) {
                        $query->where('supplier_id', $supplierId);
                    });
                });
            }

            $this->bomItemsFilters = $bomItemsFilters->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        }

        if($name == 'filters.is_inventory') {
            $this->showChildrenArea = false;
            if($value == 1) {
                $this->showChildrenArea = true;
            }

            if($value == '') {
                $this->showChildrenArea = true;
            }
        }
    }

    public function createSupplierQuotePrice()
    {
        $this->reset('supplierQuotePriceForm');
        $this->realTimeConversionPrice = 0;
    }

    public function calculateConvertion()
    {
        // dd($this->supplierQuotePriceForm);
        if($this->supplierQuotePriceForm['supplier_id']) {
            $supplier = Supplier::find($this->supplierQuotePriceForm['supplier_id']);
            $this->supplier = $supplier;
            // dd($supplier->toArray());
            $this->supplierCurrencyName = $supplier->country->currency_name;

            if(is_numeric($this->supplierQuotePriceForm['unit_price'])) {
                $baseCurrency = Profile::where('is_primary', 1)->first()->country->currencyRates()->latest()->first();
                $quotePrice = $this->supplierQuotePriceForm['unit_price'];
                $supplierCurrency = $supplier->country->currencyRates()->latest()->first();
                $this->realTimeConversionPrice = round($quotePrice/$supplierCurrency->rate, 2);
            }
        }
    }

    public function saveSupplierQuotePrice()
    {
        $this->validate([
            'supplierQuotePriceForm.supplier_id' => 'required',
            'supplierQuotePriceForm.unit_price' => 'required|numeric',
        ]);

        SupplierQuotePrice::create([
            'bom_item_id' => $this->bomItemForm->id,
            'country_id' => $this->supplier->country->id,
            'currency_rate_id' => $this->supplier->country->currencyRates()->latest()->first()->id,
            'supplier_id' => $this->supplierQuotePriceForm['supplier_id'],
            'unit_price' => $this->supplierQuotePriceForm['unit_price'],
            'base_price' => $this->realTimeConversionPrice,
        ]);

        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function deleteSingleSupplierQuotePrice(SupplierQuotePrice $supplierQuotePrice)
    {
        $supplierQuotePrice->delete();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been deleted');
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

    // public function deleteAttachment(Attachment $attachment)
    // {
    //     $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
    //     if($deleteFile){
    //         $attachment->delete();
    //     }
    //     $this->emit('updated');
    //     session()->flash('success', 'Entry has been removed');
    // }

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
    }
}
