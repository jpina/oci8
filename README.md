# Oci8

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Code Coverage][ico-coverage]][link-coverage]
[![Scrutinizer Score][ico-scrutinizer]][link-scrutinizer]
[![Code Climate Score][ico-codeclimate]][link-codeclimate]
[![Dependencies Status](https://gemnasium.com/jpina/oci8.svg)][ico-dependencies]
[![Total Downloads][ico-downloads]][link-downloads]


This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Via Composer

``` bash
$ composer require jpina/oci8
```

## Usage

``` php
$db = new Jpina\Oci8Connection('username', 'password', '//localhost:1521/XE');
$statement = $db->parse('SELECT * FROM dual');
$statement->execute();
$statement->fetchAssoc();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Josué Piña][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jpina/oci8.svg?style=flat-square
[ico-license]: https://img.shields.io/:license-mit-blue.svg
[ico-travis]: https://travis-ci.org/jpina/oci8.svg?branch=master
[ico-coverage]: https://codeclimate.com/github/jpina/oci8/badges/coverage.svg 
[ico-scrutinizer]: https://scrutinizer-ci.com/g/jpina/oci8/badges/quality-score.png?b=master
[ico-codeclimate]: https://codeclimate.com/github/jpina/oci8/badges/gpa.svg
[ico-dependencies]: https://gemnasium.com/jpina/oci8
[ico-downloads]: https://img.shields.io/packagist/dt/jpina/oci8.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jpina/oci8
[link-travis]: https://travis-ci.org/jpina/oci8
[link-coverage]: https://codeclimate.com/github/jpina/oci8/coverage 
[link-scrutinizer]: https://scrutinizer-ci.com/g/jpina/oci8/?branch=master
[link-codeclimate]: https://codeclimate.com/github/jpina/oci8
[link-downloads]: https://packagist.org/packages/jpina/oci8
[link-author]: https://github.com/jpina
[link-contributors]: https://github.com/jpina/oci8/graphs/contributors
