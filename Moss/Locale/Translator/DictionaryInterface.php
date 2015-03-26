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
 * Dictionary interface
 *
 * @package Moss Router
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface DictionaryInterface
{
    /**
     * Returns current locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Returns translation as associative array
     *
     * @param string $word
     * @return string
     */
    public function getText($word);

    /**
     * Adds new or updates entry to dictionary
     *
     * @param string $word
     * @param string $text
     * @return $this
     */
    public function setText($word, $text);

    /**
     * Gets Translators from reader
     *
     * @return array
     */
    public function getTranslations();

    /**
     * Set Translators to writer
     *
     * @param array $translations
     * @return $this
     */
    public function setTranslations(array $translations);
}
