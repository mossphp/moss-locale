<?php

namespace Moss\Locale\Translator;


class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testLanguage()
    {
        $dictionary = new Dictionary('en');
        $this->assertEquals('en', $dictionary->getLanguage());
    }

    public function testWord()
    {
        $dictionary = new Dictionary('en');
        $dictionary->setWord('foo', 'Foo');
        $this->assertEquals('Foo', $dictionary->getWord('foo'));
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Translation is missing for
     */
    public function testGetWordWithMissingTranslationWithoutSilentMode()
    {
        $dictionary = new Dictionary('en', array(), false);
        $dictionary->getWord('foo');
    }

    public function testGetWordWithMissingTranslationWithSilentMode()
    {
        $dictionary = new Dictionary('en', array(), true);
        $this->assertEquals('foo', $dictionary->getWord('foo'));
    }

    public function testTranslations()
    {
        $dictionary = new Dictionary('en', array('foo' => 'Foo'));
        $dictionary->setWord('bar', 'Bar');
        $this->assertEquals(array('foo' => 'Foo', 'bar' => 'Bar'), $dictionary->getTranslations());
    }
}
