<?php

use App\Models\Vendor;
use Livewire\Component;

new class extends Component
{
    public string $business_name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public ?int $vendor_id = null;

    public bool $isEditing = false;
    
    public function save()
    {
        $this->validate([
            'business_name' => 'required|min:3',
            'email' => 'nullable|email',
            'phone' => 'nullable|min:10',
            'address' => 'nullable',
        ]);

        Vendor::create([
            'business_name' => $this->business_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Vendor Created Successfully');

        $this->resetForm();
    }

    public function edit($id)
    {
        $vendor = Vendor::find($id);

        $this->vendor_id = $vendor->id;

        $this->business_name = $vendor->business_name;

        $this->email = $vendor->email;

        $this->phone = $vendor->phone;

        $this->address = $vendor->address;

        $this->isEditing = true;
    }

    public function update()
    {
        $this->validate([
            'business_name' => 'required|min:3',
            'email' => 'nullable|email',
            'phone' => 'nullable|min:10',
            'address' => 'nullable',
        ]);

        $vendor = Vendor::find($this->vendor_id);

        $vendor->update([
            'business_name' => $this->business_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Vendor Updated Successfully');

        $this->resetForm();
    }

    public function delete($id)
    {
        Vendor::find($id)?->delete();

        session()->flash('success', 'Vendor Deleted Successfully');
    }

    public function resetForm()
    {
        $this->reset([
            'vendor_id',
            'business_name',
            'email',
            'phone',
            'address',
            'isEditing',
        ]);
    }
};
?>

<div style="padding: 20px; max-width: 600px;">

    <hr><br>

    <h2>Vendor List</h2>

    @foreach (\App\Models\Vendor::latest()->get() as $vendor)

        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">

            <strong>{{ $vendor->business_name }}</strong><br>

            {{ $vendor->email }}<br>

            {{ $vendor->phone }}
            
                        <br><br>

            <button wire:click="edit({{ $vendor->id }})">
                Edit
            </button>

            <button
                wire:click="delete({{ $vendor->id }})"
                onclick="return confirm('Are you sure?')"
            >
                Delete
            </button>
        </div>

    @endforeach

    <h1>Create Vendor</h1>

    @if (session()->has('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="{{ $isEditing ? 'update' : 'save' }}">

        <div style="margin-bottom: 15px;">

            <label>Business Name</label><br>

            <input
                type="text"
                wire:model="business_name"
                style="width: 100%; padding: 8px;"
            >

            @error('business_name')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom: 15px;">

            <label>Email</label><br>

            <input
                type="email"
                wire:model="email"
                style="width: 100%; padding: 8px;"
            >

            @error('email')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom: 15px;">

            <label>Phone</label><br>

            <input
                type="text"
                wire:model="phone"
                style="width: 100%; padding: 8px;"
            >

            @error('phone')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom: 15px;">

            <label>Address</label><br>

            <textarea
                wire:model="address"
                style="width: 100%; padding: 8px;"
            ></textarea>

        </div>

        <button type="submit">
            {{ $isEditing ? 'Update Vendor' : 'Save Vendor' }}
        </button>

    </form>

</div>