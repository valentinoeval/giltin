<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($admin) and $admin!=true){
				if ($_SESSION['rights']==-2) {
					header('location:index.php');
				}
			}
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
	}
	catch (PDOException $error) {
		die("Erreur de connexion à la base de données");
	}
	//récupération numéro de release
	$req_version=$bddlog->query('SELECT version FROM giltin_settings');
	$version=$req_version->fetch(PDO::FETCH_ASSOC);
	list($version, $date, $num_release)=explode('.', $version['version']);
	$new_date=date('ymd');
	if ($new_date==$date) {
		$new_num_version=$num_release+1;
		$new_version=$version.'.'.$new_date.'.'.$new_num_version;
	}
	else
		$new_version=$version.'.'.$new_date.'.1';

	if (!empty($_GET['section'])) {
		$section=$_GET['section'];
		switch ($section) {
			case 'new_user':
				if (isset($_POST['login']) and !empty($_POST['login'])) {
					if (isset($_POST['nom']) and !empty($_POST['nom'])) {
						if (isset($_POST['email']) and !empty($_POST['email'])) {
							if (emailValidation($_POST['email'])) {
								$password=randomMDP();
								$hash=sha1($password);
								$uid=uniqid();
								//insertion du nouvel utilisateur
								$bddlog->exec('INSERT INTO giltin_users VALUES("", "'.$_POST['login'].'", "'.$hash.'", 0, "'.$_POST['nom'].'", "", "'.$uid.'", "'.$_POST['email'].'")');
								//récupération de l'id du nouvel utilisateur
								$req=$bddlog->query('SELECT id FROM giltin_users WHERE login="'.$_POST['login'].'"');
								$datas=$req->fetch(PDO::FETCH_ASSOC);
								//insertion de l'enregistrement de la dernière connexion
								$bddlog->exec('INSERT INTO giltin_logs VALUES('.$datas['id'].', time(), "")');
								
								$link='http://giltin.kingvalstudio.net16.net/validate.php?validate=on&id='.$datas['id'].'&uid='.$uid;
								$url="http://giltin.kingvalstudio.net16.net/";
								// création de l'email de validation du compte
								$messageEmail="<html><body>".
									"<h1 style=\"color:#a8a8a8;\">Mot de passe de votre compte sur <a href='".$url."'>Giltin'</a></h1>".
									"<br />Bonjour ".$_POST['nom'].",<br />".
									"Voici votre mot de passe g&eacute;n&eacute;r&eacute; lors de votre ajout par un membre du staff de Giltin' : <b>".$password."</b><br />".
									"Vous pourrai à tout moment le changer dans votre espace personnel.<br />".
									"Pour activer votre compte cliquer sur ce lien : <a href='".$link."'>".$link."</a><br />".
									"Nous vous remercions pour votre int&eacute;rêt envers à Giltin'.<br /><br />".
									"<i>Staff de Giltin'.</i>".
									"</body></html>";
								mail($_POST['email'], "Activation de votre compte Giltin'", $messageEmail, "From: Giltin' Staff\r\nReply-To: kingval.studio@gmail.com\r\nContent-Type: text/html; charset=\"utf8\"\r\n");
								header('location:?module=admin&msg=admin_account_create');
							}
							else
								header('location:?module=admin&msg=');//à faire
						}
						else
							header('location:?module=admin&msg=');//à faire
					}
					else
						header('location:?module=admin&msg=');//à faire
				}
				else
					header('location:?module=admin&msg=');//à faire
				break;
			case 'dump_user':
				$ad_date=date("d/m/Y H:i:s", time()+$decalage_h);
				//création du fichier de dump
				$name_file='users_list_'.date("Y-m-d_H-i-s", time()+$decalage_h);
				$file=fopen('uploads/ADMIN/dumps/'.$name_file.'.sql', 'a+');

				//requete pour récupéré toutes les lignes de la bdd
				$req=$bddlog->query('SELECT * FROM giltin_users');

				//écriture de toutes les opération du ccp
				while ($datas=$req->fetch(PDO::FETCH_ASSOC)) {
					fputs($file, "INSERT INTO giltin_users VALUES (".$datas['id'].", '".$datas['login']."', '".$datas['password']."', ".$datas['rights'].", '".$datas['nom']."', '".$datas['avatar']."', ".$datas['uid'].", '".$datas['email']."', '".$datas['lang']."');\n");
				}
				fclose($file);
				header('location:?module=admin&msg=admin_dump_user');
				break;
			case 'maj_version':
				echo 'a';
				$bddlog->exec('UPDATE giltin_settings SET version="'.$_POST['version'].'"');
				header('location:?module=admin&msg=admin_version_updated')
			;
				break;
		}
	}
?>
<h3>Ajouter un utilisateur</h3>
<form action="?module=admin&section=new_user" method="post">
	<input type="text" name="login" placeholder="Nom d'utilisateur" /><br />
	<input type="text" name="nom" placeholder="Pr&eacute;nom et nom de l'utilisateur" /><br />
	<input type="email" name="email" placeholder="Adresse email" /><br />
	<input type="submit" value="Ajouter" class="ajouter" />
</form>
<button class="button_link"><a href="?module=admin&section=dump_user">Sauvegarder les utilisateurs</a></button>
<h3>Mettre &agrave; jour le num&eacute;ro de version</h3>
Version actuelle : <?php echo $version.'.'.$date.'.'.$num_release; ?><br /><br />
<form action="?module=admin&section=maj_version" method="post">
	<input type="text" name="version" value="<?php echo $new_version; ?>" /><br />
	<input type="submit" value="Mettre à jour" />
</form>