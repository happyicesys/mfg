<?php

namespace App\Http\Livewire;

use App\Models\Profile as CompanyProfile;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class Profile extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = 'name';
    public $sortAscending = true;
    public $showEditModal = false;
    public $showFilters = false;
    public $selected = [];
    public $filters = [
        'search' => '',
        'name' => '',
        'symbol' => '',
        'date' => '',
    ];
    public CompanyProfile $form;

    public function rules()
    {
        return [
            'form.name' => 'required',
            'form.symbol' => 'required',
            'form.reg_no' => 'required',
        ];
    }


    public function render()
    {
        $profiles = CompanyProfile::query();

        // advance search
        $profiles = $profiles
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['symbol'], fn($query, $input) => $query->searchLike('symbol', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('name', $input)->orSearchLike('symbol', $input));
                // ->when($this->filters['status'], fn($query, $input) => $query->where('status', 'LIKE', '%'.$input.'%'));

        if($sortKey = $this->sortKey) {
            $profiles = $profiles->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }

        $profiles = $profiles->paginate($this->itemPerPage);

        return view('livewire.profile', ['profiles' => $profiles]);
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

    public function edit(CompanyProfile $profile)
    {
        $this->form = $profile;
    }

    public function save()
    {
        $this->validate();
        $this->form->save();
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
