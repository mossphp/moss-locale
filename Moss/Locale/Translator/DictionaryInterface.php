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
 * Translators Dictionary Interface
 *
 * @package Moss\Locale
 */
interface DictionaryInterface
{
    /**
     * Returns current locale
     *
     * @return string
     */
    public function getLanguage();

    /**
     * Sets locale
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLanguage($locale);

    /**
     * Returns Translator for set word or if missing - word
     *
     * @param string $word
     *
     * @return string
     */
    public function getWord($word);

    /**
     * Adds new or updates entry to dictionary
     *
     * @param string      $word
     * @param string      $translation
     *
     * @return $this
     */
    public function set($word, $translation);

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
     *
     * @return $this
     */
    public function setTranslations(array $translations);
}
