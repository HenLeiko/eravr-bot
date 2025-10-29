<?php

namespace App\Services;


use App\Models\UserState;
use DantSu\PHPImageEditor\Image;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CertService
{
    public function start($bot, $userId, $chatId, $message): void
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Для создания сертификата укажите его наминал в формате: "2000"'
        ]);

        $userState = UserState::updateOrCreate([
            'user_id' => $userId,
        ],
            [
                'user_id' => $userId,
                'command' => 'create_cert',
                'state' => 'set_value',
                'data' => [
                    'value' => '',
                    'code' => '',
                ],
            ]
        );
    }

    public function handle($bot, $userId, $chatId, $message): void
    {
        $userState = UserState::where('user_id', $userId)->first();
        $state = $userState->state;

        $mainKeyboard = [
            ['Создать приглашение'],
            ['Создать сертификат'],
        ];
        $reply_markup = Keyboard::make([
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'keyboard' => $mainKeyboard
        ]);

        if (!$userState) {
            return;
        }

        switch ($state) {
            case 'set_value':
                $userState->state = 'set_code';
                $userState->data = array_merge($userState->data ?? [], ['value' => $message->text]);
                $userState->save();
                $this->getResponse($bot, $chatId, 'Укажите код сертификата формата: АНДРЕЙ241224-1');
                break;
            case 'set_code':
                $userState->data = array_merge($userState->data ?? [], ['code' => $message->text]);
                $userState->state = 'done';
                $userState->save();
                $result = $this->createCertPicture($bot, $userState, $chatId);
                $result ? $this->getResponse($bot, $chatId, 'Сертификат успешно создан! :)', $reply_markup) : $this->getResponse($bot, $chatId, 'Произошла ошибка во время отправки сертификата :(');
                break;
            default: return;
        }
    }

    private function getResponse($bot, int $chatId, String $text, $reply_markup = null): void
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

    private function createCertPicture($bot, $userState, $chatId): Message
    {
        $imageName = uniqid();
        $value = $userState->data['value'];
        $code = $userState->data['code'];
        $code = mb_strtoupper($code);
        $path = storage_path('app/telegram/Сертификат.png');
        $font = storage_path('app/telegram/Montserrat-Regular.ttf');
        $savePath = storage_path('app/telegram-temps');
        Image::fromPath($path)
            ->writeText($value . ' ₽',  $font, 70, '#FFFFFF', '745', '238')
            ->writeText($code, $font, 18, '000000', '870', '960', Image::ALIGN_CENTER, Image::ALIGN_MIDDLE, 0)
            ->savePNG($savePath . $imageName . '.png');
        return $bot->sendDocument([
            'chat_id' => $chatId,
            'document' => InputFile::create($savePath . $imageName . '.png'),
        ]);
    }
}
