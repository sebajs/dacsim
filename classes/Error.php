<?php

class Error
{
    private static $aErrors;

    public static function getDescription($iErrNum)
    {
        if (!is_array(self::$aErrors)) {
            self::loadDescriptions();
        }

        if (!isset(self::$aErrors[$iErrNum])) {
            $iErrNum = -1;
        }

        return self::$aErrors[$iErrNum];
    }

    public static function loadDescriptions()
    {
        self::$aErrors = array();
        self::$aErrors[-1]   = 'Unknown Error in DACSIM';
        self::$aErrors[1]    = 'No Error';
        self::$aErrors[226]  = 'Packet checksum error';
        self::$aErrors[231]  = 'Packet size error';
        self::$aErrors[232]  = 'Packet timeout error';
        self::$aErrors[1002] = 'Invalid BSI Code. BSI Code not assigned to this WireLink port';
        self::$aErrors[1003] = 'BSI Code in the Business System Owner Component does not exist';
        self::$aErrors[1005] = 'Serial Number does not exist';
        self::$aErrors[1006] = 'Serial Number already exists';
        self::$aErrors[1007] = 'Invalid Unit Address';
        self::$aErrors[1009] = 'Invalid Equipment type';
        self::$aErrors[1010] = 'Invalid Equipment subtype';
        self::$aErrors[1011] = 'Headend handle does not exist';
        self::$aErrors[1012] = 'Upstream Plant Handle does not exist';
        self::$aErrors[1013] = 'Downstream Plant Handle does not exist';
        self::$aErrors[1014] = 'VCM Handle does not exist';
        self::$aErrors[1015] = 'Invalid Operation Code in the State Component';
        self::$aErrors[1017] = 'Region Config Handle in the Feature Component does not exist';
        self::$aErrors[1054] = 'Duplicate Type Component Error';
        self::$aErrors[1055] = 'Duplicate Plant Component Error';
        self::$aErrors[1056] = 'Duplicate State Component Error';
        self::$aErrors[1057] = 'Duplicate Feature Component Error';
        self::$aErrors[1058] = 'Duplicate Authorization Component Error';
        self::$aErrors[1061] = 'Duplicate Business System Owner (BSO) Error';
        self::$aErrors[1062] = 'Missing Type Component in 760 Command';
        self::$aErrors[1063] = 'BSI Code and Settop Serial Number Mismatch Error';
        self::$aErrors[2012] = 'Poll of Terminal Failed - Settop Not Responding';
        self::$aErrors[3011] = 'Invalid Message Type';
    }
}
