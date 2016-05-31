<?php

// <? plus de short tags

/**
 * Function liste
 * Affichage
 *
 * @return None
 */
function mainListe() {
    if (isset($_GET['class'])) {
        $_POST['class'] = $_GET['class'];
    }

    if (isset($_GET['categories'])) {
        $_POST['categories'] = $_GET['categories'];
    }

    if (isset($_GET['marques'])) {
        $_POST['marques'] = $_GET['marques'];
    }

    $html = '
	<div class="table">
	<table class="inline">
	<thead>
		<tr>
			<th class="col0" style="width:10%;"><a href="index.php?index=liste&class=categorie&categories='
            . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Catégories</a></th>
			<th class="col1" style="width:10%;"><a href="index.php?index=liste&class=marque&categories='
            . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Marques</a></th>
			<th class="col2" style="width:20%;"><a href="index.php?index=liste&class=nom&categories='
            . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Modèles</a></th>
			<th class="col3" style="width:10%;"><a href="index.php?index=liste&class=poids&categories='
            . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Poids en g.</a></th>
			<th class="col4" style="width:10%;"><a href="index.php?index=liste&class=utilisateur&categories='
            . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Utilisateurs</a></th>
			<th class="col5" style="width:30%;">Remarques</th>
			<th class="col6" style="width:10%; text-align:center;">Modifier</th>
		</tr>
	</thead>
	<tbody>';

    // On peut factoriser bien plus!
    $aParams = null;
    if ($_POST['categories'] == "Toutes" && $_POST['marques'] != "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE `marque` LIKE :marques";
        $aParams[':marques'] = $_POST['marques'];
    } else if ($_POST['categories'] != "Toutes" && $_POST['marques'] == "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE `categorie` LIKE :categories";
        $aParams[':categories'] = $_POST['categories'];
    } else if ($_POST['categories'] == "Toutes" && $_POST['marques'] == "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`";
    } else if ($_POST['categories'] != "Toutes" && $_POST['marques'] != "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE
       		`categorie` LIKE :categories
		AND `marque` LIKE :marques";
        $aParams[':categories'] = $_POST['categories'];
        $aParams[':marques'] = $_POST['marques'];
    }

    if ($_GET['utilisateur'] != "" && $_GET['categories'] == "Toutes" && $_GET['marques'] == "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE
		`utilisateur` LIKE 'utilisateur'";
        $aParams[':utilisateur'] = $_GET['utilisateur'];
    }

    if (isset($_POST['class'])) {
        if ($_POST['class'] == 'date') {
            $sRequete .= " ORDER BY `" . $_POST['class'] . "` DESC";
        } else {
            $sRequete .= " ORDER BY `" . $_POST['class'] . "` ASC";
        }
    } else {
        $sRequete .= " ORDER BY `date` DESC";
    }

    //    $result = mysql_query($sRequete) or die('Erreur SQL req1!<br />' . mysql_error());
    //while ($row = mysql_fetch_assoc($result)) {
    $aRows = sqlRead($sRequete, $aParams);
    if (count($aRows)) {
        foreach ($aRows as $row) {
            $html.='
		<tr>
		<td class="">
			<a href="index.php?index=liste&class=categorie&categories=' . str_replace(chr(34), "&quot;", $row["categorie"]) . '&marques=Toutes">
			' . $row["categorie"] . '</a></td>
		<td class="">
			<a href="index.php?index=liste&class=marque&categories=Toutes&marques=' . str_replace(chr(34), "&quot;", $row["marque"]) . '">
			' . $row["marque"] . '</td>
		<td class="">
			' . $row["nom"] . '</td>
		<td class="" style="text-align:right;padding-right:2%;">
			' . $row["poids"] . '</td>
		<td class="">
			<a href="index.php?index=liste&class=marque&categories=Toutes&marques=Toutes&utilisateur=' . $row["utilisateur"] . '">
			' . $row["utilisateur"] . '</td>
		<td class="">' .
                    str_replace(chr(13), "<br />", $row["rq"]) . '</td>
		<td style="text-align:center;">
			<a href="index.php?index=modif&ligne=' . $row["num"] . '"><img border="0" src="button_edit.png"></a>
			</td>
		</tr>';
        }
    } else {
        $html .= "<tr><td><div class='bg-warning'>pas de résultat</div></td></tr>";
    }

    $html.='</tbody></table>
		</div>
		<br />
		<a href="index.php">Retour</a>';
    echo $html;
}

