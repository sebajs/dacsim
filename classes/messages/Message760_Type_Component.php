<?php

class Message760_Type_Component extends Thing
{
    const LENGTH = 50;
    
    protected $iSubtype;
    protected $sBitMask;
    protected $sUnitAddress;
    protected $iEqType;
    protected $iEqSubType;
    
    public function __construct($sData)
    {
        $this->iSubtype     = substr($sData, 0, 2);
        $this->sBitMask     = substr($sData, 2, 8);
        $this->sUnitAddress = substr($sData, 10, 32);
        $this->iEqType      = substr($sData, 42, 4);
        $this->iEqSubType   = substr($sData, 46, 4);
    }
    
    public function show()
    {
        echo " Type Component (".$this->iSubtype."):\n";
        echo "   BitMask:   ".$this->sBitMask.    " (".$this->sBitMask.")\n";
        echo "   UnitAdd:   ".$this->sUnitAddress." (".Message::hexstr($this->sUnitAddress).")\n";
        echo "   EqType:    ".$this->iEqType.     " (".hexdec($this->iEqType).")\n";
        echo "   EqSubType: ".$this->iEqSubType.  " (".hexdec($this->iEqSubType).")\n";
    }
}
