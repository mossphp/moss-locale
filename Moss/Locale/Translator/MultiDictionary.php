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
 * Dictionary that can group multiple dictionaries as one
 *
 * @package Moss Locale
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class MultiDictionary extends ArrayDictionary implements DictionaryInterface
{
    protected $locale;
    protected $translations = [];

    /**
     * @var array|DictionaryInterface[]
     */
    protected $dictionaries = [];

    /**
     * Constructor
     *
     * @param       $locale
     * @param array $dictionaries
     * @param array $translations
     */
    public function __construct($locale, array $dictionaries = [], array $translations = [])
    {
        $this->locale = $locale;
        $this->setTranslations($translations);

        foreach ($dictionaries as $dictionary) {
            $this->addDictionary($dictionary);
        }
    }

    /**
     * Adds dictionary to collection
     *
     * @param DictionaryInterface $dictionary
     *
     * @return $this
     */
    public function addDictionary(DictionaryInterface $dictionary)
    {
        $this->dictionaries[] = $dictionary;

        return $this;
    }

    /**
     * Returns translation for set word or if missing - word
     *
     * @param string $word
     *
     * @return string
     */
    public function getText($word)
    {
        if (null !== $text = parent::getText($word)) {
            return $text;
        }

        foreach ($this->dictionaries as $dictionary) {
            if (null !== $text = $dictionary->getText($word)) {
                return $text;
            }
        }

        return null;
    }

    /**
     * Gets translations
     *
     * @return array
     */
    public function getTranslations()
    {
        $translations = $this->translations;
        foreach ($this->dictionaries as $dictionary) {
            $translations = array_merge($translations, $dictionary->getTranslations());
        }

        return $translations;
    }
}
