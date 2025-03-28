<?php

namespace App\Services;


use App\Models\UserState;
use DantSu\PHPImageEditor\Image;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CertService
{
    public function start($userId, $chatId, $message): void
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Для начала создания сертификата укажите имя!',
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

    public function handle($userId, $chatId, $message): void
    {
        $userState = UserState::where('user_id', $userId)->first();
        $state = $userState->state;

        if (!$userState) {
            return;
        }

        switch ($state) {
            case 'set_value':
                $userState->state = 'set_code';
                $userState->data = array_merge($userState->data ?? [], ['value' => $message->text]);
                $userState->save();
                $this->getResponse($chatId, 'Укажите код сертификата формата: АНДРЕЙ241224-1');
                break;
            case 'set_code':
                $userState->data = array_merge($userState->data ?? [], ['code' => $message->text]);
                $userState->state = 'done';
                $userState->save();
                $result = $this->createCertPicture($userState, $chatId);
                $result ? $this->getResponse($chatId, 'Сертификат успешно создан! :)') : $this->getResponse($chatId, 'Произошла ошибка во время отправки сертификата :(');
                break;
            default: return;
        }
    }

    private function getResponse(int $chatId, String $text): void
    {
        $result = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text
        ]);
    }

    private function createCertPicture($userState, $chatId): Message
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
        return Telegram::sendDocument([
            'chat_id' => $chatId,
            'document' => InputFile::create($savePath . $imageName . '.png')
        ]);
    }
}
