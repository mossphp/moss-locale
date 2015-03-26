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
 * Locale interface
 *
 * @package Moss Locale
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface LocaleInterface
{
    /**
     * Returns locale name
     *
     * @return string
     */
    public function locale();

    /**
     * Returns language code from locale
     *
     * @return string Returns "en" from locale "en_US"
     */
    public function language();

    /**
     * Returns territory code from locale
     *
     * @return string Returns "US" from locale "en_US"
     */
    public function territory();

    /**
     * Returns currency sub unit
     *
     * @return int
     */
    public function currencySubUnit();

    /**
     * Returns timezone
     *
     * @return string
     */
    public function timezone();
}
