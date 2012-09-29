<?php

class MessageHeader
{
    const HEADER_LENGTH = 12;
    
    const PACKET_CHECKSUM_ERROR = 226;
    const PACKET_SIZE_ERROR     = 231;
    
    private $iError = 0;
    
    private $iSize;
    private $iSequence;
    private $iType;
    private $iCheckSum;
    
    private $bCheckSumValid;
    private $bSizeValid;
    
    public function __construct($sData='')
    {        
        if ($sData != '') {
            $this->iSize     = substr($sData, 0, 4);
            $this->iSequence = substr($sData, 4, 4);
            $this->iType     = substr($sData, 8, 4);
            $this->iCheckSum = substr($sData, -2);
            
            if ($this->isValid()) {
                $this->bCheckSumValid = $this->validateChecksum($sData);
            }
            
            if ($this->isValid()) {
                $this->bSizeValid = $this->validateSize($sData);
            }
        }
    }
    
    public function setSize($iSize)
    {
        $this->iSize = str_pad(dechex($iSize), 4, '0', STR_PAD_LEFT);
    }
    
    public function getSequence()
    {
        return $this->iSequence;
    }
    
    public function setSequence($iSequence)
    {
        $this->iSequence = str_pad($iSequence, 4, '0', STR_PAD_LEFT);
    }
    
    public function setType($iType)
    {
        $this->iType = str_pad(dechex($iType), 4, '0', STR_PAD_LEFT);
    }
    
    public function getType()
    {
        return hexdec($this->iType);
    }
    
    public function setChecksum($iCheckSum)
    {
        $this->iCheckSum = str_pad($iCheckSum, 2, '0', STR_PAD_LEFT);
    }
    
    public function getChecksum()
    {
        return hexdec($this->iCheckSum);
    }
    
    public function dumpHeader()
    {
        $sDump = '';
        $sDump .= $this->iSize;
        $sDump .= $this->iSequence;
        $sDump .= $this->iType;
        
        return $sDump;
    }
    
    public function getError()
    {
        return $this->iError;
    }
    
    public function isValid()
    {
        return ($this->iError == 0);
    }
    
    public function show()
    {
        echo " Size:      ".$this->iSize.       " (".hexdec($this->iSize).": ".(($this->bSizeValid)?"Valid":"Invalid").")\n";
        echo " Sequence:  ".$this->iSequence.   " (".hexdec($this->iSequence).")\n";
        echo " Type:      ".$this->iType.       " (".hexdec($this->iType).")\n";               
        echo " CheckSum:  ".$this->iCheckSum. "   (".(($this->bCheckSumValid)?"Valid":"Invalid").")\n";               
        echo "\n";        
    }
    
    public function getResponse()
    {      
        $oError = new Response001($this->iSequence, $this->iError, !$this->bCheckSumValid, !$this->bSizeValid);
        return $oError->getMessage();
    }
    
    private function validateChecksum($sData)
    {
        $bValid = true;
        
        $sData = substr($sData, 0, -2);
        
        $sCalcSum = self::generateChecksum($sData);
        
        if ($this->iCheckSum != $sCalcSum) {
            $this->iError = self::PACKET_CHECKSUM_ERROR;
            $bValid = false;
        }
        
        return $bValid;
    }
    
    private function validateSize($sData)
    {
        $bValid = true;
        
        $sData = substr($sData, 0, -2);
        $iSize = (strlen($sData)/2);
        
        if (hexdec($this->iSize) != $iSize) {
            $this->iError = self::PACKET_SIZE_ERROR;
            $bValid = false;
        }
        
        return $bValid;
    }
    
    public static function generateChecksum($sData)
    {
        $iSum = 0;
        for ($i=0; $i<strlen($sData); $i=$i+2) {
            $iSum += hexdec(substr($sData, $i, 2));
        }
        
        // Según documentación de DAC
        $iCalcSum = ((pow(2, 16)-1) - $iSum) + 1;
        $sCalcSum = strtoupper(dechex($iCalcSum));
        
        return substr($sCalcSum, -2);
    }
    
}
