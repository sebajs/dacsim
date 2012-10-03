<?php

class Message684 extends Payload
{
    private $aActionCodes;
    
    protected $iBsiCode;
    protected $iHeadEndNumber;
    protected $iEquipType;
    protected $iEquipSubType;
    protected $sSerialNumber;
    protected $iActionCode;
    
    public function __construct($iSequence, $sData)
    {                
        parent::__construct($iSequence);

        $this->aActionCodes = array('', 
                                    'Query Service Authorizations', 
                                    'Poll Purchases', 
                                    'Query Features', 
                                    'Query Package Authorizations', 
                                    'Query Program Authorizations');
        
        $this->iBsiCode       = substr($sData, 12, 4);    
        $this->iHeadEndNumber = substr($sData, 16, 4);    
        $this->iEquipType     = substr($sData, 20, 4);    
        $this->iEquipSubType  = substr($sData, 24, 4);    
        $this->sSerialNumber  = substr($sData, 28, 24);    
        $this->iActionCode    = substr($sData, 52, 2);    
    }
    
    public function show()
    {
        echo " BSICode:   ".$this->iBsiCode." (".hexdec($this->iBsiCode).")\n";
        echo " HeadEnd:   ".$this->iHeadEndNumber." (".hexdec($this->iHeadEndNumber).")\n";
        echo " EqType:    ".$this->iEquipType." (".hexdec($this->iEquipType).")\n";
        echo " EqSubType: ".$this->iEquipSubType." (".hexdec($this->iEquipSubType).")\n";
        echo " SerialN:   ".$this->sSerialNumber." (".Message::hexstr($this->sSerialNumber).")\n";
        echo " ActionCod: ".$this->iActionCode." (".hexdec($this->iActionCode).": ".$this->aActionCodes[hexdec($this->iActionCode)].")\n";
        echo "\n";        
    }
}
