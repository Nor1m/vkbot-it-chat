<?php
/** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\commands\tech;

use App\base\BaseCommand;
use App\base\Message;
use App\models\Tech;

class Edit extends BaseCommand
{
    const FLAGS = [
        '-nam'         => 'name',
        '-name'        => 'name',
        '-cod'         => 'code',
        '-code'        => 'code',
        '-description' => 'description',
        '-desc'        => 'description',
    ];

    /**
     * @param array $args
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $args): void
    {
        if (count($args) < 3) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_args'));
        }

        list($code, $flag) = $args;

        $arg = implode(' ', array_slice($args, 2));

        $tech = Tech::getByCode($code);

        if (!$tech) {
            Message::write($this->object()['peer_id'], Message::t('warning.no_tech', [
                '{tech}' => $code,
            ]));
            return;
        }

        $flag = mb_strtolower(strval(array_shift($flag)));

        if (isset(self::FLAGS[$flag])) {
            $this->{self::FLAGS[$flag]}($tech, $arg);
            return;
        }
    }

    /**
     * @param $tech
     * @param $name
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function name(Tech $tech, string $name): void
    {
        $tech->updateName($name);

        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }

    /**
     * @param Tech $tech
     * @param string $code
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function code(Tech $tech, string $code): void
    {
        $tech->updateCode($code);

        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }

    /**
     * @param Tech $tech
     * @param string $desc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function description(Tech $tech, string $desc): void
    {
        $tech->updateDescription($desc);

        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }
}