<?php

/*
 *  Ce fichier contient les fonctions et constantes utiles pour les autres
 *  fonctions ou modules du dossier.
 */

/**
 * Logge $sMsg dans le fichier de log, en préfixant par la date et la sessionId
 *
 * @param string $sMsg Le message à logger
 *
 * @return none
 *
 * @const string CONSOLE_LOG penser à définir le nom du fichier de log en amont
 */
function console_log($sMsg)
{
    $sDate = date('Y-m-d H:i:s');
    $sData = $sDate . " : " . $sMsg;
    /*
      if (!defined('CONSOLE_LOG')) {
      echo "<p class='debug'> ** DEBUG : $sMsg</p>";
      } else {
      file_put_contents(CONSOLE_LOG, $sData . "\n", FILE_APPEND);
      } */
    if (!defined('CONSOLE_LOG')) {
        define('CONSOLE_LOG', DOCUMENT_ROOT . '/console.log');
    }
    file_put_contents(CONSOLE_LOG, $sData . "\n", FILE_APPEND);
}

/**
 * Vérifie qu'on puisse écrire dans le fichier de config
 *
 * @return None
 *
 * @throws Exception quand il est pas content
 */
function console_log_check_file()
{
    if (!defined('CONSOLE_LOG')) {
        throw new Exception("console_log: config error ");
    }
    if (!file_exists(CONSOLE_LOG)) {
        throw new Exception("console_log: missing log file");
    }
    if (!is_writeable(CONSOLE_LOG)) {
        throw new Exception("console_log: log file not writeable");
    }
}
