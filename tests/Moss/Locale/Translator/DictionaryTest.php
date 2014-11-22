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
        $dictionary->set('foo', 'Foo');
        $this->assertEquals('Foo', $dictionary->getWord('Foo'));
    }

    public function testTranslations()
    {
        $dictionary = new Dictionary('en', array('foo' => 'Foo'));
        $dictionary->set('bar', 'Bar');
        $this->assertEquals(array('foo' => 'Foo', 'bar' => 'Bar'), $dictionary->getTranslations());
    }
}
