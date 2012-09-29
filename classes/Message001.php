<?php

class Message001 extends Payload
{
    private $iEncodedStatusWord = 1;
    private $iErrorCodeWord;
    private $iWordsToFollow;
    private $sDataWord;
    
    public function __construct($sData='')
    {                
        if ($sData != '') {
            $this->iEncodedStatusWord = substr($sData, 12, 4);
            $this->sEncodedStatusWordBits = strrev(decbin(hexdec($this->iEncodedStatusWord)));
        }
        
        if (isset($this->sEncodedStatusWordBits[1]) && $this->sEncodedStatusWordBits[1] == '1') {
            $this->iErrorCodeWord = substr($sData, 16, 4);
        }
        
        if (isset($this->sEncodedStatusWordBits[8]) && $this->sEncodedStatusWordBits[8] == '1') {
            $this->iWordsToFollow = substr($sData, 20, 4);
        }
    }
    
    public function setError($iData)
    {
        if ($iData != 0) {
            $this->iEncodedStatusWord -= 1;
            $this->iEncodedStatusWord += 2;
            $this->iErrorCodeWord     = dechex($iData);
        }
    }
    
    public function setChecksumError($bData)
    {
        if ($bData) {
            $this->iEncodedStatusWord += 4;
        }
    }
    
    public function setSizeError($bData)
    {
        if ($bData) {
            $this->iEncodedStatusWord += 8;
        }
    }
    
    public function show()
    {
        echo " Status:    ".$this->iEncodedStatusWord." (".hexdec($this->iEncodedStatusWord).")\n";
        if ($this->iErrorCodeWord != '') {
            echo " ErrorCode: ".$this->iErrorCodeWord;
            echo " (".hexdec($this->iErrorCodeWord).": ".Error::getDescription(hexdec($this->iErrorCodeWord)).")\n";
        }
        if ($this->iWordsToFollow != '') {
            echo " ToFollow:  ".$this->iWordsToFollow." (".hexdec($this->iWordsToFollow).")\n";
        }
        echo "\n";
    }
    
    public function dump()
    {
        $sDump = '';
        $sDump .= str_pad(dechex($this->iEncodedStatusWord), 4, '0', STR_PAD_LEFT);
        
        if ($this->iErrorCodeWord != '') {
            $sDump .= str_pad($this->iErrorCodeWord, 4, '0', STR_PAD_LEFT);
        }        
        
        if ($this->iWordsToFollow != '') {
            $sDump .= str_pad(dechex($this->iWordsToFollow), 4, '0', STR_PAD_LEFT);
        }
        
        return $sDump;
    }
}
