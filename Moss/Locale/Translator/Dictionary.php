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
 * Basic array dictionary
 *
 * @package Moss Router
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Dictionary implements DictionaryInterface
{
    protected $locale;
    protected $translations = [];

    /**
     * @param string $locale
     * @param array $translations
     */
    public function __construct($locale, $translations = [])
    {
        $this->locale = $locale;
        $this->setTranslations($translations);
    }

    /**
     * Returns current locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Returns translation for set word or if missing - word
     *
     * @param string $word
     * @return string
     */
    public function getText($word)
    {
        return array_key_exists($word, $this->translations) ? $this->translations[$word] : null;
    }

    /**
     * Adds new or updates entry to dictionary
     *
     * @param string $word
     * @param string $text
     * @return $this
     */
    public function setText($word, $text)
    {
        $this->translations[$word] = $text;

        return $this;
    }

    /**
     * Gets translations
     *
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set translations
     *
     * @param array $translations
     * @return $this
     */
    public function setTranslations(array $translations)
    {
        $this->translations = $translations;

        return $this;
    }
}
