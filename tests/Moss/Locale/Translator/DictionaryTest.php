<?php

namespace Moss\Locale\Translator;


class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testLanguage()
    {
        $dictionary = new Dictionary('en');
        $this->assertEquals('en', $dictionary->getLocale());
    }

    public function testWord()
    {
        $dictionary = new Dictionary('en');
        $dictionary->setText('foo', 'Foo');
        $this->assertEquals('Foo', $dictionary->getText('foo'));
    }

    public function testGetWordWithMissingTranslationWithSilentMode()
    {
        $dictionary = new Dictionary('en', array(), true);
        $this->assertNull($dictionary->getText('foo'));
    }

    public function testTranslations()
    {
        $dictionary = new Dictionary('en', array('foo' => 'Foo'));
        $dictionary->setText('bar', 'Bar');
        $this->assertEquals(array('foo' => 'Foo', 'bar' => 'Bar'), $dictionary->getTranslations());
    }
}
