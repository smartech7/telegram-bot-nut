<p align="center">
  <img src="https://i.imgur.com/0KjYtTJ.png" width="400px">
</p>

# Nutgram

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nutgram/nutgram.svg?style=flat-square)](https://packagist.org/packages/nutgram/nutgram)
[![Test Suite](https://github.com/nutgram/nutgram/actions/workflows/php.yml/badge.svg)](https://github.com/nutgram/nutgram/actions/workflows/php.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/86c4ca3dae8f64db80f7/maintainability)](https://codeclimate.com/github/nutgram/nutgram/maintainability)
[![Total Downloads](https://img.shields.io/packagist/dt/nutgram/nutgram.svg?style=flat-square)](https://packagist.org/packages/nutgram/nutgram)
[![API](https://img.shields.io/badge/Telegram%20Bot%20API-5.7%09--%20January%2031,%202022-blue.svg)](https://core.telegram.org/bots/api)
[![License](https://poser.pugx.org/nutgram/nutgram/license)](//packagist.org/packages/nutgram/nutgram)

> The Telegram bot framework that doesn't drive you nuts

This framework takes advantage of the latest **PHP 8** features, and tries to make the **speed**, **scalability** and **flexibility** of use its strength, it will allow you to quickly make simple bots, but at the same time, it provides
more **advanced features** to handle even the most complicated flows. Some architectural concepts on which Nutgram is
based are heavily influenced by other open source projects such as [Botman](https://github.com/botman/botman)
and [Zanzara](https://github.com/badfarm/zanzara), check them out too!

```php
<?php

use SergiX44\Nutgram\Nutgram;

$bot = new Nutgram($_ENV['TOKEN']);

$bot->onCommand('start', fn(Nutgram $bot) => $bot->sendMessage('Ciao!'));

$bot->onText('My name is {name}', fn(Nutgram $bot, $name) => $bot->sendMessage("Hi {$name}"));

$bot->run();
```

## Installation

You can install the package via composer:

```bash
composer require nutgram/nutgram
```

## Usage

- [Official Documentation](https://nutgram.dev)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Sergio Brighenti](https://github.com/SergiX44)
- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
