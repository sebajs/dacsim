<?php

class Message760_CableCard_Component extends Thing
{
    const LENGTH = 108;
    
    protected $iSubtype;
    protected $sBitMask;
    protected $sCableCardID;
    protected $sHostID;
    protected $sData;
    protected $iOverride;
    
    public function __construct($sData)
    {
        $this->iSubtype     = substr($sData, 0, 2);
        $this->sBitMask     = substr($sData, 2, 8);
        $this->sCableCardID = substr($sData, 10, 34);
        $this->sHostID      = substr($sData, 44, 34);
        $this->sData        = substr($sData, 78, 28);
        $this->iOverride    = substr($sData, 106, 2);
    }
    
    public function show()
    {       
        echo " CableCard Component (".$this->iSubtype."):\n";
        echo "   BitMask:   ".$this->sBitMask.     " (".$this->sBitMask.")\n";
        echo "   CableCard: ".$this->sCableCardID. " (".Message::hexstr($this->sCableCardID).")\n";
        echo "   HostID:    ".$this->sHostID.      " (".Message::hexstr($this->sHostID).")\n";
        echo "   Data:      ".$this->sData.        " (".Message::hexstr($this->sData).")\n";
        echo "   Override:  ".$this->iOverride.    " (".hexdec($this->iOverride).")\n";
    }
}
