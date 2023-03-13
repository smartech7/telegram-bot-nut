<?php

namespace SergiX44\Nutgram;

use Closure;
use Laravel\SerializableClosure\SerializableClosure;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use SergiX44\Hydrator\HydratorInterface;
use SergiX44\Nutgram\Cache\Adapters\ArrayCache;
use SergiX44\Nutgram\Hydrator\NutgramHydrator;

final readonly class Configuration
{
    public const DEFAULT_API_URL = 'https://api.telegram.org';
    public const DEFAULT_POLLING_TIMEOUT = 10;
    public const DEFAULT_POLLING_LIMIT = 100;
    public const DEFAULT_CLIENT_TIMEOUT = 5;
    public const DEFAULT_HYDRATOR = NutgramHydrator::class;
    public const DEFAULT_CACHE = ArrayCache::class;
    public const DEFAULT_LOGGER = NullLogger::class;

    /**
     * @param  string  $apiUrl
     * @param  int|null  $botId
     * @param  string|null  $botName
     * @param  bool  $testEnv
     * @param  bool  $isLocal
     * @param  bool  $splitLongMessages
     * @param  int  $clientTimeout
     * @param  array  $clientOptions
     * @param  ContainerInterface|null  $container
     * @param  HydratorInterface|string  $hydrator
     * @param  CacheInterface|string  $cache
     * @param  string|LoggerInterface  $logger
     * @param  array|Closure|string|null  $localPathTransformer
     * @param  int  $pollingTimeout
     * @param  array  $pollingAllowedUpdates
     * @param  int  $pollingLimit
     */
    public function __construct(
        public string $apiUrl = self::DEFAULT_API_URL,
        public ?int $botId = null,
        public ?string $botName = null,
        public bool $testEnv = false,
        public bool $isLocal = false,
        public bool $splitLongMessages = false,
        public int $clientTimeout = self::DEFAULT_CLIENT_TIMEOUT,
        public array $clientOptions = [],
        public ?ContainerInterface $container = null,
        public HydratorInterface|string $hydrator = self::DEFAULT_HYDRATOR,
        public CacheInterface|string $cache = self::DEFAULT_CACHE,
        public string|LoggerInterface $logger = self::DEFAULT_LOGGER,
        public array|Closure|string|null $localPathTransformer = null,
        public int $pollingTimeout = self::DEFAULT_POLLING_TIMEOUT,
        public array $pollingAllowedUpdates = [],
        public int $pollingLimit = self::DEFAULT_POLLING_LIMIT
    ) {
    }


    public static function fromArray(array $config): self
    {
        return new self(
            apiUrl: $config['api_url'] ?? self::DEFAULT_API_URL,
            botId: $config['bot_id'] ?? null,
            botName: $config['bot_name'] ?? null,
            testEnv: $config['test_env'] ?? false,
            isLocal: $config['is_local'] ?? false,
            splitLongMessages: $config['split_long_messages'] ?? false,
            clientTimeout: $config['timeout'] ?? self::DEFAULT_CLIENT_TIMEOUT,
            clientOptions: $config['client'] ?? [],
            container: $config['container'] ?? null,
            hydrator: $config['hydrator'] ?? self::DEFAULT_HYDRATOR,
            cache: $config['cache'] ?? self::DEFAULT_CACHE,
            logger: $config['logger'] ?? self::DEFAULT_LOGGER,
            localPathTransformer: $config['local_path_transformer'] ?? null,
            pollingTimeout: $config['polling']['timeout'] ?? self::DEFAULT_POLLING_TIMEOUT,
            pollingAllowedUpdates: $config['polling']['allowed_updates'] ?? [],
            pollingLimit: $config['polling']['limit'] ?? self::DEFAULT_POLLING_LIMIT
        );
    }

    public function toArray(): array
    {
        return [
            'api_url' => $this->apiUrl,
            'bot_id' => $this->botId,
            'bot_name' => $this->botName,
            'test_env' => $this->testEnv,
            'is_local' => $this->isLocal,
            'split_long_messages' => $this->splitLongMessages,
            'timeout' => $this->clientTimeout,
            'client' => $this->clientOptions,
            'container' => $this->container,
            'hydrator' => $this->hydrator,
            'cache' => $this->cache,
            'logger' => $this->logger,
            'local_path_transformer' => $this->localPathTransformer,
            'polling' => [
                'timeout' => $this->pollingTimeout,
                'limit' => $this->pollingLimit,
                'allowed_updates' => $this->pollingAllowedUpdates,
            ],
        ];
    }

    public function __serialize(): array
    {
        $data = get_object_vars($this);

        unset($data['cache']);

        if ($this->logger instanceof LoggerInterface) {
            unset($data['logger']);
        }

        if ($this->localPathTransformer instanceof Closure) {
            $data['localPathTransformer'] = new SerializableClosure($this->localPathTransformer);
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        $data['cache'] = self::DEFAULT_CACHE;

        if (!isset($data['logger'])) {
            $data['logger'] = self::DEFAULT_LOGGER;
        }
        if (!isset($data['container'])) {
            $data['container'] = null;
        }

        if ($data['localPathTransformer'] instanceof SerializableClosure) {
            $data['localPathTransformer'] = $data['localPathTransformer']->getClosure();
        }

        foreach ($data as $attribute => $value) {
            $this->{$attribute} = $value;
        }
    }
}
