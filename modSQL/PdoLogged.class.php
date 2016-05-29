<?php

/**
 * Fichier
 *
 * PHP version 5
 *
 * @category   PDOTests
 * @package    PDOLib
 * @author     Fabrice Mendes <0xfab@free.fr>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    SVN: $Id:$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample(
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 *
 */
/*
 * Fichier qui définit une classe qui étend PDO pour ajouter
 *  - des logs (en cas d'échec on récupère la requête SQL (pratique en prod)
 *  - des fonctions pour récupérer des données en tableau.
 *
 * Cela s'inspire de la PDO.lib.php, donc c'est un peu cracra.
 *
 */

$sPwd = dirname(__FILE__);
require_once $sPwd . '/commun.inc.php';
require_once $sPwd . '/mysql.inc.php';
require_once $sPwd . '/PdoArnaud.class.php';

/**
 * PDOlogged : classe
 *
 * @category PDOTests
 * @package  Modules
 * @author   Fabrice Mendes <f.mendes@epoc.u-bordeaux1.fr>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  Release: SVN: $Id$
 * @link     https://localhost/WebDevTests
 */
class PdoLogged extends PdoArnaud
{
    /**
     * PDO Object
     */
    //protected $_PDO;

    /**
     * PDO Statement
     */
    protected $pdoStmt;

    /**
     * log Object
     */
    protected $pdoLogHandler;
    protected $pdoLogId;

    /**
     * __construct() : constructor
     *
     * @param mixed  $mDSN      le DSN
     * @param string $sUsername utilisateur
     * @param string $sPassword password
     * @param string $sLogFile  fichier de log
     * @param array  $aOptions  les options
     * @param string $sCharset  option pour l'encodage
     *
     * @return PDOlogged Object
     */
    function __construct($mDSN, $sUsername, $sPassword, $sLogFile,
                         $aOptions = null, $sCharset = "utf-8"
    ) {
        try {
            if (!$sLogFile) {
                throw new Exception(
                    __CLASS__ . "log file is not set"
                );
            }
            $this->pdoInitLog($sLogFile);
            if (!$this->pdoLogHandler) {
                throw new Exception(__CLASS__ . " log init error");
            }
        } catch (Exception $e) {
            throw $e; // ?
        }
        try {
            parent::__construct($mDSN, $sUsername, $sPassword, $aOptions);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setLogLevel();
            //$this->setDebug(true);
            // if ($sCharset)
            //  array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        } catch (PDOException $e) {
            $sMsg = "Connection failed: with DSN='$mDSN'" . $e->getMessage();
            //$this->log($sMsg);
            throw $e;
        }
    }

    /**
     * destructeur
     *
     * @return none
     */
    function __destruct()
    {
        fclose($this->pdoLogHandler);
    }

    /**
     * setDebug : active debug
     *
     * @param boolean $bDebug flag
     *
     * @return none
     */
    public function setDebug($bDebug = true)
    {
        $this->setAttribute(
                PDO::ATTR_EMULATE_PREPARES, $bDebug == true ? false : true
        );
        $this->setLogLevel($bDebug ? 0 : 1);
    }

    /**
     * setLogLevel
     *
     * @param int $iLevel 0: all, 1: error
     *
     * @return none
     */
    function setLogLevel($iLevel = 1)
    {
        $this->_iLogLevel = $iLevel;
    }

    /**
     * pdoInitLog
     *
     * @param string $sLogFile le nom du fichier de log
     *
     * @return none
     *
     */
    protected function pdoInitLog($sLogFile)
    {
        $this->pdoLogId = sprintf("%04d", rand(1, 9999));
        if (!file_exists($sLogFile)) {
            throw new Exception(
                __CLASS__ . " : logfile '$sLogFile' does not exists\n"
            );
        }
        if (!is_writable($sLogFile)) {
            throw new Exception(
            __CLASS__ . " : cannot open logfile in write mode\n"
            );
        }
        if (!($h = fopen($sLogFile, 'ab'))) {
            throw new Exception(__CLASS__ . " : cannot open logfile\n");
        }
        $this->pdoLogHandler = $h;
        //$this->log('PDO start');
    }

    /**
     * log
     *
     * @param string $sMsg le message à logger
     *
     * @return none
     */
    public function log($sMsg)
    {
        $sDate = date('Y-m-d H:i:s');
        $sBuffer = $sDate . " " . $this->pdoLogId . " : " . $sMsg . "\n";
        fwrite($this->pdoLogHandler, $sBuffer);
        fflush($this->pdoLogHandler);
    }

