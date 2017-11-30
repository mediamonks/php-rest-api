[![Build Status](https://travis-ci.org/mediamonks/php-rest-api.svg?branch=master)](https://travis-ci.org/mediamonks/php-rest-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mediamonks/php-rest-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/php-rest-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mediamonks/php-rest-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/php-rest-api/?branch=master)
[![Total Downloads](https://poser.pugx.org/mediamonks/rest-api/downloads)](https://packagist.org/packages/mediamonks/rest-api)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/rest-api/v/stable)](https://packagist.org/packages/mediamonks/rest-api)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/rest-api/v/unstable)](https://packagist.org/packages/mediamonks/rest-api)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/56f4ef4a-a8da-4380-8d40-fada760a665c.svg)](https://insight.sensiolabs.com/projects/56f4ef4a-a8da-4380-8d40-fada760a665c)
[![License](https://poser.pugx.org/mediamonks/rest-api/license)](https://packagist.org/packages/mediamonks/rest-api)

# MediaMonks Rest API

This library contains an event subscriber to easily create a Rest API with the [Symfony HttpKernel](http://symfony.com/doc/current/components/http_kernel.html). 
By default this library will output according to our [MediaMonks Rest API spec](https://github.com/mediamonks/documents) but since we believe it could be very useful for other companies too it's very easy to extend it or implement your own.

## Highlights

- Scalars, arrays and objects will be converted automatically
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

- PHP >= 5.4

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
