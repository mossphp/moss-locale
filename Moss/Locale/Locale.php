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
 * @package Moss Router
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Locale implements LocaleInterface
{
    protected $locale;
    protected $currencySubUnit = 100;

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
     * Returns locale name
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
        return substr($this->locale(), 0, 2);
    }

    /**
     * Returns "US" from locale "en_US"
     *
     * @return string
     */
    public function territory()
    {
        return substr($this->locale(), 3);
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
