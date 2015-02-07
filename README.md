# KSS Plugin for Pattern Lab PHP

The KSS plugin adds support for parsing and displaying [Knyle Style Sheets](http://warpspire.com/kss/) within Pattern Lab PHP. KSS is "documentation for any flavor of CSS that you’ll love to write. Human readable, machine parsable, and easy to remember." The KSS plugin uses the [scan/kss-php package](https://packagist.org/packages/scan/kss-php).

## Requirements

The KSS plugin currently only works with the [Mustache PatternEngine](https://github.com/pattern-lab/patternengine-php-mustache) for Pattern Lab PHP. Twig support will be coming soon.

## Installation

Pattern Lab PHP uses [Composer](https://getcomposer.org/) to manage project dependencies. To install the KSS plugin run:

    composer require pattern-lab/plugin-kss

## Usage

Simply use the [KSS syntax](http://warpspire.com/kss/syntax/) in your CSS, SCSS, or LESS files.

### The Styleguide Field

The styleguide field is how you connect your sections of KSS documentation with specific patterns. For example, this block of KSS would be associated with the `atoms-star-button` pattern based on the `Styleguide` field.

```
/*
A button suitable for giving stars to someone.

:hover             - Subtle hover highlight.
.stars-given       - A highlight indicating you've already given a star.
.stars-given:hover - Subtle hover highlight on top of stars-given styling.
.disabled          - Dims the button to indicate it cannot be used.

Styleguide atoms-star-button
*/
```