/**
 * Ajout de poids de Produit
 * Affichage
 *
 * @return None
 */
function mainAjout() {
    global $pun_user;

    if (pun_htmlspecialchars($pun_user['username']) == "Guest") {
        echo '
		<h2>Ajout d\'un produit</h2>
		Vous ne pouvez inscrire de données dans cette base en tant qu\'invité,
		 veuillez tout d\'abord vous connecter sur le forum.<br />
		 Merci de votre compréhension.
		 <br /><br />
		<a href="index.php">Retour</a>';
        return;
    }

    $html = '<h2>Ajout d\'un produit</h2>
		<p>Les informations suivantes ont été ajoutées à la base de données :</p>';

    // cas de la virgule
    if (ereg(',', $_POST['poids'])) {
        $_POST['poids'] = str_replace(",", ".", $_POST['poids']);
    }
    $_POST['poids'] = (float) preg_replace("'[^\d]^.'", "", $_POST['poids']);

    $sRequete = "
		INSERT INTO `poids_mat` ( 
                  `num` , `categorie` , `marque` , `nom` , `utilisateur` , 
                  `poids` , `rq`, `date` 
                ) VALUES (
                  NULL , :categories, :marques, :modele, :user, :poids, :rq, :time
                )";
    $aParams = array(
        ':categories' => $_POST['categories'],
        ':marques' => $_POST['marques'],
        ':modele' => ucfirst($_POST['modele']),
        ':user' => pun_htmlspecialchars($pun_user['username']),
        ':poids' => $_POST['poids'],
        ':rq' => ucfirst($_POST['rq']),
        ':time' => time()
    );
    $iNb = sqlWrite($sRequete, $aParams);
    $sMsg = "bdd: ok.";
    if ($iNb != 1) {
        $sMsg = "bdd: problème détecté, vérifier les logs.";
    }

    //=====Envoi d'un email quand un produit est ajouté
    $to = 'oli_v_ier@yahoo.fr, fdc.blogrum@xymail.fr, faydc@yahoo.com';
    $subject = 'Ajout d\'un produit pesé';

    //=====Création du header de l'e-mail.
    $headers = 'From: postmaster@randonner-leger.org' . "\r\n" .
            'Reply-To: postmaster@randonner-leger.org' . "\r\n" .
            'Content-Type: text/html; charset="utf-8"' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    //=====Ajout du message au format HTML
    $message .= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>' . "\r\n";
    $message .= 'Un produit vient d\'être ajouté à la liste des poids :<br />' . "\r\n\n";
    $message .= 'Catégorie : ' . $_POST['categories'] . '<br />' . "\r\n";
    $message .= 'Marque : ' . $_POST['marques'] . '<br />' . "\r\n";
    $message .= 'Modèle : ' . ucfirst($_POST['modele']) . '<br />' . "\r\n";
    $message .= 'Membre : ' . pun_htmlspecialchars($pun_user['username']) . '<br />' . "\r\n";
    $message .= 'Poids : ' . $_POST['poids'] . '<br />' . "\r\n";
    $message .= 'Remarque : ' . $_POST['rq'] . '<br />' . "\r\n";
    $message .= '<a href="http://' . $_SERVER['HTTP_HOST'] . '/wiki/poids/index.php?index=liste&class=date&categories=Toutes&marques=Toutes">lien pour modification éventuelle</a><br />' . "\r\n";
    $message .= "$sMsg<br />\r\n";
    $message .= '</html></body>' . "\r\n";

    //=====Envoi de l'email
    mail($to, $subject, $message, $headers);

    $html.='
	<table class="inline">
		<tr>
			<th class="" >Catégorie</th>
			<th class="" >Marque</th>
			<th class="" >Modèle</th>
			<th class="" >Poids en g.</th>
			<th class="" >Utilisateur</th>
			<th class="" >Remarques</th>
		</tr>
		<tr>
			<td class="" >' . $_POST['categories'] . '</td>
			<td class="" >' . $_POST['marques'] . '</td>
			<td class="" >' . stripslashes($_POST['modele']) . '</td>
			<td class="" >' . stripslashes($_POST['poids']) . '</td>
			<td class="" >' . pun_htmlspecialchars($pun_user['username']) . '</td>
			<td class="" >' . stripslashes($_POST['rq']) . '</td>
		</tr>
	</table>
	<br />
	<a href="index.php">Retour</a>';

    echo $html;
}

