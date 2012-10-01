<?php

class ChannelMap extends Topology_Object
{       
    public function __construct($sName)
    {                
        $this->sStoragePath = CHANNELMAP_PATH;
        $this->sName        = $sName;
    }
}
