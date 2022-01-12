<?php

namespace App\Http\Livewire;

use App\Models\Bom;
use App\Models\BomContent;
use App\Models\BomItem;
use App\Models\BomItemType;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class BomInventory extends Component
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
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'is_consumable' => '',
        'is_inventory' => '',
    ];
    public $bomItemForm = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'is_consumable' => '',
        'is_inventory' => '',
    ];
    public $bomItemTypes;

    public function rules()
    {
        return [
            'bomItemForm.code' => 'required',
            'bomItemForm.name' => 'required',
            'bomItemForm.bom_item_type_id' => 'sometimes',
            'bomItemForm.is_consumable' => 'sometimes',
            'bomItemForm.is_inventory' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
    }

    public function render()
    {
        $bomItems = BomItem::with([
            'attachments',
            'bomItemType',
            'bomHeaders',
            'bomContents',
        ])
        ->where('is_part', true);

        // advance search
        $bomItems = $bomItems
                ->when($this->filters['code'], fn($query, $input) => $query->searchLike('code', $input))
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input));

        if($bomItemTypeId = $this->filters['bom_item_type_id']) {
            $bomItems = $bomItems->whereHas('bomItemType', function($query) use ($bomItemTypeId) {
                $query->search('id', $bomItemTypeId);
            });
        }

        if($isConsumable = $this->filters['is_consumable']) {
            $bomItems = $bomItems->whereHas('bomItemType', function($query) use ($isConsumable) {
                if($isConsumable) {
                    $query->search('name', 'C');
                }else {
                    $query->where('name', '<>', 'C');
                }
            });
        }

        if($sortKey = $this->sortKey) {
            $bomItems = $bomItems->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $bomItems = $bomItems->orderBy('code');
        }

        $bomItems = $bomItems->paginate($this->itemPerPage);

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
    }

    public function save()
    {
        $this->validate();
        $this->bomItemForm->save();
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
