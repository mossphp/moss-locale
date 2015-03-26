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
 * Plain php formatter interface implementation
 *
 * @package Moss Locale
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class PlainFormatter implements FormatterInterface
{

    protected $locale;
    protected $currencySubUnit;
    protected $timezone;

    protected $numberPattern = ['decimals' => 3, 'point' => '.', 'separator' => ','];
    protected $currencyPattern = ['sign' => '${amount}', 'decimals' => 2, 'point' => '.', 'separator' => ','];
    protected $datePattern = 'n/j/y';
    protected $timePattern = 'g:i A';
    protected $datetimePattern = 'n/j/y, g:i A';

    /**
     * Constructor
     *
     * @param string $locale          locale name, e.g. en_CA
     * @param int    $currencySubUnit divisor that converts integer amounts back into decimals
     * @param string $timezone        eg. UTC
     * @param array  $patterns        formatting patterns overriding default ones
     */
    public function __construct($locale, $currencySubUnit = 100, $timezone = 'UTC', array $patterns = [])
    {
        $this->locale = $locale;
        $this->currencySubUnit = (int) $currencySubUnit;
        $this->timezone = $timezone;

        foreach ($patterns as $formatter => $pattern) {
            $this->setPattern($formatter, $pattern);
        }
    }

    /**
     * Sets pattern for formatter
     * Numeric pattern example #,##0.###
     * Currency pattern example $#,##0.##
     * Time, date and date time use same pattern as date().
     *
     * Pattern names: number, currency, time, date, dateTome
     *
     * @param string $formatter
     * @param string $pattern
     *
     * @return $this
     * @throws FormatterException
     */
    public function setPattern($formatter, $pattern)
    {
        switch ($formatter) {
            case 'number':
                $this->numberPattern = $this->buildNumberPattern($pattern);
                break;
            case 'currency':
                $this->currencyPattern = $this->buildCurrencyPattern($pattern);
                break;
            case 'date':
                $this->datePattern = $pattern;
                break;
            case 'time':
                $this->timePattern = $pattern;
                break;
            case 'dateTime':
                $this->datetimePattern = $pattern;
                break;
            default:
                throw new FormatterException(sprintf('Unknown formatter "%s"', $formatter));
        }

        return $this;
    }

    /**
     * Builds numeric pattern from string eg. #,##0.###
     *
     * @param string $pattern
     *
     * @return array
     */
    protected function buildNumberPattern($pattern)
    {
        preg_match_all('/#*(?P<separator>.)(#+0)(?P<point>.)(?P<decimals>#*)/i', $pattern, $matches, PREG_SET_ORDER);
        $output = [
            'decimals' => strlen($matches[0]['decimals']),
            'point' => $matches[0]['point'],
            'separator' => $matches[0]['separator']
        ];

        return $output;
    }

    /**
     * Builds currency pattern from string eg. $#,##0.##
     *
     * @param string $pattern
     *
     * @return array
     */
    protected function buildCurrencyPattern($pattern)
    {
        $output = $this->buildNumberPattern($pattern);
        $output['sign'] = preg_replace('/^([^#]*)#(.+)#([^#]*)$/i', '$1{amount}$3', $pattern);

        return $output;
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
        return number_format(
            $number,
            $this->numberPattern['decimals'],
            $this->numberPattern['point'],
            $this->numberPattern['separator']
        );
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

        return str_replace(
            '{amount}',
            number_format(
                $amount / $this->currencySubUnit,
                $this->currencyPattern['decimals'],
                $this->currencyPattern['point'],
                $this->currencyPattern['separator']
            ),
            $this->currencyPattern['sign']
        );
    }

    /**
     * Formats time according to set locale
     *
     * @param \DateTime $datetime
     *
     * @return string
     */
    public function formatTime(\DateTime $datetime)
    {
        return $datetime->format($this->timePattern);
    }

    /**
     * Formats date according to set locale
     *
     * @param \DateTime $datetime
     *
     * @return string
     */
    public function formatDate(\DateTime $datetime)
    {
        return $datetime->format($this->datePattern);
    }

    /**
     * Formats date time according to set locale
     *
     * @param \DateTime $datetime
     *
     * @return string
     */
    public function formatDateTime(\DateTime $datetime)
    {
        return $datetime->format($this->datetimePattern);
    }
}
