<?php

use Livewire\Component;
use App\Models\Campaign;
use App\Models\Vendor;

new class extends Component {

    public string $vendor_id = '';

    public string $title = '';

    public string $message = '';

    public function save()
    {
        $this->validate([
            'vendor_id' => 'required',
            'title' => 'required|min:3',
            'message' => 'required|min:5',
        ]);

        Campaign::create([
            'vendor_id' => $this->vendor_id,
            'title' => $this->title,
            'message' => $this->message,
            'status' => 'draft',
        ]);

        session()->flash(
            'success',
            'Campaign Created Successfully'
        );

        $this->reset([
            'vendor_id',
            'title',
            'message',
        ]);
    }

};
?>

<div style="padding:20px; max-width:700px;">

    <h1>Create Campaign</h1>

    @if(session()->has('success'))

        <div style="color:green;">
            {{ session('success') }}
        </div>

    @endif

    <form wire:submit="save">

        <div style="margin-bottom:15px;">

            <label>Select Vendor</label><br>

            <select wire:model="vendor_id">

                <option value="">
                    Select Vendor
                </option>

                @foreach(Vendor::all() as $vendor)

                    <option value="{{ $vendor->id }}">
                        {{ $vendor->business_name }}
                    </option>

                @endforeach

            </select>

            @error('vendor_id')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom:15px;">

            <label>Campaign Title</label><br>

            <input
                type="text"
                wire:model="title"
                style="width:100%; padding:8px;"
            >

            @error('title')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div style="margin-bottom:15px;">

            <label>Message</label><br>

            <textarea
                wire:model="message"
                rows="6"
                style="width:100%; padding:8px;"
            ></textarea>

            @error('message')
                <div style="color:red;">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <button type="submit">
            Save Campaign
        </button>

    </form>

    <hr><br>

<h2>Campaign List</h2>

    @foreach(Campaign::latest()->get() as $campaign)

        <div style="
            border:1px solid #ccc;
            padding:15px;
            margin-bottom:10px;
        ">

            <strong>
                {{ $campaign->title }}
            </strong>

            <br><br>

            {{ $campaign->message }}

            <br><br>

            Status:
            <strong>
                {{ $campaign->status }}
            </strong>

        </div>

    @endforeach
</div>