<?php

/*
 * Ce fichier contient un ensemble de fonctions dont le but est de remplacer
 * les appels à la famille de fonction mysql_* qui sont obsolètes.
 * Attention elles ont vraiement vocations à remplacer les fonctions
 * 'impératives' et non en objet. Elles ont été écrites pour le projet SOMLIT.

 *
 * *mysqli*
 * Ces fonctions ont la même signatures que celles qu'elles remplacent en
 * version mysqli on peut dont les utiliser ad-hoc.
 *
 * *log*
 * De plus elles offrent un service de log paramétrables :
 * rien > requête > +résultat
 * Les logs sont au format humain (sur plusieurs lignes) ET aussi au
 * format json ce qui permet de faire un grep sur le fichier de log à la
 * recherche du numéro de session. (Sur plusieurs lignes on perd bouts)
 *
 * Attention en version impérative, ces fonctions utilise un fichier de log
 * qui est donné par la constante CONSOLE_LOG
 *
 */

$sPwd =  dirname(__FILE__);
require_once $sPwd . '/commun.inc.php'; // Pour console_log


if (!defined('DOCUMENT_ROOT')) {
    define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
}
if (!defined('CONSOLE_LOG')) {
    define('CONSOLE_LOG', DOCUMENT_ROOT . '/console.log');
}


/*
 * Constantes qui servent au paramètres iDebug, on peut cumuler les variables
 */
/**
 * const SQL_DEBUG_NONE n'affiche rien
 */
define('SQL_DEBUG_NONE', 0);
/**
 * const SQL_DEBUG_QUERY affiche la requête exécutée
 */
define('SQL_DEBUG_QUERY', 1);
/**
 * const SQL_DEBUG_ERROR affiche qu'en cas d'erreur ?
 */
define('SQL_DEBUG_ERROR', 1);
/**
 * const SQL_DEBUG_RESULTS affiche tout
 */
define('SQL_DEBUG_RESULTS', 2);

/**
 * Encapsule mysql_query pour avoir un système de log, compatible avec
 *  la signature de MySQLi si on migre plus tard.
 * Cette fonction retourne des résultats contrairement à sql_query_log
 *
 * @global ressource $dbprotect lien BDD
 *
 * @param string    $sRequete la requête
 * @param boolean   $iDebug   affiche msg
 * @param ressource $oDBLink  lien BDD en option
 *
 * @return array les résultats ou false
 */
function sql_query($sRequete, $iDebug = SQL_DEBUG_NONE, $oDBLink = null)
{
    global $dbprotect;

    if ($oDBLink == null) {
        if (!$dbprotect) {
            $sMsgErr = __FILE__ . ":" . __FUNCTION__ . '() $dbprotect is null';
            console_log($sMsgErr);
            return false;
        }
        $oDBLink = $dbprotect;
    }

    $sJsonRequete = json_encode($sRequete);
    $oRessource = mysql_query($sRequete, $oDBLink);
    if (!$oRessource) {
        $sMsgErr = "MySQL error: "
                . mysql_errno() . " : " . mysql_error()
                . " on < $sJsonRequete >";
        $sMsgErr .= "\nclear=" . $sRequete;
        console_log($sMsgErr);
        return false;
    }

    $aResults = array();
    while ($aLine = mysql_fetch_array($oRessource, MYSQL_ASSOC)) {
        $aResults[] = $aLine;
    }

    if ($iDebug >= SQL_DEBUG_QUERY) {
        console_log("sql_query() [sRequete=" . $sJsonRequete . "]");
    }
    if ($iDebug >= SQL_DEBUG_RESULTS) {
        //console_log("sql_query() aResults=" . var_export($aResults, true));
        console_log("sql_query() aResults=" . json_encode($aResults));
    }

    return $aResults;
}

/**
 * sql_query_log remplace mysql_query pour avoir un système de log,
 *  compatible avec la signature de MySQLi si on migre plus tard.
 *  Attention, retourne une ressource et non un tableau de résultats
 *
 * @global ressource $dbprotect lien BDD
 *
 * @param string    $sRequete la requête
 * @param boolean   $iDebug   affiche msg
 * @param ressource $oDBLink  lien BDD en option
 *
 * @return array les résultats ou false
 */
function sql_query_log($sRequete, $iDebug = SQL_DEBUG_NONE, $oDBLink = null)
{
    global $dbprotect;

    if ($oDBLink == null) {
        $oDBLink = $dbprotect;
    }

    $sJsonRequete = json_encode($sRequete);
    $oRessource = mysql_query($sRequete, $oDBLink);
    if (!$oRessource) {
        $sMsgErr = "MySQL error: "
                . mysql_errno() . " : " . mysql_error()
                . " on < $sJsonRequete >\n$sRequete";
        console_log($sMsgErr);
        log_backtrace();
        return false;
    }
    if ($iDebug >= SQL_DEBUG_QUERY) {
        console_log("sql_query_log() debug sRequete=" . $sJsonRequete
                . " \nclear" . var_export($sRequete, true));
    }
    return $oRessource;
}
