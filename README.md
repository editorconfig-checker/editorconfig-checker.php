# editorconfig-checker

![Logo](https://raw.githubusercontent.com/editorconfig-checker/editorconfig-checker.php/master/Docs/logo.png "Logo")

This is a command-line tool to check if given files implement your .editorconfig rules.

[![Build Status](https://travis-ci.org/editorconfig-checker/editorconfig-checker.php.svg?branch=master)](https://travis-ci.org/editorconfig-checker/editorconfig-checker.php)
[![Coverage Status](https://coveralls.io/repos/github/editorconfig-checker/editorconfig-checker.php/badge.svg?branch=master)](https://coveralls.io/github/editorconfig-checker/editorconfig-checker.php/?branch=master)
[![Code Climate](https://codeclimate.com/github/editorconfig-checker/editorconfig-checker.php/badges/gpa.svg)](https://codeclimate.com/github/editorconfig-checker/editorconfig-checker.php)
[![Issue Count](https://codeclimate.com/github/editorconfig-checker/editorconfig-checker.php/badges/issue_count.svg)](https://codeclimate.com/github/editorconfig-checker/editorconfig-checker.php)
[![psr2](https://img.shields.io/badge/cs-PSR--2-yellow.svg)](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
[![composer.lock](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/composerlock)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Total Downloads](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/downloads)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Daily Downloads](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/d/daily)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Monthly Downloads](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/d/monthly)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Latest Stable Version](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/v/stable)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Latest Unstable Version](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/v/unstable)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![License](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/license)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)

<a href="https://www.buymeacoffee.com/mstruebing" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a>

## What?

This is a tool to check if your files consider your .editorconfig. Most tools - like linters for example - only test one filetype and need an extra configuration. This tool only needs your editorconfig to check all files.

![Sample Output](https://raw.githubusercontent.com/editorconfig-checker/editorconfig-checker.php/master/Docs/sample.png "Sample Output")

## Important

This is only a wrapper for the core [editorconfig-checker](https://github.com/editorconfig-checker/editorconfig-checker). 
You should have a look at this repository to know how this tool can be used and what possibilities/caveats are there.
This version can be used in the same way as the core as every argument is simply passed down to it.

## Installation

```
composer require --dev editorconfig-checker/editorconfig-checker
```

## Usage

```
vendor/bin/ec
```

## Configuration

You can configure analysis in `.editorconfig-checker.json` json file, e.g.:

```json
{
    "Exclude": [
        "^vendor\/",
        "^var\/",
        "^\\.idea\/",
        "\\.phar$"
    ]
}

```

Full list of config options is in [core docs](https://github.com/editorconfig-checker/editorconfig-checker?tab=readme-ov-file#configuration)

## Cli options

Please head to [core docs](https://github.com/editorconfig-checker/editorconfig-checker?tab=readme-ov-file#usage).


## Support

If you have any questions or just want to chat join #editorconfig-checker on 
freenode(IRC).
If you don't have an IRC-client set up you can use the 
[freenode webchat](https://webchat.freenode.net/?channels=editorconfig-checker).
