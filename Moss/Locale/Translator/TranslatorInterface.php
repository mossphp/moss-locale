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
 * Translator interface
 *
 * @package Moss Locale
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface TranslatorInterface
{
    /**
     * Returns locale
     *
     * @return string
     */
    public function locale();

    /**
     * Returns localized message
     *
     * @param string $word
     * @param array  $placeholders
     *
     * @return string
     */
    public function trans($word, array $placeholders = []);

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
    public function transChoice($word, $count, array $placeholders = []);

    /**
     * Returns dictionary instance
     *
     * @param DictionaryInterface $dictionary
     *
     * @return array
     */
    public function dictionary(DictionaryInterface $dictionary = null);
}
