<?php

/*
* This file is part of the moss-locale package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Moss\Locale;

class FunctionMockLocale
{
    public static $timezone = 'UTC';
}

function date_default_timezone_set($timezone) { FunctionMockLocale::$timezone = $timezone; }

function date_default_timezone_get() { return FunctionMockLocale::$timezone; }

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Moss\Locale\LocaleException
     * @expectedExceptionMessage Invalid locale format, expected something like "en_US" or "en-US"
     */
    public function testInvalidLocale()
    {
        new Locale('foo', 'UTC', 100);
    }

    /**
     * @dataProvider localeProvider
     */
    public function testLocale($string, $expected)
    {
        $locale = new Locale('en_GB', 'UTC', 100);

        $locale->locale($string);
        $this->assertEquals($expected, $locale->locale());
    }

    public function localeProvider()
    {
        return [
            ['en-GB', 'en_GB'],
            ['en_GB', 'en_GB'],
            ['arn-CL', 'arn_CL'],
            ['arn_CL', 'arn_CL']
        ];
    }

    public function testLanguage()
    {
        $locale = new Locale('en_GB', 'UTC', 100);
        $this->assertEquals('en', $locale->language());
    }

    public function testTerritory()
    {
        $locale = new Locale('en_GB', 'UTC', 100);
        $this->assertEquals('GB', $locale->territory());
    }

    public function testCurrencySubUnit()
    {
        $locale = new Locale('en_GB', 'UTC', 100);
        $this->assertEquals(100, $locale->currencySubUnit());

        $locale->currencySubUnit(1000);
        $this->assertEquals(1000, $locale->currencySubUnit());
    }

    public function testTimezone()
    {
        $locale = new Locale('en_GB', 'UTC', 100);
        $this->assertEquals('UTC', $locale->timezone());

        $locale->timezone('Europe/Berlin');
        $this->assertEquals('Europe/Berlin', $locale->timezone());
    }
}
