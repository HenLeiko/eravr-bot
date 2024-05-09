<?php

namespace App\Telegram\Models;

use App\Models\Certificate;
use App\Models\TelegramUser;
use DantSu\PHPImageEditor\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\BotsManager;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class CreateCertModel
{
    private ?Message $update;
    private null|TelegramUser|Model $user;
    private null|Certificate|Model $certificate;

    public function __construct()
    {
        $this->update = Telegram::getWebhookUpdate()->message;
        $this->user = TelegramUser::where('user_id', '=', $this->update->from->id)->first();
        $this->certificate = Certificate::where('user_id', '=', $this->user->id)->latest()->first();
    }

    /**
     * Дабовление наминала сертификата в базу
     *
     * @return void
     */
    public function setValue(): void
    {
        $certificate = new Certificate();
        $certificate->value = $this->update->text;
        $certificate->user_id = $this->user->id;
        $certificate->save();
        Telegram::sendMessage([
            'chat_id' => $this->update->chat->id,
            'text' => 'Укажите номер сертификата, пример: "ПАВЕЛ210524-1'
        ]);
        $this->user->status = 'set_cert_code';
        $this->user->save();
    }

    /**
     * Внесение кода сертификата в базу, вызов функции создания картинки и отправа ответа пользователю
     *
     * @return void
     */
    public function setCode(): void
    {
        $this->certificate->code = $this->update->text;
        $this->certificate->save();
        $imageName = $this->makeImage();
        $keyboard = [
            ['Создать сертификат'],
            ['Создать абонемент'],
            ['Создать приглашение'],
            ['Подсчёт созданых записей админами'],
        ];
        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        Telegram::sendMessage([
            'chat_id' => $this->update->chat->id,
            'text' => 'Сертификат успешно создан! :)',
            'reply_markup' => $reply_markup
        ]);
        Telegram::sendDocument([
            'chat_id' => $this->update->chat->id,
            'document' => InputFile::create(__DIR__ . '/../storage/' . $imageName . '.png')
        ]);
        $this->user->status = 'none';
        $this->user->save();
    }

    /**
     * Создания картинки сертификата
     *
     * @return string
     */
    private function makeImage(): string
    {
        $imageName = uniqid();
        $cert = Certificate::where('user_id', '=', $this->user->id)->latest()->first();
        $code = mb_strtoupper($cert->code);
        Image::fromPath(__DIR__ . '/../resources/Сертификат.png')
            ->writeText($cert->value . ' ₽', __DIR__ . '/../resources/Montserrat-Regular.ttf', 70, '#FFFFFF', '745', '238')
            ->writeText($code, __DIR__ . '/../resources/Montserrat-Regular.ttf', 18, '000000', '870', '960', Image::ALIGN_CENTER, Image::ALIGN_MIDDLE, 0)
            ->savePNG(__DIR__ . '/../storage/' . $imageName . '.png');
        return $imageName;
    }

    /**
     * Сообщение об ошибке в запросе от пользователя
     *
     * @return Message
     */
    public function getException(): Message
    {
        return Telegram::sendMessage([
            'chat_id' => $this->update->chat->id,
            'text' => "\U0000274c Введёное значение должно быть числом без каких-либо символов!"
        ]);
    }
}
