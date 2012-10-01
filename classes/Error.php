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
        self::$aErrors[1005] = 'Serial Number does not exist';
        self::$aErrors[1006] = 'Serial Number already exists';
        self::$aErrors[1007] = 'Invalid Unit Address';
        self::$aErrors[1009] = 'Invalid Equipment type';
        self::$aErrors[1010] = 'Invalid Equipment subtype';
        self::$aErrors[1011] = 'Headend handle does not exist';
        self::$aErrors[1012] = 'Upstream Plant Handle does not exist';
        self::$aErrors[1013] = 'Downstream Plant Handle does not exist';
        self::$aErrors[1014] = 'VCM Handle does not exist';
        self::$aErrors[2012] = 'Poll of Terminal Failed - Settop Not Responding';
        self::$aErrors[3011] = 'Invalid Message Type';
    }
}