/**
 * Ajout de catégorie ou de marque des poids des Produits
 * Affichage
 *
 * @return None
 */
function mainAjCatMrk() {
    $html = '
	<h2>Ajout d\'une catégorie ou d\'une marque</h2>
	<p>';

    //nouvelle categorie

    if ($_POST['newcat'] != "") {
        $_POST['newcat'] = ucfirst($_POST['newcat']);

        $sRequete = "
	SELECT 'NomCat'
	FROM `poids_categories`
	WHERE 1
	AND `Nomcat` LIKE :newcat";
        $aParams = array(':newcat' => $_POST['newcat']);

        $aRows = sqlRead($sRequete, $aParams);
        if (count($aRows) == 0) {
            $sRequete = "
INSERT INTO `poids_categories` ( `NumCat` , `NomCat` )
VALUES (NULL , :newcat)";

            sqlWrite($sRequete, $aParams); //$aParams encore newcat
            $html.='La catégorie ' . $_POST['newcat'] . ' a été ajouée à la liste.<br /><br />';
        } else {
            $html.='La categorie ' . $_POST['newcat'] . ' existe déja<br /><br />';
        }
    } elseif ($_POST['newmarq'] != "") {
        //nouvelle marque
        $_POST['newmarq'] = ucfirst($_POST['newmarq']);

        $sRequete = "
SELECT 'Nommarq'
FROM `poids_marques`
WHERE 1
AND `Nommarq` LIKE :newmarq";
        $aParams[':newmarq'] = $_POST['newmarq'];
        $aRows = sqlRead($sRequete, $aParams);
        if (count($aRows) == 0) {
            $sRequete = "
INSERT INTO `poids_marques` ( `Nummarq` , `Nommarq` )
VALUES (NULL , :newmarq)";
            sqlWrite($sRequete, $aParams);
            $html.='La marque ' . $_POST['newmarq'] . ' a été ajouée à la liste.<br />';
        } else {
            $html.='La marque ' . $_POST['newmarq'] . ' existe déja';
        }
    }
    $html.='
	</p>
	<a href="index.php">Retour</a>';

    echo $html;
}

/**
 * Modification d'une donnée
 * Affichage
 *
 * @return None
 */
