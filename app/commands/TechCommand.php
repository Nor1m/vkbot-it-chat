<?php

namespace App\commands;

use App\base\BaseCommand;
use App\base\Message;
use App\base\Protect;
use App\Log;
use App\models\Tech;
use App\models\TechProposed;

class TechCommand extends BaseCommand
{
    const FLAG_INFO = 'info';
    const FLAG_LIST = 'list';
    const FLAG_PROPOSED = 'proposed';
    const FLAG_PROPOSED_APPLY = 'proposed-apply';
    const FLAG_PROPOSED_DENY = 'proposed-deny';

    /**
     * @param array $argc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $first_arg = reset($argc);

        switch ($first_arg) {
            case self::FLAG_INFO:
                if (!isset($argc[1])) {
                    Message::write($this->object()['peer_id'], 'Неверное число аргументов');
                    return;
                }

                $tech = Tech::getByCode($argc[1]);

                Message::write($this->object()['peer_id'], <<<MSG
Технология: {$tech->name}
Код в моей базе: {$tech->code}
Описание: {$tech->description}
MSG
                );

                return;

            case self::FLAG_PROPOSED:
                $proposed_techs = TechProposed::findAll();

                Log::dump($proposed_techs);

                if (!$proposed_techs) {
                    Message::write($this->object()['peer_id'], "Список предложенных технологий пуст");
                    return;
                }

                $msg = 'Предложенные технологии:' . PHP_EOL;
                $c = 0;
                foreach ($proposed_techs as $tech) {
                    $msg .= ++$c . '. ' . $tech->code . PHP_EOL;
                }

                Message::write($this->object()['peer_id'], $msg);

                return;

            case self::FLAG_PROPOSED_APPLY:
                Protect::checkIsChatAdmin($this->fromUser(), $this->object()['peer_id']);

                if (!isset($argc[1])) {
                    Message::write($this->object()['peer_id'], 'Неверное число аргументов');
                    return;
                }

                $proposed = TechProposed::getByCode($argc[1]);

                if ($proposed === null || $proposed->closed) {
                    Message::write(
                        $this->object()['peer_id'],
                        'Такую технологию никто не предлагал или предложение уже закрыто'
                    );
                    return;
                }

                $proposed->apply();

                Message::write(
                    $this->object()['peer_id'],
                    'Технология добавлена в основной список. Предложение закрыто'
                );

                break;
            case self::FLAG_PROPOSED_DENY:
                Protect::checkIsChatAdmin($this->fromUser(), $this->object()['peer_id']);
                return;

            case self::FLAG_LIST:
            default:
                $techs = Tech::findAll();

                $msg = 'Доступные технологии:' . PHP_EOL;
                $c = 0;
                foreach ($techs as $tech) {
                    $msg .= ++$c . '. ' . $tech->name . ' (' . $tech->code . ')' . PHP_EOL;
                }

                Message::write($this->object()['peer_id'], $msg);
        }
    }
}