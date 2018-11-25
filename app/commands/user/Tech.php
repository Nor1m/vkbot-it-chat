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
     * @throws \Exception
     */
    public function run(array $args): void
    {
        if (!$args) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_args'));
            return;
        }

        if (!$user = User::getOrCreate($this->fromUser())) {
            Message::write(
                $this->object()['peer_id'],
                Message::t('error.smf_wrong')
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
     * @throws \Exception
     */
    private function add(User $user, array $args): void
    {
        if (!$args) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_args'));
            return;
        }

        $msg = Message::t('message.user_stack_add');

        foreach ($args as $arg) {
            $tech = TechModel::getByCode($arg);

            if ($tech === null) {
                $proposal = TechProposed::getByCode($arg);
                $msg .= PHP_EOL . Message::t('warning.no_tech', ['{tech}' => $arg]);
                if ($proposal === null) {
                    $user->addStackProposal(TechProposed::create($arg));
                } elseif (!$proposal->closed) {
                    $user->addStackProposal($proposal->id);
                } else {
                    $msg .= " " . Message::t('message.proposed_not_added');
                    continue;
                }

                $msg .= " " . Message::t('message.proposed_added');
                continue;
            }

            $user->addStackItem($tech->id);
            $msg .= PHP_EOL . Message::t('message.user_stack_added', [
                    '{tech}' => $arg,
                ]);
        }

        Message::write($this->object()['peer_id'], $msg);
    }

    /**
     * @param array $args
     * @param User $user
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    private function delete(User $user, array $args): void
    {
        if (count($args) < 1) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_args'));
            return;
        }

        if (is_numeric($args[0])) {
            $user->removeStackItemByOrd($args[0]);
            Message::write($this->object()['peer_id'], Message::t('success.done'));
            return;
        }

        $tech = TechModel::getByCode($args[0]);

        if ($tech === null) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_tech', [
                '{tech}' => $args[0],
            ]));
            return;
        }

        $user->removeStackItem($tech->id);
        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }

    /**
     * @param array $args
     * @param User $user
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    private function move(User $user, array $args): void
    {
        if (count($args) < 2) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_args'));
            return;
        }

        list($tech_arg, $pos) = $args;

        if (is_numeric($tech_arg)) {
            $tech = UserTech::getTechByOrd($user->vk_id, $tech_arg);
        } else {
            $tech = UserTech::getTechByCode($user->vk_id, $tech_arg);
        }

        if ($tech === null) {
            Message::write($this->object()['peer_id'], Message::t('warning.user_stack_no_tech'));
            return;
        }

        Log::dump($tech);

        if ($pos == 'end') {
            UserTech::moveTechEnd($user->vk_id, $tech->id);
            Message::write($this->object()['peer_id'], Message::t('success.done'));
            return;
        }

        if ($pos == 'up') {
            $pos = $tech->ord - 1;
        } elseif ($pos == 'down') {
            $pos = $tech->ord + 1;
        }

        UserTech::moveTech($user->vk_id, $tech->id, (int) $pos);

        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }

    /**
     * @param $args
     * @param $user
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    private function sort(User $user, array $args)
    {
        UserTech::sortTechsAlphabetical($user->vk_id);
        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }
}