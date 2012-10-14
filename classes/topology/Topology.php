<?php

class Topology
{    
    public static function show($sObjectType)
    {
        if (in_array($sObjectType, self::getObjectTypes())) {
            
            $oTopologyObject = Topology_Object_Factory::create($sObjectType);
            
            $oTopologyObject->listAll();
            
        } else {
            throw new Exception('Unknown object type: '.$sObjectType.'.');
        }
    }
    
    public static function create($sObjectType, $sObjectName)
    {
        if (in_array($sObjectType, self::getObjectTypes())) {
            
            $oTopologyObject = Topology_Object_Factory::create($sObjectType, $sObjectName);
            
            if (!$oTopologyObject->exists($sObjectName)) {
                $oTopologyObject->save();
            } else {
                throw new Exception($sObjectType.' '.$sObjectName.' already exists');
            }
            
        } else {
            throw new Exception('Unknown object type ('.$sObjectType.').');
        }
    }
    
    public static function delete($sObjectType, $sObjectName)
    {
        if (in_array($sObjectType, self::getObjectTypes())) {
            
            $oTopologyObject = Topology_Object_Factory::create($sObjectType, $sObjectName);
            
            if ($oTopologyObject->exists($sObjectName)) {
                $oTopologyObject->delete();
            } else {
                throw new Exception($sObjectType.' '.$sObjectName.' does not exist');
            }
            
        } else {
            throw new Exception('Unknown object type ('.$sObjectType.').');
        }
    }
    
    public static function showSetTopBox($sSerialNumber)
    {
        $sSerialNumber = strtoupper($sSerialNumber);
        
        $oSTB = new SetTopBox($sSerialNumber);
        if ($oSTB->exists($sSerialNumber)) {
            
            $oSTB->load($bOverrideBsiCode=true);
            $oSTB->show();
            
        } else {
            throw new Exception('Unknown SetTopBox: '.$sSerialNumber.'.');
        }
    }
    
    public static function getObjectTypes()
    {
        $aTypes = array('bsi', 
                        'stb', 
                        'equiptype', 
                        'headend', 
                        'usplant', 
                        'dsplant', 
                        'channelmap', 
                        'service',
                        'regionconf');
        
        return ($aTypes);
    }
}
