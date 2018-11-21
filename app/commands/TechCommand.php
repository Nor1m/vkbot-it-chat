<?php

namespace App\commands;

use App\base\BaseCommand;
use App\base\Message;
use App\commands\tech\Edit;
use App\commands\tech\Proposed;
use App\Log;
use App\models\Tech;

class TechCommand extends BaseCommand
{
    const FLAG_INFO = 'info';
    const FLAG_LIST = 'list';
    const FLAG_PROPOSED = ['proposed', 'proposal'];
    const FLAG_EDIT = 'edit';

    /**
     * @param array $argc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $first_arg = reset($argc);

        if (in_array($first_arg, self::FLAG_PROPOSED)) {
            array_shift($argc);
            $cmd = new Proposed($this->vk(), $this->object(), $this->fromUser(), []);
            $cmd->run($argc);
            return;
        }

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

            case self::FLAG_EDIT:
                array_shift($argc);
                Log::write('edit');
                $cmd = new Edit($this->vk(), $this->object(), $this->fromUser(), []);
                $cmd->run($argc);
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