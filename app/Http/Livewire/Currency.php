<?php

namespace App\Http\Livewire;

use App\Models\Country;
use App\Models\CurrencyRate;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class Currency extends Component
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
        'name' => '',
        'currency_name' => '',
    ];
    public $currencyForm = [
        'rate' => '',
    ];
    public Country $country;
    public $currencyRates = [];

    protected function rules()
    {
        return [
            'currencyForm.rate' => 'required',
        ];
    }

    protected $messages = [
        'currencyForm.rate.required' => 'Please fill in the currency rate',
    ];

    public function render()
    {
        $countries = Country::with([
            'currencyRates',
        ]);

        // advance search
        $countries = $countries
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['currency_name'], fn($query, $input) => $query->searchLike('currency_name', $input));

        if($sortKey = $this->sortKey) {
            $countries = $countries->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $countries = $countries->orderBy('name');
        }

        $countries = $countries->paginate($this->itemPerPage);

        return view('livewire.currency', [
            'countries' => $countries,
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

    public function edit(Country $country)
    {
        $this->currencyForm = $country->currencyRates()->latest()->first();
        $this->country = $country;
        $this->currencyRates = $this->country->currencyRates()->latest()->get();
    }

    public function save()
    {
        $validatedForm = $this->validate();
        // dd($validatedForm);
        // $this->country->currencyRates->save($validatedForm);
        CurrencyRate::create([
            'rate' => $this->currencyForm['rate'],
            'country_id' => $this->country->id,
        ]);
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
}
