<?php

namespace App\Http\Livewire;

use App\Models\User;
use DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;


class Admin extends Component
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
        'phone_number' => '',
        'email' => '',
        'status' => ''
    ];
    public User $form;
    public $roles;
    public $role_id;

    public function mount()
    {
        $this->roles = Role::whereNotIn('name', ['superadmin'])->orderBy('name')->get();
        $this->form = new User();
    }

    public function rules()
    {
        return [
            'form.name' => 'required',
            'form.username' => 'required|unique:users,username,' . $this->form->id,
            'form.phone_number' => 'numeric',
            'form.email' => 'email',
            'form.password' => 'sometimes',
        ];
    }

    public function render()
    {
        $admins = User::query();

        // advance search
        $admins = $admins
                ->with(['roles' => function($query) {
                    $query->first();
                }])
                ->when($this->filters['name'], fn($query, $input) => $query->searchLike('name', $input))
                ->when($this->filters['phone_number'], fn($query, $input) => $query->searchLike('phone_number', $input))
                ->when($this->filters['email'], fn($query, $input) => $query->searchLike('email', $input))
                ->when($this->filters['search'], fn($query, $input) => $query->searchLike('name', $input)->orSearchLike('phone_number', $input)->orSearchLike('email', $input));
                // ->when($this->filters['status'], fn($query, $input) => $query->where('status', 'LIKE', '%'.$input.'%'));

        if($sortKey = $this->sortKey) {
            $admins = $admins->orderBy($sortKey, $this->sortAscending ? 'asc' : 'desc');
        }

        $admins = $admins->paginate($this->itemPerPage);

        return view('livewire.admin', ['admins' => $admins]);
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

    public function edit(User $admin)
    {
        $this->form = $admin;
        // dd($admin->roles->toArray(), $this->form->role, $this->form->toArray());
        $this->role_id = $admin->roles->first() ? $admin->roles->first()->id : '';

    }

    public function save()
    {
        // dd($this->form->toArray(), $this->role_id);
        $this->validate();
        $this->form->save();
        if($this->role_id) {
            if($this->form->roles->first()) {
                if($this->form->roles->first()->id !== $this->role_id) {
                    $this->form->roles()->detach();
                    $this->form->assignRole($this->role_id);
                }
            }else {
                $this->form->assignRole($this->role_id);
            }

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
}
