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
    public string $vendor_id = ''; // Shared between both actions
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
        $this->reset(['name', 'mobile', 'email']);
    }

    public function importCsv()
    {
        $this->validate([
            'csv' => 'required|mimes:csv,txt',
            'vendor_id' => 'required', // Ensures a vendor is selected before importing
        ]);

        $path = $this->csv->getRealPath();
        
        // Safer file streaming processing loop
        if (($handle = fopen($path, 'r')) !== false) {
            $index = 0;
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Skip header row
                if ($index == 0) {
                    $index++;
                    continue;
                }

                // Skip completely empty lines or lines missing core data
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }

                Contact::create([
                    'vendor_id' => $this->vendor_id,
                    'name'      => trim($row[0] ?? ''),
                    'mobile'    => trim($row[1] ?? ''),
                    'email'     => trim($row[2] ?? null) ?: null, // Converts empty strings to null
                ]);
                
                $index++;
            }
            fclose($handle);
        }

        session()->flash('success', 'CSV Imported Successfully');
        $this->reset(['csv']);
    }
};
?>

<div style="padding:20px; max-width:700px;">

    <h1>Manage Contacts</h1>

    @if(session()->has('success'))
        <div style="color:green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Global Vendor Selection Container -->
    <div style="margin-bottom:25px; padding:15px; background:#f4f4f4; border-radius:5px;">
        <label style="font-weight:bold;">1. Choose Target Vendor</label><br>
        <select wire:model.live="vendor_id" style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Select Vendor</option>
            @foreach(Vendor::all() as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->business_name }}</option>
            @endforeach
        </select>
        @error('vendor_id') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Form 1: Single Input -->
        <div>
            <h3>Add Single Contact</h3>
            <form wire:submit="save">
                <div style="margin-bottom:15px;">
                    <label>Name</label><br>
                    <input type="text" wire:model="name" style="width:100%;">
                    @error('name') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label>Mobile</label><br>
                    <input type="text" wire:model="mobile" style="width:100%;">
                    @error('mobile') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label>Email</label><br>
                    <input type="email" wire:model="email" style="width:100%;">
                    @error('email') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" style="padding:8px 12px;">Save Contact</button>
            </form>
        </div>

        <!-- Form 2: Bulk Upload -->
        <div style="border-left:1px solid #ccc; padding-left:20px;">
            <h3>Import CSV File</h3>
            <form wire:submit="importCsv">
                <div style="margin-bottom:15px;">
                    <label>Select Document</label><br>
                    <input type="file" wire:model="csv" style="margin-top:5px;">
                    @error('csv') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" style="padding:8px 12px;">
                    <span wire:loading.remove>Upload & Process</span>
                    <span wire:loading>Importing...</span>
                </button>
            </form>
        </div>
    </div>
</div>
