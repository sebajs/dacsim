<?php

class Message760_State_Component extends Thing
{
    const LENGTH = 14;
    
    protected $iSubtype;
    protected $sBitMask;
    protected $iOnPlant;
    protected $iOpCode;
    
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
                          'Factory Reset', 
                          'Mate', 
                          'Remate', 
                          'Validate', 
                          'Re-Validate');            
        
        echo " State Component (".$this->iSubtype."):\n";
        echo "   BitMask:   ".$this->sBitMask.    " (".$this->sBitMask.")\n";
        echo "   OnPlant:   ".$this->iOnPlant.    " (".hexdec($this->iOnPlant).")\n";
        echo "   Op Code:   ".$this->iOpCode.     " (".hexdec($this->iOpCode).": ".$aOpCodes[hexdec($this->iOpCode)].")\n";
    }
    
    public function validateOperation()
    {
    	return ($this->iOpCode >= 1 && $this->iOpCode <= 8);
    }
}
