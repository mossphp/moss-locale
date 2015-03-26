# Moss Locale

[![Build Status](https://travis-ci.org/mossphp/moss-locale.png?branch=master)](https://travis-ci.org/mossphp/moss-locale)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mossphp/moss-locale/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mossphp/moss-locale/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mossphp/moss-locale/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mossphp/moss-locale/?branch=master)

Basic tool for handling translations, formatting and stuff.

## Locale

Class that handles locale name, timezone and currency sub unit.

```php
	$locale = new Locale('en_GB', 'UTC', 100);
	
	echo $locale->locale(); // will print "en_GB" 
	$locale->locale('en_US'); // will change locale to en_US
	
	echo $locale->language(); // will print "en"
	echo $locale->territory(); // will print "GB"
	
	echo $locale->currencySubUnit(); // will print 100
	$locale->currencySubUnit(1000); // will change sub unit to 1000
	
	echo $locale->timezone(); // will print "UTC"
	$locale->timezone('Europe/Berlin'); will change default timezone (used by all date functions) to 'Europe/Berlin'
```


## Translator

Translator translates simple texts, singular and plural, with optional placeholders.

```php
	$translator = new Translator('en_GB', []);
```

Translator uses dictionaries as source of translations
Lower priority value is better - 0 means highest priority.

```php
	$dictionary = new ArrayDictionary('en_GB', ['dude' => 'laddy']);
	$translator = new Translator('en_GB', $dictionary);
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

Translator comes with `MultiDictionary` class that allows for combining multiple dictionaries as one.
For example, when default translations come from files, and they can be changed in database.
`MultiDictionary` allows for prioritizing dictionaries.
Usually you pass dictionaries trough constructor, and such case first dictionary with requested translation wins.
But there are situations where dictionaries are added after instantiation, just when adding new dictionary provide its priority.
If not - it will be added as last one.
Lower number is better - 0 is highest priority.
 
```php
	$multi = new MultiDictionary('en_GB');
	$multi->addDictionary($dictionary, 0);
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

