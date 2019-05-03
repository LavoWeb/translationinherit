# Translation Inherit

Translation inherit/locale fallback for Magento 2

## Installation

```
composer require lavoweb/translationinherit
php bin/magento setup:upgrade
```

## Configuration

Stores => Configuration => General => Locale Options => Parent Locale

If parent locale is set and haven't the same value than current locale, they'll be merged.