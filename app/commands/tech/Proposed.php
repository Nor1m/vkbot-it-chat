<?php
/** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\commands\tech;

use App\base\BaseCommand;
use App\base\Message;
use App\base\Protect;
use App\models\TechProposed;

/**
 * Команда для работы с предложенными технологиями
 * @package App\commands\tech
 */
class Proposed extends BaseCommand
{
    const FLAGS = [
        'apply'   => ['-apply'],
        'deny'    => ['-deny'],
        'denied'  => ['-denied'],
        'applied' => ['-applied'],
        'closed'  => ['-closed'],
        'del'     => ['-del', '-rm'],
    ];

    /**
     * @param array $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $args): void
    {
        $first_arg = array_shift($args);

        foreach (self::FLAGS as $action => $flags) {
            if (in_array($first_arg, $flags)) {
                $this->{$action}($args);
                return;
            }
        }

        $this->list(is_numeric($first_arg) ? $first_arg : 1, false);
    }

    /**
     * @param int $page
     * @param bool $closed
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function list($page = 1, bool $closed = false)
    {
        $proposed_techs = TechProposed::getPaged($closed, $page);

        if (!$proposed_techs) {
            Message::write($this->object()['peer_id'], "Список предложенных технологий пуст");
            return;
        }

        $msg = $closed ? 'Закрытые предложения' : 'Предложенные технологии';

        if (($total = TechProposed::getTotalCount($closed)) > 10) {
            $start_num = 10 * ($page - 1) + 1;
            $end_num = $start_num + count($proposed_techs) - 1;

            $msg .= " ($start_num - $end_num из $total)";
        }

        $c = ($page - 1) * 10;
        foreach ($proposed_techs as $tech) {
            $msg .= PHP_EOL . ++$c . '. ' . $tech->code;
        }

        Message::write($this->object()['peer_id'], $msg);
    }

    /**
     * @param $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function closed($args)
    {
        $first_arg = reset($args);
        $this->list(is_numeric($first_arg) ? $first_arg : 1, true);
    }

    /**
     * @param $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function apply($args)
    {
        Protect::checkIsChatAdmin($this->fromUser(), $this->object()['peer_id']);

        if (count($args) < 1) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        $msg = 'Утверждение предложенных технологий.';

        foreach ($args as $arg) {

            $proposed = TechProposed::getByCode($arg);

            if ($proposed === null || $proposed->closed) {
                $msg .= PHP_EOL . $arg . ' - такую технологию никто не предлагал или предложение уже закрыто';
                continue;
            }

            $proposed->apply();
            $msg .= PHP_EOL . $proposed->code . ' - технология добавлена в основной список. Предложение закрыто';
        }

        Message::write(
            $this->object()['peer_id'],
            $msg
        );
    }

    /**
     * @param $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function deny($args)
    {
        Protect::checkIsChatAdmin($this->fromUser(), $this->object()['peer_id']);

        if (count($args) < 1) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        $msg = 'Утверждение предложенных технологий';

        foreach ($args as $arg) {

            $proposed = TechProposed::getByCode($arg);

            if ($proposed === null || $proposed->closed) {
                $msg .= PHP_EOL . $arg . ' - такую технологию никто не предлагал или предложение уже закрыто';
                continue;
            }

            $proposed->close();
            $msg .= PHP_EOL . $proposed->code . ' - технология отклонена. Предложение закрыто';
        }

        Message::write(
            $this->object()['peer_id'],
            $msg
        );
    }

    /**
     * @param $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function applied($args)
    {
        if (isset($args[0]) && is_numeric($args[0])) {
            $page = $args[0];
        } else {
            $page = 1;
        }

        $applied = TechProposed::getApplied($page);

        $msg = 'Принятые технологии';

        if (($total = TechProposed::getAppliedCount()) > 10) {
            $start_num = 10 * ($page - 1) + 1;
            $end_num = $start_num + count($applied) - 1;

            $msg .= "($start_num - $end_num из $total)";
        }

        $c = ($page - 1) * 10;
        foreach ($applied as $tech) {
            $msg .= PHP_EOL . ++$c . '. ' . $tech->code;
        }

        Message::write(
            $this->object()['peer_id'],
            $msg
        );
    }

    /**
     * @param $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function denied($args)
    {
        if (isset($args[0]) && is_numeric($args[0])) {
            $page = $args[0];
        } else {
            $page = 1;
        }

        $denied = TechProposed::getDenied($page);

        if (!$denied) {
            Message::write($this->object()['peer_id'], "Список отклонённых технологий пуст");
            return;
        }

        $msg = 'Отклонённые технологии';

        if (($total = TechProposed::getDeniedCount()) > 10) {
            $start_num = 10 * ($page - 1) + 1;
            $end_num = $start_num + count($denied) - 1;

            $msg .= "($start_num - $end_num из $total)";
        }

        $c = ($page - 1) * 10;
        foreach ($denied as $tech) {
            $msg .= PHP_EOL . ++$c . '. ' . $tech->code;
        }

        Message::write(
            $this->object()['peer_id'],
            $msg
        );
    }

    /**
     * @param $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function del($args)
    {
        if (!isset($args[0])) {
            Message::write($this->object()['peer_id'], 'Неверное число аргументов');
            return;
        }

        $proposed = TechProposed::getByCode($args[0]);

        if ($proposed === null || $proposed->isApplied()) {
            Message::write(
                $this->object()['peer_id'],
                'Такую технологию никто не предлагал либо она уже утверждена'
            );
            return;
        }

        if ($proposed->delete()) {
            $msg = 'Сделано';
        } else {
            $msg = 'Что-то не так';
        }

        Message::write($this->object()['peer_id'], $msg);
    }
}