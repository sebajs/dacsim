<?php

class Topology_Object_Factory
{   
    private static $aObjectTypes;
    
    public static function create($sObjectType, $sObjectName='')
    {
        $sClassName = self::getObjectTypesClass($sObjectType);
        
        return new $sClassName($sObjectName);
    }
    
    public static function loadDescriptions()
    {
        self::$aObjectTypes = array();
        self::$aObjectTypes['stb']        = 'SetTopBox';
        self::$aObjectTypes['equiptype']  = 'EquipType';
        self::$aObjectTypes['headend']    = 'HeadEnd';
        self::$aObjectTypes['usplant']    = 'USPlant';
        self::$aObjectTypes['dsplant']    = 'DSPlant';
        self::$aObjectTypes['channelmap'] = 'ChannelMap';
        self::$aObjectTypes['service']    = 'Service';
    }
    
    public static function getObjectTypesClass($sObjectType)
    {
        if (!is_array(self::$aObjectTypes)) {
            self::loadDescriptions();
        }
        
        return self::$aObjectTypes[$sObjectType];
    }    
}
