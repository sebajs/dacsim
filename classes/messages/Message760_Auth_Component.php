<?php

class Message760_Auth_Component extends Thing
{
    const HEADER_LENGTH = 12;
    const RECORD_LENGTH = 20;
    
    protected $iSubtype;
    protected $iClearAllPackages;
    protected $iClearAllServices;
    protected $iClearAllPrograms;
    protected $iNumRecords;
    protected $aAuthRecords;
    
    public function __construct($sData)
    {
        $this->iSubtype          = substr($sData, 0, 2);
        $this->iClearAllPackages = substr($sData, 2, 2);
        $this->iClearAllServices = substr($sData, 4, 2);
        $this->iClearAllPrograms = substr($sData, 6, 2);
        $this->iNumRecords       = substr($sData, 8, 4);
        $this->aAuthRecords      = array();
        
        for ($i=0; $i<$this->iNumRecords; $i++) {
            $iStartPos = self::HEADER_LENGTH+($i*self::RECORD_LENGTH);
            
            $this->aAuthRecords[$i] = new stdClass();
            $this->aAuthRecords[$i]->iFlag    = substr($sData, $iStartPos, 2);
            $this->aAuthRecords[$i]->iType    = substr($sData, $iStartPos+2, 2);
            $this->aAuthRecords[$i]->iHandle  = substr($sData, $iStartPos+4, 8);
            $this->aAuthRecords[$i]->iProgram = substr($sData, $iStartPos+12, 8);
        }
    }
    
    public function show()
    {        
        echo " Authorization Component (".$this->iSubtype."):\n";
        echo "   Clear Pkg: ".$this->iClearAllPackages." (".hexdec($this->iClearAllPackages).")\n";
        echo "   Clear Srv: ".$this->iClearAllServices." (".hexdec($this->iClearAllServices).")\n";
        echo "   Clear Prg: ".$this->iClearAllPrograms." (".hexdec($this->iClearAllPrograms).")\n";
        echo "   Num Recs:  ".$this->iNumRecords.      " (".hexdec($this->iNumRecords).")\n";
        
        foreach ($this->aAuthRecords AS $iPos => $oRec) {
            echo "     ".$iPos.".Flag:    ".$oRec->iFlag.   " (".hexdec($oRec->iFlag).")\n";
            echo "       Type:    ".$oRec->iType.   " (".hexdec($oRec->iType).")\n";
            echo "       Handle:  ".$oRec->iHandle. " (".hexdec($oRec->iHandle).")\n";
            echo "       Program: ".$oRec->iProgram." (".hexdec($oRec->iProgram).")\n";
        }
    }
    
    public function validateServices()
    {
        $oTempService = new Service('');
        
        foreach ($this->aAuthRecords AS $oRecord) {
            if (!$oTempService->exists(hexdec($oRecord->iHandle))) {
                // 1020 Service Handle does not exist for this Business System.
                $iError = 1020;                
                throw new Exception("Unknown Service Error {$iError}", $iError);
            }
        }
        
        return true;               
    }
}
