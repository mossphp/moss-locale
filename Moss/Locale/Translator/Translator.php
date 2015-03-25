<?php

/*
 * This file is part of the Moss Locale package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Locale\Translator;

/**
 * Translator class
 *
 * @package Moss Router
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Translator implements TranslatorInterface
{
    private $intervalRegexp = '({\s*(\-?\d+(\.\d+)?[\s*,\s*\-?\d+(\.\d+)?]*)\s*})|(?P<left_delimiter>[\[\]])\s*(?P<left>-Inf|\-?\d+(\.\d+)?)\s*,\s*(?P<right>\+?Inf|\-?\d+(\.\d+)?)\s*(?P<right_delimiter>[\[\]])';

    protected $locale;

    /**
     * @var DictionaryInterface[]
     */
    protected $dictionaries = [];

    protected $silent;

    /**
     * Constructor
     *
     * @param string                $locale
     * @param DictionaryInterface[] $dictionaries
     * @param bool                  $silent
     */
    public function __construct($locale, array $dictionaries, $silent = true)
    {
        $this->locale = $locale;
        $this->silent = (bool) $silent;

        foreach ($dictionaries as $dictionary) {
            $this->addDictionary($dictionary);
        }
    }

    /**
     * Sets default locale
     *
     * @param null|string $locale
     *
     * @return string
     */
    public function locale($locale = null)
    {
        if ($locale !== null) {
            $this->locale = $locale;
        }

        return $this->locale;
    }

    /**
     * Adds dictionary to translator
     *
     * @param DictionaryInterface $dictionary
     * @param null|int            $priority
     *
     * @return $this
     */
    public function addDictionary(DictionaryInterface $dictionary, $priority = null)
    {
        if ($priority === null) {
            $this->dictionaries[] = $dictionary;

            return $this;
        }

        array_splice($this->dictionaries, $priority, 0, array($dictionary));

        return $this;
    }

    /**
     * Returns dictionaries instances
     *
     * @return DictionaryInterface[]
     */
    public function dictionaries()
    {
        return $this->dictionaries;
    }

    /**
     * Returns localized message
     *
     * @param string $word
     * @param array  $placeholders
     *
     * @return string
     */
    public function translate($word, array $placeholders = [])
    {
        return $this->replacePlaceholders(
            $this->getText($word),
            $placeholders
        );
    }

    /**
     * Returns plural localized message
     * Input message with intervals eg.:
     * {0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf] There are many apples
     * The intervals follow the ISO 31-11 notation
     *
     * @param string $word
     * @param int    $count
     * @param array  $placeholders
     *
     * @return string
     */
    public function translatePlural($word, $count, array $placeholders = [])
    {
        $placeholders ['%count%'] = $count;
        $word = (string) $word;

        return $this->replacePlaceholders(
            $this->choose($this->getText($word), (int) $count),
            $placeholders
        );
    }

    /**
     * Returns word from first dictionary
     * If not in silent mode - throws exception when not found
     *
     * @param string $word
     *
     * @return null|string
     * @throws TranslatorException
     */
    protected function getText($word)
    {
        foreach ($this->dictionaries as $dictionary) {
            if (null !== $text = $dictionary->getText($word)) {
                return $text;
            }
        }

        if (!$this->silent) {
            throw new TranslatorException(sprintf('Unable to translate "%s" - missing translation for locale "%s"', $word, $this->locale));
        }

        return $word;
    }

    /**
     * Returns all translations from all dictionaries
     *
     * @return array
     */
    public function translations()
    {
        $translations = [];
        foreach ($this->dictionaries as $dictionary) {
            $translations = array_merge($translations, $dictionary->getTranslations());
        }

        return $translations;
    }

    /**
     * Replaces placeholders with passed values
     *
     * @param string $word
     * @param array  $placeholders
     *
     * @return string
     */
    protected function replacePlaceholders($word, array $placeholders)
    {
        if ($placeholders === array_values($placeholders)) {
            $keys = [];
            preg_match_all('/%[^%]+%/i', $word, $keys, \PREG_PATTERN_ORDER);
            $placeholders = array_combine(array_unique($keys[0]), $placeholders);
        }

        $keys = array_keys($placeholders);
        array_walk(
            $keys,
            function (&$key) {
                if (substr($key, 0, 1) === '%' && substr($key, -1, 1) === '%') {
                    return;
                }

                $key = '%' . $key . '%';
            }
        );

        return strtr(
            $word,
            array_combine($keys, $placeholders)
        );
    }

    /**
     * Chooses proper plural part from provided message
     *
     * @param string string $message
     * @param string int|float $number
     *
     * @return string
     */
    protected function choose($message, $number)
    {
        $parts = explode('|', $message);
        $explicitRules = [];
        $standardRules = [];
        foreach ($parts as $part) {
            $part = trim($part);

            if (preg_match('/^(?P<interval>' . $this->intervalRegexp . ')\s*(?P<message>.*?)$/x', $part, $matches)) {
                $explicitRules[$matches['interval']] = $matches['message'];
            } elseif (preg_match('/^\w\:\s*(.*?)$/', $part, $matches)) {
                $standardRules[] = $matches[1];
            } else {
                $standardRules[] = $part;
            }
        }

        foreach ($explicitRules as $interval => $m) {
            if ($this->test($number, $interval)) {
                return $m;
            }
        }

        return reset($standardRules);
    }

    /**
     * Checks if number is in set interval
     *
     * @param int|float $number
     * @param string    $interval
     *
     * @return bool
     */
    protected function test($number, $interval)
    {
        $interval = trim($interval);

        if (!preg_match('/^' . $this->intervalRegexp . '$/x', $interval, $matches)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid interval.', $interval));
        }

        if ($matches[1]) {
            foreach (explode(',', $matches[2]) as $n) {
                if ($number == $n) {
                    return true;
                }
            }
        } else {
            $leftNumber = $this->convertNumber($matches['left']);
            $rightNumber = $this->convertNumber($matches['right']);

            return ('[' === $matches['left_delimiter'] ? $number >= $leftNumber : $number > $leftNumber) && (']' === $matches['right_delimiter'] ? $number <= $rightNumber : $number < $rightNumber);
        }

        return false;
    }

    /**
     * Converts numbers from string to floats
     *
     * @param string $number
     *
     * @return float
     */
    protected function convertNumber($number)
    {
        if ('-Inf' === $number) {
            return log(0);
        } elseif ('Inf' === $number || 'Inf' === $number) {
            return -log(0);
        }

        return (float) $number;
    }
}
