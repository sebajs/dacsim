<?php

class Message720 extends Payload
{
    protected $iBsiCode;
    
    public function __construct($sData)
    {                
        $this->iBsiCode = substr($sData, 12, 4);    
    }
    
    public function show()
    {
        echo " BSICode:   ".$this->iBsiCode." (".hexdec($this->iBsiCode).")\n";
        echo "\n";        
    }
}
