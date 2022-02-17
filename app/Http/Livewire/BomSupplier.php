<?php

namespace App\Http\Livewire;

use App\Models\BomItem;
use App\Models\Country;
use App\Models\PaymentTerm;
use App\Models\Supplier;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class BomSupplier extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showEditModal = false;
    public $showFilters = false;
    public $selected = [];
    public $filters = [
        'company_name' => '',
        'attn_name' => '',
    ];
    public $supplierForm = [
        'company_name' => '',
        'attn_name' => '',
        'attn_contact' => '',
        'url' => '',
        'email' => '',
        'payment_term_id' => '',
        'country_id' => '',
    ];

    public $bomItems;
    public $paymentTerms;
    public $countries;

    public function rules()
    {
        return [
            'supplierForm.company_name' => 'required',
            'supplierForm.attn_name' => 'sometimes',
            'supplierForm.attn_contact' => 'sometimes',
            'supplierForm.url' => 'sometimes',
            'supplierForm.email' => 'sometimes',
            'supplierForm.payment_term_id' => 'sometimes',
            'supplierForm.country_id' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->bomItems = BomItem::where('is_part', 1)
                                ->orderBy('bom_items.code')
                                ->get();
        $this->paymentTerms = PaymentTerm::orderBy('name')->get();
        $this->countries = Country::orderBy('name')->get();
    }

    public function render()
    {
        $suppliers = Supplier::with([
            'paymentTerm',
            'transactedCurrency',
        ]);

        // advance search
        $suppliers = $suppliers
                ->when($this->filters['company_name'], fn($query, $input) => $query->searchLike('company_name', $input))
                ->when($this->filters['attn_name'], fn($query, $input) => $query->searchLike('attn_name', $input));

        if($sortKey = $this->sortKey) {
            $suppliers = $suppliers->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $suppliers = $suppliers->orderBy('company_name');
        }

        $suppliers = $suppliers->paginate($this->itemPerPage);

        return view('livewire.bom-supplier', [
            'suppliers' => $suppliers,
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

    public function create()
    {
        $this->supplierForm = new Supplier;
    }

    public function edit(Supplier $supplier)
    {
        $this->supplierForm = $supplier;

    }

    public function save()
    {
        $this->validate();
        $this->supplierForm->save();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete()
    {
        $this->supplierForm->delete();
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
}
