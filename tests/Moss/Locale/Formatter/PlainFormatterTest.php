<?php

/*
* This file is part of the moss-locale package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Moss\Locale\Formatter;


class PlainFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Moss\Locale\Formatter\FormatterException
     * @expectedExceptionMessage Unknown formatter "foo"
     */
    public function testInvalidFormatterName()
    {
        new PlainFormatter('en_US', 100, 'UTC', ['foo' => 'bar']);
    }

    /**
     * @dataProvider numberPatternProvider
     */
    public function testFormatNumber($pattern, $expected)
    {
        $formatter = new PlainFormatter('en_US', 100, 'UTC', ['number' => $pattern]);
        $this->assertEquals($expected, $formatter->formatNumber(123456.789));
    }

    public function numberPatternProvider()
    {
        return [
            ['#,##0.###', '123,456.789'],
            ['# ##0,##', '123 456,79']
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Currency amounts have to be given as integer value
     */
    public function testCurrencyFormatterNotInt()
    {
        $formatter = new PlainFormatter('en_US', 100, 'UTC');
        $formatter->formatCurrency(1234567.89);
    }

    /**
     * @dataProvider currencyPatternProvider
     */
    public function testFormatCurrency($pattern, $expected)
    {
        $formatter = new PlainFormatter('en_US', 100, 'UTC', ['currency' => $pattern]);
        $this->assertEquals($expected, $formatter->formatCurrency(123456789));
    }

    public function currencyPatternProvider()
    {
        return [
            ['$#,##0.##', '$1,234,567.89'],
            ['# ##0,##$', '1 234 567,89$']
        ];
    }

    /**
     * @dataProvider timePatternProvider
     */
    public function testFormatTime($pattern, $expected)
    {
        $date = new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'));

        $formatter = new PlainFormatter('en_US', 100, 'UTC', ['time' => $pattern]);
        $this->assertEquals($expected, $formatter->formatTime($date));
    }

    public function timePatternProvider()
    {
        return [
            ['g:i A', '1:01 PM'],
            ['H:i:s', '13:01:00']
        ];
    }

    /**
     * @dataProvider datePatternProvider
     */
    public function testFormatDate($pattern, $expected)
    {
        $date = new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'));

        $formatter = new PlainFormatter('en_US', 100, 'UTC', ['date' => $pattern]);
        $this->assertEquals($expected, $formatter->formatDate($date));
    }

    public function datePatternProvider()
    {
        return [
            ['n/j/y', '3/5/15'],
            ['Y-m-d', '2015-03-05']
        ];
    }

    /**
     * @dataProvider dateTimePatternProvider
     */
    public function testFormatDateTime($pattern, $expected)
    {
        $date = new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'));

        $formatter = new PlainFormatter('en_US', 100, 'UTC', ['dateTime' => $pattern]);
        $this->assertEquals($expected, $formatter->formatDateTime($date));
    }

    public function dateTimePatternProvider()
    {
        return [
            ['n/j/y, g:i A', '3/5/15, 1:01 PM'],
            ['Y-m-d H:i:s', '2015-03-05 13:01:00']
        ];
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormats($format)
    {
        $formatter = new PlainFormatter('en_US', 100, 'UTC');

        $this->assertEquals(
            empty($format) ? null : $formatter->formatDateTime(new \DateTime($format)),
            $formatter->formatDateTime($format)
        );
    }

    public function formatProvider()
    {
        return [
            [null],
            [''],
            ['2015-04-16'],
            ['10:22:10'],
            ['2015-04-16 10:22:10'],
            ['yesterday'],
            ['now'],
            ['tomorrow + 1 day'],
        ];
    }
}
