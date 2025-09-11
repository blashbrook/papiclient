# PAPIClient

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![PHP Composer](https://github.com/blashbrook/papiclient/actions/workflows/php.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/php.yml)
[![Node.js CI](https://github.com/blashbrook/papiclient/actions/workflows/node.js.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/node.js.yml)
[![Dependency Review](https://github.com/blashbrook/papiclient/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/dependency-review.yml)
[![Semantic-Release](https://github.com/blashbrook/papiclient/actions/workflows/semantic-release.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/semantic-release.yml)
![StyleCI](https://github.styleci.io/repos/318002634/shield)

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require blashbrook/papiclient
```

## Usage

## Change log

PAPIClient has been refactored to use fluency!
Now you can chain commands together, making the client more flexible and easier to use.

* Use Injection to instantiate PAPIClient in a class:
````
  use Blashbrook\PAPIClient\PAPIClient;

  protected PAPIClient $papiclient;

  public function __construct(PAPIClient $papiclient) {
        $this->papiclient = $papiclient;
  }
````

* In a Livewire component, use the boot method:
```
  use Blashbrook\PAPIClient\PAPIClient;

  protected PAPIClient $papiclient;

  public function boot(PAPIClient $papiclient) {
    $this->papiclient = $papiclient;
  }
```
* To make an API call to your Polaris server:
````
// Validate PAPI Access Key
$response = $this->papiclient->method('GET')->uri('apikeyvalidate')->execRequest();
````
* functions include:
  * method('GET|PUT')
  * protected() // Uses the protected API base URI instead of the default public URI.
  * patron('BARCODE') // Allows you to insert a patron's barcode into the URI.
  * uri('API Endpoint') // The part of the URI that performs the desired function (i.e 'authenicator/patron' or 'apikeyvalidate').
  * params(array) // Used for form submissions (i.e. ['Barcode'=>'55555555555555', 'Password'=> '1234'] is sent to log in a patron).
  * auth('AccessSecret') // Inserts a patron's temporary authentication token in the request headers.
  * MORE TO COME!

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

@TODO
Add Error catching

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist. 

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/blashbrook/papiclient.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/blashbrook/papiclient.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/blashbrook/papiclient/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/blashbrook/papiclient
[link-downloads]: https://packagist.org/packages/blashbrook/papiclient
[link-travis]: https://travis-ci.org/blashbrook/papiclient
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/blashbrook
[link-contributors]: ../../contributors
