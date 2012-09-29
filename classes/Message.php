<?php

class Message
{
    private $iError;
    private $sData;
    private $oHeader;
    private $oPayload;
    
    public function __construct($sData='')
    {
        $this->iError = 0;
        $this->sData  = $sData;
        
        if ($this->sData != '') {
        
            // Si comienza con STX se lo elimino.
            if (ord($this->sData[0]) == 2) {
                $this->sData = substr($this->sData, 1);
            }
            if ($this->sData[0] == ".") {
                $this->sData = substr($this->sData, 1);
            }
            if (substr($this->sData, 0, 2) == "^B") {
                $this->sData = substr($this->sData, 2);
            }
            if (substr($this->sData, -2) == "^M") {
                $this->sData = substr($this->sData, 0, -2);
            }
            
            echo "-HEADER---------------------------------------\n";
            $this->oHeader = new MessageHeader($this->sData);    
            $this->oHeader->show();  
            
            if ($this->oHeader->isValid()) {

                echo "-PAYLOAD--------------------------------------\n";
                switch ($this->oHeader->getType()) {
                    case 001:
                        $this->oPayload = new Message001($this->sData);
                        break;
                    case 684:
                        $this->oPayload = new Message684($this->sData);
                        break;
                    case 720:
                        $this->oPayload = new Message720($this->sData);
                        break;
                    case 721:
                        $this->oPayload = new Message721($this->sData);
                        break;
                    case 722:
                        $this->oPayload = new Message722($this->sData);
                        break;
                    case 760:
                        $this->oPayload = new Message760($this->sData);
                        break;
                    default:
                        echo " UNKNOWN MESSAGE ".$this->oHeader->getType()." (".hexdec($this->oHeader->getType()).")\n";
                        $this->iError = 1001;
                        break;
                }
                
                if (is_object($this->oPayload)) {
                    $this->oPayload->show();
                }
                
            } else {
                
                echo " INVALID HEADER (Error ".$this->oHeader->getError().": ".Error::getDescription($this->oHeader->getError()).")\n";
                
            }
        }
    }
    
    public function show()
    {
        if (is_object($this->oHeader)) {
            echo "-HEADER---------------------------------------\n";
            $this->oHeader->show();
        }
        
        if (is_object($this->oPayload)) {
            echo "-PAYLOAD--------------------------------------\n";
            $this->oPayload->show();
        }
    }
    
    public function run()
    {
        if ($this->oHeader->isValid()) {
            if (method_exists($this->oPayload, 'run')) {
                $this->oPayload->run($this->oHeader->getSequence());
            }
        }
    }
    
    public function getResponse()
    {
        $oResponse = "";
        
        if ($this->oHeader->isValid()) {
            $oResponse = $this->oPayload->getResponse($this->oHeader->getSequence());
        } else {
            $oResponse = $this->oHeader->getResponse();
        }
        
        return $oResponse;
    }
    
    public function setHeader($oHeader)
    {
        $this->oHeader = $oHeader;
    }
    
    public function setPayload($oPayload)
    {
        $this->oPayload = $oPayload;
    }
    
    public function dump()
    {
        $sDump  = '';
        $sDump .= $this->oHeader->dumpHeader();
        $sDump .= $this->oPayload->dump();
        $sDump .= str_pad(dechex($this->oHeader->getChecksum()), 2, '0', STR_PAD_LEFT);
        return strtoupper($sDump);
    }
    
    public static function showFooter($iNumber=-1)
    {
        if ($iNumber >= 0) {
            echo str_pad("", 80, "-")."\n";
            echo str_pad("=END OF REQUEST #{$iNumber}=", 80, "=")."\n";
            echo str_pad("", 80, "-")."\n";        
        } else {
            echo str_pad("", 80, "-")."\n";
            echo str_pad("=END OF MESSAGE=", 80, "=")."\n";
            echo str_pad("", 80, "-")."\n";
        }
    }
      
    public static function hexstr($hexstr) {
        $hexstr = str_replace(' ', '', $hexstr);
        $hexstr = str_replace('\x', '', $hexstr);
        $retstr = pack('H*', $hexstr);
        return $retstr;
    }
    
    public function strhex($string) { 
        $sHex = '';
        $iLen = strlen($string);
        
        for ($i=0; $i<$iLen; $i++) {
            $sHex .= strtoupper(dechex(ord($string[$i])));
        }
        
        return $sHex;
    }     
}
