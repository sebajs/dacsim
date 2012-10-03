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
                    if (!isset($this->aDataComponents['auth'])) {
                        $sHeader    = substr($sComponentsData, 0, Message760_Auth_Component::HEADER_LENGTH);
                        $iNumRecs   = hexdec(substr($sComponent, -4));
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
                $oSTB->sUnitAddress = Message::hexstr($this->aDataComponents['type']->sUnitAddress);
                $oSTB->iEqType      = hexdec($this->aDataComponents['type']->iEqType);
                $oSTB->iEqSubType   = hexdec($this->aDataComponents['type']->iEqSubType);
            }
            if (isset($this->aDataComponents['plant'])) {
                $oSTB->iHeadEnd    = hexdec($this->aDataComponents['plant']->iHeadEnd);
                $oSTB->iUsPlant    = hexdec($this->aDataComponents['plant']->iUsPlant);
                $oSTB->iDsPlant    = hexdec($this->aDataComponents['plant']->iDsPlant);
                $oSTB->iChannelMap = hexdec($this->aDataComponents['plant']->iVcmHandle);
            }
            if (isset($this->aDataComponents['state'])) {
                $oSTB->iOnPlant = hexdec($this->aDataComponents['state']->iOnPlant);

                if (!$this->aDataComponents['state']->validateOperation()) {
                	$bFoundError = true;

        		    // 1015 Invalid Operation Code in the State Component.
		            $this->oResponse = new Response001($this->iSequence, 1015);
                }
            }
            if (isset($this->aDataComponents['feature'])) {
                $oSTB->iCreditAllowed    = hexdec($this->aDataComponents['feature']->iCreditAllowed);
                $oSTB->iPurchasesAllowed = hexdec($this->aDataComponents['feature']->iPurchAllowed);
                $oSTB->iMaxPackCost      = hexdec($this->aDataComponents['feature']->iMaxPackCost);
                $oSTB->iTimeZoneId       = hexdec($this->aDataComponents['feature']->iTimeZoneID);
                $oSTB->iEpgRegion        = hexdec($this->aDataComponents['feature']->iEpgRegion);
                $oSTB->iRegionConfig     = hexdec($this->aDataComponents['feature']->iRegionConfig);
                $oSTB->iTurnOnVC         = hexdec($this->aDataComponents['feature']->iTurnOnVC);
                $oSTB->iTurnOffVC        = hexdec($this->aDataComponents['feature']->iTurnOffVC);
                $oSTB->iOutputChannel    = hexdec($this->aDataComponents['feature']->iOutputChannel);
                $oSTB->iFeatureSetting   = hexdec($this->aDataComponents['feature']->iFeatSetting);
                $oSTB->sFeatureSetting   = $this->aDataComponents['feature']->sFeatSetting;
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
                $oSTB->sUnitAddress = Message::hexstr($this->aDataComponents['type']->sUnitAddress);
                $oSTB->iEqType      = hexdec($this->aDataComponents['type']->iEqType);
                $oSTB->iEqSubType   = hexdec($this->aDataComponents['type']->iEqSubType);
            }
            
            if (isset($this->aDataComponents['plant']) && !$bFoundError) {
                $oSTB->iHeadEnd    = hexdec($this->aDataComponents['plant']->iHeadEnd);
                $oSTB->iUsPlant    = hexdec($this->aDataComponents['plant']->iUsPlant);
                $oSTB->iDsPlant    = hexdec($this->aDataComponents['plant']->iDsPlant);
                $oSTB->iChannelMap = hexdec($this->aDataComponents['plant']->iVcmHandle);
            }
            
            if (isset($this->aDataComponents['state']) && !$bFoundError) {
               	$oSTB->iOnPlant = hexdec($this->aDataComponents['state']->iOnPlant);

                if (!$this->aDataComponents['state']->validateOperation()) {
                	$bFoundError = true;

        		    // 1015 Invalid Operation Code in the State Component.
		            $this->oResponse = new Response001($this->iSequence, 1015);
                }
            }
            
            if (isset($this->aDataComponents['feature']) && !$bFoundError) {
                $oSTB->iCreditAllowed    = hexdec($this->aDataComponents['feature']->iCreditAllowed);
                $oSTB->iPurchasesAllowed = hexdec($this->aDataComponents['feature']->iPurchAllowed);
                $oSTB->iMaxPackCost      = hexdec($this->aDataComponents['feature']->iMaxPackCost);
                $oSTB->iTimeZoneId       = hexdec($this->aDataComponents['feature']->iTimeZoneID);
                $oSTB->iEpgRegion        = hexdec($this->aDataComponents['feature']->iEpgRegion);
                $oSTB->iRegionConfig     = hexdec($this->aDataComponents['feature']->iRegionConfig);
                $oSTB->iTurnOnVC         = hexdec($this->aDataComponents['feature']->iTurnOnVC);
                $oSTB->iTurnOffVC        = hexdec($this->aDataComponents['feature']->iTurnOffVC);
                $oSTB->iOutputChannel    = hexdec($this->aDataComponents['feature']->iOutputChannel);
                $oSTB->iFeatureSetting   = hexdec($this->aDataComponents['feature']->iFeatSetting);
                $oSTB->sFeatureSetting   = $this->aDataComponents['feature']->sFeatSetting;
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
