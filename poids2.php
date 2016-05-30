<?php

// <? plus de short tags
//nettoyage des signes dangereux

$motif = '`[][<>{}!\$?\*\|\"\^=/:\`;&#%]`';

foreach ($_POST as $key => $value) {
    $_POST[$key] = preg_replace($motif, "", $value);
    $_POST[$key] = addslashes($value);
}

//Liste des poids des Produits

if ($_POST['index'] == "liste" | $_GET['index'] == "liste") {
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
			<th class="col0" style="width:10%;"><a href="index.php?index=liste&class=categorie&categories=' . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Catégories</a></th>
			<th class="col1" style="width:10%;"><a href="index.php?index=liste&class=marque&categories=' . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Marques</a></th>
			<th class="col2" style="width:20%;"><a href="index.php?index=liste&class=nom&categories=' . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Modèles</a></th>
			<th class="col3" style="width:10%;"><a href="index.php?index=liste&class=poids&categories=' . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Poids en g.</a></th>
			<th class="col4" style="width:10%;"><a href="index.php?index=liste&class=utilisateur&categories=' . $_POST['categories'] . '&marques=' . $_POST['marques'] . '">Utilisateurs</a></th>
			<th class="col5" style="width:30%;">Remarques</th>
			<th class="col6" style="width:10%; text-align:center;">Modifier</th>
		</tr>
	</thead>
	<tbody>';

    if ($_POST['categories'] == "Toutes" & $_POST['marques'] != "Toutes") {
        $req = "
		SELECT *
		FROM `poids_mat`
		WHERE `marque` LIKE '" . $_POST['marques'] . "'";
    }

    if ($_POST['categories'] != "Toutes" & $_POST['marques'] == "Toutes") {
        $req = "
		SELECT *
		FROM `poids_mat`
		WHERE `categorie` LIKE '" . $_POST['categories'] . "'";
    }

    if ($_POST['categories'] == "Toutes" & $_POST['marques'] == "Toutes") {
        $req = "
		SELECT *
		FROM `poids_mat`";
    }

    if ($_POST['categories'] != "Toutes" & $_POST['marques'] != "Toutes") {
        $req = "
		SELECT *
		FROM `poids_mat`
		WHERE
		`categorie` LIKE '" . $_POST['categories'] . "'
		AND `marque` LIKE '" . $_POST['marques'] . "'";
    }

    if ($_POST['categories'] != "Toutes" & $_POST['marques'] != "Toutes") {
        $req = "
		SELECT *
		FROM `poids_mat`
		WHERE
		`categorie` LIKE '" . $_POST['categories'] . "'
		AND `marque` LIKE '" . $_POST['marques'] . "'";
    }

    if ($_GET['utilisateur'] != "" & $_GET['categories'] == "Toutes" & $_GET['marques'] == "Toutes") {
        $req = "
		SELECT *
		FROM `poids_mat`
		WHERE
		`utilisateur` LIKE '" . $_GET['utilisateur'] . "'";
    }

    if (isset($_POST['class'])) {
        if ($_POST['class'] == 'date') {
            $req.="ORDER BY `" . $_POST['class'] . "` DESC";
        } else {
            $req.="ORDER BY `" . $_POST['class'] . "` ASC";
        }
    } else {
        $req.="ORDER BY `date` DESC";
    }

    $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
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
    $html.='</tbody></table>
		</div>
		<br />
		<a href="index.php">Retour</a>';

    mysql_free_result($result);

    echo $html;
}

