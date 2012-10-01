#!/usr/bin/php
<?php

require_once('config/config.php');

error_reporting(E_ALL);

/* Permitir al script esperar para conexiones. */
set_time_limit(0);

/* Activar el volcado de salida implícito, así veremos lo que estamo obteniendo
 * mientras llega. */
ob_implicit_flush();

Bootstrap::run($_SERVER['argv']);
