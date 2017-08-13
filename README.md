# editorconfig-checker

![Logo](https://raw.githubusercontent.com/editorconfig-checker/editorconfig-checker.php/master/Docs/logo.png "Logo")

This is a command-line tool to check if given files implement your .editorconfig rules.

[![Build Status](https://travis-ci.org/editorconfig-checker/editorconfig-checker.php.svg?branch=master)](https://travis-ci.org/editorconfig-checker/editorconfig-checker.php)
[![Coverage Status](https://coveralls.io/repos/github/editorconfig-checker/editorconfig-checker.php/badge.svg?branch=master)](https://coveralls.io/github/editorconfig-checker/editorconfig-checker.php?branch=master)
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
    "check-editorconfig": "editorconfig-checker src/*"
}
```

You could also check for single files with explicit call them e.g. `editorconfig-checker src/index.php`
Shell globbing is possible for example: `editorconfig-checker ./src/EditorconfigChecker/{Cli,Fix}/*`

If you want to filter the files you could do this via the `-e|--exclude` parameter 

__CAUTION__ after using this parameter you __HAVE TO__ write a single
regular expression or string or your files you want to check will be interpreted as the exclude pattern.

If you use a regular expression you should __always__ put single quotes around it 
because the special characters(e.g. `|`, `*`, `.` or whatever) will be interpreted by your shell before if you don't.

Some examples:
```sh
# will filter all files with json extension
editorconfig-checker -e '\\.json$' ./*
editorconfig-checker --exclude '\\.json$' ./*

# will only filter all files which has TestFiles in their name
bin/editorconfig-checker -e TestFiles ./*
bin/editorconfig-checker --exclude TestFiles ./*

# will filter all files which has TestFiles in their name and json as extension
bin/editorconfig-checker -e 'TestFiles|\\.json$' ./*
bin/editorconfig-checker --exclude 'TestFiles|\\.json$' ./*

# will filter all files which has TestFiles in their name and exclude dotfiles
bin/editorconfig-checker -d -e TestFiles  ./*
bin/editorconfig-checker --dotfiles --exclude TestFiles  ./*

# will filter all files which has TestFiles in their name and exclude dotfiles and will try to fix issues if they occur
bin/editorconfig-checker -a -d -e TestFiles  ./*
bin/editorconfig-checker --auto-fix --dotfiles --exclude TestFiles  ./*

# will don't use default excludes and filter all files which has TestFiles in their name
bin/editorconfig-checker -a -i -d -e TestFiles  ./*
bin/editorconfig-checker --auto-fix --ignore-defaults --dotfiles --exclude TestFiles  ./*
```

If you just want to filter for one string you don't have to worry and if you want to filter for more strings you could also pass the `-e|--exclude` option more than once like this:

```sh
bin/editorconfig-checker -e vendor -e myBinary -e someGeneratedFile -e myPicture ./*
bin/editorconfig-checker --exclude vendor --exclude myBinary --exclude someGeneratedFile --exclude myPicture ./*
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
    will automatically fix fixable issues(insert_final_newline, end_of_line, trim_trailing_whitespace, tabs to spaces)
-d, --dotfiles
    use this flag if you want to exclude dotfiles
-e <PATTERN>, --exclude <PATTERN>
    string or regex to filter files which should not be checked
-i, --ignore-defaults
        will ignore default excludes, see README for details
-h, --help
    will print this help text
-l, --list-files
    will print all files which are checked to stdout
```


## Default ignores:

```
'vendor',
'node_modules',
'\.gif$',
'\.png$',
'\.bmp$',
'\.jpg$',
'\.svg$',
'\.ico$',
'\.lock$',
'\.eot$',
'\.woff$',
'\.woff2$',
'\.ttf$',
'\.bak$',
'\.bin$',
'\.min.js$',
'\.min.css$'
```

Suggestions are welcome!

## Additional Notes

I use semantic versioning so every breaking change will result in the increase of the major version.

If you encounter any bugs or anything else please open an issue with as many details as possible.

You should use the `-l` option after installing and configuring this tool to see if all files are
checked.

The `auto-fix` parameter puts a copy of every original file which gets fixed in `/tmp/editorconfig-checker.php/<filename>-<timestamp>-<sha1>`
