<?php

class Message760_BsOwner_Component extends Thing
{
    const LENGTH = 6;
    
    protected $iSubtype;
    protected $iNewBsiCode;
    
    public function __construct($sData)
    {
        $this->iSubtype    = substr($sData, 0, 2);
        $this->iNewBsiCode = substr($sData, 2, 4);
    }
    
    public function show()
    {
        echo " BS Owner Component (".$this->iSubtype."):\n";
        echo "   New BSI:   ".$this->iNewBsiCode.  " (".hexdec($this->iNewBsiCode).")\n";
    }
}
