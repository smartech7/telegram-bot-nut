<?php


namespace SergiX44\Nutgram\Conversation;

use Closure;
use Opis\Closure\SerializableClosure;
use Psr\SimpleCache\CacheInterface;

class ConversationRepository
{
    protected const CONVERSATION_TTL = 43200;

    protected const CONVERSATION_PREFIX = 'CONVERSATION';

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var int
     */
    private int $ttl;

    public function __construct(CacheInterface $cache, $ttl = self::CONVERSATION_TTL)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * @param $user
     * @param $chat
     * @return string
     */
    protected function makeKey($user, $chat): string
    {
        return implode('_', [self::CONVERSATION_PREFIX, $user, $chat]);
    }

    /**
     * @param $userId
     * @param $chatId
     * @return Conversation|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get($userId, $chatId): ?Conversation
    {
        $data = $this->cache->get($this->makeKey($chatId, $userId));
        if ($data !== null) {
            $handler = unserialize($data);

            if ($handler instanceof SerializableClosure) {
                $handler = $handler->getClosure();
            }

            return $handler;
        }

        return null;
    }

    /**
     * @param $userId
     * @param $chatId
     * @param  Conversation|callable  $conversation
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function store($userId, $chatId, $conversation): bool
    {
        if ($conversation instanceof Closure) {
            $conversation = new SerializableClosure($conversation);
        }

        $data = serialize($conversation);

        return $this->cache->set($this->makeKey($userId, $chatId), $data, $this->ttl);
    }

    /**
     * @param $userId
     * @param $chatId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete($userId, $chatId): bool
    {
        return $this->cache->delete($this->makeKey($chatId, $userId));
    }
}
