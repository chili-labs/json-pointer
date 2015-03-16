# JSONPointer
###### Simple library for accessing PHP data structures with JSON Pointers (RFC 6901)

[![Latest Stable Version](https://img.shields.io/packagist/v/chili-labs/json-pointer.svg?style=flat&label=stable)](https://packagist.org/packages/chili-labs/json-pointer)
[![Total Downloads](https://img.shields.io/packagist/dt/chili-labs/json-pointer.svg?style=flat)](https://packagist.org/packages/chili-labs/json-pointer)
[![License](https://img.shields.io/packagist/l/chili-labs/json-pointer.svg?style=flat)](https://packagist.org/packages/chili-labs/json-pointer)
[![Build Status](https://secure.travis-ci.org/chili-labs/json-pointer.png?branch=master)](http://travis-ci.org/chili-labs/json-pointer)
[![Coverage Status](https://img.shields.io/coveralls/chili-labs/json-pointer.svg?style=flat)](https://coveralls.io/r/chili-labs/json-pointer?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/25634e78-a742-45f4-bf23-cd6f48536e3e/mini.png)](https://insight.sensiolabs.com/projects/25634e78-a742-45f4-bf23-cd6f48536e3e)

## Description

This library implements the [RFC 6901](https://tools.ietf.org/html/rfc6901) for PHP. It is possible to 
parse a path (e.g.```/some/path```) or create the ```JsonPointer``` object from array (e.g. ```['some','path']```).
There are several other good libraries (
[1](https://github.com/gamringer/JSONPointer), 
[2](https://github.com/webnium/php-json-pointer),
[3](https://github.com/raphaelstolt/php-jsonpointer)
) for parsing json-pointers, but none of 
them was able to access other php structures besides default arrays. With different accessors, which are part of this
library, you can apply json-pointers to either arrays or also plain objects. Custom accessors can simply be created
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


