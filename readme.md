# Exception Handler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

Handles exceptions and send email when it does happen
It does send you a mail with anonymized debug data

## Installation

Via Composer

``` bash
$ composer require kwaadpepper/exception-handler
```

## Usage

  - `php artisan handler:install`
  - Change configuration in `config/exception-handler.php` if needed.
  - Put at least your team email in the above file in `contactsList` array or using `.env` put a list in `EXCEPTION_MAIL_LIST`. You can add emails separated with `,` like `admin1@email.com,admin2@email.com`

You can uninstall it by using `php artisan handler:remove` or just revert changes in Exception/Handler.php
## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

# Todo

- Add other transmission possibilities (other than mail)

## Security

If you discover any security related issues, please email `github@jeremydev.ovh` instead of using the issue tracker.

## Credits

- [Jérémy Munsch][link-author]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kwaadpepper/exception-handler?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kwaadpepper/exception-handler?style=flat-square

[link-packagist]: https://packagist.org/packages/kwaadpepper/exception-handler
[link-downloads]: https://packagist.org/packages/kwaadpepper/exception-handler
[link-author]: https://github.com/kwaadpepper
