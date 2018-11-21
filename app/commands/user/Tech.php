<?php

namespace App\commands\user;

use App\base\BaseCommand;
use App\base\Message;
use App\Log;
use App\models\Tech as TechModel;
use App\models\TechProposed;
use App\models\User;
use App\models\UserTech;

class Tech extends BaseCommand
{
    const FLAGS = [
        'delete' => ['-rm', '-del'],
        'move'   => ['-move', '-mov', '-mv'],
        'sort'   => ['-sort'],
        'add'    => ['-add'],
    ];

    /**
     * @param array $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $args): void
    {
        if (!$args) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        if (!$user = User::getOrCreate($this->fromUser())) {
            Message::write(
                $this->object()['peer_id'],
                'Что-то явно пошло не так, не трогайте меня'
            );
            return;
        }

        $first_arg = reset($args);

        foreach (self::FLAGS as $action => $flags) {
            if (in_array($first_arg, $flags)) {
                array_shift($args);
                $this->{$action}($user, $args);
                return;
            }
        }

        $this->add($user, $args);
    }

    /**
     * @param User $user
     * @param array $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function add(User $user, array $args): void
    {
        if (!$args) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        $msg = "Добавление технологий в стак.";

        foreach ($args as $arg) {
            $tech = TechModel::getByCode($arg);

            if ($tech === null) {
                $proposal = TechProposed::getByCode($arg);

                if ($proposal === null) {
                    $user->addStackProposal(TechProposed::create($arg));
                } elseif (!$proposal->closed) {
                    $user->addStackProposal($proposal->id);
                } else {
                    $msg .= PHP_EOL . "$arg - такой технологии нет, и добавлять я её не собираюсь";
                    continue;
                }

                $msg .= PHP_EOL
                    . "$arg - такой технологии нет, но я её запомню."
                    . " Если позже её утвердят, она у тебя появится";
                continue;
            }

            $user->addStackItem($tech->id);
            $msg .= PHP_EOL . "$arg - технология добавлена";
        }

        Message::write($this->object()['peer_id'], $msg);
    }

    /**
     * @param array $args
     * @param User $user
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function delete(User $user, array $args): void
    {
        if (count($args) < 1) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        if (is_numeric($args[0])) {
            $user->removeStackItemByOrd($args[0]);
            Message::write($this->object()['peer_id'], "Сделано");
            return;
        }

        $tech = TechModel::getByCode($args[0]);

        if ($tech === null) {
            Message::write($this->object()['peer_id'], "Такой технологи я не знаю, соре");
            return;
        }

        $user->removeStackItem($tech->id);
        Message::write($this->object()['peer_id'], "Сделано");
    }

    /**
     * @param array $args
     * @param User $user
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function move(User $user, array $args): void
    {
        if (count($args) < 2) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        list($tech_arg, $pos) = $args;

        if (is_numeric($tech_arg)) {
            $tech = UserTech::getTechByOrd($user->vk_id, $tech_arg);
        } else {
            $tech = UserTech::getTechByCode($user->vk_id, $tech_arg);
        }

        if ($tech === null) {
            Message::write($this->object()['peer_id'], "Такой технологии у тебя в стаке нет");
            return;
        }

        Log::dump($tech);

        if ($pos == 'end') {
            UserTech::moveTechEnd($user->vk_id, $tech->id);
            Message::write($this->object()['peer_id'], "Сделано");
            return;
        }

        if ($pos == 'up') {
            $pos = $tech->ord - 1;
        } elseif ($pos == 'down') {
            $pos = $tech->ord + 1;
        }

        UserTech::moveTech($user->vk_id, $tech->id, (int) $pos);

        Message::write($this->object()['peer_id'], "Сделано");
    }

    /**
     * @param $args
     * @param $user
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function sort(User $user, array $args)
    {
        UserTech::sortTechsAlphabetical($user->vk_id);
        Message::write($this->object()['peer_id'], "Сделано");
    }
}