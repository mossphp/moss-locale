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
 * Dictionary for Translator
 *
 * @package Moss\Locale
 */
class Dictionary implements DictionaryInterface
{
    protected $language;
    protected $silentMode;
    protected $translations = array();

    /**
     * @param string $language
     * @param array  $translations
     * @param bool   $silentMode
     */
    public function __construct($language, $translations = array(), $silentMode = true)
    {
        $this->setLanguage($language);
        $this->silentMode = $silentMode;
        $this->setTranslations($translations);
    }

    /**
     * Returns current locale
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets locale
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLanguage($locale)
    {
        $this->language = $locale;

        return $this;
    }

    /**
     * Returns Translator for set word or if missing - word
     *
     * @param string $word
     *
     * @return string
     */
    public function getWord($word)
    {
        if (!array_key_exists($word, $this->translations)) {
            if ($this->silentMode) {
                return $word;
            }

            throw new \DomainException(sprintf('Translation is missing for "%s"', $word));
        }

        return $this->translations[$word];
    }

    /**
     * Adds new or updates entry to dictionary
     *
     * @param string      $word
     * @param string      $translation
     *
     * @return $this
     */
    public function setWord($word, $translation)
    {
        $this->translations[$word] = $translation;

        return $this;
    }

    /**
     * Gets Translators from reader
     *
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set Translators to writer
     *
     * @param array $translations
     *
     * @return $this
     */
    public function setTranslations(array $translations)
    {
        $this->translations = $translations;

        return $this;
    }
}
