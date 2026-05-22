<?php

use Livewire\Component;
use App\Models\WhatsAppAccount;

new class extends Component
{
    public $accounts = [];

    public function mount()
    {
        $this->loadAccounts();
    }

    public function loadAccounts()
    {
        $this->accounts = WhatsAppAccount::where('vendor_id', auth()->user()->vendor_id)
            ->latest()
            ->get();
    }

    public function setDefault($id)
    {
        WhatsAppAccount::where('vendor_id', auth()->user()->vendor_id)->update(['is_default' => false]);
        WhatsAppAccount::find($id)->update(['is_default' => true]);

        $this->loadAccounts();
    }

    public function deleteAccount($id)
    {
        WhatsAppAccount::find($id)->delete();
        $this->loadAccounts();
    }
};
?>

<div class="p-6">
    <div class="flex justify-between mb-5">
        <h1 class="text-2xl font-bold">WhatsApp Accounts</h1>
        <a href="/connect-whatsapp" class="bg-green-600 text-white px-4 py-2 rounded">Connect Account</a>
    </div>

    <div class="bg-white rounded shadow">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Number</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Default</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr class="border-t">
                        <td class="p-3">{{ $account->name }}</td>
                        <td class="p-3">{{ $account->phone_number }}</td>
                        <td class="p-3">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">
                                {{ $account->status ?? 'Connected' }}
                            </span>
                        </td>
                        <td class="p-3">
                            @if($account->is_default)
                                <span class="text-green-600 font-bold">YES</span>
                            @else
                                <button wire:click="setDefault({{ $account->id }})" class="text-blue-600">Make Default</button>
                            @endif
                        </td>
                        <td class="p-3">
                            <button wire:click="deleteAccount({{ $account->id }})" class="text-red-600">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-5 text-center text-gray-500">No WhatsApp Accounts Connected</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
