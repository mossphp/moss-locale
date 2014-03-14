<?php
namespace Moss\Locale;


class LocaleTest extends \PHPUnit_Framework_TestCase
{

    public function testLocale()
    {
        $locale = new Locale('pl');
        $this->assertEquals('en', $locale->locale('en'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetSet($arr, $word, $expected, $setLocale = 'pl', $getLocale = 'pl')
    {
        $locale = new Locale($setLocale);
        $locale->set($arr, $setLocale);
        $this->assertEquals($expected, $locale->get($word, $getLocale));
    }

    public function dataProvider()
    {
        return array(
            array(array(), 'foo', 'foo'),
            array(array('foo' => 'Foo foo'), 'foo', 'Foo foo'),
            array(array('foo' => 'Foo foo'), 'foo', 'Foo foo', 'en', 'en'),
            array(array('foo' => 'Foo foo'), 'foo', 'foo', 'pl', 'en')
        );
    }

    /**
     * @dataProvider paramProvider
     */
    public function testTrans($key, $val)
    {
        $locale = new Locale('pl');
        $this->assertEquals('Some sample '.$val, $locale->trans('Some sample '.$key, array($key => $val)));
    }

    public function paramProvider()
    {
        return array(
            array('%some%', 'Foo'),
            array('%sample%', 'Bar'),
            array('%name%', 'Yada'),
        );
    }

    /**
     * @dataProvider choiceProvider
     */
    public function testTransChoice($num, $expected)
    {
        $word = '{0} There are no %name%|{1} There is one %name%|]1,19] There are %count% %name%s|[20,Inf] There are many %name%s';

        $locale = new Locale('pl');
        $result = $locale->transChoice($word, $num, array('%name%' => 'Foo'));

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
