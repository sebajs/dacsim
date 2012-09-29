<?php

class SetTopBox extends Topology_Object
{   
    private $sSerialNumber;
    
    public function __construct($sSerialNumber)
    {                
        $this->sStoragePath  = STBS_PATH;
        $this->sName         = $sSerialNumber;
        $this->sSerialNumber = $sSerialNumber;
    }
    
    public function show()
    {
        echo " SerialN:   ".$this->sSerialNumber." (".Message::hexstr($this->sSerialNumber).")\n";
        echo "\n"; 
    }
    
    public function save()
    {
        $sFileName = DATA_PATH.$this->sStoragePath.Message::strhex($this->sName).DATA_EXTENSION;
        file_put_contents($sFileName, serialize($this));
    }
    
    public function delete()
    {
        $sFileName = DATA_PATH.$this->sStoragePath.Message::strhex($this->sName).DATA_EXTENSION;
        unlink($sFileName);
    }
    
    public function exists($sName, $sObjectPath='')
    {
        if ($sObjectPath == '') {
            $sFileName = DATA_PATH.$this->sStoragePath.Message::strhex($this->sName).DATA_EXTENSION;
        } else {
            $sFileName = DATA_PATH.$sObjectPath.Message::strhex($this->sName).DATA_EXTENSION;
        }
        return file_exists($sFileName);
    }
      
    public function listAll()
    {
        $sDir  = DATA_PATH.STBS_PATH;
        $aStbs = array();
        
        if ($handle = opendir($sDir)) {
            while (false !== ($entry = readdir($handle))) {
                $sSerialNumber = Message::hexstr(substr($entry, 0, -4));
                if ($sSerialNumber != '') {
                    $aStbs[] = $sSerialNumber;
                }
            }

            closedir($handle);
        }
        
        asort($aStbs);
        
        echo "Found ".count($aStbs)." ".get_class($this).":\n";
        foreach ($aStbs AS $sSTB) {
            echo " - {$sSTB}\n";
        }    
    }
}
