<?php

namespace App\Http\Livewire\VmmfgSetting;

use App\Models\Attachment;
use App\Models\VmmfgItem;
use App\Models\VmmfgScope;
use App\Models\VmmfgTitle;
use DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;

class Scope extends Component
{
    use WithFileUploads, WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $itemPerPage = 100;
    public $sortKey = 'created_at';
    public $sortAscending = false;
    public $showEditModal = false;
    public $showFilters = false;
    public $showCreateTitleArea = false;
    public $showCreateTaskArea = '';
    public $selected = [];
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

    public function rules()
    {
        return [
            'scope.name' => 'required',
            'scope.remarks' => 'sometimes',
            'title.sequence' => 'numeric|required',
            'title.name' => 'required',
            'item.sequence' => 'numeric|required',
            'item.name' => 'required',
            'item.remarks' => 'sometimes',
            'item.is_required_upload' => 'sometimes',
            'item.is_required' => 'sometimes',
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

    public function edit(VmmfgScope $scope)
    {
        // dd($scope->toArray());
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
                        $item->delete();
                    }
                }
                $title->delete();
            }
        }
        $this->scope->delete();
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
        // dd($this->title);
        if($this->title and $this->title->id) {
            $this->title->update([
                'sequence' => $this->title->sequence,
                'name' => $this->title->name,
            ]);
        }else {
            // dd($this->title->toArray());
            VmmfgTitle::create([
                'sequence' => $this->title->sequence,
                'name' => $this->title->name,
                'vmmfg_scope_id' => $this->scope->id,
            ]);
        }

        // $this->reset('title');
        // $this->showCreateTitleArea = false;
        $this->emit('updated');
        session()->flash('success', 'Entry has been created');
    }

    public function deleteTitle()
    {
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
            ]);
        }else {
            VmmfgItem::create([
                'sequence' => $this->item->sequence,
                'name' => $this->item->name,
                'remarks' => $this->item->remarks,
                'vmmfg_title_id' => $this->title->id,
            ]);
        }
        // $this->reset('item');
        // $this->item = $this->item->fresh();
        // $this->emit('updated');
        // session()->flash('success', 'Entry has been created');
    }

    public function deleteTask()
    {
        if($this->item->attachments){
            foreach($this->item->attachments as $attachment) {
                $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
                if($deleteFile){
                    $attachment->delete();
                }
            }
        }
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
