<?php

class Message760 extends Payload
{
    const IDENTIFIER_COMP_LENGTH = 30;
    const STATUS_COMP_LENGTH     = 40;

    protected $oResponse;
    protected $aReqTypes;
    protected $iBsiCode;
    protected $iReqType;
    protected $sSerialN;
    protected $aDataComponents;

    public function __construct($iSequence, $sData)
    {
        parent::__construct($iSequence);
        
        $this->aReqTypes = array('', 'Add', 'Change', 'Delete');

        $this->iBsiCode = substr($sData, MessageHeader::HEADER_LENGTH, 4);
        $this->iReqType = substr($sData, 16, 2);
        $this->sSerialN = substr($sData, 18, 24);

        $this->aDataComponents = array();
        $sComponentsData = substr($sData, (MessageHeader::HEADER_LENGTH+self::IDENTIFIER_COMP_LENGTH), -2); // los 2 finales son el checksum.

        $this->extractComponents($sComponentsData);
    }

    public function show()
    {
        if (isset($this->aReqTypes[hexdec($this->iReqType)])) {
            $sReqType = $this->aReqTypes[hexdec($this->iReqType)];
        } else {
            $sReqType = "Unknown";
        }

        echo " Identifier Component:\n";
        echo "   BSICode:   ".$this->iBsiCode." (".hexdec($this->iBsiCode).")\n";
        echo "   ReqType:   ".$this->iReqType." (".$sReqType.")\n";
        echo "   SerialN:   ".$this->sSerialN." (".Message::hexstr($this->sSerialN).")\n";
        
        foreach ($this->aDataComponents AS $oComp) {
            $oComp->show();
        }

        echo "\n";
    }

    public function run()
    {
        $oBSI = new BSI(hexdec($this->iBsiCode));

        if ($oBSI->exists()) {

            switch ($this->iReqType) {
                case 1: //Add
                    $this->add();
                    break;
                case 2: //Change
                    $this->change();
                    break;
                case 3: //Delete
                    $this->delete();
                    break;
                default:
                    // 3011 Invalid Message Type.
                    $this->oResponse = new Response001($this->iSequence, 3011);
                    break;
            }

        } else {
            // 1002 Invalid BSI Code. BSI Code not assigned to this WireLink port.
            $this->oResponse = new Response001($this->iSequence, 1002);
        }
    }

    private function extractComponents($sComponentsData)
    {        
        $bExtractOk = true;
        $iError     = 0; 
        
        while (strlen($sComponentsData) > 0 && $bExtractOk) {
            $sCompType = substr($sComponentsData, 0, 2);
            
            switch ($sCompType) {
                
                case '01': // Busco type component
                    if (!isset($this->aDataComponents['type'])) {
                        $sComponent = substr($sComponentsData, 0, Message760_Type_Component::LENGTH);
                        $this->aDataComponents['type'] = new Message760_Type_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_Type_Component::LENGTH);
                        continue;
                    } else {
                        // 1054 Duplicate Type Component Error.
                        $iError     = 1054; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                case '02': // Busco plant component
                    if (!isset($this->aDataComponents['plant'])) {
                        $sComponent = substr($sComponentsData, 0, Message760_Plant_Component::LENGTH);
                        $this->aDataComponents['plant'] = new Message760_Plant_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_Plant_Component::LENGTH);
                        continue;
                    } else {
                        // 1055 Duplicate Plant Component Error.
                        $iError     = 1055; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                case '03': // Busco state component
                    if (!isset($this->aDataComponents['state'])) {
                        $sComponent = substr($sComponentsData, 0, Message760_State_Component::LENGTH);
                        $this->aDataComponents['state'] = new Message760_State_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_State_Component::LENGTH);
                        continue;
                    } else {
                        // 1056 Duplicate State Component Error.
                        $iError     = 1056; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                case '04': // Busco feature component
                    if (!isset($this->aDataComponents['feature'])) {
                        $sComponent = substr($sComponentsData, 0, Message760_Feature_Component::LENGTH);
                        $this->aDataComponents['feature'] = new Message760_Feature_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_Feature_Component::LENGTH);
                        continue;
                    } else {
                        // 1057 Duplicate Feature Component Error.
                        $iError     = 1057; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                case '06': // Busco BSOwner component
                    if (!isset($this->aDataComponents['bsowner'])) {
                        $sComponent = substr($sComponentsData, 0, Message760_BsOwner_Component::LENGTH);
                        $this->aDataComponents['bsowner'] = new Message760_BsOwner_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_BsOwner_Component::LENGTH);
                        continue;
                    } else {
                        // 1061 Duplicate Business System Owner (BSO) Error.
                        $iError     = 1061; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                case '07': // Busco authorization component
                    echo "sCompType: ".$sCompType."\n";
                    if (!isset($this->aDataComponents['auth'])) {
                        $sHeader    = substr($sComponentsData, 0, Message760_Auth_Component::HEADER_LENGTH);
                        $iNumRecs   = hexdec(substr($sHeader, -4));
                        $iRecsLen   = $iNumRecs * Message760_Auth_Component::RECORD_LENGTH;
                        $sComponent = substr($sComponentsData, 0, Message760_Auth_Component::HEADER_LENGTH+$iRecsLen);
                        
                        $this->aDataComponents['auth'] = new Message760_Auth_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_Auth_Component::HEADER_LENGTH+$iRecsLen);
                        continue;
                    } else {
                        // 1058 Duplicate Authorization Component Error.
                        $iError     = 1058; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                case '08': // Busco CableCard component
                    if (!isset($this->aDataComponents['cablecard'])) {
                        $sComponent = substr($sComponentsData, 0, Message760_CableCard_Component::LENGTH);
                        $this->aDataComponents['cablecard'] = new Message760_CableCard_Component($sComponent);
                        $sComponentsData = substr($sComponentsData, Message760_CableCard_Component::LENGTH);
                        continue;
                    } else {
                        // 1065 Duplicate CableCARD/Host Component Error.
                        $iError     = 1065; 
                        $bExtractOk = false;
                    }                
                    break;
                    
                default:
                    // 1059 Unknown Component Error.
                    $iError     = 1059; 
                    $bExtractOk = false;
                    break;
            }
        }
        
        if (!isset($this->aDataComponents['type'])) {
            // 1062 Missing Type Component in 760 Command.
            $iError     = 1062;           
            $bExtractOk = false;
        }
        
        if (!$bExtractOk && ($iError != 0)) {
            $this->oResponse = new Response001($this->iSequence, $iError);
        }
        
        return $bExtractOk;
    }

