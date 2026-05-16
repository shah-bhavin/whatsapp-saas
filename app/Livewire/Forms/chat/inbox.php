<?php

use Livewire\Component;

use App\Models\Contact;

use App\Models\Message;

new class extends Component {

    public $contacts = [];

    public $selectedContact = null;

    public $messages = [];

    public $replyMessage = '';

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
        $this->selectedContact =
            Contact::find($contactId);

        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = Message::where(
                'mobile',
                $this->selectedContact->mobile
            )
            ->latest()
            ->get()
            ->reverse();
    }

};

?>

<div style="display:flex; height:80vh;">

    <!-- CONTACT SIDEBAR -->
    <div style="
        width:30%;
        border-right:1px solid #ccc;
        overflow:auto;
        padding:10px;
    ">

        <h2>Contacts</h2>

        @foreach($contacts as $contact)

            <div
                wire:click="
                    selectContact(
                        {{ $contact->id }}
                    )
                "

                style="
                    padding:10px;
                    cursor:pointer;
                    border-bottom:1px solid #eee;
                "
            >

                {{ $contact->name }}

                <br>

                {{ $contact->mobile }}

            </div>

        @endforeach

    </div>

    <!-- CHAT AREA -->
    <div style="
        width:70%;
        padding:20px;
    ">

        @if($selectedContact)

            <h2>
                {{ $selectedContact->name }}
            </h2>

            <div style="
                height:500px;
                overflow:auto;
                border:1px solid #ccc;
                padding:10px;
                margin-bottom:10px;
            ">

                @foreach($messages as $message)

                    <div style="
                        margin-bottom:15px;

                        text-align:
                        {{
                            $message->direction
                            === 'outgoing'
                            ? 'right'
                            : 'left'
                        }};
                    ">

                        <div style="
                            display:inline-block;
                            padding:10px;
                            border-radius:10px;
                            background:
                            {{
                                $message->direction
                                === 'outgoing'
                                ? '#DCF8C6'
                                : '#FFF'
                            }};
                            border:1px solid #ddd;
                        ">

                            {{ $message->message }}

                            <br>

                            <small>
                                {{ $message->created_at }}
                            </small>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <h2>Select Contact</h2>

        @endif

    </div>

</div>