<?php

/**
 * Inclusion diverses, déclaration des fonctions qui vont bien
 *
 *  * PHP version 5
 *
 * @category   RLPoids
 * @package    RLPoids
 * @author     Fabrice Mendes <fab@antaya.fr>
 * @author     Thierry Vezin <contact@randonner-leger.org>
 * @copyright  2016 randonner-leger.org
 * @license    https://creativecommons.org/licenses/by-nc-sa/4.0/ CC-by-nc-sa
 * @version    SVN: $Id:$
 * @link       None
 * @see        NetOther, Net_Sample::Net_Sample(
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */

/**
 * Nettoie $_GET et $_POST pour coller au script existant
 *
 * @global object $oHtmlPurifier Instance antiXss
 *
 * @return none
 */
function sanitizeAllInputs()
{
    global $oHtmlPurifier;

    if (count($_GET)) {
        foreach ($_GET as $sKey => $mValue) {
            $_GET[$sKey] = $oHtmlPurifier->purify($mValue);
        }
    }
    if (count($_POST)) {
        foreach ($_POST as $sKey => $mValue) {
            $_POST[$sKey] = $oHtmlPurifier->purify($mValue);
        }
    }
}

/**
 * Lance une requête de lecture. Sert juste à na pas s'éloigne de ce qu'il y
 * avait avant et à masque la partie objet.
 *
 * @param string $sRequete la requête SQL
 * @param array  $aParams  les paramètres
 *
 * @return mixed data
 *
 * @global object $oBDD le connecteur
 */
function sqlRead($sRequete, $aParams=null)
{
    global $oBDD;

    $mData = $oBDD->execRead($sRequete, $aParams, SQL_DEBUG_RESULTS);
    return $mData;
}

/**
 * Écrit dans la BDD
 *
 * @param string $sRequete la requête
 * @param array  $aParams  les paramètres
 *
 * @return int nb lignes
 *
 * @global object $oBDD
 */
function sqlWrite($sRequete, $aParams=null)
{
    global $oBDD;

    $iNbLines = $oBDD->execWrite($sRequete, $aParams, SQL_DEBUG_ERROR);
    return $iNbLines;
}

/**
 * Gestion de la BDD un peu protégée.
 */
require 'modSQL/PdoLogged.class.php';
/*
  // À la recherche des identifiants
  index.php
  -> include '../conf/local.protected.php'
  -> forum/include/dblayer/common_db.php
  $db = new DBLayer(
  $db_host, $db_username, $db_password, $db_name, $db_prefix, $p_connect
  );
 *
 */

$db_type = 'mysqli';
require '../conf/local.protected.php';
$sLogFile = 'poids.log';
$sDSN = 'mysql:dbname=' . $db_name . ';host=' . $db_host;
$sCharset = 'latin1';
$oBDD = new PdoLogged($sDSN, $db_username, $db_password, $sLogFile, null, $sCharset);
if (!$oBDD) {
    throw new Exception("DB handler error");
}

/**
 * Gestion anti-XSS
 */

require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();
$oHtmlPurifier = new HTMLPurifier($config);
//$clean_html = $oHtmlPurifier->purify($dirty_html);
