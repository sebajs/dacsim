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
    protected $aAuthServices;
    protected $aAuthPrograms;
    protected $iInstallTimestamp;

    public function __construct($sSerialNumber)
    {
        $this->sStoragePath  = STBS_PATH;
        $this->sName         = $sSerialNumber;
        $this->sSerialNumber = $sSerialNumber;
        $this->aAuthServices = array();
        $this->aAuthPrograms = array();
    }

    public function show()
    {
        Output::line("STB Description");
        Output::line();
        Output::line(" SerialN:   ".$this->sSerialNumber);
        Output::line(" BSICode:   ".hexdec($this->iBsiCode));
        Output::line(" Instaled:  ".date('d.m.Y H:i:s', $this->iInstallTimestamp));
        Output::line(" UnitAdd:   {$this->sUnitAddress} | EqType: {$this->iEqType} | EqSubType: {$this->iEqSubType}");
        Output::line(" HeadEnd:   {$this->iHeadEnd} | US Plant: {$this->iUsPlant} | DS Plant: {$this->iDsPlant} | VCMhandle: {$this->iChannelMap}");
        Output::line(" OnPlant:   ".$this->iOnPlant);
        Output::line(" Credit:    {$this->iCreditAllowed} | Purchases: {$this->iPurchasesAllowed} | MaxCost: {$this->iMaxPackCost}");
        Output::line(" TimeZone:  {$this->iTimeZoneId} | EPGRegion: {$this->iEpgRegion} | RegionCfg: {$this->iRegionConfig}");
        Output::line(" TurnOnVC:  {$this->iTurnOnVC} | TurnOffVC: {$this->iTurnOffVC} | Output CH: {$this->iOutputChannel}");
        Output::line(" FeatSets:  {$this->iFeatureSetting} ({$this->sFeatureSetting})");
        if (false) {
            $oHelper = new Message760_Feature_Component('');
            $aReversedSettings = strrev($this->sFeatureSetting);
            for ($i=0; $i<strlen($this->sFeatureSetting); $i++) {
                if ($aReversedSettings[$i] == '1') {
                    Output::line("     + ".$oHelper->getSettingName($i));
                }
            }
        }
        
        Output::line(" Services:  ".implode(', ', $this->aAuthServices));
        Output::line(" Programs:  ".implode(', ', $this->aAuthPrograms));
        Output::line();
    }

    public function save()
    {
        $iError = $this->validate();

        if ($iError == 0) {
            $sFileName = DATA_PATH.$this->sStoragePath.($this->sName).DATA_EXTENSION;
            file_put_contents($sFileName, serialize($this));
        } else {
            throw new Exception("Validation Error {$iError}", $iError);
        }
    }

    public function load($bOverrideBsiCode=false)
    {
        if ($this->exists($this->sName)) {
            $sFileName  = DATA_PATH.$this->sStoragePath.($this->sName).DATA_EXTENSION;
            $oStoredStb = unserialize(file_get_contents($sFileName));
            
            if ($oStoredStb->iBsiCode == $this->iBsiCode || $bOverrideBsiCode) {
                
                $this->iBsiCode          = $oStoredStb->iBsiCode;
                $this->sSerialNumber     = $oStoredStb->sSerialNumber;
                $this->sUnitAddress      = $oStoredStb->sUnitAddress;
                $this->iEqType           = $oStoredStb->iEqType;
                $this->iEqSubType        = $oStoredStb->iEqSubType;
                $this->iHeadEnd          = $oStoredStb->iHeadEnd;
                $this->iUsPlant          = $oStoredStb->iUsPlant;
                $this->iDsPlant          = $oStoredStb->iDsPlant;
                $this->iChannelMap       = $oStoredStb->iChannelMap;
                $this->iOnPlant          = $oStoredStb->iOnPlant;
                $this->iCreditAllowed    = $oStoredStb->iCreditAllowed;
                $this->iPurchasesAllowed = $oStoredStb->iPurchasesAllowed;
                $this->iMaxPackCost      = $oStoredStb->iMaxPackCost;
                $this->iTimeZoneId       = $oStoredStb->iTimeZoneId;
                $this->iEpgRegion        = $oStoredStb->iEpgRegion;
                $this->iRegionConfig     = $oStoredStb->iRegionConfig;
                $this->iTurnOnVC         = $oStoredStb->iTurnOnVC;
                $this->iTurnOffVC        = $oStoredStb->iTurnOffVC;
                $this->iOutputChannel    = $oStoredStb->iOutputChannel;
                $this->iFeatureSetting   = $oStoredStb->iFeatureSetting;
                $this->sFeatureSetting   = $oStoredStb->sFeatureSetting;
                $this->aAuthServices     = $oStoredStb->aAuthServices;
                $this->aAuthPrograms     = $oStoredStb->aAuthPrograms;
                $this->iInstallTimestamp = $oStoredStb->iInstallTimestamp;
                
            } else {
                // 1063 BSI Code and Settop Serial Number Mismatch Error.
                $iError = 1063;
                throw new Exception("Load Error {$iError}", $iError);
            }
        }
    }

    public function delete()
    {
        $sFileName = DATA_PATH.$this->sStoragePath.($this->sName).DATA_EXTENSION;
        unlink($sFileName);
    }

    public function exists($sName, $sObjectPath='')
    {
        if ($sObjectPath == '') {
            $sFileName = DATA_PATH.$this->sStoragePath.($this->sName).DATA_EXTENSION;
        } else {
            $sFileName = DATA_PATH.$sObjectPath.($this->sName).DATA_EXTENSION;
        }
        return file_exists($sFileName);
    }

    public function listAll()
    {
        $sDir  = DATA_PATH.STBS_PATH;
        $aStbs = array();

        if ($handle = opendir($sDir)) {
            while (false !== ($entry = readdir($handle))) {
                $sSerialNumber = (substr($entry, 0, -4));
                if ($sSerialNumber != '') {
                    $aStbs[] = $sSerialNumber;
                }
            }

            closedir($handle);
        }

        asort($aStbs);

        Output::line("Found ".count($aStbs)." ".get_class($this).":");
        foreach ($aStbs AS $sSTB) {
            Output::line(" - {$sSTB}");
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

        // RegionConfig
        if ($iError == 0) {
            $oTemp = new RegionConfig($this->iRegionConfig);
            if (!$oTemp->exists()) {
                // 1017 Region Config Handle in the Feature Component does not exist.
                $iError = 1017;
            }
            unset($oTemp);
        }

        return $iError;
    }
    
    public function clearServices()
    {
        $this->aAuthServices = array();
    }
    
    public function addService($iService)
    {
        $this->aAuthServices[$iService] = $iService;
        asort($this->aAuthServices);
    }
    
    public function removeService($iService)
    {
        unset($this->aAuthServices[$iService]);
        asort($this->aAuthServices);
    }
}
