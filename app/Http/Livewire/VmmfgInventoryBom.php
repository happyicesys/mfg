<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use App\Models\Bom;
use App\Models\BomCategory;
use App\Models\BomHeader;
use App\Models\BomSubCategory;
use App\Models\BomItem;
use App\Models\BomItemType;
use App\Models\BomContent;
use App\Models\Supplier;
use App\Models\VmmfgItem;
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
    public BomContent $bomContent;
    public BomHeader $bomHeader;
    public BomItem $bomItem;

    public $bomCategories;
    public $bomSubCategories;
    public $bomItemTypes;
    public $bomHeaderForm = [
        'sequence' => '',
        'qty' => '',
        'bom_item_id' => '',
        'bom_id' => '',
        'vmmfg_item_id' => '',
        'bom_category_id' => '',
        'is_existing' => false,
        'is_edit' => false,
        'bom_item_type_id' => '',
        'is_inventory' => false,
        'is_header' => false,
    ];
    public $bomContentForm = [
        'sequence' => '',
        'qty' => '',
        'bom_item_id' => '',
        'bom_header_id' => '',
        'vmmfg_item_id' => '',
        'bom_sub_category_id' => '',
        'is_existing' => false,
        'is_edit' => false,
        'is_group' => false,
        'bom_item_type_id' => '',
        'is_inventory' => true,
        'is_sub_header' => false,
        'is_part' => false,

    ];
    public $bomContentFormFilters = [
        'code' => '',
        'name' => '',
        'bom_item_type_id' => '',
        'supplier_id' => '',
    ];
    public $vmmfgItems;
    public $bomItemGroups;
    public $bomItemParts;
    public $bomItemSubGroups;
    public $file;
    public $attachments;
    public $selectAll = false;
    public $selectBomHeader = [];
    public $selectBomContent = [];
    public $suppliers = [];
    public $bomItems;
    public $bomItemsFilters;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function rules()
    {
        return [
            'bom.name' => 'required',
            'bom.remarks' => 'sometimes',
            'bomHeaderForm.code' => 'required',
            'bomHeaderForm.name' => 'required',
            'bomHeaderForm.sequence' => 'required',
            'bomHeaderForm.qty' => 'sometimes',
            'bomHeaderForm.bom_category_id' => 'sometimes',
            'bomHeaderForm.bom_item_id' => 'sometimes',
            'bomHeaderForm.bom_id' => 'required',
            'bomHeaderForm.vmmfg_item_id' => 'sometimes',
            'bomHeaderForm.is_existing' => 'sometimes',
            'bomHeaderForm.is_edit' => 'sometimes',
            'bomHeaderForm.bom_item_type_id' => 'sometimes',
            'bomHeaderForm.is_header' => 'sometimes',
            'bomContentForm.code' => 'required',
            'bomContentForm.name' => 'required',
            'bomContentForm.sequence' => 'required',
            'bomContentForm.qty' => 'sometimes',
            'bomContentForm.bom_sub_category_id' => 'sometimes',
            'bomContentForm.bom_item_id' => 'sometimes',
            'bomContentForm.bom_id' => 'required',
            'bomContentForm.vmmfg_item_id' => 'sometimes',
            'bomContentForm.is_existing' => 'sometimes',
            'bomContentForm.is_edit' => 'sometimes',
            'bomContentForm.is_group' => 'sometimes',
            'bomContentForm.bom_item_type_id' => 'sometimes',
            'bomContentForm.is_inventory' => 'sometimes',
            'bomContentForm.is_sub_header' => 'sometimes',
            'bomContentForm.is_part' => 'sometimes',
            // 'bomContentForm.bom_item_parent_id' => 'sometimes',
            'bomContentFormFilters.code' => 'sometimes',
            'bomContentFormFilters.name' => 'sometimes',
            'bomContentFormFilters.bom_item_type_id' => 'sometimes',
            'bomContentFormFilters.supplier_id' => 'sometimes',
            'file' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->bomCategories = BomCategory::oldest()->get();
        $this->bomSubCategories = BomSubCategory::with('bomCategory')->oldest()->get();
        $this->bomItemTypes = BomItemType::orderBy('name')->get();
        $this->vmmfgItems = VmmfgItem::with(['vmmfgTitle', 'vmmfgTitle.vmmfgScope'])
                                ->leftJoin('vmmfg_titles', 'vmmfg_titles.id', '=', 'vmmfg_items.vmmfg_title_id')
                                ->leftJoin('vmmfg_scopes', 'vmmfg_scopes.id', '=', 'vmmfg_titles.vmmfg_scope_id')
                                ->select('*', 'vmmfg_items.id AS id')
                                ->orderBy('vmmfg_scopes.remarks', 'desc')
                                ->orderBy('vmmfg_titles.sequence')
                                ->orderBy('vmmfg_items.sequence')
                                ->get();
        $this->bomItemGroups = BomItem::with(['bomHeaders', 'bomHeaders.bomCategory'])
                                    ->has('bomHeaders.bomCategory')
                                    ->orderBy('bom_items.code')
                                    ->get();
        $this->bomItems = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        $this->bomItemsFilters = BomItem::where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        $this->bomHeaderForm = new BomHeader();
        $this->bomContentForm = new BomContent();
        $this->bom = new Bom();
        $this->suppliers = Supplier::orderBy('company_name')->get();

    }

    public function render()
    {
        $boms = Bom::with([
            'bomHeaders',
            'bomHeaders.bomItem',
            'bomHeaders.bomItem.attachments',
            'bomHeaders.bomCategory',
            'bomHeaders.vmmfgItem',
            'bomHeaders.bomContents.bomItem',
            'bomHeaders.bomContents.bomItem.attachments',
            'bomHeaders.bomContents.bomSubCategory',
            'bomHeaders.bomContents.vmmfgItem',
        ]);

        // advance search
        $boms = $boms
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['remarks'], fn($query, $input) => $query->searchLike('remarks', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('name', $input)->orSearchLike('remarks', $input));

        if($sortKey = $this->sortKey) {
            $boms = $boms->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }

        $boms = $boms->paginate($this->itemPerPage);
        // dd($boms->toArray());

        return view('livewire.vmmfg-inventory-bom', ['boms' => $boms]);
    }

    public function updatedSelectAll($value)
    {
        if($value) {
            $selectedBomId = $this->bom->id;
            $this->selectBomHeader = BomHeader::where('bom_id', $selectedBomId)->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $this->selectBomContent = BomContent::whereHas('bomHeader', function($query) use ($selectedBomId) {
                $query->where('bom_id', $selectedBomId);
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }else {
            $this->selectBomHeader = [];
            $this->selectBomContent = [];
        }
    }

    public function updated($name, $value)
    {
        if($name == 'bomContentFormFilters.code' or $name == 'bomContentFormFilters.name' or $name == 'bomContentFormFilters.bom_item_type_id' or $name == 'bomContentFormFilters.supplier_id') {
            $bomItemsFilters = BomItem::when($this->bomContentFormFilters['code'], fn($query, $input) => $query->searchLike('code', $input))
                                    ->when($this->bomContentFormFilters['name'], fn($query, $input) => $query->searchLike('name', $input))
                                    ->when($this->bomContentFormFilters['bom_item_type_id'], fn($query, $input) => $query->whereHas('bomItemType', function($query) use ($input) { $query->search('id', $input); }));

            if($supplierId = $this->bomContentFormFilters['supplier_id']) {
                $bomItemsFilters = $bomItemsFilters->where(function($query) use ($supplierId) {
                    $query->whereHas('supplierQuotePrices', function($query) use ($supplierId) {
                        $query->where('supplier_id', $supplierId);
                    });
                });
            }


            $this->bomItemsFilters = $bomItemsFilters->where('is_part', 1)->where('is_inventory', 1)->orderBy('code')->get();
        }
    }

    public function selectedHeader($value)
    {
        if($value) {
            $subBomHeaderIds = BomContent::whereHas('bomHeader', function($query) use ($value) {
                $query->where('id', $value);
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();

            if(in_array($value, $this->selectBomHeader)) {
                $this->selectBomContent = array_unique(array_merge($this->selectBomContent, $subBomHeaderIds));
            }else {
                if($subBomHeaderIds) {
                    foreach($this->selectBomContent as $selectBomContentIndex => $selectBomContentValue) {
                        foreach($subBomHeaderIds as $idValue) {
                            if($selectBomContentValue == $idValue) {
                                unset($this->selectBomContent[$selectBomContentIndex]);
                            }
                        }
                    }
                }
            }
        }
        $this->selectBomContent = array_values($this->selectBomContent);
    }

    public function selectedContent($value)
    {
        if($value) {
            $selectedBomContent = BomContent::findOrFail($value);

            if($selectedBomContent->is_group) {
                $subBomContentIds = array_map('strval', BomContent::where('sequence', 'LIKE', $selectedBomContent->sequence.'%')->pluck('id')->map(fn($id) => (string) $id)->toArray());

                if(in_array($value, $this->selectBomContent)) {
                    $this->selectBomContent = array_unique(array_merge($this->selectBomContent, $subBomContentIds));
                }else {
                    if($subBomContentIds) {
                        foreach($this->selectBomContent as $selectBomContentIndex => $selectBomContentValue) {
                            foreach($subBomContentIds as $idValue) {
                                if($selectBomContentValue == $idValue) {
                                    unset($this->selectBomContent[$selectBomContentIndex]);
                                }
                            }
                        }
                    }
                }
                $this->selectBomContent = array_values($this->selectBomContent);
            }
        }
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
        $this->bom = new Bom();
        $this->bom = $bom;
        // dd($bom->bomHeaders->toArray());
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

    public function createHeader(Bom $bom)
    {
        $this->bom = $bom;
        $this->bomHeader = new BomHeader;
        $this->bomHeaderForm = new BomHeader;
        $this->bomHeaderForm->is_edit = false;
        $this->bomHeaderForm->sequence = BomHeader::where('bom_id', $this->bom->id)->max('sequence') + 1;
        $this->reset('file');
        $this->reset('attachments');
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function editHeader(BomHeader $bomHeader)
    {
        $this->reset('file');
        $this->reset('attachments');
        $this->bomHeader = $bomHeader;
        $this->bomHeaderForm = $bomHeader;
        $this->bomHeaderForm->code = $bomHeader->bomItem->code;
        $this->bomHeaderForm->name = $bomHeader->bomItem->name;
        $this->bomHeaderForm->bom_item_type_id = $bomHeader->bomItem->bomItemType->id;
        $this->bomHeaderForm->is_edit = true;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function saveHeader()
    {
        if($this->bomHeaderForm->is_existing) {
            $this->validate([
                'bomHeaderForm.bom_item_id' => 'required',
            ], [
                'bomHeaderForm.bom_item_id.required' => 'Please select an Existing Group'
            ]);
        }else {
            $this->validate([
                'bomHeaderForm.code' => 'required',
                'bomHeaderForm.name' => 'required',
            ], [
                'bomHeaderForm.code.required' => 'Please fill in the Part Code',
                'bomHeaderForm.name.required' => 'Please fill in the Part Name',
            ]);
        }
        if($this->bomHeaderForm->is_edit) {
            $this->bomHeader->update([
                'sequence' => $this->bomHeaderForm->sequence,
                'bom_category_id' => $this->bomHeaderForm->bom_category_id,
                'vmmfg_item_id' => $this->bomHeaderForm->vmmfg_item_id,
                'qty' => $this->bomHeaderForm->qty,
            ]);
            $bomHeaderItem = $this->bomHeader->bomItem()->update([
                'code' => $this->bomHeaderForm->code,
                'name' => $this->bomHeaderForm->name,
                'bom_item_type_id' => $this->bomHeaderForm->bom_item_type_id,
            ]);
            $currentBomHeader = $this->bomHeader;
        }else {
            if($this->bomHeaderForm->is_existing) {
                $bomItemId = $this->bomHeaderForm->bom_item_id;
            }else {
                $bomHeaderItem = BomItem::create([
                    'code' => $this->bomHeaderForm->code,
                    'name' => $this->bomHeaderForm->name,
                    'bom_item_type_id' => $this->bomHeaderForm->bom_item_type_id,
                    'is_header' => true,
                ]);
                $bomItemId = $bomHeaderItem->id;
            }
            $currentBomHeader = $this->bomHeader->create([
                'sequence' => $this->bomHeaderForm->sequence,
                'bom_id' => $this->bom->id,
                'bom_category_id' => $this->bomHeaderForm->bom_category_id,
                'bom_item_id' => $bomItemId,
                'vmmfg_item_id' => $this->bomHeaderForm->vmmfg_item_id,
                'qty' => $this->bomHeaderForm->qty,
            ]);
        }

        if($currentBomHeader->bomItem and $this->file) {
            $oriFileName = $this->file->getClientOriginalName();
            $url = $this->file->storePubliclyAs('bom', $oriFileName, 'digitaloceanspaces');
            $fullUrl = Storage::url($url);
            $currentBomHeader->bomItem->attachments()->create([
                'url' => $url,
                'full_url' => $fullUrl,
            ]);
        }

        $this->reset('file');
        $this->emit('refresh');
        session()->flash('success', 'Entry has been created');
    }

    public function deleteHeader()
    {
        if($this->bomHeader->bomContents()->exists()) {
            foreach($this->bomHeader->bomContents as $bomContent) {
                $bomContent->delete();
            }
        }
        $this->bomHeader->delete();
        $this->bomHeader = new BomHeader();
        $this->bomHeaderForm = new BomHeader();

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Entry has been deleted');
    }

    public function createSubGroup(BomHeader $bomHeader)
    {
        $this->bomHeader = $bomHeader;
        $this->bomContent = new BomContent();
        $this->bomContentForm = new BomContent();
        $this->bomContentForm->is_group = true;
        $this->bomItemSubGroups = BomItem::where('is_header', 1)
                                        ->orderBy('bom_items.code')
                                        ->get();
        $this->bomItemSubGroups = BomItem::where('is_sub_header', 1)
                                        ->orderBy('bom_items.code')
                                        ->get();
        $this->bomContentForm->is_edit = false;
        $this->bomContentForm->sequence = $this->incrementNestedSequence(
                                            $this->getBomContentLastSequence(
                                                BomContent::where('bom_header_id', $this->bomHeader->id)->get()
                                            )
                                        );
        $this->reset('file');
        $this->reset('attachments');
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function createContent(BomHeader $bomHeader)
    {
        $this->bomHeader = $bomHeader;
        $this->bomContent = new BomContent();
        $this->bomContentForm = new BomContent();
        $this->bomContentForm->is_group = false;
        $this->bomItemSubGroups = BomItem::where('is_part', 1)
                                    ->orderBy('bom_items.code')
                                    ->get();
        $this->bomSubCategories = BomSubCategory::with('bomCategory')
                                    ->whereHas('bomCategory', function($query) use ($bomHeader) {
                                        $query->where('id', $bomHeader->bom_category_id);
                                    })
                                    ->orderBy('name')
                                    ->get();
        $this->bomContentForm->is_edit = false;
        $this->bomContentForm->sequence = $this->incrementNestedSequence(
                                            $this->getBomContentLastSequence(
                                                BomContent::where('bom_header_id', $this->bomHeader->id)->get()
                                            )
                                        );
        $this->reset('file');
        $this->reset('attachments');
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function savePart()
    {
        if($this->bomContentForm->is_existing) {
            $this->validate([
                'bomContentForm.bom_item_id' => 'required',
            ], [
                'bomContentForm.bom_item_id.required' => 'Please select an Existing SubGroup or Part'
            ]);
        }else {
            $this->validate([
                'bomContentForm.code' => 'required',
                'bomContentForm.name' => 'required',
            ], [
                'bomContentForm.code.required' => 'Please fill in the Part Code',
                'bomContentForm.name.required' => 'Please fill in the Part Name',
            ]);
        }
        if($this->bomContentForm->is_edit) {

            if($this->bomContentForm->is_existing) {
                $this->bomContent->update([
                    'bom_item_id' => $this->bomContentForm->bom_item_id,
                ]);
                $this->bomContent->bomItem()->update([
                    'bom_item_type_id' => $this->bomContentForm->bom_item_type_id,
                ]);
            }else {
                $this->validate([
                    'bomContentForm.code' => 'unique:bom_items,code,'.$this->bomContentForm->bomItem->id,
                ], [
                    'bomContentForm.code.unique' => 'This Code is already been used',
                ]);

                $this->bomContent->update([
                    'sequence' => $this->bomContentForm->sequence,
                    'bom_sub_category_id' => $this->bomContentForm->bom_sub_category_id,
                    'bom_header_id' => $this->bomHeader->id,
                    'is_group' => $this->bomContentForm->is_group,
                    'vmmfg_item_id' => $this->bomContentForm->vmmfg_item_id,
                    'qty' => $this->bomContentForm->qty,
                    // 'bom_item_parent_id' => $this->bomContentForm->bom_item_parent_id,
                ]);
                $this->bomContent->bomItem()->update([
                    'code' => $this->bomContentForm->code,
                    'name' => $this->bomContentForm->name,
                    'bom_item_type_id' => $this->bomContentForm->bom_item_type_id,
                    'is_inventory' => $this->bomContentForm->is_inventory ? $this->bomContentForm->is_inventory : false,
                ]);
            }


            $currentBomContent = $this->bomContent;
        }else {
            if($this->bomContentForm->is_existing) {
                $bomItemId = $this->bomContentForm->bom_item_id;
            }else {
                $this->validate([
                    'bomContentForm.code' => 'unique:bom_items,code',
                ], [
                    'bomContentForm.code.unique' => 'This Code is already been used',
                ]);
                $bomContentItem = BomItem::create([
                    'code' => $this->bomContentForm->code,
                    'name' => $this->bomContentForm->name,
                    'bom_item_type_id' => $this->bomContentForm->bom_item_type_id,
                    'is_inventory' => $this->bomContentForm->is_inventory ? $this->bomContentForm->is_inventory : false,
                    'is_sub_header' => $this->bomContentForm->is_group ? true : false,
                    'is_part' => $this->bomContentForm->is_group ? false : true,
                ]);
                $bomItemId = $bomContentItem->id;
            }
            $currentBomContent = $this->bomContent->create([
                'sequence' => $this->bomContentForm->sequence,
                'bom_header_id' => $this->bomHeader->id,
                'bom_sub_category_id' => $this->bomContentForm->bom_sub_category_id,
                'bom_item_id' => $bomItemId,
                'is_group' => $this->bomContentForm->is_group,
                'vmmfg_item_id' => $this->bomContentForm->vmmfg_item_id,
                'qty' => $this->bomContentForm->qty,
                // 'bom_item_parent_id' => $this->bomContentForm->bom_item_parent_id,
            ]);
        }

        if($currentBomContent->bomItem and $this->file) {
            $oriFileName = $this->file->getClientOriginalName();
            $url = $this->file->storePubliclyAs('bom', $oriFileName, 'digitaloceanspaces');
            $fullUrl = Storage::url($url);
            $currentBomContent->bomItem->attachments()->create([
                'url' => $url,
                'full_url' => $fullUrl,
            ]);
        }

        $this->reset('file');
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Entry has been created');
    }

    public function editPart(BomContent $bomContent)
    {
        $this->reset('file');
        $this->reset('attachments');
        $this->bomHeader = $bomContent->bomHeader;
        $this->bomContent = $bomContent;
        $this->bomContentForm = $bomContent;
        $this->bomContentForm->code = $bomContent->bomItem->code;
        $this->bomContentForm->name = $bomContent->bomItem->name;
        $this->bomContentForm->is_inventory = $bomContent->bomItem->is_inventory;
        $this->bomContentForm->bom_item_type_id = $bomContent->bomItem->bomItemType ? $bomContent->bomItem->bomItemType->id : null;
        $this->bomContentForm->is_edit = true;

        if($this->bomContentForm->is_group) {
            $this->bomItemSubGroups = BomItem::where('is_sub_header', 1)
                                            ->orderBy('bom_items.code')
                                            ->get();
        }else {
            $this->bomItemSubGroups = BomItem::where('is_part', 1)
                                        ->orderBy('bom_items.code')
                                        ->get();
        }
        $this->bomSubCategories = BomSubCategory::with('bomCategory')
                                    ->whereHas('bomCategory', function($query) use ($bomContent) {
                                        $query->where('id', $bomContent->bomHeader->bom_category_id);
                                    })
                                    ->orderBy('name')
                                    ->get();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function deletePart()
    {
        $this->bomContent->delete();
        $this->bomContent = new BomContent();
        $this->bomContentForm = new BomContent();
        // $this->emit('updated');
        $this->emit('refresh');

        session()->flash('success', 'Entry has been deleted');
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

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
    }

    public function viewAttachmentsByBomItem(BomItem $bomItem)
    {
        $this->reset('attachments');
        $this->attachments = $bomItem->attachments;
    }

    public function replicateBom(Bom $bom)
    {
        $replicatedBom = $bom->replicate()->fill([
            'name' => $bom->name.'-replicated',
        ]);
        $replicatedBom->save();

        if($bom->bomHeaders()->exists()) {
            foreach($bom->bomHeaders as $bomHeader) {
                $replicatedBomHeader = $bomHeader->replicate()->fill([
                    'bom_id' => $replicatedBom->id,
                ]);
                $replicatedBomHeader->save();

                if($bomHeader->bomContents()->exists()) {
                    foreach($bomHeader->bomContents as $bomContent) {
                        $replicatedBomContent = $bomContent->replicate()->fill([
                            'bom_header_id' => $replicatedBomHeader->id,
                        ]);
                        $replicatedBomContent->save();
                    }
                }
            }
        }

        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Entry has been created');
    }

    public function deleteBom(Bom $bom)
    {
        if($bom->bomHeaders()->exists()) {
            foreach($bom->bomHeaders as $bomHeader) {
                if($bomHeader->bomContents()->exists()) {
                    foreach($bomHeader->bomContents as $bomContent) {
                        $bomContent->delete();
                    }
                }
                $bomHeader->delete();
            }
        }
        $bom->delete();
        $this->bom = new Bom();
        $bom = new Bom();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Entry has been deleted');
    }

    public function batchDeleteBomHeaderBomContent(Bom $bom)
    {
        if($this->selectBomContent) {
            if($bom->bomHeaders()->exists()) {
                foreach($bom->bomHeaders as $bomHeader) {
                    $bomHeader->bomContents()->whereIn('id', $this->selectBomContent)->delete();
                }
            }
        }

        if($bom->bomHeaders()->exists()) {
            foreach($bom->bomHeaders as $bomHeader) {
                if($bomHeader->bomContents()->doesntExist()) {
                    $bomHeader->delete();
                }
            }
        }
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Entry has been deleted');
    }

    private function incrementNestedSequence($value)
    {
        for($updatedValue = explode( ".", $value ), $i = count($updatedValue) - 1; $i > -1; --$i) {
            if ( ++$updatedValue[$i] < 10 || !$i ) break;
            $updatedValue[$i] = 0;
        }
        $updatedValue = implode( ".", $updatedValue );
        return $updatedValue;
    }

    private function getBomContentLastSequence($collections)
    {
        $lastSequence = 0;
        $sequenceArr = [];
        if($collections) {
            for($i=0; $i < count($collections); $i++) {
                array_push($sequenceArr, $collections[$i]['sequence']);
            }
        }
        natsort($sequenceArr);
        $lastSequence = end($sequenceArr);

        return $lastSequence;
    }

}
