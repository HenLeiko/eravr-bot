<?php

namespace App\Telegram\Models;

use App\Models\invitations;
use App\Models\TelegramUser;
use DantSu\PHPImageEditor\Image;
use Illuminate\Database\Eloquent\Builder;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class CreateInviteModel
{
    protected BotsManager $botsManager;
    protected Builder|TelegramUser $user;
    private Update $webhookUpdate;

    /**
     * @throws TelegramSDKException
     */
    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
        $this->webhookUpdate = $this->botsManager->bot()->getWebhookUpdate();
        $this->user = TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->from->id)->first();
    }

    /**
     * @throws TelegramSDKException
     */
    public function selectClub(): void
    {
        $invite = new invitations();
        $invite->user_id = $this->user->id;
        $invite->club = $this->webhookUpdate->message->text;
        $invite->save();
        $this->user->status = 'add_invite_title';
        $this->user->save();
        $this->botsManager->bot()->sendMessage([
            'chat_id' => $this->webhookUpdate->message->chat->id,
            'text' => 'Введите текст приглашения например: "На одиннадцатилетие Андрея"'
        ]);
    }

    /**
     * @throws TelegramSDKException
     */
    public function setTitle(): void
    {
        $invite = invitations::where('user_id', '=', $this->user->id)->latest()->first();
        $invite->title = $this->webhookUpdate->message->text;
        $invite->save();
        $this->user->status = 'add_invite_code';
        $this->user->save();
        $this->botsManager->bot()->sendMessage([
            'chat_id' => $this->webhookUpdate->message->chat->id,
            'text' => 'Введите дату мероприятие например: "3 марта с 14:00 до 16:00"'
        ]);
    }

    /**
     * @throws TelegramSDKException
     */
    public function setCode(): void
    {
        $invite = invitations::where('user_id', '=', $this->user->id)->latest()->first();
        $invite->code = $this->webhookUpdate->message->text;
        $invite->save();
        $imageName = $this->makeImage();
        $keyboard = [
            ['Создать сертификат'],
            ['Создать абонемент'],
            ['Создать приглашение'],
        ];
        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        $this->botsManager->bot()->sendMessage([
            'chat_id' => $this->webhookUpdate->message->chat->id,
            'text' => 'Приглашение успешно создано! :)',
            'reply_markup' => $reply_markup
        ]);
        $this->botsManager->bot()->sendDocument([
            'chat_id' => $this->webhookUpdate->message->chat->id,
            'document' => \Telegram\Bot\FileUpload\InputFile::create(__DIR__ . '/../storage/' . $imageName . '.png',)
        ]);
    }

    private function makeImage(): string
    {
        $imageName = uniqid();
        $invite = invitations::where('user_id', '=', $this->user->id)->latest()->first();
        $test = mb_strlen($invite->code)-3;
        if ($invite->code <= 25) {
            $fontSize = 27;
        } else {
            $fontSize = 24;
        }
        Image::fromPath(__DIR__ . '/../resources/' . $invite->club . '.png')
            ->writeText($invite->title, __DIR__ . '/../resources/Montserrat-Regular.ttf', 36, '#FFFFFF', '595', '205')
            ->writeText($invite->code, __DIR__ . '/../resources/Montserrat-Light.ttf', $fontSize, '000000', '595', '280', Image::ALIGN_CENTER, Image::ALIGN_MIDDLE, 0, -0.8)
            ->savePNG(__DIR__ . '/../storage/' . $imageName . '.png');
        $this->user->status = 'none';
        $this->user->save();
        return $imageName;
    }
}