    private function add()
    {
    	$bFoundError = false;
        $oSTB        = new SetTopBox(Message::hexstr($this->sSerialN));

        if (!$oSTB->exists(Message::hexstr($this->sSerialN))) {
            $oSTB->iBsiCode = $this->iBsiCode;

            if (isset($this->aDataComponents['type'])) {
                $sMask = (decbin(hexdec($this->aDataComponents['type']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->sUnitAddress = Message::hexstr($this->aDataComponents['type']->sUnitAddress);
                if ($sMask[1] == '1') $oSTB->iEqType      = hexdec($this->aDataComponents['type']->iEqType);
                if ($sMask[2] == '1') $oSTB->iEqSubType   = hexdec($this->aDataComponents['type']->iEqSubType);
            }
            
            if (isset($this->aDataComponents['plant'])) {
                $sMask = (decbin(hexdec($this->aDataComponents['plant']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->iHeadEnd    = hexdec($this->aDataComponents['plant']->iHeadEnd);
                if ($sMask[1] == '1') $oSTB->iUsPlant    = hexdec($this->aDataComponents['plant']->iUsPlant);
                if ($sMask[2] == '1') $oSTB->iDsPlant    = hexdec($this->aDataComponents['plant']->iDsPlant);
                if ($sMask[5] == '1') $oSTB->iChannelMap = hexdec($this->aDataComponents['plant']->iVcmHandle);
            }
            
            if (isset($this->aDataComponents['state'])) {
                $sMask = (decbin(hexdec($this->aDataComponents['state']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->iOnPlant = hexdec($this->aDataComponents['state']->iOnPlant);

                if (!$this->aDataComponents['state']->validateOperation()) {
                	$bFoundError = true;

        		    // 1015 Invalid Operation Code in the State Component.
		            $this->oResponse = new Response001($this->iSequence, 1015);
                }
            }
            
            if (isset($this->aDataComponents['feature'])) {
                $sMask = (decbin(hexdec($this->aDataComponents['feature']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->iCreditAllowed    = hexdec($this->aDataComponents['feature']->iCreditAllowed);
                if ($sMask[1] == '1') $oSTB->iPurchasesAllowed = hexdec($this->aDataComponents['feature']->iPurchAllowed);
                if ($sMask[2] == '1') $oSTB->iMaxPackCost      = hexdec($this->aDataComponents['feature']->iMaxPackCost);
                if ($sMask[3] == '1') $oSTB->iTimeZoneId       = hexdec($this->aDataComponents['feature']->iTimeZoneID);
                if ($sMask[4] == '1') $oSTB->iEpgRegion        = hexdec($this->aDataComponents['feature']->iEpgRegion);
                if ($sMask[5] == '1') $oSTB->iRegionConfig     = hexdec($this->aDataComponents['feature']->iRegionConfig);
                if ($sMask[6] == '1') $oSTB->iTurnOnVC         = hexdec($this->aDataComponents['feature']->iTurnOnVC);
                if ($sMask[7] == '1') $oSTB->iTurnOffVC        = hexdec($this->aDataComponents['feature']->iTurnOffVC);
                if ($sMask[8] == '1') $oSTB->iOutputChannel    = hexdec($this->aDataComponents['feature']->iOutputChannel);
                if ($sMask[9] == '1') $oSTB->iFeatureSetting   = hexdec($this->aDataComponents['feature']->iFeatSetting);
                if ($sMask[9] == '1') $oSTB->sFeatureSetting   = $this->aDataComponents['feature']->sFeatSetting;
            }
            
            if (isset($this->aDataComponents['auth'])) {
                try {
                    $bServicesOk = $this->aDataComponents['auth']->validateServices();
                    
                    if (hexdec($this->aDataComponents['auth']->iClearAllServices) == 1) {
                        $oSTB->clearServices();
                    }
                    
                    foreach ($this->aDataComponents['auth']->aAuthRecords AS $oRec) {
                        if (hexdec($oRec->iFlag) == 1) {
                            $oSTB->addService(hexdec($oRec->iHandle));
                        } else {
                            $oSTB->removeService(hexdec($oRec->iHandle));
                        }
                    }
                } catch (Exception $e) {                    
                	$bFoundError = true;
	                $this->oResponse = new Response001($this->iSequence, $e->getCode());
                }
            }

            if (!$bFoundError) {
	            try {
    	        	$oSTB->iInstallTimestamp = time();
    	        	$oSTB->save();

	                // 0    No error.
	                $this->oResponse = new Response001($this->iSequence, 0);
	            } catch (Exception $e) {
	                $this->oResponse = new Response001($this->iSequence, $e->getCode());
	            }
            }
        } else {
            // 1006 Serial Number already exists.
            $this->oResponse = new Response001($this->iSequence, 1006);
        }
    }

    private function change()
    {
    	$bFoundError = false;
        $oSTB        = new SetTopBox(Message::hexstr($this->sSerialN));

        if ($oSTB->exists(Message::hexstr($this->sSerialN))) {
            $oSTB->iBsiCode = $this->iBsiCode;
            
            try {
                $oSTB->load();
            } catch (Exception $e) {
                $bFoundError     = true;
                $this->oResponse = new Response001($this->iSequence, $e->getCode());
            }

            if (isset($this->aDataComponents['type']) && !$bFoundError) {
                $sMask = (decbin(hexdec($this->aDataComponents['type']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->sUnitAddress = Message::hexstr($this->aDataComponents['type']->sUnitAddress);
                if ($sMask[1] == '1') $oSTB->iEqType      = hexdec($this->aDataComponents['type']->iEqType);
                if ($sMask[2] == '1') $oSTB->iEqSubType   = hexdec($this->aDataComponents['type']->iEqSubType);
            }
            
            if (isset($this->aDataComponents['plant']) && !$bFoundError) {
                $sMask = (decbin(hexdec($this->aDataComponents['plant']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->iHeadEnd    = hexdec($this->aDataComponents['plant']->iHeadEnd);
                if ($sMask[1] == '1') $oSTB->iUsPlant    = hexdec($this->aDataComponents['plant']->iUsPlant);
                if ($sMask[2] == '1') $oSTB->iDsPlant    = hexdec($this->aDataComponents['plant']->iDsPlant);
                if ($sMask[5] == '1') $oSTB->iChannelMap = hexdec($this->aDataComponents['plant']->iVcmHandle);
            }
            
            if (isset($this->aDataComponents['state']) && !$bFoundError) {
                $sMask = (decbin(hexdec($this->aDataComponents['state']->sBitMask)));
                $sMask = strrev($sMask);
                
               	if ($sMask[0] == '1') $oSTB->iOnPlant = hexdec($this->aDataComponents['state']->iOnPlant);

                if (!$this->aDataComponents['state']->validateOperation()) {
                	$bFoundError = true;

        		    // 1015 Invalid Operation Code in the State Component.
		            $this->oResponse = new Response001($this->iSequence, 1015);
                }
            }
            
            if (isset($this->aDataComponents['feature']) && !$bFoundError) {
                $sMask = (decbin(hexdec($this->aDataComponents['feature']->sBitMask)));
                $sMask = strrev($sMask);
                
                if ($sMask[0] == '1') $oSTB->iCreditAllowed    = hexdec($this->aDataComponents['feature']->iCreditAllowed);
                if ($sMask[1] == '1') $oSTB->iPurchasesAllowed = hexdec($this->aDataComponents['feature']->iPurchAllowed);
                if ($sMask[2] == '1') $oSTB->iMaxPackCost      = hexdec($this->aDataComponents['feature']->iMaxPackCost);
                if ($sMask[3] == '1') $oSTB->iTimeZoneId       = hexdec($this->aDataComponents['feature']->iTimeZoneID);
                if ($sMask[4] == '1') $oSTB->iEpgRegion        = hexdec($this->aDataComponents['feature']->iEpgRegion);
                if ($sMask[5] == '1') $oSTB->iRegionConfig     = hexdec($this->aDataComponents['feature']->iRegionConfig);
                if ($sMask[6] == '1') $oSTB->iTurnOnVC         = hexdec($this->aDataComponents['feature']->iTurnOnVC);
                if ($sMask[7] == '1') $oSTB->iTurnOffVC        = hexdec($this->aDataComponents['feature']->iTurnOffVC);
                if ($sMask[8] == '1') $oSTB->iOutputChannel    = hexdec($this->aDataComponents['feature']->iOutputChannel);
                if ($sMask[9] == '1') $oSTB->iFeatureSetting   = hexdec($this->aDataComponents['feature']->iFeatSetting);
                if ($sMask[9] == '1') $oSTB->sFeatureSetting   = $this->aDataComponents['feature']->sFeatSetting;
            }
            
            if (isset($this->aDataComponents['auth'])) {
                try {
                    $bServicesOk = $this->aDataComponents['auth']->validateServices();
                    
                    if (hexdec($this->aDataComponents['auth']->iClearAllServices) == 1) {
                        $oSTB->clearServices();
                    }
                    
                    foreach ($this->aDataComponents['auth']->aAuthRecords AS $oRec) {
                        if (hexdec($oRec->iFlag) == 1) {
                            $oSTB->addService(hexdec($oRec->iHandle));
                        } else {
                            $oSTB->removeService(hexdec($oRec->iHandle));
                        }
                    }
                } catch (Exception $e) {                    
                	$bFoundError = true;
	                $this->oResponse = new Response001($this->iSequence, $e->getCode());
                }
            }
            
            if (isset($this->aDataComponents['bsowner']) && !$bFoundError) {
                $oNewBSI = new BSI(hexdec($this->aDataComponents['bsowner']->iNewBsiCode));

                if ($oNewBSI->exists()) {
                    // Updates STB owner
                    $oSTB->iBsiCode = $oNewBSI->sName;
                } else {
                	$bFoundError = true;

        		    // 1003 BSI Code in the Business System Owner Component does not exist.
		            $this->oResponse = new Response001($this->iSequence, 1003);
                }
            }

            if (!$bFoundError) {
				try {
					$oSTB->save();

					// 0    No error.
					$this->oResponse = new Response001($this->iSequence, 0);
				} catch (Exception $e) {
					$this->oResponse = new Response001($this->iSequence, $e->getCode());
				}
            }
        } else {
            // 1005 Serial Number does not exist.
            $this->oResponse = new Response001($this->iSequence, 1005);
        }
    }

    private function delete()
    {
        $bFoundError = false;
        $oSTB        = new SetTopBox(Message::hexstr($this->sSerialN));

        if ($oSTB->exists(Message::hexstr($this->sSerialN))) {
            $oSTB->iBsiCode = $this->iBsiCode;
            
            try {
                $oSTB->load();
            } catch (Exception $e) {
                $bFoundError     = true;
                $this->oResponse = new Response001($this->iSequence, $e->getCode());
            }

            if (!$bFoundError) {
				try {
					$oSTB->delete();

					// 0    No error.
					$this->oResponse = new Response001($this->iSequence, 0);
				} catch (Exception $e) {
					$this->oResponse = new Response001($this->iSequence, $e->getCode());
				}
            }
        } else {
            // 1005 Serial Number does not exist.
            $this->oResponse = new Response001($this->iSequence, 1005);
        }
    }

    public function getResponse()
    {
        return $this->oResponse->getMessage();
    }
}
