<?php

class RegionConfig extends Topology_Object
{       
    public function __construct($sName)
    {                
        $this->sStoragePath = REGIONCONFIG_PATH;
        $this->sName        = $sName;
    }
}
