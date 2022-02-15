<?php


namespace SergiX44\Nutgram\RunningMode;

use JsonMapper;
use JsonMapper_Exception;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Common\Update;
use Throwable;

class Webhook implements RunningMode
{

    /**
     * @var array|\string[][]
     */
    protected array $telegramIpRanges = [
        ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
        ['lower' => '91.108.4.0', 'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
    ];


    /**
     * In safe mode If received request from a ip other than telegram ips, the robot will not respond
     * @var bool
     */
    protected bool $safeMode = false;


    /**
     * @param  Nutgram  $bot
     * @throws JsonMapper_Exception
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function processUpdates(Nutgram $bot): void
    {
        if ($this->safeMode && !$this->isSafe()) {
            return;
        }

        $input = file_get_contents('php://input');
        $update = $bot->getContainer()
            ->get(JsonMapper::class)
            ->map(
                json_decode($input, flags: JSON_THROW_ON_ERROR),
                $bot->getContainer()->get(Update::class)
            );
        $bot->processUpdate($update);
    }


    /**
     * @return bool
     */
    public function isSafe(): bool
    {
        $ip_dec = (float)sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));

        foreach ($this->telegramIpRanges as $ipRange) {
            // Make sure the IP is valid.
            if ($ip_dec >= (float)sprintf("%u", ip2long($ipRange['lower'])) &&
                $ip_dec <= (float)sprintf("%u", ip2long($ipRange['upper']))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSafeMode(): bool
    {
        return $this->safeMode;
    }

    /**
     * @param  bool  $safeMode
     */
    public function setSafeMode(bool $safeMode): void
    {
        $this->safeMode = $safeMode;
    }
}
