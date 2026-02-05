<?php

use App\Http\Controllers\Google\CalendarWatchController;
use App\Http\Controllers\Google\CalendarWebhookController;
use App\Http\Controllers\Google\GoogleOAuthController;
use App\Http\Controllers\TinkoffWebhookController;
use App\Http\Controllers\WebhookController;
use DigitalStars\Sheets\DSheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhook/{botName}', WebhookController::class);
Route::get('/sendTestMesaage', function () {
    Telegram::bot('eravrsmm')->sendMessage([
        'chat_id' => '-1002402724986',
        'message_thread_id' => '3',
        'text' => 'Тестовое сообщение!'
    ]);
});
Route::post('/tinkoff/webhook', [TinkoffWebhookController::class, 'handle']);

///
Route::get('/eravr', function (Request $request) {
    $club = $request->query('club');
    $config = Storage::path('google-calendar/calendar-419415-3bb51b0d7788.json');
    print_r($config);
    $spreadsheet_id = '10EyxVi9MHMwTpQS6oW21D66FEJcnqE6tsaAAqaz0WYk';
    $sheet = DSheets::create($spreadsheet_id, '../storage/app/google-calendar/calendar-419415-3bb51b0d7788.json')->setSheet('Лист1');
    $sheet->append([[$club, date("d.m.Y H:i")]]);

});

Route::get('/google/oauth', [GoogleOauthController::class, 'redirectGoogle']);
Route::get('/google/oauth/callback', [GoogleOauthController::class, 'callbackGoogle'])->name('google.oauth.callback');
Route::post('/google/calendar/watch', [CalendarWatchController::class, 'init'])->name('google.calendar.init');
Route::post('/google/calendar/webhook', [CalendarWebhookController::class, 'handle'])->name('google.calendar.webhook');
//require __DIR__.'/auth.php';
