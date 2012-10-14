<?php

class Result_Register
{
    private static $bActive;
    private static $aResultLines;
    
    public static function init()
    {
        if (!is_array(self::$aResultLines)) {
            self::$aResultLines = array();
        }
    }
    
    public static function clean()
    {
        self::$aResultLines = array();
    }
    
    public static function add($sLine)
    {
        self::init();
        self::$aResultLines[] = $sLine;
    }
    
    public static function getLines()
    {
        self::init();
        return self::$aResultLines;
    }
    
    public static function getCount()
    {
        self::init();
        return count(self::$aResultLines);
    }
    
    public static function activate()
    {
        self::$bActive = true;
    }
    
    public static function deactivate()
    {
        self::$bActive = false;
    }
    
    public static function isActive()
    {
        if (!isset(self::$bActive)) {
            self::deactivate();
        }
        return (self::$bActive);
    }
}
