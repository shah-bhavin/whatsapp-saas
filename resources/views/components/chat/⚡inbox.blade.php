<?php

use Livewire\Component;
use App\Models\Contact;
use App\Models\Message;
use Livewire\Attributes\On;

new class extends Component {
    public $contacts = [];
    public $selectedContact = null;
    public $messages = [];
    public $replyMessage = '';
    public $noteText = '';

    public $statuses = [

        'new_lead',

        'interested',

        'customer',

        'closed'

    ];

    public $selectedStatus = '';

    public $selectedLabels = [];

    public $allLabels = [];

    #[On('refreshChat')]
    public function refreshChat()
    {
        if ($this->selectedContact) {
            $this->loadMessages();
        }
    }

    public function mount()
    {
        $this->allLabels = \App\Models\Label::all();
        $this->loadContacts();
    }

    public function loadContacts()
    {
        $this->contacts = Contact::query()
            ->when(
                auth()->user()->hasRole('agent'),
                fn($query) => $query->where('assigned_user_id', auth()->id())
            )
            ->latest()
            ->get();
            
    }

    public function selectContact($contactId)
    {
        $this->selectedContact = Contact::find($contactId);
        $this->selectedStatus = $this->selectedContact->status;
        $this->selectedLabels = $this->selectedContact->labels->pluck('id')->toArray();
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = Message::where('mobile', $this->selectedContact->mobile)
            ->latest()
            ->get()
            ->reverse();
    }
    
    public function sendReply()
    {
        $service = new \App\Services\WhatsAppService();
        $response = $service->sendTextMessage(
            $this->selectedContact->mobile,
            $this->replyMessage
        );

        if ($response->successful()) {
            $data = $response->json();
            $whatsappMessageId = $data['messages'][0]['id'] ?? null;
            Message::create([
                'contact_id'          => $this->selectedContact->id,
                'mobile'              => $this->selectedContact->mobile,
                'message'             => $this->replyMessage,
                'direction'           => 'outgoing',
                'status'              => 'sent',
                'whatsapp_message_id' => $whatsappMessageId,
            ]);

            $this->replyMessage = '';
            $this->loadMessages();
        }
    }

    public function saveNote()
    {
        \App\Models\ContactNote::create([
            'contact_id' => $this->selectedContact->id,
            'user_id'    => auth()->id(),
            'note'       => $this->noteText
        ]);

        $this->noteText = '';

        $this->selectedContact->refresh();
    }

public function updateStatus() 
{
    // Safety check to ensure a real status is selected
    if (empty($this->selectedStatus)) {
        return;
    }

    $this->selectedContact->update([
        'status' => $this->selectedStatus
    ]);

    $this->selectedContact->refresh();
}


public function updateLabels()
{
    $this->selectedContact
        ->labels()
        ->sync(
            $this->selectedLabels
        );

    $this->selectedContact->refresh();
}
};

?>

