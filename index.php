<?php
	session_start();
	include('configs.php');
	
	if (isset($_SERVER['HTTP_REFERER'])) {
		setcookie('last_page', $_SERVER['HTTP_REFERER'], time()+604800);
	}
?>
<html lang="fr-FR">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="templates/design-desktop<?php if ((!isset($_SESSION['login']) and empty($_SESSION['login'])) or (isset($_SESSION['login']) and empty($_SESSION['login']))) echo '-logout' ?>.css" type="text/css" />
		<script type="text/javascript" src="templates/js/jquery-1.4.3.min.js"></script>
		<script type="text/javascript" src="templates/js/jquery.perso.js"></script>
		<script type="text/javascript" src="templates/js/functions.js"></script>
		<link rel="icon" type="image/ico" href="templates/images/fav.ico">
		<title>Giltin'</title>
	</head>
	<body>
		<?php
			if ((!isset($_SESSION['login']) and empty($_SESSION['login'])) or (isset($_SESSION['login']) and empty($_SESSION['login']))) {
		?>
		<div id="container_login">
			<?php
				if (isset($_GET['msg']) and !empty($_GET['msg']))
					echo print_msg($_GET['msg']);
			?>
			<div id="container_login_form">
				<img src="templates/images/logo.png" id ="logo" />
				<form action="login.php" method="post" name="login" id="login">
					<input type="text" name="login" placeholder="Nom d'utilisateur" /><br />
					<input type="password" name="password" placeholder="Mot de passe" /><br /><br />
					<?php if (isset($_GET['m']) and !empty($_GET['m'])) echo '<input type="hidden" name="module" value="'.$_GET['m'].'" />'; ?>
					<input type="submit" value="Connexion" /><br />
					<a href="#" id="showFormReset">Mot de passe perdu ?</a>
				</form>
				<form action="reset.php" method="post" name="reset" id="reset">
					<a href="#" id="showFormLogin"><i>&lsaquo; <small>Formulaire de connexion</small></i></a><br />
					R&eacute;initialiser votre mot de passe perdu :<br /><br />
					<input type="text" placeholder="Nom d'utilisateur" name="login" /><br />
					<input type="email" placeholder="Email" name="email" /><br />
					<input type="submit" value="Restaurer" />
				</form>
			</div>
		</div>
		<?php
			}
			elseif (isset($_SESSION['login']) and !empty($_SESSION['login'])) {
				include('langs/'.$_SESSION['lang'].'.php');
				//récupération des comptes de l'utlisateur pour les afficher dans le menu
				try {
					$bddlog=new PDO($bdd, $bdduser, $bddmdp);
				}
				catch (PDOException $message) {
					//die("Erreur de connexion : ".$message->getMessage());
				}
				$req_accounts_menu=$bddlog->query('SELECT id_compte, nom FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' ORDER BY id_compte ASC');
				include('templates/menu.php');
		?>
		<div id="barLeft">
			<div id="barLeft-content">
				<form action="?m=search" method="post" class="searchbar">
					<div id="contentSearchBox">
					    <span class="SearchBox">
					        <div  id="c_search_psb_f" class="c_search_mc" >
					        	<!-- lors du focus du input de recherche avec pour valeur "Rechercher par date" on le vide sinon si le input prends le focus mais que ca valeur est différentes
					        	de Rechercher par date on ne le vide pas -->
					            <label for="c_search_psb_box">Rechercher par date</label>    
					            <input id="c_search_psb_box" class="c_search_box c_ml " type="text" name="name" autocomplete="Off" maxlength="500" onfocus="&#36;SB.sbs&#91;&#39;c_search_psb_box&#39;&#93;.focus&#40;&#41;&#59;" onblur="&#36;SB.sbs&#91;&#39;c_search_psb_box&#39;&#93;.blur&#40;&#41;&#59;" />
					            <input title="Rechercher" class="c_search_go" id="c_search_psb_go" type="submit" style="background-image:url(templates/images/loupe.png);" value="" />
					        </div>
					    </span>
					</div>
				</form>
				<?php include('notice.php'); ?>
			</div>
			<footer><span>Giltin' &copy; 2013</span></footer>
		</div>
		<div id="content">
		<?php
				//affichage d'un message d'erreur si besoin
				if (isset($_GET['msg']) and !empty($_GET['msg']))
					echo print_msg($_GET['msg']);	
				//tests de validité du module appelé
				if (!isset($_GET['m'])) {
					//include("modules/home.php");
				}
				elseif ((isset($_GET['m']) and empty($_GET['m'])) or (isset($_GET['m']) and !file_exists("modules/".$_GET['m'].".php"))) {
					header('location:?msg=module_not_exist');
				}
				elseif (isset($_GET['m']) and file_exists("modules/".$_GET['m'].".php")) {
					include("modules/".$_GET['m'].".php");
				}
			}
		?>
		</div>
	</body>
</html>