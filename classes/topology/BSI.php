<?php

class BSI extends Topology_Object
{       
    public function __construct($sName)
    {                
        $this->sStoragePath = BSI_PATH;
        $this->sName        = $sName;
    }
}
