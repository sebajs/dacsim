<?php

class Message722 extends Payload
{
    protected $iBsiCode;
    protected $iAckNak;
    
    public function __construct($sData)
    {                
        $this->iBsiCode = substr($sData, 12, 4);    
        $this->iAckNak  = substr($sData, 16, 2);    
    }
    
    public function show()
    {
        echo " BSICode:   ".$this->iBsiCode." (".hexdec($this->iBsiCode).")\n";
        echo " ACK_NAK:   ".$this->iAckNak." (".hexdec($this->iAckNak).")\n";
        echo "\n";        
    }
}
