<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\Bom;
use App\Models\BomCategory;
use App\Models\BomSubCategory;
use App\Models\BomItemType;
use App\Models\VmmfgBom;
use Carbon\Carbon;
use DB;
use Excel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;

class VmmfgInventoryBom extends Component
{
    use WithFileUploads, WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = '';
    public $sortAscending = true;
    public $showFilters = false;
    public $filters = [
        'search' => '',
        'name' => '',
        'remarks' => '',
    ];

    public Attachment $attachment;
    public Bom $bom;

    public $bomCategories;
    public $bomSubCategories;
    public $bomItemTypes;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function rules()
    {
        return [
            'bom.name' => 'required',
            'bom.remarks' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->bomCategories = BomCategory::oldest()->get();
        $this->bomSubCategories = BomSubCategory::oldest()->get();
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
    }

    public function render()
    {
        $boms = Bom::with(['bomGroups', 'bomGroups.bomItems']);

        // advance search
        $boms = $boms
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['remarks'], fn($query, $input) => $query->searchLike('remarks', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('name', $input)->orSearchLike('remarks', $input));

        if($sortKey = $this->sortKey) {
            $boms = $boms->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }

        $boms = $boms->paginate($this->itemPerPage);

        return view('livewire.vmmfg-inventory-bom', ['boms' => $boms]);
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

    public function edit(Bom $bom)
    {
        $this->bom = $bom;
    }

    public function create()
    {
        $this->bom = new Bom;
    }

    public function save()
    {
        $this->validate([
            'bom.name' => 'required',
            'bom.remarks' => 'sometimes',
        ]);
        $this->bom->save();
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

}
