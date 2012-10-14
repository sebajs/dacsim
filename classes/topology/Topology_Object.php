<?php

class Topology_Object extends Thing
{   
    protected $sStoragePath;
    protected $sName;
    
    public function __construct($sName)
    {                
        $this->sName = $sName;
    }
       
    public function save()
    {
        $sFileName = DATA_PATH.$this->sStoragePath.$this->sName.DATA_EXTENSION;
        file_put_contents($sFileName, serialize($this));
    }
    
    public function delete()
    {
        $sFileName = DATA_PATH.$this->sStoragePath.$this->sName.DATA_EXTENSION;
        unlink($sFileName);
    }
    
    public function exists($sName='', $sObjectPath='')
    {
        if ($sName == '') {
            $sName = $this->sName;
        }
        
        if ($sObjectPath == '') {
            $sFileName = DATA_PATH.$this->sStoragePath.$sName.DATA_EXTENSION;
        } else {
            $sFileName = DATA_PATH.$sObjectPath.$sName.DATA_EXTENSION;
        }
        return file_exists($sFileName);
    }
    
    public function listAll()
    {
        $sDir     = DATA_PATH.$this->sStoragePath;
        $aObjects = array();
        
        if ($handle = opendir($sDir)) {
            while (false !== ($entry = readdir($handle))) {
                $sName = substr($entry, 0, -4);
                if ($sName != '') {
                    $aObjects[] = $sName;
                }
            }

            closedir($handle);
        }
        
        asort($aObjects);
        
        Output::line("Found ".count($aObjects)." ".get_class($this).":");
        foreach ($aObjects AS $sObject) {
            Output::line(" - {$sObject}");
        }    
    }
}
