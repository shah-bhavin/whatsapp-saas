<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Contact;
use App\Models\Vendor;

new class extends Component {

    use WithFileUploads;

    public string $name = '';

    public string $mobile = '';

    public string $email = '';

    public string $vendor_id = '';

    public $csv;

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'mobile' => 'required|min:10',
            'email' => 'nullable|email',
            'vendor_id' => 'required',
        ]);

        Contact::create([
            'vendor_id' => $this->vendor_id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Contact Created');

        $this->reset([
            'name',
            'mobile',
            'email',
        ]);
    }

    public function importCsv()
    {
        $this->validate([
            'csv' => 'required|mimes:csv,txt',
            'vendor_id' => 'required',
        ]);

        $path = $this->csv->getRealPath();

        $rows = array_map('str_getcsv', file($path));

        foreach ($rows as $index => $row) {

            if ($index == 0) {
                continue;
            }

            Contact::create([
                'vendor_id' => $this->vendor_id,
                'name' => $row[0] ?? '',
                'mobile' => $row[1] ?? '',
                'email' => $row[2] ?? '',
            ]);
        }

        session()->flash('success', 'CSV Imported Successfully');
    }

};
?>

<div style="padding:20px; max-width:700px;">

    <h1>Create Contact</h1>

    @if(session()->has('success'))

        <div style="color:green;">
            {{ session('success') }}
        </div>

    @endif

    <form wire:submit="save">

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

        <div style="margin-bottom:15px;">

            <label>Name</label><br>

            <input type="text" wire:model="name">

        </div>

        <div style="margin-bottom:15px;">

            <label>Mobile</label><br>

            <input type="text" wire:model="mobile">

        </div>

        <div style="margin-bottom:15px;">

            <label>Email</label><br>

            <input type="email" wire:model="email">

        </div>

        <button type="submit">
            Save Contact
        </button>

    </form>

    <hr><br>

    <h2>Import CSV Contacts</h2>

    <form wire:submit="importCsv">

        <input type="file" wire:model="csv">

        @error('csv')
            <div style="color:red;">
                {{ $message }}
            </div>
        @enderror

        <br><br>

        <button type="submit">
            Import CSV
        </button>

    </form>

</div>