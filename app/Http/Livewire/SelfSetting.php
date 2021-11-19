<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class SelfSetting extends Component
{
    public User $form;

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

    public function mount()
    {
        $this->form = auth()->user();
    }

    public function render()
    {
        return view('livewire.self-setting');
    }

    public function save()
    {
        $this->validate();
        $this->form->save();
        $this->emit('updated');
        session()->flash('success', 'Your entry has been updated');
    }
}
