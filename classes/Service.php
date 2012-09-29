<?php

class Service extends Topology_Object
{   
    public function __construct($sName)
    {                
        $this->sStoragePath = SERVICE_PATH;
        $this->sName        = $sName;
    }
}
