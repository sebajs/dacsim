<?php

require_once('config/config.php');

error_reporting(E_ALL);

/* Permitir al script esperar para conexiones. */
set_time_limit(0);

/* Activar el volcado de salida implícito, así veremos lo que estamo obteniendo
 * mientras llega. */
ob_implicit_flush();

// Obtener los parametros desde línea de comandos
$sStream       = '';
$sHost         = '';
$iPort         = 0;
$bSendResponse = true;
$bStartSim     = true;

$argv = $_SERVER['argv'];

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
        case '-s';
        case '--stream';
            $bStartSim = false;
            $sStream   = $argv[$key + 1];
            ParserRunner::run($sStream);
            break;
        case '--describe-stb':
            $bStartSim     = false;
            $sSerialNumber = $argv[$key + 1];
            Topology::showSetTopBox($sSerialNumber);
            break;
        case '--show':
            $bStartSim   = false;
            $sObjectType = $argv[$key + 1];
            try {
                Topology::show($sObjectType);
            } catch (Exception $e) {
                echo 'Error: ',  $e->getMessage(), "\n";
            }
            break;
        case '--create':
            $bStartSim   = false;
            $sObjectType = $argv[$key + 1];
            $sObjectName = $argv[$key + 2];
            try {
                Topology::create($sObjectType, $sObjectName);
                echo $sObjectType." created successfuly\n";
            } catch (Exception $e) {
                echo 'Error: ',  $e->getMessage(), "\n";
            }
            break;
        case '--delete':
            $bStartSim   = false;
            $sObjectType = $argv[$key + 1];
            $sObjectName = $argv[$key + 2];
            try {
                Topology::delete($sObjectType, $sObjectName);
                echo $sObjectType." deleted successfuly\n";
            } catch (Exception $e) {
                echo 'Error: ',  $e->getMessage(), "\n";
            }
            break;
        
    }
}

if ($bStartSim) {
    
    SimulatorRunner::run($sHost, $iPort, $bSendResponse);

}
