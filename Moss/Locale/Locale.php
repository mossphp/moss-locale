<?php

/*
 * This file is part of the Moss Locale package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Locale;

/**
 * Locale
 *
 * @package Moss Locale
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Locale implements LocaleInterface
{
    protected $locale;
    protected $currencySubUnit = 100;

    protected static $locales = [
        'az' => 'az_AZ',
        'bg' => 'bg_BG',
        'de' => 'de_DE',
        'en' => 'en_US',
        'es' => 'es_ES',
        'fi' => 'fi_FI',
        'fo' => 'fo_FO',
        'fr' => 'fr_FR',
        'hr' => 'hr_HR',
        'ht' => 'ht_HT',
        'hu' => 'hu_HU',
        'id' => 'id_ID',
        'is' => 'is_IS',
        'it' => 'it_IT',
        'lt' => 'lt_LT',
        'lv' => 'lv_LV',
        'mg' => 'mg_MG',
        'mk' => 'mk_MK',
        'mn' => 'mn_MN',
        'mt' => 'mt_MT',
        'nl' => 'nl_NL',
        'pl' => 'pl_PL',
        'pt' => 'pt_PT',
        'ro' => 'ro_RO',
        'ru' => 'ru_RU',
        'rw' => 'rw_RW',
        'sk' => 'sk_SK',
        'so' => 'so_SO',
        'th' => 'th_TH',
        'tr' => 'tr_TR',
        'uz' => 'uz_UZ',
    ];

    /**
     * @param string $locale
     * @param string $timezone
     * @param int    $currencySubUnit
     */
    public function __construct($locale, $timezone = null, $currencySubUnit = 100)
    {
        $this->locale($locale);
        $this->timezone($timezone);
        $this->currencySubUnit($currencySubUnit);
    }

    /**
     * Creates instance based on language
     *
     * @param string $lang
     *
     * @return Locale
     * @throws LocaleException
     */
    public function constructFromLang($lang)
    {
        if (!isset(static::$locales, $lang)) {
            throw new LocaleException('Unable to create locale for language ' . $lang);
        }

        return new self(static::$locales[$lang]);
    }

    /**
     * Returns locale name
     *
     * @param string $locale
     *
     * @return string
     * @throws \Moss\Locale\LocaleException
     */
    public function locale($locale = null)
    {
        if ($locale !== null) {
            if (!preg_match('/^[a-z]{2,3}[-_][A-Z]{2}$/', $locale)) {
                throw new LocaleException('Invalid locale format, expected something like "en_US" or "en-US"');
            }

            $this->locale = str_replace(['-', '_'], '_', $locale);
        }

        return $this->locale;
    }

    /**
     * Returns "en" from locale "en_US"
     *
     * @return string
     */
    public function language()
    {
        return substr($this->locale(), 0, strpos($this->locale, '_'));
    }

    /**
     * Returns "US" from locale "en_US"
     *
     * @return string
     */
    public function territory()
    {
        return substr($this->locale(), strpos($this->locale, '_') + 1);
    }

    /**
     * Returns currency sub unit
     *
     * @param int $subUnit
     *
     * @return int
     */
    public function currencySubUnit($subUnit = null)
    {
        if ($subUnit !== null) {
            $this->currencySubUnit = (int) $subUnit;
        }

        return $this->currencySubUnit;
    }

    /**
     * Returns timezone
     *
     * @param string $timezone
     *
     * @return string
     */
    public function timezone($timezone = null)
    {
        if ($timezone !== null) {
            date_default_timezone_set($timezone);
        }

        return date_default_timezone_get();
    }
}
