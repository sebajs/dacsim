<?php

class Purchase
{
    const LENGTH = 106;
    
    private $sSerialNumber;
    private $iServiceHandle;
    private $iProgramHandle;
    private $sPollDate;
    private $sStartDate;
    
    public function __construct($sData)
    {                
        $this->sSerialNumber  = substr($sData, 0, 24);
        $this->iServiceHandle = substr($sData, 24, 8);
        $this->iProgramHandle = substr($sData, 32, 8);
        $this->sPollDate      = substr($sData, 40, 32);
        $this->sStartDate     = substr($sData, 74, 32);
    }
    
    public function show()
    {   
        echo " SerialN:   ".$this->sSerialNumber. " (".Message::hexstr($this->sSerialNumber).")\n";
        echo " Service:   ".$this->iServiceHandle." (".hexdec($this->iServiceHandle).")\n";
        echo " Program:   ".$this->iProgramHandle." (".hexdec($this->iProgramHandle).")\n";
        echo " PollDate:  ".$this->sPollDate.     " (".Message::hexstr($this->sPollDate).")\n";
        echo " StartDate: ".$this->sStartDate.    " (".Message::hexstr($this->sStartDate).")\n";
        echo "\n"; 
    }
}
