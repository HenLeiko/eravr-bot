<?php

namespace App\Services;

use App\Models\UserState;

class MenuService
{
    protected array $menu;

    public function __construct()
    {
        $this->menu = config('menu');
    }

    public function getCurrentMenu(UserState $userState): array
    {
        $path = $userState->step['current_path'] ?? ['main'];
        $menu = $this->menu;

        foreach ($path as $item) {
            if (!isset($menu[$item]['options'])) break;
            $menu = $menu[$item]['options'] ?? [];
        }

        return $menu;
    }

    public function navigate(UserState $userState, string $section): array
    {
        $currentMenu = $this->getCurrentMenu($userState);

        if (!isset($currentMenu[$section])) {
            return ['err' => 'Пути не существует'];
        }

        $path = $userState->step['current_path'] ?? ['main'];
        $path[] = $section;
        $userState->step = ['current_path' => $path];
        $userState->save();

        return $currentMenu[$section];
    }

    public function getBack(UserState $userState): array
    {
        $path = $userState->step['back_path'] ?? ['main'];

        if (count($path) > 1) array_pop($path);

        $userState->step = ['back_path' => $path];
        $userState->save();

        return $this->getCurrentMenu($userState);
    }

    public function reset(UserState $userState): void
    {
        $userState->step = ['current_path' => ['main']];
        $userState->save();
    }
}
