<?php


namespace SergiX44\Nutgram\Telegram;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Message;
use SergiX44\Nutgram\Telegram\Types\MessageId;
use SergiX44\Nutgram\Telegram\Types\Update;
use SergiX44\Nutgram\Telegram\Types\User;
use stdClass;

/**
 * Trait Client
 * @package SergiX44\Nutgram\Telegram
 * @mixin Nutgram
 */
trait Client
{

    /**
     * @param  array  $parameters
     * @return mixed
     */
    public function getUpdates(array $parameters = [])
    {
        return $this->requestJson(__FUNCTION__, $parameters, Update::class, [
            'timeout' => $parameters['timeout'] + 1,
        ]);
    }

    /**
     * @return User
     */
    public function getMe(): User
    {
        return $this->requestJson(__FUNCTION__, mapTo: User::class);
    }

    /**
     * @return bool
     */
    public function logOut(): bool
    {
        return $this->requestJson(__FUNCTION__);
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return $this->requestJson(__FUNCTION__);
    }

    /**
     * @param  string  $text
     * @param  array|null  $opt
     * @return Message
     */
    public function sendMessage(string $text, ?array $opt = []): Message
    {
        $chat_id = $this->getChatId();
        $required = compact('text', 'chat_id');
        return $this->requestJson(__FUNCTION__, array_merge($required, $opt), Message::class);
    }

    /**
     * @param  string|int  $chat_id
     * @param  string|int  $from_chat_id
     * @param  int  $message_id
     * @param  array  $opt
     * @return Message
     */
    public function forwardMessage(string|int $chat_id, string|int $from_chat_id, int $message_id, array $opt = []): Message
    {
        $required = compact('chat_id', 'from_chat_id', 'message_id');
        return $this->requestJson(__FUNCTION__, array_merge($required, $opt), Message::class);
    }

    /**
     * @param  string|int  $chat_id
     * @param  string|int  $from_chat_id
     * @param  int  $message_id
     * @param  array  $opt
     * @return MessageId
     */
    public function copyMessage(string|int $chat_id, string|int $from_chat_id, int $message_id, array $opt = []): MessageId
    {
        $required = compact('chat_id', 'from_chat_id', 'message_id');
        return $this->requestJson(__FUNCTION__, array_merge($required, $opt), MessageId::class);
    }

    /**
     * @param $photo
     * @param  string|int|null  $chat_id
     * @param  array  $opt
     * @return Message
     */
    public function sendPhoto($photo, string|int|null $chat_id = null, array $opt = []): Message
    {
        $required = compact('photo', 'chat_id');
        if (is_resource($photo)) {
            return $this->requestMultipart(__FUNCTION__, array_merge($required, $opt), Message::class);
        } else {
            return $this->requestJson(__FUNCTION__, array_merge($required, $opt), Message::class);
        }
    }

    /**
     * @param $audio
     * @param  string|int|null  $chat_id
     * @param  array  $opt
     * @return Message
     */
    public function sendAudio($audio, string|int|null $chat_id = null, array $opt = []): Message
    {
        $required = compact('audio', 'chat_id');
        if (is_resource($audio)) {
            return $this->requestMultipart(__FUNCTION__, array_merge($required, $opt), Message::class);
        } else {
            return $this->requestJson(__FUNCTION__, array_merge($required, $opt), Message::class);
        }
    }

    /**
     * @param $document
     * @param  string|int|null  $chat_id
     * @param  array  $opt
     * @return Message
     */
    public function sendDocument($document, string|int|null $chat_id = null, array $opt = []): Message
    {
        $required = compact('document', 'chat_id');
        if (is_resource($document)) {
            return $this->requestMultipart(__FUNCTION__, array_merge($required, $opt), Message::class);
        } else {
            return $this->requestJson(__FUNCTION__, array_merge($required, $opt), Message::class);
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
        $parameters = array_map(fn($name, $value) => [
            'name' => $name,
            'value' => $value,
        ], $multipart);
        $promise = $this->http->postAsync($endpoint, array_merge(['multipart' => $parameters], $options));
        return $this->mapResponse($promise, $mapTo);
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
        $promise = $this->http->postAsync($endpoint, array_merge([
            'json' => $json,
        ], $options));

        return $this->mapResponse($promise, $mapTo);
    }

    /**
     * @param  PromiseInterface  $promise
     * @param  string  $mapTo
     * @return mixed
     */
    private function mapResponse(PromiseInterface $promise, string $mapTo): mixed
    {
        return $promise->then(function (ResponseInterface $response) use ($mapTo) {
            $body = $response->getBody()->getContents();
            $json = json_decode($body);

            return match (true) {
                is_scalar($json->result) => $json->result,
                is_array($json->result) => $this->mapper->mapArray($json->result, [], $mapTo),
                default => $this->mapper->map($json->result, new $mapTo)
            };
        }, function ($e) {
            if ($e instanceof RequestException) {
                $body = $e->getResponse()->getBody()->getContents();
                $json = json_decode($body);

                $e = new TelegramException($json->description, $json->error_code);
            }

            if ($this->onApiError !== null) {
                $handler = $this->onApiError;
                $handler->setParameters([$e]);
                $handler($this);
            } else {
                throw $e;
            }
        })->wait();
    }
}
