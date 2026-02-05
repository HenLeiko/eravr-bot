<?php

namespace App\Support\Telegram;

class ReplyMarkupBuilder
{
    public static function create(array $options, int $columns = 1): array
    {
        $keyboard = [];
        $row = [];

        foreach ($options as $option) {
            $row[] = ['text' => $option['title']];

            if (count($row) === $columns) {
                $keyboard[] = $row;
                $row = [];
            }
        }

        if ($row) {
            $keyboard[] = $row;
        }

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
        ];
    }
}
