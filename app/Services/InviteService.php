<?php

namespace App\Services;

use App\Models\UserState;
use DantSu\PHPImageEditor\Image;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class InviteService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Чтобы создать приглашение выберите клуб в меню',
        ]);

        $userState = UserState::updateOrCreate(
            [
                'user_id' => $userId,
            ],
            [
                'user_id' => $userId,
                'command' => 'create_invite',
                'state' => 'set_club',
                'data' => [
                    'club' => '',
                    'title' => '',
                    'date' => '',
                ]
            ]
        );
    }

    public function handle($bot, $userId, $chatId, $message)
    {
        $userState = UserState::where('user_id', $userId)->first();
        $state = $userState->state;

        if (!$userState) {
            return;
        }

        switch ($state) {
            case 'set_club':
                $userState->state = 'title';
                $userState->data = array_merge($userState->data ?? [], ['club' => $message->text]);
                $userState->save();
                $this->getResponse($bot, $chatId, 'Введите текст приглашения например: "На одиннадцатилетие Андрея"');
                break;
            case 'title':
                $userState->state = 'date';
                $userState->data = array_merge($userState->data ?? [], ['title' => $message->text]);
                $userState->save();
                $this->getResponse($bot, $chatId, 'Введите дату мероприятие например: "3 марта с 14:00 до 16:00"');
                break;
            case 'date':
                $userState->state = 'done';
                $userState->data = array_merge($userState->data ?? [], ['date' => $message->text]);
                $userState->save();
                $result = $this->createInvitePicture($bot, $chatId, $userState);
                $result ? $this->getResponse($bot, $chatId, 'Приглашение успешно создано! :)') : $this->getResponse($bot, $chatId, 'Произошла ошибка во время отправки приглашения :(');
                break;
            default: return;
        }
    }
    private function getResponse($bot, int $chatId, String $text): void
    {
        $result = $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    private function createInvitePicture($bot, $chatId, $userState): Message
    {
        $imageName = uniqid();
        $club = $userState->data['club'];
        $title = $userState->data['title'];
        $date = $userState->data['date'];
        $path = storage_path('app/telegram/' . $club . '.png');
        $savePath = storage_path('app/telegram-temps');
        if (mb_strlen($date, 'UTF-8') <= 25) {
            $fontSize = 27;
        } else {
            $fontSize = 24;
        }
        Image::fromPath($path)
            ->writeText($title, storage_path('app/telegram/Montserrat-Regular.ttf'), 36, '#FFFFFF', '595', '205')
            ->writeText($date, storage_path('app/telegram/Montserrat-Light.ttf'), $fontSize, '000000', '595', '280', Image::ALIGN_CENTER, Image::ALIGN_MIDDLE, 0, -0.8)
            ->savePNG($savePath . $imageName . '.png');
        return $bot->sendMessage([
            'chat_id' => $chatId,
            'document' => InputFile::create($savePath . $imageName . '.png')
        ]);
    }
}
