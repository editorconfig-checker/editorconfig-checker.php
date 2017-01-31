# editorconfig-checker

![Logo](https://raw.githubusercontent.com/mstruebing/editorconfig-checker/master/Docs/logo.png "Logo")

This is a __dependency-free__ tool to check if given files implement your .editorconfig rules.

[![Latest Stable Version](https://poser.pugx.org/mstruebing/editorconfig-checker/v/stable)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![Total Downloads](https://poser.pugx.org/mstruebing/editorconfig-checker/downloads)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![Latest Unstable Version](https://poser.pugx.org/mstruebing/editorconfig-checker/v/unstable)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![License](https://poser.pugx.org/mstruebing/editorconfig-checker/license)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![Monthly Downloads](https://poser.pugx.org/mstruebing/editorconfig-checker/d/monthly)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![Daily Downloads](https://poser.pugx.org/mstruebing/editorconfig-checker/d/daily)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![composer.lock](https://poser.pugx.org/mstruebing/editorconfig-checker/composerlock)](https://packagist.org/packages/mstruebing/editorconfig-checker)
[![psr2](https://img.shields.io/badge/cs-PSR--2-yellow.svg)](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)]

## Installation

Installation via composer is recommended:

```
composer require --dev mstruebing/editorconfig-checker
```

Ohterwise you could clone the repository and execute the script manually.

## Usage

If you installed it via composer you have a binary in your bin folder called `editorconfig-checker`.
Then you could create a script in your `composer.json` like this:

```json
"scripts": {
    "check-editorconfig": "editorconfig-checker src/**/.php"
}
```

Or any other [glob](http://php.net/manual/en/function.glob.php) you want. You could also check single files.

If you installed it manually you would have to do something like this:

```
<PATH/TO/ROOT/OF/THIS/REPOS>/src/editorconfig-checker src/**/.php
```

The exit value is 0 if no error occurred and 1 to 254 - every error adds 1 to the exit value.
255 means that there is more than 254 violations of your `.editorconfig` rules.

Usage output if no file or glob was provided:
```
Usage:
editorconfig-checker <FILE>|<FILEGLOB>
```

## Additional Notes

Please be aware that this is still experimental.
If you encounter any bugs or anything else please open an issue with as many details as possible.