function mainModif() {
    global $pun_user;

    //die("Not tested. Die.");
    $_GET['ligne'] = preg_replace($motif, "", $_GET['ligne']);
    $_GET['ligne'] = (float) $_GET['ligne'];


    $sRequete = "
SELECT *
FROM `poids_mat`
WHERE 1 AND `num` = :ligne";
    $aParams[':ligne'] = $_GET['ligne'];
    $aRows = sqlRead($sRequete, $aParams);
    $lg = $aRows[0];

    if (($pun_user['group_id'] != '1') && (pun_htmlspecialchars($pun_user['username']) != $lg[4]) && !isAdminPoids()) {
        $html = '
		<h2>Modifier un produit</h2>
		<p>
		Vous n\'êtes pas l\'auteur de ces données ou n\'êtes pas identifié(e), vous ne pouvez les modifier.<br />
		<br />
		<a href="index.php">Retour</a>';
        echo $html;
    } elseif (time() - $lg[7] < 60 && $pun_user['group_id'] != '1' && !isAdminPoids()) {
        $html = '
		<h2>Modifier un produit</h2>
		<p>
		Veuillez attendre quelques minutes avant de modifier à nouveau vos données.<br />
		<br />
		<a href="index.php">Retour</a>';
        echo $html;
    } else {
        $html = '
	<h2>Modifier un produit</h2>
	<p>
	<form name="f2" method="POST" action="index.php" onSubmit="return verif_formulaire()">
	<table class="inline">
		<tr>
			<th class="" >Catégorie</th>
			<th class="" >Marque</th>
			<th class="" >Modèle</th>
			<th class="" >Poids en g.</th>
			<th class="" >Remarques</th>
		</tr>
		<tr>
		<td class=""> ' . "\n";

        $sRequete = "SELECT *
	FROM `poids_categories`
	ORDER BY `NomCat` ASC";
        $aRows = sqlRead($sRequete);
        if (count($aRows)) {
            $html .= '<select size="1" name="categories">
	<option>-Choisissez-</option>' . "\n";
            foreach ($aRows as $row) {
                if ($lg[1] == $row["NomCat"]) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                $html.= "<option " . $selected . ">" . $row["NomCat"] . "</option>" . "\n";
            }
            $html.='</select>';
        } else {
            $html .= 'pas de données';
        }

        $html.='</td>
		<td class="">' . "\n";
        $sRequete = "SELECT *
	FROM `poids_marques`
	ORDER BY `NomMarq` ASC ";
        $aRows = sqlRead($sRequete);
        if (count($aRows)) {
            $html .= '<select size="1" name="marques">
	<option>-Choisissez-</option>' . "\n";
            foreach ($aRows as $row) {
                if ($lg[2] == $row["NomMarq"]) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                $html.= "<option " . $selected . ">" . $row["NomMarq"] . "</option>" . "\n";
            }

            $html.='</select>';
        } else {
            $html .= "pas de données marques";
        }

        $html.='
		</td>
		<td class=""><input type="text" name="modele" size="20" value="' . str_replace(chr(34), "'", $lg[3]) . '"></td>
		<td class=""><input type="text" name="poids" size="10" value="' . $lg[5] . '"></td>
		<td class=""><textarea name="rq" cols="30" rows="4">' . $lg[6] . '</textarea></td>
		</tr>
	</table>
		<input type="hidden" name="utilisateur" value="' . $lg[4] . '">
	<br />
	<button type="submit" title="Modifier">Modifier</button>
	<input name="ligne" type="hidden" value="' . $lg[0] . '">
	<input name="index" type="hidden" value="valmod">
	</form>
	<br />
	<a href="index.php">Retour</a>
	';

        echo $html;
    }
}

/**
 * Validation des modifications d'une donnée
 * Affichage
 *
 * @return None
 */
function mainValmod() {
    global $pun_user;

    //die("Not tested. Die.");
    $html = '<h2>Modifier un produit</h2>';

    // cas de la virgule
    if (ereg(',', $_POST['poids'])) {
        $_POST['poids'] = str_replace(",", ".", $_POST['poids']);
    }
    $_POST['poids'] = (float) preg_replace("'[^\d]^.'", "", $_POST['poids']);
    $sTime = time();

    $sReqUpdate = "
	UPDATE `poids_mat` SET `categorie` = :categories,
	 `marque` = :marques'], `nom` = :modele'],
	 `poids` = :poids'], `rq` = :rq', `date` = :time
        WHERE `num` = :ligne LIMIT 1";
    $aParamUpdate = array(
        ':categories' => $_POST['categories'],
        ':marques' => $_POST['marques'],
        ':modele' => $_POST['modele'],
        ':poids' => $_POST['poids'],
        'rq' => $_POST['rq'],
        ':time' => $sTime,
        ':ligne' => $_POST['ligne']
    );
    $sReqInsert = "
	INSERT INTO `poids_backup` ( `num` , `categorie` , `marque` , `nom` , `utilisateur` , `poids` , `rq`, `date` )
	VALUES (NULL, :categories, :marques, :modele, :user, :poids, :rq, :time')";
    $aParamIns = array(
        ':categories' => $_POST['categories'],
        ':marques' => $_POST['marques'],
        ':modele' => ucfirst($_POST['modele']),
        ':user' => pun_htmlspecialchars($pun_user['username']),
        ':poids' => $_POST['poids'],
        ':rq' => ucfirst($_POST['rq']),
        ':time' => $sTime,
    );
    try {
        sqlWrite($sReqUpdate, $aParamUpdate);
        sqlWrite($sReqInsert, $aParamIns);
        $html.='Votre enregistrement été modifié
	<br /><br />
	<a href="index.php">Retour</a>';
    } catch (Exception $e) {
        $html.='Une erreur est survenue (désolé)(logs)
	<br /><br />
	<a href="index.php">Retour</a>';
    }
    echo $html;
}

/**
 * Function du cas par défaut
 * Affichage
 *
 * @return None
 */
