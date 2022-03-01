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
        'is_inventory' => '',
        'is_consumable' => '0',
    ];
    public $bomItemForm = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'is_inventory' => '',
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
            'supplierQuotePriceForm.supplier_id' => 'sometimes',
            'supplierQuotePriceForm.unit_price' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('company_name')->get();
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
            'supplierQuotePrices.supplier'
        ])
        ->where('is_inventory', true);

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
                    $query->search('name', 'C');
                }else {
                    $query->whereNotIn('name', ['C']);
                }
            });
        }

        $bomItems = $bomItems->where('is_inventory', 1);

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

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function updatedFilters()
    {
        $this->resetPage();
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
        $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
        if($deleteFile){
            $attachment->delete();
        }
        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
    }

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
    }
}
