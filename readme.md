# Exception Handler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

Handles exceptions and send email when it does happen

## Installation

Via Composer

``` bash
$ composer require kwaadpepper/exception-handler
```

## Usage

1 - Publish config if you want to customize things

    php artisan vendor:publish --provider="Kwaadpepper\ExceptionHandler\ExceptionHandlerServiceProvider"

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email github@jeremydev.ovh instead of using the issue tracker.

## Credits

- [Jérémy Munsch][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kwaadpepper/exception-handler?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kwaadpepper/exception-handler?style=flat-square

[link-packagist]: https://packagist.org/packages/kwaadpepper/exception-handler
[link-downloads]: https://packagist.org/packages/kwaadpepper/exception-handler
[link-author]: https://github.com/kwaadpepper
