<?php

// <? plus de short tags

/**
 * Function liste
 * Affichage
 *
 * @return None
 */
function mainListe()
{
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

    $aParams = null;
    if ($_POST['categories'] == "Toutes" && $_POST['marques'] != "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE `marque` LIKE ':marques'";
        $aParams[':marques'] = $_POST['marques'];
    } else if ($_POST['categories'] != "Toutes" && $_POST['marques'] == "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE `categorie` LIKE ':categories'";
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
       		`categorie` LIKE ':categories'
		AND `marque` LIKE ':marques'";
        $aParams[':categories'] = $_POST['categories'];
        $aParams[':marques'] = $_POST['marques'];
    }

    if ($_GET['utilisateur'] != "" & $_GET['categories'] == "Toutes" & $_GET['marques'] == "Toutes") {
        $sRequete = "
		SELECT *
		FROM `poids_mat`
		WHERE
		`utilisateur` LIKE 'utilisateur'";
        $aParams[':utilisateur'] = $_GET['utilisateur'];
    }

    if (isset($_POST['class'])) {
        if ($_POST['class'] == 'date') {
            $sRequete.="ORDER BY `" . $_POST['class'] . "` DESC";
        } else {
            $sRequete.="ORDER BY `" . $_POST['class'] . "` ASC";
        }
    } else {
        $sRequete.="ORDER BY `date` DESC";
    }

    //    $result = mysql_query($sRequete) or die('Erreur SQL req1!<br />' . mysql_error());
    //while ($row = mysql_fetch_assoc($result)) {
    $aRows = sqlRead($sRequete, $aParams);
    if (count($aRows)) {
        foreach ($aRows as $row) {
            $html.='
		<tr>
		<td class="">
			<a href="index.php?index=liste&class=categorie&categories=' . str_replace(chr(34),
                            "&quot;", $row["categorie"]) . '&marques=Toutes">
			' . $row["categorie"] . '</a></td>
		<td class="">
			<a href="index.php?index=liste&class=marque&categories=Toutes&marques=' . str_replace(chr(34),
                            "&quot;", $row["marque"]) . '">
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
    } else {
        $html .= "<div class='bg-warning'>pas de résultat</div>";
    }

    echo $html;
}

/**
 * Ajout de poids de Produit
 * Affichage
 *
 * @return None
 */
function mainAjout()
{
    global $pun_user;

    die("Not tested. Die.");
}

/**
 * Function liste
 * Affichage
 *
 * @return None
 */
function mainAjCatMrk()
{
    die("Not tested. Die.");
}

/**
 * Modification d'une donnée
 * Affichage
 *
 * @return None
 */
function mainModif()
{
    global $pun_user;

    die("Not tested. Die.");
}

/**
 * Validation des modifications d'une donnée
 * Affichage
 *
 * @return None
 */
function mainValmod()
{
    global $pun_user;

    die("Not tested. Die.");
}

/**
 * Function du cas par défaut
 * Affichage
 *
 * @return None
 */
function mainDefault()
{
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
        $html .= "<div>pas de données</p>";
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
                $html.='<option>' . $row["NomCat"] . '</option>' . "\n";
            }
            $html.='</select>';
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
			<option>-Choisissez-</option>' . "\n";
            foreach ($aRows as $row) {
                $html .= '<option>' . $row['NomMarq'] . '</option>' . "\n";
            }
            $html .= '
				</select>';
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
echo "Main! avant appel sanitize\n";
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
