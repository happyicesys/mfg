<div>
    <x-flash></x-flash>
    <div class="modal-dialog modal-lg bg-light" role="document">
        <div class="modal-content">
            <div class="modal-header">
                User Account Setting
            </div>
            <div class="modal-body">
                <x-input type="text" model="form.name">
                    Name
                </x-input>
                <x-input type="text" model="form.username">
                    Username
                </x-input>
                <x-input type="text" model="form.phone_number">
                    Phone Number
                </x-input>
                <x-input type="text" model="form.email">
                    Email
                </x-input>
                <x-input type="password" model="form.password">
                    Password (Overwrite, Leave Blank to use the same)
                </x-input>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success d-none d-sm-block" wire:click.prevent="save">
                    Submit
                </button>
                <button type="submit" class="btn btn-success btn-block d-block d-sm-none" wire:click.prevent="save">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
