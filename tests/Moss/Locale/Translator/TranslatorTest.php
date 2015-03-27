<?php

namespace Moss\Locale\Translator;


class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');

        $translator = new Translator('en_US', $dictionary);
        $this->assertEquals('en_US', $translator->locale());
    }

    public function testDictionary()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB = clone $dictionaryA;

        $translator = new Translator('en_US', $dictionaryA);
        $this->assertEquals($dictionaryA, $translator->dictionary());

        $translator->dictionary($dictionaryB);
        $this->assertEquals($dictionaryB, $translator->dictionary());
    }

    public function testTranslationMissingWithSilentMode()
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');

        $translator = new Translator('en_US', $dictionary, true);
        $this->assertEquals('foo', $translator->trans('foo'));
    }

    /**
     * @expectedException \Moss\Locale\Translator\TranslatorException
     * @expectedExceptionMessage Unable to translate "foo" - missing translation for locale "en_US"
     */
    public function testMissingTranslationWithoutSilentMode()
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');

        $translator = new Translator('en_US', $dictionary, false);
        $translator->trans('foo');
    }

    /**
     * @dataProvider dictionaryProvider
     */
    public function testTranslation($key, $val)
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getText')->with($key)->will($this->returnValue($val));

        $translator = new Translator('en_US', $dictionary);
        $this->assertEquals($val, $translator->trans($key));
    }

    public function dictionaryProvider()
    {
        return [
            ['foo', ''],
            ['foo', 'Foo'],
            ['bar', 'Bar'],
            ['yada', 'Yada']
        ];
    }

    /**
     * @dataProvider placeholderProvider
     */
    public function testTranslationWithPlaceHolders($word, $placeholders, $expected)
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getText')->will($this->returnValue($word));

        $translator = new Translator('en_US', $dictionary);
        $this->assertEquals($expected, $translator->trans($word, $placeholders));
    }

    public function placeholderProvider()
    {
        return [
            [
                '%some% blah blah',
                ['some' => 'Foo'],
                'Foo blah blah'
            ],
            [
                '%some% %sample%',
                ['some' => 'Foo', 'sample' => 'Bar'],
                'Foo Bar'
            ],
            [
                '%some% %some%',
                ['some' => 'Yada'],
                'Yada Yada'
            ],
        ];
    }

    public function testPluralTranslationMissingWithSilentMode()
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');

        $translator = new Translator('en_US', $dictionary, true);
        $this->assertEquals('foo', $translator->transChoice('foo', 1));
    }

    /**
     * @expectedException  \Moss\Locale\Translator\TranslatorException
     * @expectedExceptionMessage Unable to translate "foo" - missing translation for locale "en_US"
     */
    public function testPluralTranslationWithoutSilentMode()
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');

        $translator = new Translator('en_US', $dictionary, false);
        $translator->transChoice('foo', 1);
    }

    /**
     * @dataProvider pluralProvider
     */
    public function testPluralTranslation($num, $expected)
    {
        $word = '[-Inf, 0[ There are not enough %name%s|{0} There are no %name%|{1} There is one %name%|]1,19] There are %count% %name%s|[20,Inf] There are many %name%s';

        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getText')->will($this->returnValue($word));

        $translator = new Translator('en_US', $dictionary);
        $result = $translator->transChoice($word, $num, ['name' => 'Foo']);

        $this->assertEquals($expected, $result);
    }

    public function pluralProvider()
    {
        return [
            [-10, 'There are not enough Foos'],
            [0, 'There are no Foo'],
            [1, 'There is one Foo'],
            [2, 'There are 2 Foos'],
            [5, 'There are 5 Foos'],
            [10, 'There are 10 Foos'],
            [19, 'There are 19 Foos'],
            [20, 'There are many Foos'],
            [30, 'There are many Foos'],
            [99, 'There are many Foos'],
        ];
    }
}
