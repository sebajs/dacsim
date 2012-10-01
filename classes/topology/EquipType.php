<?php

class EquipType extends Topology_Object
{   
    public function __construct($sName)
    {                
        $this->sStoragePath = EQUIPTYPE_PATH;
        $this->sName        = $sName;
    }
    
    public function validateSubType($iSubType)
    {
        return (is_numeric($iSubType));
    }
}
