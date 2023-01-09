<?php

namespace SergiX44\Nutgram\Telegram\Types\Command;

use Attribute;
use InvalidArgumentException;
use SergiX44\Hydrator\Annotation\ConcreteResolver;

#[Attribute(Attribute::TARGET_CLASS)]
class MenuButtonResolver extends ConcreteResolver
{
    public function getConcreteClass(array $data): string
    {
        return match ($data['type']) {
            'commands' => MenuButtonCommands::class,
            'default' => MenuButtonDefault::class,
            'web_app' => MenuButtonWebApp::class,
            default => throw new InvalidArgumentException('Unknown MenuButton type: '.$data['type']),
        };
    }
}
