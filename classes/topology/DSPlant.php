<?php

class DSPlant extends Topology_Object
{       
    public function __construct($sName)
    {                
        $this->sStoragePath = DSPLANT_PATH;
        $this->sName        = $sName;
    }
}