    /**
     * run : exécute la requête
     *
     * @param string  $sSQL    le message à logger
     * @param array   $aParams tableau de paramètres/arguments
     * @param boolean $bDoGet  force la récupération des données
     * @param int     $iDebug  niveau de debug SQL_DEBUG_*
     *
     * @return array les lignes résultats
     */
    function run($sSQL, $aParams = null, $bDoGet = true,
        $iDebug = SQL_DEBUG_NONE
    ) {
        try {
            $aRows = null;
            $this->pdoStmt = $this->prepare($sSQL);
            if ($aParams) {
                $this->pdoStmt->execute($aParams);
            } else {
                $this->pdoStmt->execute();
            }
            if ($bDoGet) {
                $aRows = $this->pdoStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            //$this->pdoStmt->closeCursor();
            if ($this->_iLogLevel == 0 || $iDebug > SQL_DEBUG_NONE) {
                $sPdoDbg = $this->debugDumpParams();

                if ($bDoGet) {
                    $iCount = count($aRows);
                } else {
                    $iCount = $this->pdoStmt->rowCount();
                }
                $sParams = serialize($aParams);
                $sJSql = json_encode($sSQL);
                $this->log(__CLASS__."::run() $sJSql + $sParams => count($iCount)");
                $this->log(__CLASS__."::run() debugDumpParams: $sPdoDbg");
                $this->log(__CLASS__."::run() $sSQL");

                if ($iDebug >= SQL_DEBUG_RESULTS) {
                    $this->log("aResults=" . json_encode($aRows));
                }
            }
        } catch (PDOException $e) {
            $sMsg = __CLASS__ . "::run() " . $e->getMessage();
            $this->log($sMsg);
            $this->log("### json(SQL) = " . json_encode($sSQL));
            $this->log("### SQL=$sSQL)");
            $this->log("### json(aParams) = " . json_encode($aParams));
            throw $e;
        }
        return $aRows;
    }

    /**
     * execGet : exécute la requête et récupère le résultat
     *
     * @param string $sSQL    le SQL à exécuter
     * @param array  $aParams tableau de paramètres/arguments
     * @param int    $iDebug  niveau de debug SQL_DEBUG_*
     *
     * @return array les lignes résultats
     */
    function execGet($sSQL, $aParams = null, $iDebug = SQL_DEBUG_NONE)
    {
        return $this->run($sSQL, $aParams, true, $iDebug);
    }

    /**
     * execRead : exécute la requête et récupère le résultat
     *   alias de execGet
     *
     * @param string $sSQL    le SQL à exécuter
     * @param array  $aParams tableau de paramètres/arguments
     * @param int    $iDebug  niveau de debug
     *
     * @return array les lignes résultats
     */
    function execRead($sSQL, $aParams = null, $iDebug = SQL_DEBUG_NONE)
    {
        return $this->execGet($sSQL, $aParams, $iDebug);
    }

    /**
     * execPush : exécute la requête et récupère le résultat, sert en écriture
     *   dans la BDD (impact sur le num_rows/rowCount
     *
     * @param string $sSQL    le SQL à jouer
     * @param array  $aParams tableau de paramètres/arguments
     * @param int    $iDebug  niveau de debug
     *
     * @return int nombre de lignes altérées
     */
    function execPush($sSQL, $aParams = null, $iDebug = SQL_DEBUG_NONE)
    {
        $this->run($sSQL, $aParams, false, $iDebug);
        $iCount = $this->pdoStmt->rowCount();
        if ($iDebug > SQL_DEBUG_NONE) {
            $this->log("$sSQL => count($iCount)");
        }
        return $iCount;
    }

    /**
     * execWrite : exécute la requête et récupère le résultat
     *   alias pour execPush
     *
     * @param string $sSQL    le SQL à jouer
     * @param array  $aParams tableau de paramètres/arguments
     * @param int    $iDebug  niveau de debug SQL_DEBUG_*
     *
     * @return array les lignes résultats
     */
    function execWrite($sSQL, $aParams = null, $iDebug = SQL_DEBUG_NONE)
    {
        return $this->execPush($sSQL, $aParams, $iDebug);
    }

    /**
     * runNamed : exécute la requête
     *
     * @param string $sSQL    le message à logger
     * @param array  $aParams tableau de paramètres/arguments
     * @param int    $iDebug  niveau de debug SQL_DEBUG_*
     *
     * @return none
     */
    function runNamed($sSQL, $aParams, $iDebug = SQL_DEBUG_NONE)
    {
        try {
            $this->pdoStmt = $this->prepare($sSQL);

            foreach ($aParams as $aTriplet) {
                list ($sKey, $sVar, $iType) = $aTriplet;
                //echo html_p("[sKey=$sKey][sVar=$sVar][iType=$iType]");
                //if (! $this->pdoStmt->bindParam($sKey, $sVar, $iType)) {
                if (! $this->pdoStmt->bindValue($sKey, $sVar, $iType)) {
                    throw new PDOException(
                        __FILE__ . " bindParam($sKey, $sVar, $iType) failed"
                    );
                }
            }

            if (! $this->pdoStmt->execute()) {
                throw new PDOException("execute($sSQL) failed");
            }

            $aRows = $this->pdoStmt->fetchAll(PDO::FETCH_ASSOC);
            if ($this->_iLogLevel == 0 || $iDebug != SQL_DEBUG_NONE) {
                $iCount = $this->pdoStmt->rowCount();
                $sPdoDbg = $this->debugDumpParams();
                $sParams = json_encode($aParams);
                $sJSql = json_encode($sSQL);
                $this->log("@@@ [sql=$sJSql][sParams=$sParams] => count($iCount)");
                $this->log("@@@ [sql=$sSQL] => count($iCount)");
                $this->log("@@@ [PDO::debudDumpParams()=$sPdoDbg]");
                $this->log("@@@ [aParams=".var_export($aParams, true)."]");

                if ($iDebug >= SQL_DEBUG_RESULTS) {
                    $this->log("@@@ json(aResults)=" . json_encode($aRows));
                    $this->log("@@@ aResults=" . var_export($aRows, true));
                }
            }
        } catch (PDOException $e) {
            $sMsg = __CLASS__ . "::runNamed() " . $e->getMessage();
            $this->log($sMsg);
            $this->log("### SQL = $sSQL");
            $this->log("### json(SQL) = " . json_encode($sSQL));
            $this->log("### json(aParams) = " . json_encode($aParams));
            throw $e;
        }
        return $aRows;
    }

    /**
     * Fonction debudDumpParams encapsulée
     *
     * @return string
     */
    function debugDumpParams()
    {
        ob_start();
        $this->pdoStmt->debugDumpParams();
        $sPdoDbg = ob_get_contents();
        ob_end_clean();
        return $sPdoDbg;
    }

}

?>