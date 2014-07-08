<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($gallery) and $gallery!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	$action='';
	if (!empty($_GET['action']))
		$action=$_GET['action'];

	switch ($action) {
		case 'change':
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);
			}
			catch (PDOException $message) {
				die("Erreur de connexion : ".$message->getMessage());
			}
			$directory='uploads/'.$_SESSION['login'].'/avatar/';
			list($nom, $extension)=explode('.', $_POST['avatar']);
			if ($bddlog->exec('UPDATE giltin_users SET avatar="'.$_POST['avatar'].'" WHERE id='.$_SESSION['id'])) {
				$_SESSION['avatar']=$directory.$nom.'.'.$extension;
				$_SESSION['avatar_mini']=$directory.$nom.'_mini.'.$extension;
				header('location:?m=gallery&msg=avatar_changed');
			}
			break;
	}

	echo '<h3>Galerie des avatars</h3>';
	$handle=opendir('uploads/'.$_SESSION['login'].'/avatar/');
	$directory='uploads/'.$_SESSION['login'].'/avatar/';
	while($file=readdir($handle)) {
		$lenght=strlen($file);
		if ($lenght>=36 and $lenght<=37) {
			echo '<section class="avatar_list">
					<section class="avatar_list_layer">
						<img src="'.$directory.$file.'" class="del_link" onclick="hydrating_form_avatar(\''.$file.'\', \''.$directory.'\')" />
					</section>
				</section>';
		}
	}
	echo '<section id="wrapper_overLayer">
			<section id="overLayer">
				<section id="contentOverLayer">
					Etes vous sur de vouloir remplacer votre avatar par celui-ci : <span id="avatar_mini"></span>?<br /><br />
					<form action="?m=gallery&action=change" method="post" id="del_form">
						<input type="hidden" id="avatar" name="avatar" />
						<input type="submit" id="btn_confirm" value="Modifier" /><input type="reset" id="btn_cancel" value="Annuler" />
					</form>
				</section>
			</section>
		</section>';