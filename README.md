[![Build Status](https://img.shields.io/github/workflow/status/mediamonks/php-rest-api/CI?label=CI&logo=github&style=flat-square)](https://github.com/mediamonks/doctrine-extensions/actions?query=workflow%3ACI)
[![Code Coverage](https://img.shields.io/codecov/c/gh/mediamonks/php-rest-api?label=codecov&logo=codecov&style=flat-square)](https://codecov.io/gh/mediamonks/doctrine-extensions)
[![Total Downloads](https://poser.pugx.org/mediamonks/php-rest-api/downloads)](https://packagist.org/packages/mediamonks/php-rest-api)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/php-rest-api/v/stable)](https://packagist.org/packages/mediamonks/php-rest-api)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/php-rest-api/v/unstable)](https://packagist.org/packages/mediamonks/php-rest-api)
[![License](https://poser.pugx.org/mediamonks/php-rest-api/license)](https://packagist.org/packages/mediamonks/php-rest-api)

# MediaMonks Rest API

This library contains an event subscriber to easily create a Rest API with the [Symfony HttpKernel](http://symfony.com/doc/current/components/http_kernel.html). 
By default this library will output according to our [MediaMonks Rest API spec](https://github.com/mediamonks/documents) but since we believe it could be very useful for other companies too it's very easy to extend it or implement your own.

## Highlights

- Thrown exceptions will be converted automatically
- Supports custom serializers like JMS, uses json serializer by default
- Supports custom response models
- Supports application/json, application/x-www-form-urlencoded & multipart/form-data input
- Supports method overriding
- Supports forcing a "200 OK" status method
- Supports paginated responses
- Supports wrapping json response in a method (jsonp) and post message
- Should work with any framework that uses HttpKernel

## Documentation

Documentation and examples can be found in the [/docs](/docs) folder.

## Requirements

- PHP >= 8.0

To use the library.

## Installation

For Symfony Framework users it is recommended to install the [Rest API Bundle](https://github.com/mediamonks/symfony-rest-api-bundle) instead of this library.

Install this package by using Composer.

```
$ composer require mediamonks/rest-api
```

## Security

If you discover any security related issues, please email devmonk@mediamonks.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
