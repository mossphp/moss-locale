# Moss Locale

Basic tool for handling localised stuff

## Locale

Class that handles locale name, timezone and currency sub unit.

```php
	$locale = new Locale('en_GB', 'UTC', 100);
```

To get current locale:
```php
	echo $locale->locale();
```

To set new locale:
```php
	$locale->locale('en_US');
```

To get language from locale:
```php
	$locale->language();
```

To get territory from locale:
```php
	$locale->territory();
```

To get currency sub unit (used as divisor/multiplier for converting amounts to integers):
```php
	echo $locale->currencySubUnit();
```

To set currency sub unit:
```php
	$locale->currencySubUnit(1000);
```

To get current timezone (by default, uses value from INI `date.timezone`)
```php
	echo $locale->timezone();
```

To set timezone (changes also value for default timezone used by all date/time functions):
```php
	$locale->timezone('UTC');
```

To set new timezone:

## Translator

Translator translates simple texts, singular and plural, with optional placeholders.

```php
	$translator = new Translator('en_GB', []);
```

As translation sources dictionaries are used. Translator can handle multiple sources at once with different priorities.
Lower priority value is better - 0 means highest priority.

```php
	$dictionary = new Dictionary('en_GB', ['dude' => 'laddy']);
	$translator->addDictionary($dictionary, 0);
```

Dictionaries are list of key-value pairs, where key is a word/sentence/identifier and is translated text.
Eg. EN to DE:
```php
[
	'There be %placeholder%' => 'dort %placeholder%',
	'welcome.string' => 'Hallo %name%!'
]
```

```php
	echo $translator->translate('There be %placeholder%', ['placeholder' => 'Drachen'])
	// prints dort Drachen
```

For plural translation additional syntax is used in dictionaries to describe intervals with proper translations.
Plural translations also support placeholders.
Intervals follow ISO 31-11 notation:

```
[, ]	[a, b]	closed interval in ℝ from a (included) to b (included)
], ]	]a, b]	left half-open interval in ℝ from a (excluded) to b (included)
[, [	[a, b[	right half-open interval in ℝ from a (included) to b (excluded)
], [	]a, b[	open interval in ℝ from a (excluded) to b (excluded)
```

```php
[
	'apple.count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf] There are many apples'
]
```

```php
	echo $translator->translatePlural('apple.count', $count)
	// prints There are no apples when $count = 0
	// prints There is one apple when $count = 1
	// prints There are %count% apples when $count > 1 && $count >= 19
	// prints There are many apples when $count >= 20
```

## Formatter

Formatter provides set of functions for formatting numbers, currencies and datetime values.

 * `::formatNumber($number)`
 * `::formatCurrency($amount)`
 * `::formatTime(\DateTime $datetime)`
 * `::formatDate(\DateTime $datetime)`
 * `::formatDateTime(\DateTime $datetime)`

Locale comes with two formatter implementations: `Intl` that requires extension and plain php formatter.

`PlainFormatter` can be configured to meet your needs:

 ```php
    $formatter = new PlainFormatter('en_GB', 100, 'UTC', [
        'number' = '#,##0.###',
        'currency' = '#,##0.##£',
        'date' = 'n/j/y',
        'time' = 'g:i A',
        'datetime' = 'n/j/y, g:i A'
    ]);
 ```

