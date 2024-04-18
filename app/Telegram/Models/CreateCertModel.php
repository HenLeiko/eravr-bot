<?php

namespace App\Telegram\Models;

use App\Models\Certificate;
use App\Models\TelegramUser;
use DantSu\PHPImageEditor\Image;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CreateCertModel
{
    private mixed $botsManager;
    private $webhookUpdate;
    private $user;

    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
        $this->webhookUpdate = $this->botsManager->bot()->getWebhookUpdate();
        $this->user = TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->from->id)->first();
    }

    /**
     * @throws TelegramSDKException
     */
    public function setValue(): void
    {
        $certificate = new Certificate();
        $certificate->value = $this->botsManager->bot()->getWebhookUpdate()->message->text;
        $certificate->user_id = $this->user->id;
        $certificate->save();
        $this->botsManager->bot()->sendMessage([
            'chat_id' => $this->webhookUpdate->message->chat->id,
            'text' => 'Укажите номер сертификата, пример: "ПАВЕЛ210524-1'
        ]);
        $this->user->status = 'set_cert_code';
        $this->user->save();
    }

    /**
     * @throws TelegramSDKException
     */
    public function setCode(): void
    {
        $cert = Certificate::where('user_id', '=', $this->user->id)->latest()->first();
        $cert->code = $this->webhookUpdate->message->text;
        $cert->save();
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
        $this->user->status = 'none';
        $this->user->save();
    }

    private function makeImage(): string
    {
        $imageName = uniqid();
        $cert = Certificate::where('user_id', '=', $this->user->id)->latest()->first();
        $code = mb_strtoupper($cert->code);
        print_r($code);
        Image::fromPath(__DIR__ . '/../resources/Сертификат.png')
            ->writeText($cert->value . ' ₽', __DIR__ . '/../resources/Montserrat-Regular.ttf', 70, '#FFFFFF', '745', '238')
            ->writeText($code, __DIR__ . '/../resources/Montserrat-Regular.ttf', 18, '000000', '870', '961', Image::ALIGN_CENTER, Image::ALIGN_MIDDLE, 0)
            ->savePNG(__DIR__ . '/../storage/' . $imageName . '.png');
        return $imageName;
    }

    /**
     * @throws TelegramSDKException
     */
    public function getException()
    {
        $this->botsManager->bot()->sendMessage([
            'chat_id' => $this->webhookUpdate->message->chat->id,
            'text' => 'Введёное значение должно быть числом без каких-либо символов!'
        ]);
    }
}
