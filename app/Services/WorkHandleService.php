<?php

namespace App\Services;

use App\Models\UserState;
use App\Models\Work_times;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;

class WorkHandleService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $user = UserState::where('user_id', '=', $userId)->first();
        if (! $user->accessLevel || $user->accessLevel->access_index === 0) {
            return;
        }
        if (Str::lower($message->text) == "открыть смену") {
            $workShift = Work_times::where('user_id', $user->id)->whereNull('check_out')->first();
            if ($workShift) {
                print_r('Смена уже открыта');
                return;
            }
            Work_times::create([
                'user_id' => $user->id,
                'club_id' => 1,
                'check_in' => now(),
            ]);
            print_r('Смена открылась!');
        }
    }

    public function handle()
    {

    }
}