function mainDefault() {
    global $pun_user;

    $html .='<div class="wrap_center wrap_round wrap_info plugin_wrap" style="width: 80%;">';
    $html .='<p>';
    $html .='Souvent déçu par l\'écart entre le poids annoncé par le fabricant et le poids effectif du matériel ?<br />';
    $html .='<strong>Vous trouverez sur ces pages les mesures faites par les utilisateurs de randonner-léger</strong>';
    $html .='</p>';
    $html .='<p>';
    $html .='<strong>Quelques règles à respecter pour enregistrer vos mesures :</strong>';
    $html .='<ul>';
    $html .='<li class="level1"><div class="li"> Vous devez être connecté au <a href="http://www.randonner-leger.org/forum" class="urlextern" title="http://www.randonner-leger.org/forum">forum</a> : la mesure s\'enregistrera automatiquement sous le nom de votre pseudo.</div></li>';
    $html .='<li class="level1"><div class="li"> Merci d\'utiliser une balance suffisamment précise, au pire à 5g près pour effectuer les mesures. Une balance électronique de cuisine sera parfaite.</div></li>';
    $html .='<li class="level1"><div class="li"> N\'oubliez pas de préciser la taille des vêtements.</div></li>';
    $html .='</ul>';
    $html .='</p>';
    $html .='</div>';

    $html.='<h2>Rechercher le poids d\'un produit</h2>
	<p><form name="f1" method="POST" action="index.php">
	<table class="inline">
		<tr>
			<th class="" >Catégorie</th>
			<th class="" >Marque</th>
		</tr>
			<tr>
		<td class="">';
    // Liste categories
    $sRequete = "SELECT *
		FROM `poids_categories`
		ORDER BY `NomCat` ASC";

    /*
      $result = mysql_query($sRequete) or die('Erreur SQL req1!<br />' . mysql_error());
      while ($row = mysql_fetch_assoc($result)) {
      $html.= '<option>' . $row['NomCat'] . '</option>' . "\n";
      }
      mysql_free_result($result);
     */
    $aRows = sqlRead($sRequete, null);
    if (count($aRows)) {
        $html .= '
	<select size="1" name="categories">
	<option>Toutes</option>' . "\n";

        foreach ($aRows as $row) {
            $html.= '<option>' . $row['NomCat'] . '</option>' . "\n";
        }

        echo '		</select>' . "\n";
    } else {
        echo "<div> pas de données en base. </div>";
    }
    $html.='
		</td>
		<td class="">';

    $sRequete = "SELECT *
		FROM `poids_marques`
		ORDER BY `NomMarq` ASC ";
    $aRows = sqlRead($sRequete, null);
    if (count($aRows)) {
        $html .= '
		<select size="1" name="marques">
		<option>Toutes</option>' . "\n";
        foreach ($aRows as $row) {
            $html.='<option>' . $row['NomMarq'] . '</option>' . "\n";
        }
        $html .= '
    	</select>';
    } else {
        $html .= "<div>pas de données</div>";
    }


    // Liste marques
    /*
      $sRequete = "SELECT *
      FROM `poids_marques`
      ORDER BY `NomMarq` ASC ";

      $result = mysql_query($sRequete) or die('Erreur SQL req1!<br />' . mysql_error());
      while ($row = mysql_fetch_assoc($result)) {
      $html.='<option>' . $row['NomMarq'] . '</option>' . "\n";
      }
      mysql_free_result($result);
     */
    $html.='
		</td>
		</tr>
	</table>
	<input name="index" type="hidden" value="liste">
	<button type="submit" title="Rechercher">Rechercher</button>
	</form>
	</p>';

    //Ajouter un produit

    if (pun_htmlspecialchars($pun_user['username']) == "Guest") {
        $html.='<h2>Ajouter un produit, une catégorie ou une marque</h2>';
        $html .='<div class="wrap_center wrap_round wrap_important plugin_wrap" style="width: 80%;">';
        $html .='<p>';
        $html .='Vous devez être identifié(e) pour ajouter un produit, une catégorie ou une marque';
        $html .='</p>';
        $html .='</div>';
    } else {
        $html .='
			<br />
			<h2>Ajouter un produit</h2>
			<p>
			<form name="f2" method="POST" action="index.php" onSubmit="return verif_formulaire()">
			<table class="inline">
				<tr>
					<th class="" >Catégorie</th>
					<th class="" >Marque</th>
					<th class="" >Modèle</th>
					<th class="" >Poids en g.</th>
					<th class="" >Remarques</th>
				</tr>
					<tr>
				<td class="">';

        $sRequete = "SELECT *
			FROM `poids_categories`
			ORDER BY `NomCat` ASC";
        $aRows = sqlRead($sRequete, null);
        if (count($aRows)) {
            $html .= '
		<select size="1" name="categories">
		<option>-Choisissez-</option>' . "\n";
            foreach ($aRows as $row) {
                $html .= '<option>' . $row["NomCat"] . '</option>' . "\n";
            }
            $html .= '</select>';
        } else {
            $html .= "<div>pas de données</p>";
        }


        /*
          $result = mysql_query($sRequete) or die('Erreur SQL req1!<br />' . mysql_error());
          while ($row = mysql_fetch_assoc($result)) {
          $html.='<option>' . $row["NomCat"] . '</option>' . "\n";
          }
          mysql_free_result($result);
         */

        $html.='</td>
	<td class="">';

        $sRequete = "
            SELECT *
            FROM `poids_marques`
            ORDER BY `NomMarq` ASC ";
        $aRows = sqlRead($sRequete, null);
        if (count($aRows)) {
            $html .= '<select size="1" name="marques">
			<option>-Choisissez-</option>' . "\n";
            foreach ($aRows as $row) {
                $html .= '<option>' . $row['NomMarq'] . '</option>' . "\n";
            }
            $html .= '</select>';
        }
        /*
          $result = mysql_query($sRequete) or die('Erreur SQL req1!<br />' . mysql_error());
          while ($row = mysql_fetch_assoc($result)) {
          $html.='<option>' . $row['NomMarq'] . '</option>' . "\n";
          }
          mysql_free_result($result); */


        $html.='
					</td>
					<td class=""><input type="text" name="modele" size="20" placeholder="Modèle"></td>
					<td class=""><input type="text" name="poids" size="10" placeholder="Poids"></td>
					<td class=""><textarea name="rq" cols="30" rows="4" placeholder="Remarques"></textarea></td>
					</tr>
				</table>

				<input name="index" type="hidden" value="ajout">
				<button type="submit" title="Ajouter">Ajouter</button>
				<button type="reset" title="Annuler">Annuler</button>
				</form>
				</p>

				<br />
				<h2>Ajouter une catégorie ou une marque</h2>
				<p>
				<form name="f3" method="POST" action="index.php" onSubmit="return verif_formulaire2()">

				<table class="inline">
				<tr>
						<th class="" >Nouvelle catégorie :</th>
						<td class="" ><input type="text" name="newcat" size="20" placeholder="Catégorie"></td>
				</tr>
				<tr>
						<th class="" >Nouvelle marque :</th>
						<td class="" ><input type="text" name="newmarq" size="20" placeholder="Marque"></td>
				</tr>
				</table>

				<input name="index" type="hidden" value="ajcatmrk">
				<button type="submit" title="Ajouter">Ajouter</button>
				<button type="reset" title="Annuler">Annuler</button>

				</form>
				</p>';
    }

    echo $html;  //ecrit la page
}

/* * ******************************************
 *               Main
 */

//nettoyage des signes dangereux
/*
  $motif = '`[][<>{}!\$?\*\|\"\^=/:\`;&#%]`';

  foreach ($_POST as $key => $value) {
  $_POST[$key] = preg_replace($motif, "", $value);
  $_POST[$key] = addslashes($value);
  }

 */

$aAdminPoids = array('Opitux', 'Faydc', 'Oli_v_ier');
sanitizeAllInputs();



if ($_POST['index'] == "liste" || $_GET['index'] == "liste") {
    //Liste des poids des Produits
    mainListe();
} elseif ($_POST['index'] == "ajout") {
    //Ajout de poids de Produit
    mainAjout();
} elseif ($_POST['index'] == "ajcatmrk") {
    //Ajout de catégorie ou de marque des poids des Produit
    maintAjCatMrk();
} elseif ($_GET['index'] == "modif") {
    mainModif();
} elseif ($_POST['index'] == "valmod") {
    mainValmod();
} else {
    mainDefault();
}
?>
