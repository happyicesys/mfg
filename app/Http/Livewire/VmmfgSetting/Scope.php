<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\VmmfgItem;
use App\Models\VmmfgScope;
use App\Models\VmmfgTitle;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class Scope extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = 'created_at';
    public $sortAscending = false;
    public $showEditModal = false;
    public $showFilters = false;
    public $showCreateTitleArea = false;
    public $selected = [];
    public $filters = [
        'search' => '',
        'name' => '',
        'remarks' => '',
    ];
    public $titleForm = [
        'name' => '',
        'remarks' => '',
    ];

    public VmmfgScope $form;

    public function rules()
    {
        return [
            'form.name' => 'required',
            'form.remarks' => 'sometimes',
        ];
    }


    public function render()
    {

        // $scopes = VmmfgScope::with([
        //     'vmmfgTitles' => function($query) {
        //         $query->orderBy('sequence', 'asc');
        //     },
        //     'vmmfgTitles.VmmfgItems' => function($query) {
        //         $query->orderBy('sequence', 'asc');
        //     }
        // ]);
        $scopes = VmmfgScope::with(['vmmfgTitles', 'vmmfgTitles.VmmfgItems']);

        // advance search
        $scopes = $scopes
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['remarks'], fn($query, $input) => $query->searchLike('remarks', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('name', $input)->orSearchLike('remarks', $input));
                // ->when($this->filters['status'], fn($query, $input) => $query->where('status', 'LIKE', '%'.$input.'%'));

        if($sortKey = $this->sortKey) {
            $scopes = $scopes->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }

        $scopes = $scopes->paginate($this->itemPerPage);

        return view('livewire.vmmfg-setting.scope', ['scopes' => $scopes]);
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

    public function edit(VmmfgScope $scopes)
    {
        $this->form = $scopes;

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

    public function generateTitle()
    {
        $this->validate([
            'titleForm.sequence' => 'numeric',
            'titleForm.name' => 'required'
        ]);

        VmmfgTitle::create([
            'sequence' => $this->titleForm['sequence'],
            'name' => $this->titleForm['name'],
            'vmmfg_scope_id' => $this->form->id,
        ]);
        $this->reset('titleForm');
        $this->showCreateTitleArea = false;
        $this->emit('updated');
        session()->flash('success', 'Entry has been created');
    }
}
