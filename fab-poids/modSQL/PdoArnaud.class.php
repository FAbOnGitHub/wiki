<?php

/*
 * Classe qui étend la classe PDO pour faciliter l'usage des colonnes.
 */
class PdoArnaud extends PDO {
    
    /**
     *
     * @param  array $aResult tableau de résultat d'une requête (et non un statment)
     *
     * @return array les titres de colonne
     */
    public function getColumnName($aResult) {

        $aHeaders = array_keys($aResult[0]);
        return $aHeaders;

    }

}

?>
