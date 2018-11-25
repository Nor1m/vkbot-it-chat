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
                    Message::write($this->object()['peer_id'], Message::t('warning.no_args'));
                    return;
                }

                $tech = Tech::getByCode($argc[1]);

                Message::write($this->object()['peer_id'], Message::t('message.tech_info', [
                    '{name}' => $tech->name,
                    '{code}' => $tech->code,
                    '{description}' => $tech->description,
                ]));

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
                    $msg .= Message::t('message.tech_item_with_code', [
                            '{ord}' => ++$c,
                            '{name}' => $tech->name,
                            '{code}' => $tech->code,
                        ]) . PHP_EOL;
                }

                Message::write($this->object()['peer_id'], $msg);
        }
    }
}