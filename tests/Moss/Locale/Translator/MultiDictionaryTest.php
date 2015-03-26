<?php

namespace Moss\Locale\Translator;


class MultiDictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $dictionary = new MultiDictionary('en_US');
        $this->assertEquals('en_US', $dictionary->getLocale());
    }

    /**
     * @dataProvider translationProvider
     */
    public function testText($word, $expected)
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->any())->method('getText')->will(
            $this->returnValueMap(
                [
                    ['foo', 'Foo'],
                    ['bar', null],
                ]
            )
        );

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->any())->method('getText')->will(
            $this->returnValueMap(
                [
                    ['foo', null],
                    ['bar', 'Bar'],
                ]
            )
        );

        $dictionary = new MultiDictionary('en_US', [$dictionaryA, $dictionaryB]);
        $dictionary->setText('yada', 'Yada');
        $this->assertEquals($expected, $dictionary->getText($word));
    }

    public function translationProvider()
    {
        return [
            ['foo', 'Foo'],
            ['bar', 'Bar'],
            ['yada', 'Yada'],
            ['dakadaka', null],
        ];
    }

    public function testTranslations()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->any())->method('getTranslations')->with()->will($this->returnValue(['foo' => 'Foo']));

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->any())->method('getTranslations')->with()->will($this->returnValue(['bar' => 'Bar']));

        $dictionary = new MultiDictionary('en_US', [$dictionaryA, $dictionaryB]);
        $this->assertEquals(['foo' => 'Foo', 'bar' => 'Bar'], $dictionary->getTranslations());
    }
}
