<?php

class Bootstrap
{   
    public static function run($argv)
    {
        // Obtener los parametros desde línea de comandos
        $sStream       = '';
        $sHost         = '';
        $iPort         = 0;
        $bSendResponse = true;
        $bStartSim     = true;

        foreach ($argv as $key => $argument) {
            switch ($argument) {
                case '-h':
                case '--host':
                    $sHost = $argv[$key + 1];
                    break;
                case '-p':
                case '--port';
                    $iPort = $argv[$key + 1];
                    break;
                case '--no-response';
                    $bSendResponse = false;
                    break;
                case '--checksum':
                    $bStartSim = false;
                    $sStream   = $argv[$key + 1];
                    ParserRunner::checksum($sStream);
                    break;
                case '-s';
                case '--stream';
                    $bStartSim = false;
                    $sStream   = $argv[$key + 1];
                    ParserRunner::run($sStream);
                    break;
                case '--describe-stb':
                    $bStartSim     = false;
                    $sSerialNumber = $argv[$key + 1];
                    try {
                        Topology::showSetTopBox($sSerialNumber);
                    } catch (Exception $e) {
                        Output::line('Error: '.$e->getMessage());
                    }
                    break;
                case '--show-all':
                    $bStartSim = false;
                    $aTypes    = Topology::getObjectTypes();
                    try {
                        foreach ($aTypes AS $sType) {
                            if ($sType != 'stb') {
                                Topology::show($sType);
                            }
                        }
                    } catch (Exception $e) {
                        Output::line('Error: '.$e->getMessage());
                    }
                    break;
                case '--show':
                    $bStartSim   = false;
                    $sObjectType = $argv[$key + 1];
                    try {
                        Topology::show($sObjectType);
                    } catch (Exception $e) {
                        Output::line('Error: '.$e->getMessage());
                    }
                    break;
                case '--create':
                    $bStartSim   = false;
                    $sObjectType = $argv[$key + 1];
                    $sObjectName = $argv[$key + 2];
                    try {
                        Topology::create($sObjectType, $sObjectName);
                        Output::line($sObjectType." created successfuly");
                    } catch (Exception $e) {
                        Output::line('Error: '.$e->getMessage());
                    }
                    break;
                case '--delete':
                    $bStartSim   = false;
                    $sObjectType = $argv[$key + 1];
                    $sObjectName = $argv[$key + 2];
                    try {
                        Topology::delete($sObjectType, $sObjectName);
                        Output::line($sObjectType." deleted successfuly");
                    } catch (Exception $e) {
                        Output::line('Error: '.$e->getMessage());
                    }
                    break;
                
            }
        }

        if ($bStartSim) {
            
            SimulatorRunner::run($sHost, $iPort, $bSendResponse);

        }
    }
}
