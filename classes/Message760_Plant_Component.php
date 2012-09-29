<?php

class Message760_Plant_Component
{
    const LENGTH = 58;
    
    private $iSubtype;
    private $sBitMask;
    private $iHeadEnd;
    private $iUsPlant;
    private $iDsPlant;
    private $iReserved1;
    private $iReserved2;
    private $iVcmHandle;
    
    public function __construct($sData)
    {
        $this->iSubtype   = substr($sData, 0, 2);
        $this->sBitMask   = substr($sData, 2, 8);
        $this->iHeadEnd   = substr($sData, 10, 8);
        $this->iUsPlant   = substr($sData, 18, 8);
        $this->iDsPlant   = substr($sData, 26, 8);
        $this->iReserved1 = substr($sData, 34, 8);
        $this->iReserved2 = substr($sData, 42, 8);
        $this->iVcmHandle = substr($sData, 50, 8);
    }
    
    public function show()
    {
        echo " Plant Component (".$this->iSubtype."):\n";
        echo "   BitMask:   ".$this->sBitMask.    " (".$this->sBitMask.")\n";
        echo "   HeadEnd:   ".$this->iHeadEnd.    " (".hexdec($this->iHeadEnd).")\n";
        echo "   US Plant:  ".$this->iUsPlant.    " (".hexdec($this->iUsPlant).")\n";
        echo "   DS Plant:  ".$this->iDsPlant.    " (".hexdec($this->iDsPlant).")\n";
        echo "   Reserved1: ".$this->iReserved1.  " (".hexdec($this->iReserved1).")\n";
        echo "   Reserved2: ".$this->iReserved2.  " (".hexdec($this->iReserved2).")\n";
        echo "   VCMhandle: ".$this->iVcmHandle.  " (".hexdec($this->iVcmHandle).")\n";
    }
}
