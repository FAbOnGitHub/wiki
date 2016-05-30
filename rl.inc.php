<?php

/**
 * Inclusion diverses, déclaration des fonctions qui vont bien
 * 
 *  * PHP version 5
 *
 * @category   PDOTests
 * @package    PDOLib
 * @author     Fabrice Mendes <0xfab@free.fr>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    SVN: $Id:$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample(
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */


/**
 * Gestion de la BDD un peu protégée.
 */
include 'modSQL/PdoLogged.class.php';
// index.php 
// -> include '../conf/local.protected.php' 
// -> forum/include/dblayer/common_db.php
// $db = new DBLayer($db_host, $db_username, $db_password, $db_name, $db_prefix, $p_connect);
$db_type = 'mysqli';
include '../conf/local.protected.php';

$sLogFile = 'poids.log';
$sDSN = 'mysql:dbname=' . $db_name . ';host=' . $db_host;
$oBDD = new PdoLogged($sDSN, $db_username, $db_password, $sLogFile);
if (!$oBDD) {
    throw new Exception("DB handler error");
}


/**
 * Gestion anti-XSS
 */
require_once 'htmlpurifier/HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();
$oPurifier = new HTMLPurifier($config);

//$clean_html = $purifier->purify($dirty_html);