<?php

class Message760 extends Payload
{
    const IDENTIFIER_COMP_LENGTH = 30;
    const STATUS_COMP_LENGTH     = 40;
    const BSOWNER_COMP_LENGTH    = 6;
    
    private $oResponse;
    private $aReqTypes;
    private $iBsiCode;
    private $iReqType;
    private $sSerialN;
    private $aDataComponents;
    
    public function __construct($sData)
    {        
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
        
        if (isset($this->aDataComponents['type'])) {
            $this->aDataComponents['type']->show();
        }
        
        if (isset($this->aDataComponents['plant'])) {
            $this->aDataComponents['plant']->show();

        }
        
        if (isset($this->aDataComponents['state'])) {
            $this->aDataComponents['state']->show();
        }
        
        if (isset($this->aDataComponents['feature'])) {
            $this->aDataComponents['feature']->show();
        }
        
        echo "\n";
    }
    
    public function run($iSequence)
    {
        switch ($this->iReqType) {
            case 1: //Add
                $this->add($iSequence);
                break;
            case 2: //Change
                $this->change($iSequence);
                break;
            case 3: //Delete
                $this->delete($iSequence);
                break;
            default:
                // 3011 Invalid Message Type.
                $this->oResponse = new Response001($iSequence, 3011);
                break;
        }
    }
    
    private function extractComponents($sComponentsData)
    {        
        // Busco type component
        if (substr($sComponentsData, 0, 2) == '01') {
            $sTypeCompData = substr($sComponentsData, 0, Message760_Type_Component::LENGTH);
            
            $this->aDataComponents['type'] = new Message760_Type_Component($sTypeCompData);
            
            $sComponentsData = substr($sComponentsData, Message760_Type_Component::LENGTH);
        }
        
        // Busco plant component
        if (substr($sComponentsData, 0, 2) == '02') {
            $sPlantCompData = substr($sComponentsData, 0, Message760_Plant_Component::LENGTH);
            
            $this->aDataComponents['type'] = new Message760_Plant_Component($sPlantCompData);
            
            $sComponentsData = substr($sComponentsData, Message760_Plant_Component::LENGTH);
        }
        
        // Busco state component
        if (substr($sComponentsData, 0, 2) == '03') {
            $sPlantCompData = substr($sComponentsData, 0, Message760_State_Component::LENGTH);
            
            $this->aDataComponents['state'] = new Message760_State_Component($sPlantCompData);
            
            $sComponentsData = substr($sComponentsData, Message760_State_Component::LENGTH);
        }
        
        // Busco feature component
        if (substr($sComponentsData, 0, 2) == '04') {
            $sFeatureCompData = substr($sComponentsData, 0, Message760_Feature_Component::LENGTH);
            
            $this->aDataComponents['feature'] = new Message760_Feature_Component($sFeatureCompData);
            
            $sComponentsData = substr($sComponentsData, Message760_Feature_Component::LENGTH);
        }
        
        // Busco status component
        if (substr($sComponentsData, 0, 2) == '05') {
            $sStatusCompData = substr($sComponentsData, 0, self::STATUS_COMP_LENGTH);
            
            $sComponentsData = substr($sComponentsData, self::STATUS_COMP_LENGTH);
        }
        
        // Busco BSOwner component
        if (substr($sComponentsData, 0, 2) == '06') {
            $sBsOwnerCompData = substr($sComponentsData, 0, self::BSOWNER_COMP_LENGTH);
            
            $sComponentsData = substr($sComponentsData, self::BSOWNER_COMP_LENGTH);
        }
        
        // Busco authorization component
        if (substr($sComponentsData, 0, 2) == '07') {
            $sAuthCompData = $sComponentsData;
        }        
    }
    
    private function add($iSequence)
    {
        // TODO Esto se rompió!
        // Se debe instanciar el STB y lueg ejecutar el exists()
        
        if (!SetTopBox::exists($this->sSerialN)) {
            $oSTB = new SetTopBox($this->sSerialN);
            $oSTB->save();
            
            // 0    No error.
            $this->oResponse = new Response001($iSequence, 0);
        } else {
            // 1006 Serial Number already exists.
            $this->oResponse = new Response001($iSequence, 1006);
        }
    }
    
    private function change($iSequence)
    {
        if (SetTopBox::exists($this->sSerialN)) {
            $oSTB = new SetTopBox($this->sSerialN);
            $oSTB->save();
            
            // 0    No error.
            $this->oResponse = new Response001($iSequence, 0);
        } else {
            // 1005 Serial Number does not exist.
            $this->oResponse = new Response001($iSequence, 1005);            
        }
    }
    
    private function delete($iSequence)
    {
        if (SetTopBox::exists($this->sSerialN)) {
            $oSTB = new SetTopBox($this->sSerialN);
            $oSTB->delete();
            
            // 0    No error.
            $this->oResponse = new Response001($iSequence, 0);
        } else {
            // 1005 Serial Number does not exist.
            $this->oResponse = new Response001($iSequence, 1005);            
        }
    }
    
    public function getResponse($iSequence)
    {      
        return $this->oResponse->getMessage();
    }    
}
