<?php

use App\Http\Controllers\MetaEmbeddedSignupController;
use Illuminate\Support\Facades\Route;
use App\Services\WhatsAppTemplateSender;
use App\Models\WhatsAppAccount;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::livewire('/vendors', 'vendors.index')->middleware(['auth', 'role:admin']);
Route::livewire('/vendor-users', 'vendor-users.index')->middleware(['auth', 'role:admin']);
Route::livewire('/contacts', 'contacts.index')->middleware(['auth']);

Route::middleware(['auth', 'vendor.limit'])->group(function () {
    Route::livewire('/campaigns', 'campaigns.index')->middleware(['auth']);
    Route::livewire('/chat', 'chat.inbox')->middleware('auth');
});

Route::middleware(['auth'])->group(function () {
    Route::livewire('/connect-whatsapp', 'settings.connect-whatsapp');
});
Route::post('/meta/exchange-token', [MetaEmbeddedSignupController::class, 'exchangeToken'])->middleware('auth');
Route::get('/meta/callback', function () { return 'Meta Callback Working';} )->middleware('auth');
Route::post('/meta/save-account', [MetaEmbeddedSignupController::class, 'saveAccount'])->middleware('auth');
Route::livewire('/whatsapp-accounts', 'whatsapp.accounts')->middleware('auth');
Route::livewire('/whatsapp/templates', 'whatsapp.templates')->middleware('auth');



Route::get('/test-template-send', function () {
    $account = WhatsAppAccount::first();
    $sender = new WhatsAppTemplateSender();

    $response = $sender->send(
        account: $account,
        to: '917405217574',
        templateName: 'sale_offer',
        variables: [
            'Bhavin',
        ],
        mediaUrl: 'https://example.com/invoice.pdf',
        mediaType: 'document'
    );

    dd($response);
});
