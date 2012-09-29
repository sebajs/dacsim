<?php

class USPlant extends Topology_Object
{   
    public function __construct($sName)
    {                
        $this->sStoragePath = USPLANT_PATH;
        $this->sName        = $sName;
    }
}
