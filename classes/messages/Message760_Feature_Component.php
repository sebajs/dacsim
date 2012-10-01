<?php

class Message760_Feature_Component extends Thing
{
    const LENGTH = 60;
    
    protected $aSettings;
    protected $iSubtype;
    protected $sBitMask;
    protected $iCreditAllowed;
    protected $iPurchAllowed;
    protected $iMaxPackCost;
    protected $iTimeZoneID;
    protected $iEpgRegion;
    protected $iRegionConfig;
    protected $iTurnOffVC;
    protected $iTurnOnVC;
    protected $iOutputChannel;
    protected $iFeatSetting;
    protected $sFeatSetting;
    
    public function __construct($sData)
    {
        $this->iSubtype       = substr($sData, 0, 2);
        $this->sBitMask       = substr($sData, 2, 8);
        $this->iCreditAllowed = substr($sData, 10, 8);
        $this->iPurchAllowed  = substr($sData, 18, 8);
        $this->iMaxPackCost   = substr($sData, 26, 4);
        $this->iTimeZoneID    = substr($sData, 30, 4);
        $this->iEpgRegion     = substr($sData, 34, 4);
        $this->iRegionConfig  = substr($sData, 38, 4);
        $this->iTurnOffVC     = substr($sData, 42, 4);
        $this->iTurnOnVC      = substr($sData, 46, 4);
        $this->iOutputChannel = substr($sData, 50, 2);
        $this->iFeatSetting   = substr($sData, 52, 8);
        $this->sFeatSetting   = str_pad(decbin(hexdec($this->iFeatSetting)), 32, '0', STR_PAD_LEFT);        
    }
    
    public function show()
    {
        echo " Feature Component (".$this->iSubtype."):\n";
        echo "   BitMask:   ".$this->sBitMask.       " (".$this->sBitMask.")\n";
        echo "   Credit:    ".$this->iCreditAllowed. " (".hexdec($this->iCreditAllowed).")\n";
        echo "   Purchases: ".$this->iPurchAllowed.  " (".hexdec($this->iPurchAllowed).")\n";
        echo "   MaxCost:   ".$this->iMaxPackCost.   " (".hexdec($this->iMaxPackCost).")\n";
        echo "   TimeZone:  ".$this->iTimeZoneID.    " (".hexdec($this->iTimeZoneID).")\n";
        echo "   EPGRegion: ".$this->iEpgRegion.     " (".hexdec($this->iEpgRegion).")\n";
        echo "   RegionCfg: ".$this->iRegionConfig.  " (".hexdec($this->iRegionConfig).")\n";
        echo "   TurnOffVC: ".$this->iTurnOffVC.     " (".hexdec($this->iTurnOffVC).")\n";
        echo "   TurnOnVC:  ".$this->iTurnOnVC.      " (".hexdec($this->iTurnOnVC).")\n";
        echo "   Output CH: ".$this->iOutputChannel. " (".hexdec($this->iOutputChannel).")\n";
        echo "   FeatSets:  ".$this->iFeatSetting.   " (".$this->sFeatSetting.")\n";     
        
        $aReversedSettings = strrev($this->sFeatSetting);
        for ($i=0; $i<strlen($this->sFeatSetting); $i++) {
            if ($aReversedSettings[$i] == '1') {
                echo "     + ".$this->getSettingName($i)."\n";
            }
        }
    }
    
    public function loadSettingsNames()
    {
        $this->aSettings = array();
        $this->aSettings[-1] = 'Unknown Setting';
        $this->aSettings[0]  = 'down_loadable';
        $this->aSettings[1]  = 'rf_bypass';
        $this->aSettings[2]  = 'parental_control_channel';
        $this->aSettings[3]  = 'parental_control_rating';
        $this->aSettings[4]  = 'volume_mute';
        $this->aSettings[5]  = 'vcr_tuning';
        $this->aSettings[6]  = 'last_channel';
        $this->aSettings[7]  = 'favorite_channel';
        $this->aSettings[8]  = 'parental_control_time';
        $this->aSettings[9]  = 'parental_control_cost';
        $this->aSettings[10] = 'interactive_ir';
        $this->aSettings[11] = 'purchase_cancellation';
        $this->aSettings[12] = 'IPPV';
        $this->aSettings[13] = 'remote_control';
        $this->aSettings[14] = 'time_controlled_programming';
        $this->aSettings[15] = 'user_specified_language';
        $this->aSettings[16] = 'ab_output';
        $this->aSettings[17] = 'applications_interface_port';
        $this->aSettings[18] = 'force_standalone';
        $this->aSettings[19] = 'ignore_blackout';
        $this->aSettings[20] = 'nvod_enabled';
        $this->aSettings[21] = 'high_speed_serial_output';
    }
    
    public function getSettingName($iSetting)
    {
        if (!is_array($this->aSettings)) {
            $this->loadSettingsNames();
        }
        
        if (!isset($this->aSettings[$iSetting])) {
            $iSetting = -1;
        }
        
        return $this->aSettings[$iSetting];
    }
}
