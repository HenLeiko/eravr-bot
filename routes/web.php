<?php

use App\Http\Controllers\WebhookController;
use DigitalStars\Sheets\DSheets;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::post('/webhook', WebhookController::class);


///
Route::get('/eravr', function (Request $request) {
    $club = $request->query('club');
    $config = Storage::path('google-calendar/calendar-419415-3bb51b0d7788.json');
    print_r($config);
    $spreadsheet_id = '10EyxVi9MHMwTpQS6oW21D66FEJcnqE6tsaAAqaz0WYk';
    $sheet = DSheets::create($spreadsheet_id, '../storage/app/google-calendar/calendar-419415-3bb51b0d7788.json')->setSheet('Лист1');
    $sheet->append([[$club, date("d.m.Y H:i")]]);
    
});

//require __DIR__.'/auth.php';
