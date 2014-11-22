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
 * Translator implementation
 *
 * @package Moss\Locale
 */
class Translator implements TranslatorInterface
{
    private $intervalRegexp = '({\s*(\-?\d+(\.\d+)?[\s*,\s*\-?\d+(\.\d+)?]*)\s*})|(?P<left_delimiter>[\[\]])\s*(?P<left>-Inf|\-?\d+(\.\d+)?)\s*,\s*(?P<right>\+?Inf|\-?\d+(\.\d+)?)\s*(?P<right_delimiter>[\[\]])';

    protected $language;
    protected $dictionary;

    /**
     * Constructor
     *
     * @param string              $language
     * @param DictionaryInterface $dictionary
     */
    public function __construct($language, DictionaryInterface $dictionary)
    {
        $this->language($language);
        $this->dictionary = $dictionary;
    }

    /**
     * Sets default locale
     *
     * @param null|string $locale
     *
     * @return string
     */
    public function language($locale = null)
    {
        if ($locale !== null) {
            $this->language = $locale;
        }

        return $this->language;
    }

    /**
     * Returns localized message
     *
     * @param string $word
     * @param array  $placeholders
     *
     * @return string
     */
    public function trans($word, array $placeholders = [])
    {
        return strtr(
            $this->dictionary->getWord($word),
            $this->preparePlaceholders($placeholders)
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
    public function transChoice($word, $count, array $placeholders = [])
    {
        $placeholders ['%count%'] = $count;
        $word = (string) $word;

        return strtr(
            $this->choose($this->dictionary->getWord($word), (int) $count),
            $this->preparePlaceholders($placeholders)
        );
    }

    /**
     * Fills placeholder keys with %
     *
     * @param array $placeholders
     *
     * @return array
     */
    protected function preparePlaceholders(array $placeholders)
    {
        if (empty($placeholders)) {
            return array();
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

        return array_combine($keys, array_values($placeholders));
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
        $explicitRules = array();
        $standardRules = array();
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

        $position = $this->getPluralRule($number, $this->language);

        if (!isset($standardRules[$position])) {
            if (1 === count($parts) && isset($standardRules[0])) {
                return $standardRules[0];
            }

            throw new \InvalidArgumentException(sprintf('Unable to choose a translation for "%s" with locale "%s"', $message));
        }

        return $standardRules[$position];
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

    /**
     * Returns the plural position to use for the given locale and number.
     *
     * @param integer $number The number
     * @param string  $locale The locale
     *
     * @return integer The plural position
     */
    protected function getPluralRule($number, $locale)
    {
        if ("pt_BR" == $locale) {
            $locale = "xbr";
        }

        if (strlen($locale) > 3) {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
        }

        /*
        * The plural rules are derived from code of the Zend Framework (2010-09-25),
        * which is subject to the new BSD license (http://framework.zend.com/license/new-bsd).
        * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
        */
        switch ($locale) {
            case 'bo':
            case 'dz':
            case 'id':
            case 'ja':
            case 'jv':
            case 'ka':
            case 'km':
            case 'kn':
            case 'ko':
            case 'ms':
            case 'th':
            case 'tr':
            case 'vi':
            case 'zh':
                return 0;
                break;

            case 'af':
            case 'az':
            case 'bn':
            case 'bg':
            case 'ca':
            case 'da':
            case 'de':
            case 'el':
            case 'en':
            case 'eo':
            case 'es':
            case 'et':
            case 'eu':
            case 'fa':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
            case 'gl':
            case 'gu':
            case 'ha':
            case 'he':
            case 'hu':
            case 'is':
            case 'it':
            case 'ku':
            case 'lb':
            case 'ml':
            case 'mn':
            case 'mr':
            case 'nah':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'om':
            case 'or':
            case 'pa':
            case 'pap':
            case 'ps':
            case 'pt':
            case 'so':
            case 'sq':
            case 'sv':
            case 'sw':
            case 'ta':
            case 'te':
            case 'tk':
            case 'ur':
            case 'zu':
                return ($number == 1) ? 0 : 1;

            case 'am':
            case 'bh':
            case 'fil':
            case 'fr':
            case 'gun':
            case 'hi':
            case 'ln':
            case 'mg':
            case 'nso':
            case 'xbr':
            case 'ti':
            case 'wa':
                return (($number == 0) || ($number == 1)) ? 0 : 1;

            case 'be':
            case 'bs':
            case 'hr':
            case 'ru':
            case 'sr':
            case 'uk':
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);

            case 'cs':
            case 'sk':
                return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);

            case 'ga':
                return ($number == 1) ? 0 : (($number == 2) ? 1 : 2);

            case 'lt':
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);

            case 'sl':
                return ($number % 100 == 1) ? 0 : (($number % 100 == 2) ? 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3));

            case 'mk':
                return ($number % 10 == 1) ? 0 : 1;

            case 'mt':
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3));

            case 'lv':
                return ($number == 0) ? 0 : ((($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2);

            case 'pl':
                return ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2);

            case 'cy':
                return ($number == 1) ? 0 : (($number == 2) ? 1 : ((($number == 8) || ($number == 11)) ? 2 : 3));

            case 'ro':
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);

            case 'ar':
                return ($number == 0) ? 0 : (($number == 1) ? 1 : (($number == 2) ? 2 : ((($number >= 3) && ($number <= 10)) ? 3 : ((($number >= 11) && ($number <= 99)) ? 4 : 5))));

            default:
                return 0;
        }
    }


}
