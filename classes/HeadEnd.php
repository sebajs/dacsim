<?php

class HeadEnd extends Topology_Object
{   
    public function __construct($sName)
    {                
        $this->sStoragePath = HEADEND_PATH;
        $this->sName        = $sName;
    }
}
