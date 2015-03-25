<?php

namespace Moss\Locale\Translator;


class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $translator = new Translator('en_US', []);
        $this->assertEquals('en_US', $translator->locale());
    }

    public function testDictionaries()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB = clone $dictionaryA;
        $dictionaryC = clone $dictionaryA;

        $translator = new Translator('en_US', [$dictionaryA]);
        $translator->addDictionary($dictionaryB);
        $translator->addDictionary($dictionaryC);

        $this->assertEquals([$dictionaryA, $dictionaryB, $dictionaryC], $translator->dictionaries());
    }

    public function testTranslationMissingWithSilentMode()
    {
        $translator = new Translator('en_US', []);
        $this->assertEquals('foo', $translator->translate('foo'));
    }

    /**
     * @expectedException  \Moss\Locale\Translator\TranslatorException
     * @expectedExceptionMessage Unable to translate "foo" - missing translation for locale "en_US"
     */
    public function testMissingTranslationWithoutSilentMode()
    {
        $translator = new Translator('en_US', [], false);
        $translator->translate('foo');
    }

    /**
     * @dataProvider dictionaryProvider
     */
    public function testTranslation($key, $val)
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getText')->with($key)->will($this->returnValue($val));

        $translator = new Translator('en_US', [$dictionary]);
        $this->assertEquals($val, $translator->translate($key));
    }

    public function dictionaryProvider()
    {
        return [
            ['foo', 'Foo'],
            ['bar', 'Bar'],
            ['yada', 'Yada']
        ];
    }

    public function testTranslationWithMultipleDictionaries()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->once())->method('getText')->with('foo')->will($this->returnValue(null));

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->once())->method('getText')->with('foo')->will($this->returnValue('Foo'));

        $translator = new Translator('en_US', []);
        $translator->addDictionary($dictionaryA);
        $translator->addDictionary($dictionaryB);
        $this->assertEquals('Foo', $translator->translate('foo'));
    }

    public function testTranslationWithMultipleDictionariesWithPriority()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->never())->method('getText')->with('foo')->will($this->returnValue(null));

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->once())->method('getText')->with('foo')->will($this->returnValue('Foo'));

        $translator = new Translator('en_US', []);
        $translator->addDictionary($dictionaryA);
        $translator->addDictionary($dictionaryB, 0);
        $this->assertEquals('Foo', $translator->translate('foo'));
    }

    /**
     * @dataProvider placeholderProvider
     */
    public function testTranslationWithPlaceHolders($word, $placeholders, $expected)
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getText')->will($this->returnValue($word));

        $translator = new Translator('en_US', [$dictionary]);
        $this->assertEquals($expected, $translator->translate($word, $placeholders));
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
        $translator = new Translator('en_US', []);
        $this->assertEquals('foo', $translator->translatePlural('foo', 1));
    }

    /**
     * @expectedException  \Moss\Locale\Translator\TranslatorException
     * @expectedExceptionMessage Unable to translate "foo" - missing translation for locale "en_US"
     */
    public function testPluralTranslationWithoutSilentMode()
    {
        $translator = new Translator('en_US', [], false);
        $translator->translatePlural('foo', 1);
    }

    /**
     * @dataProvider pluralProvider
     */
    public function testPluralTranslation($num, $expected)
    {
        $word = '[-Inf, 0[ There are not enough %name%s|{0} There are no %name%|{1} There is one %name%|]1,19] There are %count% %name%s|[20,Inf] There are many %name%s';

        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getText')->will($this->returnValue($word));

        $translator = new Translator('en_US', [$dictionary]);
        $result = $translator->translatePlural($word, $num, ['name' => 'Foo']);

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

    public function testPluralTranslationWithMultipleDictionaries()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->once())->method('getText')->with('foo')->will($this->returnValue(null));

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->once())->method('getText')->with('foo')->will($this->returnValue('Foo'));

        $translator = new Translator('en_US', []);
        $translator->addDictionary($dictionaryA);
        $translator->addDictionary($dictionaryB);
        $this->assertEquals('Foo', $translator->translatePlural('foo', 0));
    }

    public function testPluralTranslationWithMultipleDictionariesWithPriority()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->never())->method('getText')->with('foo')->will($this->returnValue(null));

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->once())->method('getText')->with('foo')->will($this->returnValue('Foo'));

        $translator = new Translator('en_US', []);
        $translator->addDictionary($dictionaryA);
        $translator->addDictionary($dictionaryB, 0);
        $this->assertEquals('Foo', $translator->translatePlural('foo', 0));
    }

    public function testTranslations()
    {
        $dictionaryA = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryA->expects($this->any())->method('getTranslations')->will($this->returnValue(['foo' => 'Foo', 'yada' => 'Yada']));

        $dictionaryB = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionaryB->expects($this->any())->method('getTranslations')->will($this->returnValue(['bar' => 'Bar', 'yada' => 'Yada']));

        $translator = new Translator('en_US', []);
        $translator->addDictionary($dictionaryA);
        $translator->addDictionary($dictionaryB);

        $expected = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'yada' => 'Yada'
        ];

        $this->assertEquals($expected, $translator->translations());
    }
}
