<?php


namespace SergiX44\Nutgram\Telegram;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use SergiX44\Nutgram\Handlers\Handler;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Endpoints\AvailableMethods;
use SergiX44\Nutgram\Telegram\Endpoints\Games;
use SergiX44\Nutgram\Telegram\Endpoints\InlineMode;
use SergiX44\Nutgram\Telegram\Endpoints\Passport;
use SergiX44\Nutgram\Telegram\Endpoints\Payments;
use SergiX44\Nutgram\Telegram\Endpoints\Stickers;
use SergiX44\Nutgram\Telegram\Endpoints\UpdatesMessages;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Message;
use SergiX44\Nutgram\Telegram\Types\Update;
use SergiX44\Nutgram\Telegram\Types\WebhookInfo;
use stdClass;

/**
 * Trait Client
 * @package SergiX44\Nutgram\Telegram
 * @mixin Nutgram
 */
trait Client
{
    use AvailableMethods,
        UpdatesMessages,
        Stickers,
        InlineMode,
        Payments,
        Passport,
        Games;

    /**
     * @var Handler|null
     */
    protected ?Handler $onApiError = null;

    /**
     * @param  array  $parameters
     * @return mixed
     */
    public function getUpdates(array $parameters = [])
    {
        return $this->requestJson(__FUNCTION__, $parameters, Update::class, [
            'timeout' => ($parameters['timeout'] ?? 0) + 1,
        ]);
    }

    /**
     * @param  string  $url
     * @param  array|null  $opt
     * @return bool|null
     */
    public function setWebhook(string $url, ?array $opt = []): ?bool
    {
        $required = compact('url');
        return $this->requestJson(__FUNCTION__, array_merge($required, $opt));
    }

    /**
     * @param  array|null  $opt
     * @return bool|null
     */
    public function deleteWebhook(?array $opt = []): ?bool
    {
        return $this->requestJson(__FUNCTION__, $opt);
    }

    /**
     * @return WebhookInfo|null
     */
    public function getWebhookInfo(): ?WebhookInfo
    {
        return $this->requestJson(__FUNCTION__, mapTo: WebhookInfo::class);
    }

    /**
     * @param  string  $endpoint
     * @param  array|null  $parameters
     * @param  array|null  $options
     * @return mixed
     */
    public function sendRequest(string $endpoint, ?array $parameters = [], ?array $options = []): mixed
    {
        return $this->http->postAsync($endpoint, array_merge(['multipart' => $parameters], $options))
            ->then(function (ResponseInterface $response) {
                $body = $response->getBody()->getContents();
                return json_decode($body);
            })->wait();
    }

    /**
     * @param  string  $endpoint
     * @param  string  $param
     * @param $value
     * @param  array  $opt
     * @return Message|null
     */
    protected function sendAttachment(string $endpoint, string $param, $value, array $opt = []): ?Message
    {
        $required = [
            'chat_id' => $this->chatId(),
            $param => $value,
        ];
        if (is_resource($value)) {
            return $this->requestMultipart($endpoint, array_merge($required, $opt), Message::class);
        } else {
            return $this->requestJson($endpoint, array_merge($required, $opt), Message::class);
        }
    }

    /**
     * @param  string  $endpoint
     * @param  array|null  $multipart
     * @param  string  $mapTo
     * @param  array|null  $options
     * @return mixed
     */
    protected function requestMultipart(string $endpoint, ?array $multipart = null, string $mapTo = stdClass::class, ?array $options = []): mixed
    {
        $parameters = [];
        foreach ($multipart as $name => $contents) {
            $parameters[] = [
                'name' => $name,
                'contents' => $contents,
            ];
        }

        try {
            $response = $this->http->post($endpoint, array_merge(['multipart' => $parameters], $options));
            return $this->mapResponse($response, $mapTo);
        } catch (RequestException $exception) {
            if (!$exception->hasResponse()) {
                throw $exception;
            }
            $response = $exception->getResponse();
            return $this->mapResponse($response, $mapTo, $exception);
        }
    }

    /**
     * @param  string  $endpoint
     * @param  array|null  $json
     * @param  string  $mapTo
     * @param  array|null  $options
     * @return mixed
     */
    protected function requestJson(string $endpoint, ?array $json = null, string $mapTo = stdClass::class, ?array $options = []): mixed
    {
        try {
            $response = $this->http->post($endpoint, array_merge([
                'json' => $json,
            ], $options));
            return $this->mapResponse($response, $mapTo);
        } catch (RequestException $exception) {
            if (!$exception->hasResponse()) {
                throw $exception;
            }
            $response = $exception->getResponse();
            return $this->mapResponse($response, $mapTo, $exception);
        }
    }

    /**
     * @param  ResponseInterface  $response
     * @param  string  $mapTo
     * @param  \Exception|null  $clientException
     * @return mixed
     * @throws TelegramException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonMapper_Exception
     */
    private function mapResponse(ResponseInterface $response, string $mapTo, \Exception $clientException = null): mixed
    {
        $json = json_decode((string) $response->getBody());
        if ($json?->ok) {
            return match (true) {
                is_scalar($json->result) => $json->result,
                is_array($json->result) => $this->mapper->mapArray($json->result, [], $mapTo),
                default => $this->mapper->map($json->result, new $mapTo)
            };
        } else {
            $e = new TelegramException($json?->description ?? '', $json?->error_code ?? 0, $clientException);

            if ($this->onApiError !== null) {
                return $this->fireApiErrorHandler($this->onApiError, $e);
            } else {
                throw $e;
            }
        }
    }
}
