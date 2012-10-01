<?php

class Response001
{
    private $oMessage;
    
    public function __construct($iSequence, $iError, $bCheckSumValid=false, $bSizeValid=false)
    {                
        $this->oMessage = new Message();
        
        $oPayload = new Message001();
        $oPayload->setError($iError);
        $oPayload->setChecksumError($bCheckSumValid);
        $oPayload->setSizeError($bSizeValid);
        
        $this->oMessage->setPayload($oPayload);
        
        $oHeader = new MessageHeader();
        $oHeader->setSize(6+$oPayload->getSize());        
        $oHeader->setSequence($iSequence);
        $oHeader->setType(1);
        
        $sData = $oHeader->dumpHeader().$oPayload->dump();
        $oHeader->setChecksum(MessageHeader::generateChecksum($sData));
        
        $this->oMessage->setHeader($oHeader);
    }
    
    public function getMessage()
    {
        return $this->oMessage;
    }
}
