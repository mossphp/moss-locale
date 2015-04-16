<?php

/*
 * This file is part of the Moss Locale package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Locale\Formatter;

/**
 * Intl implementation of FormatterInterface
 *
 * @package Moss Locale
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class IntlFormatter implements FormatterInterface
{

    protected $locale;
    protected $currencySubUnit;
    protected $timezone;

    /**
     * @var \NumberFormatter
     */
    protected $numberFormatter;

    /**
     * @var \NumberFormatter
     */
    protected $currencyFormatter;

    /**
     * @var \IntlDateFormatter
     */
    protected $dateFormatter;

    /**
     * @var \IntlDateFormatter
     */
    protected $timeFormatter;

    /**
     * @var \IntlDateFormatter
     */
    protected $datetimeFormatter;

    /**
     * Constructor
     *
     * @param string $locale          locale name, e.g. en_CA
     * @param int    $currencySubUnit divisor that converts integer amounts back into decimals
     * @param string $timezone        eg. UTC
     */
    public function __construct($locale, $currencySubUnit = 100, $timezone = 'UTC')
    {
        $this->locale = $locale;
        $this->currencySubUnit = (int) $currencySubUnit;
        $this->timezone = $timezone;
    }

    /**
     * Formats number according to set locale
     *
     * @param float $number
     *
     * @return string
     */
    public function formatNumber($number)
    {
        if (!$this->numberFormatter) {
            $this->numberFormatter = new \NumberFormatter($this->locale, \NumberFormatter::DECIMAL);
        }

        return $this->numberFormatter->format($number);
    }

    /**
     * Formats currency according to set locale
     *
     * @param int $amount
     *
     * @return string
     */
    public function formatCurrency($amount)
    {
        if (!is_int($amount)) {
            throw new \InvalidArgumentException('Currency amounts have to be given as integer value');
        }

        if (!$this->currencyFormatter) {
            $this->currencyFormatter = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
        }

        return $this->currencyFormatter->format($amount / $this->currencySubUnit);
    }

    /**
     * Formats time according to set locale
     *
     * @param mixed $time
     *
     * @return string
     */
    public function formatTime($time = null)
    {
        if (!$this->timeFormatter) {
            $this->timeFormatter = $this->getIntlDateFormatter(\IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);
        }

        return $this->convertToDateTime($time, $this->timeFormatter);
    }

    /**
     * Formats date according to set locale
     *
     * @param mixed $date
     *
     * @return string
     */
    public function formatDate($date = null)
    {
        if (!$this->dateFormatter) {
            $this->dateFormatter = $this->getIntlDateFormatter(\IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
        }

        return $this->convertToDateTime($date, $this->dateFormatter);
    }

    /**
     * Formats date time according to set locale
     *
     * @param mixed $datetime
     *
     * @return string
     */
    public function formatDateTime($datetime = null)
    {
        if (!$this->datetimeFormatter) {
            $this->datetimeFormatter = $this->getIntlDateFormatter(\IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        }

        return $this->convertToDateTime($datetime, $this->datetimeFormatter);
    }

    /**
     * Creates instance of Intl date formatter
     *
     * @param $dateType
     * @param $timeType
     *
     * @return \IntlDateFormatter
     */
    protected function getIntlDateFormatter($dateType, $timeType)
    {
        return new \IntlDateFormatter(
            $this->locale,
            $dateType,
            $timeType,
            $this->timezone
        );
    }

    /**
     * @param mixed              $datetime
     * @param \IntlDateFormatter $formatter
     *
     * @return null|string
     */
    protected function convertToDateTime($datetime, \IntlDateFormatter $formatter)
    {
        if (empty($datetime)) {
            return null;
        }

        try {
            if (!is_object($datetime) || !$datetime instanceof \DateTime) {
                $datetime = new \DateTime((string) $datetime);
            }

            return $formatter->format($datetime);
        } catch (\Exception $e) {
            return null;
        }
    }
}
