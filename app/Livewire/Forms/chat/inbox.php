<?php

use Livewire\Component;
use App\Models\Contact;
use App\Models\Message;

new class extends Component {
    public $contacts = [];
    public $selectedContact = null;
    public $messages = [];
    public $replyMessage = '';
    public $noteText = '';

    public function mount()
    {
        $this->loadContacts();
    }

    public function loadContacts()
    {
        $this->contacts = Contact::latest()->get();
    }

    public function selectContact($contactId)
    {
        $this->selectedContact = Contact::find($contactId);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = Message::where('mobile', $this->selectedContact->mobile)
            ->latest()
            ->get()
            ->reverse();
    }

    public function saveNote()
    {
        \App\Models\ContactNote::create([
            'contact_id' => $this->selectedContact->id,
            'user_id' => auth()->id(),
            'note' => $this->noteText
        ]);

        $this->noteText = '';

        $this->selectedContact->refresh();
    }

}; 
?>

<div style="display:flex; height:80vh;">
    <!-- CONTACT SIDEBAR -->
    <div style="width:25%; border-right:1px solid #ccc; overflow:auto; padding:10px;">
        <h2>Contacts</h2>
        @foreach($contacts as $contact)
            <div wire:click="selectContact({{ $contact->id }})" style="padding:10px; cursor:pointer; border-bottom:1px solid #eee;">
                {{ $contact->name }}
                <br>
                {{ $contact->mobile }}
            </div>
        @endforeach
    </div>

    <!-- CHAT AREA -->
    <div style="width:50%; padding:20px;">
        @if($selectedContact)
            <h2>{{ $selectedContact->name }}</h2>
            <div style="height:500px; overflow:auto; border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                @foreach($messages as $message)
                    <div style="margin-bottom:15px; text-align: {{ $message->direction === 'outgoing' ? 'right' : 'left' }};">
                        <div style="display:inline-block; padding:10px; border-radius:10px; background: {{ $message->direction === 'outgoing' ? '#DCF8C6' : '#FFF' }}; border:1px solid #ddd;">
                            {{ $message->message }}
                            <br>
                            <small>{{ $message->created_at }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h2>Select Contact</h2>
        @endif
    </div>

    <!-- CRM SIDEBAR -->
    <div style="width:25%; border-left:1px solid #ccc; padding:15px;">
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
            <h4>Notes</h4>
            @foreach($selectedContact->notes as $note)
                <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px;">
                    {{ $note->note }}
                    <br>
                    <small>{{ $note->user->name }}</small>
                </div>
            @endforeach


            <form wire:submit="saveNote">

                <textarea
                    wire:model="noteText"
                    placeholder="Internal note"
                    style="width:100%; height:100px;"
                ></textarea>

                <button type="submit">
                    Save Note
                </button>

            </form>

        @endif
    </div>
</div>