<div style="display: flex; height: 80vh; max-height: 80vh; gap: 10px; font-family: sans-serif;" id="chat-wrapper">
    <!-- CONTACT SIDEBAR -->
    <div style="width: 25%; border-right: 1px solid #ccc; overflow-y: auto; padding: 10px;">
        <h2 style="margin: 0 0 10px 0; font-size: 1.2rem;">Contacts</h2>
        @foreach($contacts as $contact)
            <div wire:click="selectContact({{ $contact->id }})" style="padding: 6px 8px; cursor: pointer; border-bottom: 1px solid #eee;">
                <strong style="display: block; font-size: 0.9rem;">{{ $contact->name }}</strong>
                <span style="color: #666; font-size: 0.8rem;">{{ $contact->mobile }}</span>
            </div>
        @endforeach
    </div>

    <!-- CHAT AREA -->
    <div style="width: 50%; padding: 10px; display: flex; flex-direction: column;">
        @if($selectedContact)
            <h2 style="margin: 0 0 10px 0; font-size: 1.2rem;">{{ $selectedContact->name }}</h2>
            <div style="flex-grow: 1; height: 0; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 4px; background: #f9f9f9;">
                @foreach($messages as $message)
                    <div style="margin-bottom: 8px; text-align: {{ $message->direction === 'outgoing' ? 'right' : 'left' }};">
                        <div style="display: inline-block; padding: 6px 10px; border-radius: 6px; background: {{ $message->direction === 'outgoing' ? '#DCF8C6' : '#FFF' }}; border: 1px solid #ddd; max-width: 75%; text-align: left;">
                            <p style="margin: 0; font-size: 0.85rem; word-break: break-word;">{{ $message->message }}</p>
                            <small style="display: block; color: #888; font-size: 0.7rem; margin-top: 2px; text-align: right;">
                                {{ $message->created_at }}
                            </small>
                            @if($message->direction === 'outgoing')
                                <div>
                                    @if($message->status === 'sent')
                                        ✓
                                    @elseif($message->status === 'delivered')
                                        ✓✓
                                    @elseif($message->status === 'read')
                                        ✓✓ Read
                                    @elseif($message->status === 'failed')
                                        Failed
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- COMPACT INPUT FORM -->
            <form wire:submit="sendReply" style="display: flex; gap: 5px; margin-top: 8px; width: 100%; align-items: center; flex-wrap: nowrap;">
                <input type="text" wire:model="replyMessage" placeholder="Type message..." style="flex-grow: 1; padding: 6px 10px; border: 1px solid #ccc; border-radius: 3px; font-size: 0.85rem; outline: none; min-width: 0;">
                <button type="submit" style="padding: 6px 12px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.85rem; white-space: nowrap; flex-shrink: 0;">
                    Send
                </button>
            </form>
        @else
            <div style="margin: auto; text-align: center; color: #888; font-size: 0.9rem;">
                Select a contact to start chatting
            </div>
        @endif
    </div>

    <!-- CRM SIDEBAR -->
    <div style="width: 25%; border-left: 1px solid #ccc; padding: 15px; overflow-y: auto;">
        @if($selectedContact)
            <h3>Customer Info</h3>
            <p><strong>Name:</strong> {{ $selectedContact->name }}</p>
            <p><strong>Mobile:</strong> {{ $selectedContact->mobile }}</p>
            <p><strong>Status:</strong> {{ $selectedContact->status }}</p>
            <p><strong>Follow Up:</strong> {{ $selectedContact->follow_up_at }}</p>
            <hr>
            <h4>Labels</h4>
            @foreach($selectedContact->labels as $label)
                <span style="background: {{ $label->color }}; color:#fff; padding:5px 10px; border-radius:10px; display:inline-block; margin-bottom:5px;">
                    {{ $label->name }}
                </span>
            @endforeach
            <hr>
            <hr>

<h4>Status</h4>

<select wire:model.live="selectedStatus" wire:change="updateStatus" style="width: 100%; padding: 10px;">
    <option value="">Select Status</option>
    @foreach($statuses as $status)
        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
    @endforeach
</select>

<hr>

<h4>Labels</h4>

@foreach($allLabels as $label)

    <label style="
        display:block;
        margin-bottom:10px;
    ">

        <input
            type="checkbox"

            value="{{ $label->id }}"

            wire:model="selectedLabels"

            wire:change="updateLabels"
        >

        {{ $label->name }}

    </label>

@endforeach


            <h4>Notes</h4>
            @foreach($selectedContact->notes as $note)
                <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px;">
                    {{ $note->note }}
                    <br>
                    <small>{{ $note->user->name }}</small>
                </div>
            @endforeach

            <form wire:submit="saveNote">
                <textarea wire:model="noteText" placeholder="Internal note" style="width:100%; height:100px; margin-bottom: 5px;"></textarea>
                <button type="submit">
                    Save Note
                </button>
            </form>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Echo.channel('whatsapp-chat')
            .listen('NewWhatsAppMessageReceived', (event) => {
                console.log('New Message', event);
                Livewire.dispatch('refreshChat');
            }).listen('WhatsAppMessageStatusUpdated', (event) => {
                console.log('Status Update', event);
                Livewire.dispatch('refreshChat');
            });
        });
    </script>
</div>
