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
        $iError = 0;
        
        // UnitAddress
        if ($iError == 0) {
            if (strlen($this->sUnitAddress) != 16 || !is_numeric($this->sUnitAddress)) {
                // 1007 Invalid Unit Address.
                $iError = 1007;
            }
        }
        
        // EquipType and EquipSubType
        if ($iError == 0) {
            $oTemp = new EquipType($this->iEqType);
            if (!$oTemp->exists()) {
                // 1009 Invalid Equipment type.
                $iError = 1009;
            }
            
            // EquipSubType
            if (!$oTemp->validateSubType($this->iEqSubType)) {
                // 1010 Invalid Equipment subtype.
                $iError = 1010;
            }
            unset($oTemp);
        }
        
        // HeadEnd
        if ($iError == 0) {
            $oTemp = new HeadEnd($this->iHeadEnd);
            if (!$oTemp->exists()) {
                // 1011 Headend handle does not exist.
                $iError = 1011;
            }
            unset($oTemp);
        }
        
        // USPlant
        if ($iError == 0) {
            $oTemp = new USPlant($this->iUsPlant);
            if (!$oTemp->exists()) {
                // 1012 Upstream Plant Handle does not exist.
                $iError = 1012;
            }
            unset($oTemp);
        }
        
        // DSPlant
        if ($iError == 0) {
            $oTemp = new DSPlant($this->iDsPlant);
            if (!$oTemp->exists()) {
                // 1013 Downstream Plant Handle does not exist.
                $iError = 1013;
            }
            unset($oTemp);
        }
        
        // ChannelMap
        if ($iError == 0) {
            $oTemp = new ChannelMap($this->iChannelMap);
            if (!$oTemp->exists()) {
                // 1014 VCM Handle does not exist.
                $iError = 1014;
            }
            unset($oTemp);
        }
        
        return $iError;
    }
}
