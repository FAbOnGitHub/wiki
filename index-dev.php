<?php
// L 'idée est de mettre ce dossier en parallèle de l'existant puis on fera
// la bascule.
//    include '../conf/local.protected.php'

try {
    include 'rl.inc.php';
} catch (Exception $e) {
    die("EXCEPTION : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Poids du matériel de randonnée , mesuré par les utilisateurs</title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="ROBOTS" content="NOINDEX, FOLLOW" />
        <!-- BEGIN MODIF META RL -->
        <?php include PUN_ROOT . 'include/user/header_favicon.php'; ?>
        <?php include PUN_ROOT . 'include/user/header_img_aleatoire.php'; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo path_to_wiki ?>/lib/exe/css.php?t=RL&amp;tseed=0828d570c7ee429c6fcfd2387e140482"/>
        <link rel="stylesheet" type="text/css" href="<?php echo path_to_forum . 'style/' . $conf['pun_style'] . '.css'; ?>" />
        <!-- BEGIN MODIF META RL -->

        <script src="functions.js" type="text/javascript" language="javascript"></script>

    </head>

    <body>
        <!-- BEGIN HEADER RL -->
        <?php include PUN_ROOT . '/include/user/header.php' ?>
        <?php include PUN_ROOT . '/include/user/menuG.php' ?>
        <!-- END RL -->

        <div id="dokuwiki__site">
            <div id="dokuwiki__top" class="site dokuwiki mode_show tpl_RL     ">

                <!-- ********** HEADER ********** -->
                <div id="dokuwiki__header"><div class="pad group">


                        <div class="headings group">
                            <ul class="a11y skip">
                                <li><a href="#dokuwiki__content">Aller au contenu</a></li>
                            </ul>

                            <h1><a href="/wiki/doku.php?id=accueil_du_wiki"  accesskey="h" title="[H]"><img src="/wiki/lib/tpl/RL/images/logo.png" width="64" height="64" alt="" /> <span>Randonner leger ou ultra-léger</span></a></h1>
                        </div>

                        <div class="tools group">
                            <!-- USER TOOLS -->
                            <!-- BEGIN SUP RL
                                    <div id="dokuwiki__usertools">
                                            <h3 class="a11y">Outils pour utilisateurs</h3>
                                            <ul>
                                                                                    </ul>
                                    </div>
            END SUP RL -->

                            <!-- SITE TOOLS -->
                            <div id="dokuwiki__sitetools">
                                <h3 class="a11y">Outils du site</h3>
                                <form action="/wiki/doku.php?id=accueil_du_wiki" accept-charset="utf-8" class="search" id="dw__search" method="get" role="search">
                                    <div class="no">
                                        <input type="hidden" name="do" value="search" />
                                        <input type="text" placeholder="Rechercher" id="qsearch__in" accesskey="f" name="id" class="edit" title="[F]" />
                                        <button type="submit" title="Rechercher">Rechercher</button><div id="qsearch__out" class="ajax_qsearch JSpopup"></div>
                                    </div>
                                </form>
                                <div class="mobileTools">
                                    <form action="/wiki/doku.php" method="get" accept-charset="utf-8">
                                        <div class="no"><input type="hidden" name="id" value="camp_de_base_2015" />
                                            <select name="do" class="edit quickselect" title="Outils">
                                                <option value="">Outils</option>
                                                <optgroup label="Outils de la page">
                                                    <option value="edit">Afficher le texte source</option>
                                                    <option value="revisions">Anciennes révisions</option><option value="backlink">Liens de retour</option>
                                                </optgroup>
                                                <optgroup label="Outils du site">
                                                    <option value="recent">Derniers changements</option>
                                                    <option value="media">Gestionnaire Multimédia</option>
                                                    <option value="index">Plan du site</option>
                                                </optgroup>
                                                <optgroup label="Outils pour utilisateurs">
                                                </optgroup>
                                            </select>
                                            <button type="submit">&gt;</button>
                                        </div>
                                    </form>
                                </div>
                                <ul>
                                    <li><a href="/wiki/doku.php?id=camp_de_base_2015&amp;do=recent"  class="action recent" accesskey="r" rel="nofollow"
                                           title="Derniers changements [R]">Derniers changements</a></li>
                                    <li><a href="/wiki/doku.php?id=camp_de_base_2015&amp;do=media&amp;ns="  class="action media" rel="nofollow"
                                           title="Gestionnaire Multimédia">Gestionnaire Multimédia</a></li>
                                    <li><a href="/wiki/doku.php?id=camp_de_base_2015&amp;do=index"  class="action index" accesskey="x"
                                           rel="nofollow" title="Plan du site [X]">Plan du site</a></li>
                                </ul>
                            </div>

                            <!-- BEGIN MODIF RL-->
                            <div id="brdwelcome" class="inbox">
                                <p class="conl">
                                    <?php
                                    if (pun_htmlspecialchars($pun_user['username']) != "Guest") {
                                        echo 'Connecté en tant que : <bdi>' . $pun_user['username']
                                        . '</bdi> (<bdi>' . $pun_user['username'] . '</bdi>)</p>';
                                    } else {
                                        echo 'Vous n\'êtes pas identifié(e).';
                                    }
                                    ?>
                                <ul class="conr">
                                </ul>
                                <div class="clearer"></div>
                            </div>
                        </div>
                        <!-- END MODIF RL-->

                        <!-- BREADCRUMBS -->
                        <div class="breadcrumbs">
                            <div class="youarehere"><span class="bchead">Vous êtes ici: </span><span class="home"><bdi><a href="/wiki/doku.php?id=accueil_du_wiki" class="wikilink1" title="accueil_du_wiki">accueil_du_wiki</a></bdi></span> » <bdi><span class="curid"><a href="/wiki/poids/index.php" class="wikilink1" title="mesures_du_poids_du_materiel">mesures_du_poids_du_materiel</a></span></bdi></div>
                        </div>



                        <hr class="a11y" />
                    </div></div><!-- /header -->

                <div id="dokuwiki__content">
                    <div class="pad group">
                        <div class="page group">
                            <h1>Mesures du poids du matériel de randonnée</h1>
                            <? include 'poids2.php'; ?>
                        </div>
                    </div>
                </div>


                <!-- ********** FOOTER ********** -->
                <div id="dokuwiki__footer"><div class="pad">
                        <div class="license">Sauf mention contraire, le contenu de ce wiki est placé sous les termes de la licence suivante : <bdi><a href="http://creativecommons.org/licenses/by-sa/3.0/" rel="license" class="urlextern">CC Attribution-Share Alike 3.0 Unported</a></bdi></div>
                        <div class="buttons">
                            <a href="http://creativecommons.org/licenses/by-sa/3.0/" rel="license"><img src="/wiki/lib/images/license/button/cc-by-sa.png" alt="CC Attribution-Share Alike 3.0 Unported" /></a>        <a href="http://www.dokuwiki.org/donate" title="Donate" ><img
                                    src="/wiki/lib/tpl/RL/images/button-donate.gif" width="80" height="15" alt="Donate" /></a>
                            <a href="http://www.php.net" title="Powered by PHP" ><img
                                    src="/wiki/lib/tpl/RL/images/button-php.gif" width="80" height="15" alt="Powered by PHP" /></a>
                            <a href="http://validator.w3.org/check/referer" title="Valid HTML5" ><img
                                    src="/wiki/lib/tpl/RL/images/button-html5.png" width="80" height="15" alt="Valid HTML5" /></a>
                            <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3" title="Valid CSS" ><img
                                    src="/wiki/lib/tpl/RL/images/button-css.png" width="80" height="15" alt="Valid CSS" /></a>
                            <a href="http://dokuwiki.org/" title="Driven by DokuWiki" ><img
                                    src="/wiki/lib/tpl/RL/images/button-dw.png" width="80" height="15" alt="Driven by DokuWiki" /></a>
                        </div>
                    </div></div>
                <!-- /footer -->

            </div><!-- /dokuwiki__top -->
        </div><!-- /dokuwiki__site-->

        <!-- BEGIN HEADER RL -->
        <?php include PUN_ROOT . '/include/user/footer.php' ?>
        <!-- END RL -->

    </body>
</html>