//Ajout de poids de Produit
elseif ($_POST['index'] == "ajout") {
    if (pun_htmlspecialchars($pun_user['username']) == "Guest") {
        echo '
		<h2>Ajout d\'un produit</h2>
		Vous ne pouvez inscrire de données dans cette base en tant qu\'invité,
		 veuillez tout d\'abord vous connecter sur le forum.<br />
		 Merci de votre compréhension.
		 <br /><br />
		<a href="index.php">Retour</a>';
    } else {
        $html = '<h2>Ajout d\'un produit</h2>
		<p>Les informations suivantes ont été ajoutées à la base de données :</p>';

        // cas de la virgule

        if (ereg(',', $_POST['poids'])) {
            $_POST['poids'] = str_replace(",", ".", $_POST['poids']);
        }
        $_POST['poids'] = (float) preg_replace("'[^\d]^.'", "", $_POST['poids']);

        //=====Envoi d'un email quand un produit est ajouté
        $to = 'oli_v_ier@yahoo.fr, fdc.blogrum@xymail.fr';
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
        $message .= '</html></body>' . "\r\n";

        //=====Envoi de l'email
        mail($to, $subject, $message, $headers);

        $req = "
		INSERT INTO `poids_mat` ( `num` , `categorie` , `marque` , `nom` , `utilisateur` , `poids` , `rq`, `date` )
		VALUES (
		NULL , '" . $_POST['categories'] . "', '" . $_POST['marques'] . "', '" . ucfirst($_POST['modele']) . "', '" . pun_htmlspecialchars($pun_user['username']) . "', '" . $_POST['poids'] . "', '" . ucfirst($_POST['rq']) . "', '" . time() . "')";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

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
}

//Ajout de catégorie ou de marque des poids des Produit
elseif ($_POST['index'] == "ajcatmrk") {

    $html = '
	<h2>Ajout d\'une catégorie ou d\'une marque</h2>
	<p>';

    //nouvelle categorie

    if ($_POST['newcat'] != "") {
        $_POST['newcat'] = ucfirst($_POST['newcat']);

        $req = "
	SELECT 'NomCat'
	FROM `poids_categories`
	WHERE 1
	AND `Nomcat` LIKE '" . $_POST['newcat'] . "'";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

        if (mysql_num_rows($result) == 0) {
            $req = "
	INSERT INTO `poids_categories` ( `NumCat` , `NomCat` )
	VALUES (
	NULL , '" . $_POST['newcat'] . "'
	)";
            $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

            $html.='La catégorie ' . $_POST['newcat'] . ' a été ajouée à la liste.<br /><br />';
        } else {
            $html.='La categorie ' . $_POST['newcat'] . ' existe déja<br /><br />';
        }
    }

    //nouvelle marque
    elseif ($_POST['newmarq'] != "") {
        $_POST['newmarq'] = ucfirst($_POST['newmarq']);

        $req = "
	SELECT 'Nommarq'
	FROM `poids_marques`
	WHERE 1
	AND `Nommarq` LIKE '" . $_POST['newmarq'] . "'";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

        if (mysql_num_rows($result) == 0) {
            $req = "
	INSERT INTO `poids_marques` ( `Nummarq` , `Nommarq` )
	VALUES (
	NULL , '" . $_POST['newmarq'] . "'
	)";
            $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());


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

//modification d'une donnée
elseif ($_GET['index'] == "modif") {

    $_GET['ligne'] = preg_replace($motif, "", $_GET['ligne']);
    $_GET['ligne'] = (float) $_GET['ligne'];


    $req = "SELECT *
	FROM `poids_mat`
	WHERE 1 AND `num` =" . $_GET['ligne'];

    $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

    $lg = mysql_fetch_row($result);

    if (($pun_user['group_id'] != '1') && (pun_htmlspecialchars($pun_user['username']) != $lg[4]) && (pun_htmlspecialchars($pun_user['username']) != 'Opitux')) {
        $html = '
		<h2>Modifier un produit</h2>
		<p>
		Vous n\'êtes pas l\'auteur de ces données ou n\'êtes pas identifié(e), vous ne pouvez les modifier.<br />
		<br />
		<a href="index.php">Retour</a>';
        echo $html;
    } elseif (time() - $lg[7] < 60 && ($pun_user['group_id'] != '1') && (pun_htmlspecialchars($pun_user['username']) != 'Opitux')) {
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
		<td class="">
	<select size="1" name="categories">
	<option>-Choisissez-</option>' . "\n";

        $req = "SELECT *
	FROM `poids_categories`
	ORDER BY `NomCat` ASC";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            if ($lg[1] == $row["NomCat"]) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html.= "<option " . $selected . ">" . $row["NomCat"] . "</option>" . "\n";
        }

        mysql_free_result($result);

        $html.='
	</select>
		</td>
		<td class="">
	<select size="1" name="marques">
	<option>-Choisissez-</option>' . "\n";

        $req = "SELECT *
	FROM `poids_marques`
	ORDER BY `NomMarq` ASC ";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            if ($lg[2] == $row["NomMarq"]) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html.= "<option " . $selected . ">" . $row["NomMarq"] . "</option>" . "\n";
        }

        mysql_free_result($result);

        $html.='
	</select>
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

// Validation des modifications d'une donnée
elseif ($_POST['index'] == "valmod") {
    $html = '<h2>Modifier un produit</h2>';

    // cas de la virgule
    if (ereg(',', $_POST['poids'])) {
        $_POST['poids'] = str_replace(",", ".", $_POST['poids']);
    }
    $_POST['poids'] = (float) preg_replace("'[^\d]^.'", "", $_POST['poids']);

    $req = "
	UPDATE `poids_mat` SET `categorie` = '" . $_POST['categories'] . "',
	`marque` = '" . $_POST['marques'] . "',
	`nom` = '" . $_POST['modele'] . "',
	`poids` = '" . $_POST['poids'] . "',
	`rq` = '" . $_POST['rq'] . "', `date` = '" . time() .
            "' WHERE `num` = '" . $_POST['ligne'] . "' LIMIT 1";

    $req2 = "
	INSERT INTO `poids_backup` ( `num` , `categorie` , `marque` , `nom` , `utilisateur` , `poids` , `rq`, `date` )
	VALUES (NULL , '" . $_POST['categories'] . "', '" . $_POST['marques'] . "', '" . ucfirst($_POST['modele']) . "', '" . pun_htmlspecialchars($pun_user['username']) . "', '" . $_POST['poids'] . "', '" . ucfirst($_POST['rq']) . "', '" . time() . "')";

    $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());
    $result = mysql_query($req2) or die('Erreur SQL req1!<br />' . mysql_error());
    $html.='Votre enregistrement été modifié
	<br /><br />
	<a href="index.php">Retour</a>';

    echo $html;
} else {

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
		<td class="">
	<select size="1" name="categories">
	<option>Toutes</option>' . "\n";

    // Liste categories

    $req = "SELECT *
		FROM `poids_categories`
		ORDER BY `NomCat` ASC";

    $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        $html.= '<option>' . $row['NomCat'] . '</option>' . "\n";
    }
    mysql_free_result($result);

    $html.='
		</select>
		</td>
		<td class="">
		<select size="1" name="marques">
		<option>Toutes</option>' . "\n";

    // Liste marques

    $req = "SELECT *
		FROM `poids_marques`
		ORDER BY `NomMarq` ASC ";

    $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        $html.='<option>' . $row['NomMarq'] . '</option>' . "\n";
    }
    mysql_free_result($result);

    $html.='
	</select>
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
				<td class="">
			<select size="1" name="categories">
			<option>-Choisissez-</option>' . "\n";

        $req = "SELECT *
			FROM `poids_categories`
			ORDER BY `NomCat` ASC";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $html.='<option>' . $row["NomCat"] . '</option>' . "\n";
        }
        mysql_free_result($result);

        $html.='</select>
				</td>
				<td class="">
			<select size="1" name="marques">
			<option>-Choisissez-</option>' . "\n";


        $req = "SELECT *
			FROM `poids_marques`
			ORDER BY `NomMarq` ASC ";

        $result = mysql_query($req) or die('Erreur SQL req1!<br />' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $html.='<option>' . $row['NomMarq'] . '</option>' . "\n";
        }

        mysql_free_result($result);

        $html.='
				</select>
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

    Echo $html;  //ecrit la page
}

mysql_close($db);
?>
