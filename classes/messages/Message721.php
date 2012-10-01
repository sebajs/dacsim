<?php

class Message721 extends Payload
{
    const PURCHASE_1_START = 16;
    const PURCHASE_2_START = 122;
    
    protected $iBsiCode;
    protected $iLastRecord;
    protected $aPurchases;
    
    public function __construct($sData)
    {                
        $this->iBsiCode = substr($sData, 12, 4);    
        $this->iLastRecord = substr($sData, -4, 2);
        
        if ($this->iLastRecord != '05') {
            $this->aPurchases[0] = new Purchase(substr($sData, self::PURCHASE_1_START, (self::PURCHASE_1_START+Purchase::LENGTH)));
            if ($this->iLastRecord == '07') {
                $this->aPurchases[1] = new Purchase(substr($sData, self::PURCHASE_2_START, (self::PURCHASE_2_START+Purchase::LENGTH)));
            }
        }
    }
    
    public function show()
    {
        echo " BSICode:   ".$this->iBsiCode." (".hexdec($this->iBsiCode).")\n";
        echo " LastRec:   ".$this->iLastRecord." (".hexdec($this->iLastRecord).")\n";
        echo "\n";
        
        if (isset($this->aPurchases[0])) {
            echo " --- Purchase 0 ---\n";
            $this->aPurchases[0]->show();
            
            if (isset($this->aPurchases[1])) {
                echo " --- Purchase 1 ---\n";
                $this->aPurchases[1]->show();
            } else {
                echo " --- No second purchase! ---\n";
            }
        } else {
            echo " --- No more purchases! ---\n";
        }
        
        echo "\n";
    }
}
