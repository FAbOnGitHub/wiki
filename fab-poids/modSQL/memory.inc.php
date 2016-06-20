<?php

/*
 * Ce fichier contient des fonctions utiles au débug pour par exemple afficher
 * la pile des appels.
 */

// Pour console_log
require_once dirname(__FILE__).'/commun.inc.php';

/**
 * Fonction à inclure comme un coucou
 */
function log_backtrace()
{
    $aBT = debug_backtrace();
    console_log(var_export($aBT, true));
}

define('BACKTRACE_FULL', 1);
define('BACKTRACE_LIGHT', 2);
/**
 * Affiche la pile des appels
 *
 * @global boolean $BACKTRACE_VERBOSE
 *
 * @return string la pile en texte brut
 */
function getBackTrace()
{
    global $BACKTRACE_VERBOSE;
    $sRes = '';
    $aBT = debug_backtrace();
    if ($BACKTRACE_VERBOSE == BACKTRACE_FULL) {
        $Res = var_export($aBT, true);
    } else {
        //echo "<pre>".var_export($aBT)."</pre>";
        $iLine = 0;
        foreach ($aBT as $aNode) {
            $sRes .= "[" . ++$iLine . "]";
            if (isset($aNode['file'])) {
                $sRes .= '[file=' . $aNode['file'] . ']';
            }
            if (isset($aNode['line'])) {
                $sRes .= '[line=' . $aNode['line'] . ']';
            }
            if (isset($aNode['function'])) {
                $sRes .= '[function=' . $aNode['function'] . ']';
            }
            $sRes = "\n";
        }
    }
    return $sRes;
}

/**
 * Affiche toute la mémoire et la pile si appelé dans un gestionnaire d'erreur
 *  et répond à la signature des error-handler pour être appel ad-hoc
 *  bool handler ( int $errno , string $errstr [, string $errfile [, int $errline [, array $errcontext ]]] )
 *
 * @param int    $iErrno      numéro d'erreur
 * @param stting $sErrstr     libellé d'erreur
 * @param string $sErrfile    le fichier en erreur
 * @param int    $iErrline    numéro de la ligne dans le fichier
 * @param array  $aErrcontext le contexte
 *
 * @return string $sBuffer
 */
function dumpMemory($iErrno, $sErrstr, $sErrfile, $iErrline, $aErrcontext)
{
    global $BACKTRACE_VERBOSE;

    $sBuffer = "";
    $aLines = file($sErrfile);
    $sLine = $aLines[$iErrline];
    unset($aLines);
    $sBuffer .= "*** ERROR $iErrno: $sErrfile:$iErrline: $sErrstr\n$sLine\n"
            . "Backtrace =\n"
            . getBackTrace();
    if ($BACKTRACE_VERBOSE == BACKTRACE_FULL) {
        $sBuffer .= "\nContext =\n" . var_export($aErrcontext, true);
    }
    return $sBuffer;
}

function errorMemory($iErrno, $sErrstr, $sErrfile, $iErrline, $aErrcontext)
{
    echo dumpMemory($iErrno, $sErrstr, $sErrfile, $iErrline, $aErrcontext);
}

function errorLogMemory($iErrno, $sErrstr, $sErrfile, $iErrline, $aErrcontext)
{
    $sTrait = "***************** DUMP MEMOIRE ******************";
    console_log($sTrait . " vvvvv");
    console_log(
            dumpMemory($iErrno, $sErrstr, $sErrfile, $iErrline, $aErrcontext)
    );
    console_log($sTrait . " ^^^^^");
    trigger_error($sErrstr, E_USER_WARNING);
}
