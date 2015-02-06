# JSONPointer
###### Simple library for accessing PHP data structures with JSON Pointers (RFC 6901)

[![Latest Stable Version](https://poser.pugx.org/chili-labs/json-pointer/v/stable.png)](https://packagist.org/packages/chili-labs/json-pointer)
[![Latest Unstable Version](https://poser.pugx.org/chili-labs/json-pointer/v/unstable.png)](https://packagist.org/packages/chili-labs/json-pointer)
[![Total Downloads](https://poser.pugx.org/chili-labs/json-pointer/downloads.png)](https://packagist.org/packages/chili-labs/json-pointer)
[![Build Status](https://secure.travis-ci.org/chili-labs/json-pointer.png?branch=master)](http://travis-ci.org/chili-labs/json-pointer)
[![Coverage Status](https://coveralls.io/repos/chili-labs/json-pointer/badge.png?branch=master)](https://coveralls.io/r/chili-labs/json-pointer?branch=master)

## Description

This library implements the [RFC 6901](https://tools.ietf.org/html/rfc6901) for PHP. It is possible to 
parse an path (e.g.```/some/path```) or create the ```JsonPointer``` object from array (e.g. ```['some','path']```).
There are several other good libraries ([1](/gamringer/JSONPointer), [2](/webnium/php-json-pointer),
[3](/raphaelstolt/php-jsonpointer)) for parsing json-pointers, but none of 
them was able to access other php structures besides default arrays. With different accessors, which are part of this
library, you can apply json-pointers to either arrays or also plain objects. Custom accesors can simply be created
on your own.

## Installation

To install this library, run the command below and you will get the latest
version:

    composer require chili-labs/json-pointer

## Usage

The documentation is work in progress.

## Tests

To run the test suite, you need [composer](http://getcomposer.org).

    composer install
    phpunit

## License

JSONPointer is licensed under the MIT license.


