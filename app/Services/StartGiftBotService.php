<?php

namespace App\Services;

use App\Models\TelegramChannelMember;
use Telegram\Bot\Keyboard\Keyboard;

class StartGiftBotService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $inlineKeyboard = [
            ['text' => 'Перейти в канал', 'url' => 'https://t.me/era_vr']
        ];
        $refCode = trim(substr($message->text, 7));
        $channelMemberInfo = $this->getChannelMemberInfo($bot, $chatId);
        $referrer = TelegramChannelMember::where('ref_code', $refCode)->first();
        $telegramChannelMember = TelegramChannelMember::createOrFirst(
            ['user_id' => $userId, 'chat_id' => $chatId],
            [
                'chat_id' => $chatId,
                'user_id' => $userId,
                'status' => $channelMemberInfo->status,
                'timeout' => null,
                'is_participating' => false,
            ]
        );
        if ($referrer && $telegramChannelMember->ref_code !== $referrer->ref_code && !empty($referrer->id)) {
            $telegramChannelMember->ref_by = $referrer->id;
            $telegramChannelMember->save();
        }

        if ($channelMemberInfo == 'member' || $telegramChannelMember == 'administrator') {
            $telegramChannelMember->update(['status' => $channelMemberInfo->status]);
            $telegramChannelMember->save();
        }
//        не подписан и есть таймаут
        if ($telegramChannelMember->status == 'left' && !$telegramChannelMember->isTimeout()) {
            $bot->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Вы не подписаны на канал и не участвуете в конкурсе!'
            ]);
            return;
        }
//        подписан и нажал участвовать, нет таймаута
        if ($telegramChannelMember->status == 'member' || $telegramChannelMember->status == 'creator' || $telegramChannelMember->status == 'administrator' && $refCode !== '') {
            if ($telegramChannelMember->ref_code == null) {
                $telegramChannelMember->ref_code = $telegramChannelMember::generateRefCode();
                $telegramChannelMember->save();
            }

            if ($referrer && $referrer->id !== $telegramChannelMember->user_id && $telegramChannelMember->isTimeout()) {
                $telegramChannelMember->ref_by = $referrer->id;
                $telegramChannelMember->save();
            }

            $memberId = $telegramChannelMember->id;
            $callback_data = 'get:referrals:' . $memberId;
            $memberRefCode = $telegramChannelMember->ref_code;

            $text = <<<HTML
👋 <b>Здравствуйте!</b> Поздравляем — <b>вы участвуете в розыгрыше</b>. Среди всех случайным образом будут выбраны три победителя, которые получат призы.

<u>Дополнительная возможность:</u>

Кроме участия в основном розыгрыше, предоставляется шанс — получить подарок гарантированно. Просто делитесь реферальной ссылкой и приглашайте друзей. Вот реферальная ссылка -  <b>начинайте собирать команду!</b>

Также вы сможете следить за статистикой — количеством приглашённых, выполнивших все условия.

<b>Кто считается приглашённым, за которого получаете приз ❓</b>
<u>Чтобы человек засчитался, он должен:</u>
1️⃣ <i>Подписаться на канал</i>
2️⃣ <i>Нажать кнопку «Участвовать»</i>

Только такие участники идут в зачёт и приближают к подаркам.

<b>Призы:</b>

👉 <i>от 8 до 14 человек — бесплатный час в VR (в будний день)</i>
👉 <i>от 15 до 19 человек — 2 часа антикафе на 8 человек + PS + настолки</i>
👉 <i>от 20 и больше человек — абонемент на 10 часов VR</i>

Важно:
⚠️ <b>Если вы отписываетесь от Telegram-канала - автоматически выбываете из розыгрыша. Это означает потерю шанса стать одним из трёх случайно выбранных победителей. Кроме того, если вы будете делиться ссылкой после отписки и люди будут переходить, выполнять все условия, они могут отображаться в статистике, но призы за них не начисляются. Как всё это исправить? Просто повторно подпишитесь на канал и оставайтесь в нём до окончания розыгрыша.</b>

⚠️ <b>Если приглашённый участник выполнил все условия, но затем отписался от канала — он перестаёт считаться вашим рефералом даже после переподписки.</b>
HTML;

            $keyboard = Keyboard::make()
                ->inline()
                ->row([
                    Keyboard::inlineButton([
                        'text' => 'Проверить кол-во рефералов',
                        'callback_data' => $callback_data,
                    ]),
                ]);
//            Если пользователь нажал кнопку в первый раз
            if (!$telegramChannelMember->is_participating) {
                $bot->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $text,
                    'reply_markup' => $keyboard,
                    'parse_mode' => 'HTML'
                ]);
                $telegramChannelMember->update([
                    'status' => $telegramChannelMember->status,
                    'is_participating' => true,
                ]);
                $bot->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Ваша ссылка для приглашения друзей: https://t.me/eravrtest2bot?start=' . $telegramChannelMember->ref_code . ' Не забудьте попросить друга заранее подписаться на канал, а уже после перейти по ссылке!!!',
                ]);
                return;
            }

//            Уже участвует в конкурсе
            if ($telegramChannelMember->is_participating) {
                $bot->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Вы уже участвуете в конкурсе!',
                ]);
            }
        }

//        не подписан и не нажал участвую
        if ($telegramChannelMember->status !== 'member' && !isset($refCode)) {
            $bot->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Вы не подписаны на канал и не нажали кнопку участвовать!',
                'reply_markup' => $inlineKeyboard,
                'parse_mode' => 'HTML'
            ]);
        }

//        не подписан и нет таймаута
        $timeOut = $telegramChannelMember->isTimeout();
        if ($telegramChannelMember->status == 'left' && $telegramChannelMember->isTimeout() && $refCode !== '') {
            $text = <<<HTML
👋 Здравствуйте. Чтобы стать участником розыгрыша, вам необходимо <b>подписаться на наш Telegram-канал</b>. Без подписки участие невозможно, а также реферальная ссылка не выдаётся.

➡️ <i>Подпишитесь, затем снова нажмите кнопку «Участвовать» — вы в игре!</i>

➡️ <i>Если пришли по реферальной ссылке, но не подписались, вы не засчитываетесь человеку, который вас пригласил.</i>
HTML;

            $inlineKeyboard = [
            [
                ['text' => 'Подписаться на канал!', 'url' => 'https://t.me/era_vr?start']
            ]
        ];
            $bot->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
            ]);
        }
    }

    private function getChannelMemberInfo($bot, $userId)
    {
        return $bot->getChatMember([
            'chat_id' => '@testchannelforeravrbot',
            'user_id' => $userId,
        ]);
    }

    private function getReferals()
    {
        print_r('test');
    }
}
