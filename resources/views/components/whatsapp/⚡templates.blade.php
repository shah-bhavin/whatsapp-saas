<?php

use Livewire\Component;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppAccount;
use App\Services\MetaTemplateService;

new class extends Component
{
    /*
    FORM
    */
    public $template_name = '';
    public $category = 'MARKETING';
    public $language = 'en';
    public $header_text = '';
    public $body_text = '';
    public $footer_text = '';
    public $has_media = false;
    public $media_type = 'IMAGE';
    public $whats_app_account_id = '';

    /*
    DATA
    */
    public $templates = [];
    public $accounts = [];

    /*
    LOAD
    */
    public function mount()
    {
        $this->loadTemplates();
        $this->accounts = WhatsAppAccount::where('vendor_id', auth()->user()->vendor_id)->get();
    }

    /*
    LOAD TEMPLATES
    */
    public function loadTemplates()
    {
        $this->templates = WhatsAppTemplate::where('vendor_id', auth()->user()->vendor_id)
            ->latest()
            ->get();
    }

    /*
    CREATE TEMPLATE
    */
    public function createTemplate()
    {
        $this->validate([
            'template_name'        => 'required',
            'body_text'            => 'required',
            'whats_app_account_id' => 'required',
        ]);

        WhatsAppTemplate::create([
            'vendor_id'            => auth()->user()->vendor_id,
            'whats_app_account_id' => $this->whats_app_account_id,
            'template_name'        => strtolower(str_replace(' ', '_', $this->template_name)),
            'category'             => $this->category,
            'language'             => $this->language,
            'header_text'          => $this->header_text,
            'body_text'            => $this->body_text,
            'footer_text'          => $this->footer_text,
            'has_media'            => $this->has_media,
            'media_type'           => $this->media_type,
            'status'               => 'DRAFT',
        ]);

        $this->reset([
            'template_name',
            'header_text',
            'body_text',
            'footer_text',
        ]);

        $this->loadTemplates();

        session()->flash('success', 'Template Created');
    }

    public function submitToMeta($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        $service = new MetaTemplateService();
        $result = $service->submit($template);

        if ($result['success']) {
            session()->flash('success', 'Template Submitted To Meta');
        } else {
            session()->flash('error', json_encode($result['data']));
        }

        $this->loadTemplates();
    }

};
?>

<div class="p-6">
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-5">WhatsApp Templates</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- CREATE FORM -->
    <div class="bg-white shadow rounded p-5 mb-6">
        <h2 class="text-lg font-bold mb-4">Create Template</h2>

        <form wire:submit="createTemplate">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Template Name</label>
                    <input type="text" wire:model="template_name" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block mb-1">WhatsApp Account</label>
                    <select wire:model="whats_app_account_id" class="w-full border rounded p-2">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->phone_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Category</label>
                    <select wire:model="category" class="w-full border rounded p-2">
                        <option value="MARKETING">MARKETING</option>
                        <option value="UTILITY">UTILITY</option>
                        <option value="AUTHENTICATION">AUTHENTICATION</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Language</label>
                    <input type="text" wire:model="language" class="w-full border rounded p-2">
                </div>
            </div>

            <div class="mt-4">
                <label class="block mb-1">Header Text</label>
                <input type="text" wire:model="header_text" class="w-full border rounded p-2">
            </div>

            <div class="mt-4">
                <label class="block mb-1">Body Text</label>
                <textarea wire:model="body_text" class="w-full border rounded p-2" rows="5"></textarea>
            </div>

            <div class="mt-4">
                <label class="block mb-1">Footer Text</label>
                <input type="text" wire:model="footer_text" class="w-full border rounded p-2">
            </div>

            <div class="mt-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="has_media">
                    Has Media
                </label>
            </div>

            @if($has_media)
                <div class="mt-4">
                    <label class="block mb-1">Media Type</label>
                    <select wire:model="media_type" class="w-full border rounded p-2">
                        <option value="IMAGE">IMAGE</option>
                        <option value="VIDEO">VIDEO</option>
                        <option value="DOCUMENT">DOCUMENT</option>
                    </select>
                </div>
            @endif

            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded mt-5">
                Create Template
            </button>
        </form>
    </div>

    <!-- TEMPLATE LIST -->
    <div class="bg-white shadow rounded">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Media</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                    <tr class="border-b">
                        <td class="p-3">{{ $template->template_name }}</td>
                        <td class="p-3">{{ $template->category }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $template->status === 'APPROVED' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $template->status }}
                            </span>
                        </td>
                        <td class="p-3">{{ $template->has_media ? $template->media_type : 'None' }}</td>
                        <td class="p-3">
                            @if($template->status == 'DRAFT')
                                <button 
                                    wire:click="submitToMeta({{ $template->id }})" 
                                    wire:loading.attr="disabled" 
                                    class="bg-blue-600 text-white px-3 py-1 rounded disabled:opacity-50"
                                >
                                    Submit
                                </button>
                            @else
                                <span class="text-gray-500">Submitted</span>
                            @endif
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
