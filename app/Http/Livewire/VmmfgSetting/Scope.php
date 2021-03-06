<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Exports\VmmfgScopeExcel;
use App\Models\Attachment;
use App\Models\VmmfgItem;
use App\Models\VmmfgScope;
use App\Models\VmmfgTitle;
use App\Models\VmmfgTitleCategory;
use Carbon\Carbon;
use DB;
use Excel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;

class Scope extends Component
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
    public VmmfgScope $scope;
    public VmmfgTitle $title;
    public VmmfgItem $item;
    public $file;
    public $vmmfgTitleCategories;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function rules()
    {
        return [
            'scope.name' => 'required',
            'scope.remarks' => 'sometimes',
            'title.sequence' => 'required',
            'title.name' => 'required',
            'title.vmmfg_title_category_id' => 'sometimes',
            'item.sequence' => 'required',
            'item.name' => 'required',
            'item.remarks' => 'sometimes',
            'item.is_required_upload' => 'sometimes',
            'item.is_required' => 'sometimes',
            'item.flag_id' => 'sometimes',
        ];
    }

    public function mount()
    {
        $this->vmmfgTitleCategories = VmmfgTitleCategory::oldest()->get();
        $this->item = new VmmfgItem();
    }


    public function render()
    {
        $scopes = VmmfgScope::with(['vmmfgTitles', 'vmmfgTitles.VmmfgItems']);

        // advance search
        $scopes = $scopes
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['remarks'], fn($query, $input) => $query->searchLike('remarks', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('name', $input)->orSearchLike('remarks', $input));
                // ->when($this->filters['status'], fn($query, $input) => $query->where('status', 'LIKE', '%'.$input.'%'));

        if($sortKey = $this->sortKey) {
            $scopes = $scopes->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }else {
            $scopes = $scopes->orderBy('remarks', 'desc');
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

    public function edit(VmmfgScope $scope)
    {
        $this->scope = $scope;
    }

    public function create()
    {
        $this->scope = new VmmfgScope;
    }

    public function save()
    {
        $this->validate([
            'scope.name' => 'required',
            'scope.remarks' => 'sometimes',
        ]);
        $this->scope->save();
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }

    public function delete()
    {
        // dd($this->form->toArray());
        if($this->scope){
            foreach($this->scope->vmmfgTitles as $title) {
                if($title) {
                    foreach($title->vmmfgItems as $item) {
                        if($item->attachments()->exists()) {
                            foreach($item->attachments as $attachment) {
                                $this->deleteAttachment($attachment);

                                // if(Attachment::where('full_url', $attachment->full_url)->count() === 1) {
                                //     Storage::disk('digitaloceanspaces')->delete($attachment->url);
                                // }
                                // $attachment->delete();
                            }
                        }
                        $item->delete();
                    }
                }
                $title->delete();
            }
        }
        $this->scope->delete();
        $this->emit('refresh');
        $this->emit('updated');
        session()->flash('success', 'Your entry has been removed');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function createTitle(VmmfgScope $scope)
    {
        $this->scope = $scope;
        $this->title = new VmmfgTitle;
    }

    public function editTitle(VmmfgTitle $title)
    {
        $this->title = $title;
        $this->titleForm = $title;
    }

    public function saveTitle()
    {
        if(! $this->title->vmmfg_title_category_id) {
            $this->title->vmmfg_title_category_id = null;
        }

        if($this->title and $this->title->id) {
            $this->title->update([
                'sequence' => $this->title->sequence,
                'name' => $this->title->name,
                'vmmfg_title_category_id' => $this->title->vmmfg_title_category_id,
            ]);
        }else {
            // dd($this->title->toArray());
            VmmfgTitle::create([
                'sequence' => $this->title->sequence,
                'name' => $this->title->name,
                'vmmfg_scope_id' => $this->scope->id,
                'vmmfg_title_category_id' => $this->title->vmmfg_title_category_id,
            ]);
        }

        // $this->reset('title');
        // $this->showCreateTitleArea = false;
        // $this->emit('updated');
        $this->emit('refresh');
        session()->flash('success', 'Entry has been created');
    }

    public function deleteTitle()
    {
        // if($this->title->vmmfgItems()->exists()) {
        //     foreach($this->title->vmmfgItems() as $item) {
        //         // $item
        //     }
        // }

        $this->title->vmmfgItems()->delete();
        $this->title->delete();
        // $this->title = new VmmfgTitle;
        // $this->item = new VmmfgItem;
        $this->emit('updated');
        session()->flash('success', 'Your entry has been removed');
        return redirect(request()->header('Referer'));
    }

    // public function showCreateTask($id)
    // {
    //     $this->title =
    //     if($this->showCreateTaskArea === $id) {
    //         $this->showCreateTaskArea = '';
    //     }else{
    //         $this->showCreateTaskArea = $id;
    //     }
    // }

    public function createTask(VmmfgTitle $title)
    {
        $this->title = $title;
        $this->item = new VmmfgItem;
    }

    public function editTask(VmmfgItem $item)
    {
        $this->item = $item;
        $this->taskForm = $item;
    }

    public function saveTask()
    {
        if($this->item and $this->item->id) {
            $this->item->update([
                'sequence' => $this->item->sequence,
                'name' => $this->item->name,
                'remarks' => $this->item->remarks,
                'flag_id' => $this->item->flag_id ? $this->item->flag_id : null,
            ]);
        }else {
            VmmfgItem::create([
                'sequence' => $this->item->sequence,
                'name' => $this->item->name,
                'remarks' => $this->item->remarks,
                'vmmfg_title_id' => $this->title->id,
                'flag_id' => $this->item->flag_id ? $this->item->flag_id : null,
            ]);
        }
        // $this->emit('updated');
        $this->emit('refresh');
        session()->flash('success', 'Your entry has been updated');
        // $this->reset('item');
        // $this->item = $this->item->fresh();
        // $this->emit('updated');
        // session()->flash('success', 'Entry has been created');
    }

    public function deleteTask()
    {
        if($this->item->attachments){
            foreach($this->item->attachments as $attachment) {
                $this->deleteAttachment($attachment);

                // $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
                // if($deleteFile){
                //     $attachment->delete();
                // }
            }
        }
        $this->item->vmmfgTasks()->delete();
        $this->item->delete();
        $this->emit('updated');
        session()->flash('success', 'Entry has been removed');
        return redirect(request()->header('Referer'));
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'sometimes',
        ]);

        $url = $this->file->storePublicly('items', 'digitaloceanspaces');
        $fullUrl = Storage::url($url);
        $this->item->attachments()->create([
            'url' => $url,
            'full_url' => $fullUrl,
        ]);
        $this->emit('refresh');
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

    public function replicateScope(VmmfgScope $scope)
    {
        $replicatedScope = $scope->replicate()->fill([
            'name' => $scope->name.'-replicated'
        ]);
        $replicatedScope->save();

        if($scope->vmmfgTitles()->exists()) {
            foreach($scope->vmmfgTitles as $title) {
                $replicatedTitle = '';
                $replicatedTitle = $title->replicate()->fill([
                    'vmmfg_scope_id' => $replicatedScope->id,
                ]);
                $replicatedTitle->save();

                if($title->vmmfgItems()->exists()) {
                    foreach($title->vmmfgItems as $item) {
                        $replicatedItem = '';
                        $replicatedItem = $item->replicate()->fill([
                            'vmmfg_title_id' => $replicatedTitle->id,
                        ]);
                        $replicatedItem->save();

                        if($item->attachments()->exists()) {
                            foreach($item->attachments as $attachment) {
                                $replicatedAttachment = '';
                                $replicatedAttachment = $attachment->replicate()->fill([
                                    'modelable_id' => $replicatedItem->id
                                ]);
                                $replicatedAttachment->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function exportExcel(VmmfgScope $scope)
    {
        return Excel::download(new VmmfgScopeExcel($scope), 'scope_'.Carbon::now()->format('ymdHis').'.xlsx');
        // dd('here');
    }
}
