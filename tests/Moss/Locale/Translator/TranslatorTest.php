<?php

namespace Moss\Locale\Translator;


class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dictionaryProvider
     */
    public function testTrans($key, $val)
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getWord')->with($key)->will($this->returnValue($val));

        $Locale = new Translator('en', $dictionary);
        $this->assertEquals($val, $Locale->trans($key));
    }

    public function dictionaryProvider()
    {
        return array(
            array('foo', 'Foo'),
            array('bar', 'Bar'),
            array('yada', 'Yada')
        );
    }

    /**
     * @dataProvider placeholderProvider
     */
    public function testTransWithPlaceHolders($word, $placeholders, $expected)
    {
        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getWord')->will($this->returnValue($word));

        $Locale = new Translator('en', $dictionary);
        $this->assertEquals($expected, $Locale->trans($word, $placeholders));
    }

    public function placeholderProvider()
    {
        return array(
            array(
                '%some% blah blah',
                array('some' => 'Foo'),
                'Foo blah blah'
            ),
            array(
                '%some% %sample%',
                array('some' => 'Foo', 'sample' => 'Bar'),
                'Foo Bar'
            ),
            array(
                '%some% %some%',
                array('some' => 'Yada'),
                'Yada Yada'
            ),
        );
    }

    /**
     * @dataProvider choiceProvider
     */
    public function testTransChoice($num, $expected)
    {
        $word = '{0} There are no %name%|{1} There is one %name%|]1,19] There are %count% %name%s|[20,Inf] There are many %name%s';

        $dictionary = $this->getMock('\Moss\Locale\Translator\DictionaryInterface');
        $dictionary->expects($this->any())->method('getWord')->will($this->returnValue($word));

        $locale = new Translator('en', $dictionary);
        $result = $locale->transChoice($word, $num, ['name' => 'Foo']);

        $this->assertEquals($expected, $result);
    }

    public function choiceProvider()
    {
        return array(
            array(0, 'There are no Foo'),
            array(1, 'There is one Foo'),
            array(2, 'There are 2 Foos'),
            array(5, 'There are 5 Foos'),
            array(10, 'There are 10 Foos'),
            array(19, 'There are 19 Foos'),
            array(20, 'There are many Foos'),
            array(30, 'There are many Foos'),
            array(99, 'There are many Foos'),
        );
    }
}
