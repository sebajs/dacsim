<?php

class Payload
{
    
    public function __construct()
    {                
    }
    
    public function dump()
    {
        return '';
    }
    
    public function getSize()
    {
        return (strlen($this->dump())/2); 
    }
    
    public function getResponse($iSequence)
    {      
        $oResponse = new Response001($iSequence, 0);
        return $oResponse->getMessage();
    }
}
