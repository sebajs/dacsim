<?php

class Message760_State_Component
{
    const LENGTH = 14;
    
    private $iSubtype;
    private $sBitMask;
    private $iOnPlant;
    private $iOpCode;
    
    public function __construct($sData)
    {
        $this->iSubtype = substr($sData, 0, 2);
        $this->sBitMask = substr($sData, 2, 8);
        $this->iOnPlant = substr($sData, 10, 2);
        $this->iOpCode  = substr($sData, 12, 2);        
    }
    
    public function show()
    {
        $aOpCodes = array('', 
                          'Cold Init', 
                          'Init', 
                          'Refresh', 
                          'Warm Reset', 
                          'De-Activate', 
                          'Activate', 
                          'Clear PIN', 
                          'Factory Reset');            
        
        echo " State Component (".$this->iSubtype."):\n";
        echo "   BitMask:   ".$this->sBitMask.    " (".$this->sBitMask.")\n";
        echo "   OnPlant:   ".$this->iOnPlant.    " (".hexdec($this->iOnPlant).")\n";
        echo "   Op Code:   ".$this->iOpCode.     " (".hexdec($this->iOpCode).": ".$aOpCodes[hexdec($this->iOpCode)].")\n";
    }
}
