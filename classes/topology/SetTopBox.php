<?php

class SetTopBox extends Topology_Object
{   
    protected $iBsiCode;
    protected $sSerialNumber;
    protected $sUnitAddress;
    protected $iEqType;
    protected $iEqSubType;
    protected $iHeadEnd;
    protected $iUsPlant;
    protected $iDsPlant;
    protected $iChannelMap;
    protected $iOnPlant;
    protected $iCreditAllowed;
    protected $iPurchasesAllowed;
    protected $iMaxPackCost;
    protected $iTimeZoneId;
    protected $iEpgRegion;
    protected $iRegionConfig;
    protected $iTurnOnVC;
    protected $iTurnOffVC;
    protected $iOutputChannel;
    protected $iFeatureSetting;
    protected $sFeatureSetting;
    
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
        echo print_r($this, 1);
        
        $iError = $this->validate();
        
        if ($iError == 0) {
            $sFileName = DATA_PATH.$this->sStoragePath.Message::strhex($this->sName).DATA_EXTENSION;
            file_put_contents($sFileName, serialize($this));
        } else {
            throw new Exception("Validation Error {$iError}", $iError);
        }
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
    
    private function validate()
    {
        return 0;
    }
}
