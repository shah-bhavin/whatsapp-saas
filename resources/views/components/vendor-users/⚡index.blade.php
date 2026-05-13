<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

new class extends Component {

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $vendor_id = '';

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'vendor_id' => 'required',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'vendor_id' => $this->vendor_id,
        ]);

        $user->assignRole('vendor');

        session()->flash('success', 'Vendor User Created');

        $this->reset();
    }

};

?>

<div style="padding:20px; max-width:600px;">

    <h1>Create Vendor User</h1>

    @if(session()->has('success'))

        <div style="color:green;">
            {{ session('success') }}
        </div>

    @endif

    <form wire:submit="save">

        <div style="margin-bottom:15px;">

            <label>Name</label><br>

            <input type="text" wire:model="name">

            @error('name')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom:15px;">

            <label>Email</label><br>

            <input type="email" wire:model="email">

            @error('email')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom:15px;">

            <label>Password</label><br>

            <input type="password" wire:model="password">

            @error('password')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom:15px;">

            <label>Select Vendor</label><br>

            <select wire:model="vendor_id">

                <option value="">Select Vendor</option>

                @foreach(Vendor::all() as $vendor)

                    <option value="{{ $vendor->id }}">
                        {{ $vendor->business_name }}
                    </option>

                @endforeach

            </select>

        </div>

        <button type="submit">
            Save User
        </button>

    </form>

</div>