<?php

namespace Moss\Locale\Translator;


class ArrayDictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $dictionary = new ArrayDictionary('en_US');
        $this->assertEquals('en_US', $dictionary->getLocale());
    }

    public function testText()
    {
        $dictionary = new ArrayDictionary('en_US');
        $dictionary->setText('foo', 'Foo');
        $this->assertEquals('Foo', $dictionary->getText('foo'));
    }

    public function testTranslations()
    {
        $dictionary = new ArrayDictionary('en_US', ['foo' => 'Foo']);
        $dictionary->setText('bar', 'Bar');
        $this->assertEquals(['foo' => 'Foo', 'bar' => 'Bar'], $dictionary->getTranslations());
    }
}
