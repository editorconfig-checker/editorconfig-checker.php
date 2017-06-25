# editorconfig-checker

![Logo](https://raw.githubusercontent.com/editorconfig-checker/editorconfig-checker.php/master/Docs/logo.png "Logo")

This is a command-line tool to check if given files implement your .editorconfig rules.

[![Build Status](https://travis-ci.org/editorconfig-checker/editorconfig-checker.php.svg?branch=master)](https://travis-ci.org/editorconfig-checker/editorconfig-checker.php)
[![Coverage Status](https://coveralls.io/repos/github/editorconfig-checker/editorconfig-checker.php/badge.svg?branch=master)](https://coveralls.io/github/editorconfig-checker/editorconfig-checker.php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/v/stable)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Latest Unstable Version](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/v/unstable)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Total Downloads](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/downloads)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Daily Downloads](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/d/daily)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![Monthly Downloads](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/d/monthly)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![psr2](https://img.shields.io/badge/cs-PSR--2-yellow.svg)](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
[![composer.lock](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/composerlock)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)
[![License](https://poser.pugx.org/editorconfig-checker/editorconfig-checker/license)](https://packagist.org/packages/editorconfig-checker/editorconfig-checker)

## Installation

Installation via composer is recommended:

```
composer require --dev editorconfig-checker/editorconfig-checker
./vendor/bin/editorconfig-checker

# or in a composer-script just
editorconfig-checker
```

Otherwise you could clone the repository and execute the script manually.

```
git clone git@github.com:editorconfig-checker/editorconfig-checker.php.git
./editorconfig-checker.php/bin/editorconfig-checker
```

## Usage

If you installed it via composer you have a binary in your bin folder called `editorconfig-checker`.
Then you could create a script in your `composer.json` like this:

```json
"scripts": {
    "check-editorconfig": "editorconfig-checker src/*.php"
}
```

__ATTENTION!__: You could not use shell-like globbing like `src/**/*.php` to find all files.
You have to explicitly specify the directory under which is searched for certain file types.
So the above example would become to `src/*.php` to find all `.php` files in `src` and it's subdirectories.

You could also check for single files with explicit call them e.g. `editorconfig-checker src/index.php`

If you want to filter the files you could do this via the `-e|--exclude` parameter 

__CAUTION__ after using this parameter you __HAVE TO__ write a single
regular expression or string or your files you want to check will be interpreted as the exclude pattern.

If you use a regular expression you should __always__ put single quotes around it 
because the special characters(e.g. `|`, `*`, `.` or whatever) will be interpreted by your shell before if you don't.

Some examples:
```sh
# will filter all files which has vendor in their name or git, png or lock as extension
bin/editorconfig-checker -d -e 'vendor|\.git$|\.png$|\.lock$' ./*
bin/editorconfig-checker --dotfiles --exclude 'vendor|.git$|.png$|.lock$' ./*

# will filter all files with png and json extension
bin/editorconfig-checker -d -e '\.png$|\.json$' ./*
bin/editorconfig-checker --dotfiles --exclude '\.png$|\.json$' ./*

# will only filter all files which has vendor in their name
bin/editorconfig-checker -d -e vendor ./*
bin/editorconfig-checker --dotfiles -exclude vendor ./*

# will filter all files which has vendor or .git in their name
bin/editorconfig-checker -d -e vendor -e '\.git' ./*
bin/editorconfig-checker --dotfiles -exclude vendor -e '\.git' ./*

# will filter all files which has vendor in their name and doesn't include dotfiles/dotdirs (like .git or .travis.yml)
bin/editorconfig-checker -e vendor  ./*
bin/editorconfig-checker --exclude vendor  ./*

# will filter all files which has vendor in their name and doesn't include dotfiles/dotdirs (like .git or .travis.yml) and will try to fix issues if they occur
bin/editorconfig-checker -a -e vendor  ./*
bin/editorconfig-checker --auto-fix --exclude vendor  ./*
```

If you just want to filter for one string you don't have to worry and if you want to filter for more strings you could also pass the `-e|--exclude` option more than once like this:

```sh
bin/editorconfig-checker -d -e vendor -e myBinary -e someGeneratedFile -e myPicture ./*
bin/editorconfig-checker --dotfiles --exclude vendor --exclude myBinary --exclude someGeneratedFile --exclude myPicture ./*
```

If you installed it manually you would have to do something like this:

```sh
<PATH/TO/ROOT/OF/THIS/REPOS>/bin/editorconfig-checker src/*.php
```

The exit value is 0 if no error occurred and 1 to 254 - every error adds 1 to the exit value.
255 means that there is more than 254 violations of your `.editorconfig` rules.

Usage output:
```
Usage:
editorconfig-checker [OPTIONS] <FILE>|<FILEGLOB>
available options:
-a, --auto-fix
    will automatically fix fixable issues(insert_final_newline, end_of_line, trim_trailing_whitespace)
-d, --dotfiles
    use this flag if you want to also include dotfiles/dotdirectories
-e <PATTERN>, --exclude <PATTERN>
    string or regex to filter files which should not be checked
-h, --help
    will print this help text
-l, --list-files
    will print all files which are checked to stdout
```

## Additional Notes

I use semantic versioning so every breaking change will result in the increase of the major version.

If you encounter any bugs or anything else please open an issue with as many details as possible.

You should use the `-f` option after installing and configuring this tool to see if all files are
checked.

The `auto-fix` parameter puts a copy of every original file which gets fixed in `/tmp/editorconfig-checker.php/<filename>-<timestamp>-<sha1>`
