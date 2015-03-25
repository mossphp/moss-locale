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


class IntlFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatNumber()
    {
        $formatter = new IntlFormatter('en_US', 100, 'UTC');
        $this->assertEquals('123,456.789', $formatter->formatNumber(123456.789));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Currency amounts have to be given as integer value
     */
    public function testCurrencyFormatterNotInt()
    {
        $formatter = new IntlFormatter('en_US', 100, 'UTC');
        $formatter->formatCurrency(1234567.89);
    }

    public function testFormatCurrency()
    {
        $formatter = new IntlFormatter('en_US', 100, 'UTC');
        $this->assertEquals('$1,234,567.89', $formatter->formatCurrency(123456789));
    }

    public function testFormatTime()
    {
        $date = new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'));

        $formatter = new IntlFormatter('en_US', 100, 'UTC');
        $this->assertEquals('1:01 PM', $formatter->formatTime($date));
    }

    public function testFormatDate()
    {
        $date = new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'));

        $formatter = new IntlFormatter('en_US', 100, 'UTC');
        $this->assertEquals('3/5/15', $formatter->formatDate($date));
    }

    public function testFormatDateTime()
    {
        $date = new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'));

        $formatter = new IntlFormatter('en_US', 100, 'UTC');
        $this->assertRegExp('/^3\/5\/15,? 1:01 PM$/', $formatter->formatDateTime($date));
    }
}
