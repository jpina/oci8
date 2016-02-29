# Oci8

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

[![Build Status][ico-travis]][link-travis]
[![Dependencies Status][ico-dependencies]][link-dependencies]
[![PHP 7 ready][ico-php7]][link-php7]

[![Code Coverage][ico-coverage]][link-coverage]
[![Scrutinizer Score][ico-scrutinizer]][link-scrutinizer]
[![Code Climate Score][ico-codeclimate]][link-codeclimate]

[![SensioLabsInsight Score][ico-sensiolabsinsight]][link-sensiolabsinsight]

Oci8 is a wrapper for the PHP [Oracle OCI](http://php.net/manual/en/book.oci8.php) functions that allows interaction
with Oracle databases by using objects in place of the regular `oci_*` functions.

Oci8 converts the warnings thrown by the `oci_*` function into `Oci8Exceptions` for better error handling.

## Install

Via Composer

``` bash
$ composer require jpina/oci8
```

## Usage

Connect to a database, execute a query and fetch a row:

``` php
$db = new Jpina\Oci8Connection('username', 'password', '//localhost:1521/XE');
$statement = $db->parse('SELECT * FROM dual');
$statement->execute();
$row = $statement->fetchAssoc();
```

Handing errors

```php
try {
    $db = new Jpina\Oci8Connection('username', 'password', '//localhost:1521/XE');
    // Closing database to force an error on next statement
    $db->close();
    // This statement will throw an Oci8Exception since there is no active connection
    $statement = $db->parse('SELECT * FROM dual');
} catch (Jpina\Oci8Exception $ex) {
    // Handle the Exception
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

In order to run the tests you will need access to an Oracle Database and copy the `.env.example` to `.env`, then you
can provide your own values within the `.env`, these values will be used by the tests to connect to the database.

You can run the tests with composer once you have your `.env` file configured properly.

``` bash
$ composer test
```

If you don't have access to an Oracle Database server, you can also run a docker container like
[wnameless/oracle-xe-11g](https://hub.docker.com/r/wnameless/oracle-xe-11g) and then connect to it to run the tests
against a containerized Oracle Database.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email [jabdhel@gmail.com](mailto:jabdhel@gmail.com) instead of
using the issue tracker.

## Credits

- [Josué Piña][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jpina/oci8.svg?style=flat-square
[ico-license]: https://img.shields.io/:license-mit-blue.svg
[ico-travis]: https://travis-ci.org/jpina/oci8.svg?branch=master
[ico-coverage]: https://scrutinizer-ci.com/g/jpina/oci8/badges/coverage.png?b=master
[ico-scrutinizer]: https://scrutinizer-ci.com/g/jpina/oci8/badges/quality-score.png?b=master
[ico-codeclimate]: https://codeclimate.com/github/jpina/oci8/badges/gpa.svg
[ico-sensiolabsinsight]: https://insight.sensiolabs.com/projects/8e542895-54fb-42e6-aa59-840b8acc3241/small.png
[ico-dependencies]: https://gemnasium.com/jpina/oci8.svg
[ico-php7]: http://php7ready.timesplinter.ch/jpina/oci8/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/jpina/oci8.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jpina/oci8
[link-travis]: https://travis-ci.org/jpina/oci8
[link-coverage]: https://scrutinizer-ci.com/g/jpina/oci8/?branch=master
[link-scrutinizer]: https://scrutinizer-ci.com/g/jpina/oci8/?branch=master
[link-codeclimate]: https://codeclimate.com/github/jpina/oci8
[link-sensiolabsinsight]: https://insight.sensiolabs.com/projects/8e542895-54fb-42e6-aa59-840b8acc3241
[link-dependencies]: https://gemnasium.com/jpina/oci8
[link-php7]: https://travis-ci.org/jpina/oci8
[link-downloads]: https://packagist.org/packages/jpina/oci8
[link-author]: https://github.com/jpina
[link-contributors]: https://github.com/jpina/oci8/graphs/contributors
