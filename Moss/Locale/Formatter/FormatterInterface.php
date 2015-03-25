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
 * Formatter interface
 *
 * @package Moss Router
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface FormatterInterface
{

    /**
     * Formats number according to set locale
     *
     * @param float $number
     * @return string
     */
    public function formatNumber($number);

    /**
     * Formats currency according to set locale
     *
     * @param int $amount
     * @return string
     */
    public function formatCurrency($amount);

    /**
     * Formats time according to set locale
     *
     * @param \DateTime $datetime
     * @return string
     */
    public function formatTime(\DateTime $datetime);

    /**
     * Formats date according to set locale
     *
     * @param \DateTime $datetime
     * @return string
     */
    public function formatDate(\DateTime $datetime);

    /**
     * Formats date time according to set locale
     *
     * @param \DateTime $datetime
     * @return string
     */
    public function formatDateTime(\DateTime $datetime);
}
