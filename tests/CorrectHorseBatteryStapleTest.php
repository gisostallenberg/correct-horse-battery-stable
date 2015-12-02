<?php

namespace GisoStallenberg\Test;

use GisoStallenberg\CorrectHorseBatteryStaple\CorrectHorseBatteryStaple;
use PHPUnit_Framework_TestCase;

/**
 * CorrectHorseBatteryStapleTest.
 *
 * @author  Giso Stallenberg <gisostallenberg@gmail.com>
 */
class CorrectHorseBatteryStapleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests instantiation of CorrectHorseBatteryStaple.
     */
    public function testConstruct()
    {
        new CorrectHorseBatteryStaple();
    }

    /**
     * Tests if the getters return expected results.
     *
     * @dataProvider providePasswordStatusMessages
     */
    public function testStatusMessages($password, $expectedStatus, $expectedMessage)
    {
        $correctHorseBatteryStaple = new CorrectHorseBatteryStaple();
        $correctHorseBatteryStaple->check($password);

        $this->assertEquals($expectedStatus, $correctHorseBatteryStaple->getLastStatus());
        $this->assertEquals($expectedMessage, $correctHorseBatteryStaple->getLastMessage());
    }

    /**
     * Provide data for getters test.
     *
     * @return array
     */
    public function providePasswordStatusMessages()
    {
        return [
            ['bTDiBMEnVSp76', 0, 'OK'],
            ['correcthorsebatterystaple', 0, 'OK'],
            ['aaabbbccc', 100, 'it does not contain enough DIFFERENT characters'],
            ['      ', 100, 'it does not contain enough DIFFERENT characters'],
            //[chr(9) . chr(8201) . chr(11) . chr(12) . chr(32) . chr(9), 101, 'it is all whitespace'],
            ['yranoitcid', 102, 'it is based on a (reversed) dictionary word'],
            ['Tr0ub4dor', 103, 'it is based on a dictionary word'],
            ['dictionary', 103, 'it is based on a dictionary word'],
            ['12345', 108, 'it is too short'],
            ['123456', 109, 'it is too simplistic/systematic'],
            ['1', 110, 'it is WAY too short'],
        ];
    }

}