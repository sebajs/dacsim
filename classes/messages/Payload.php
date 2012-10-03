<?php

abstract class Payload extends Thing
{
    protected $iSequence;
    
    public function __construct($iSequence)
    {
        $this->iSequence = $iSequence;
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
