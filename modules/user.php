<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($user) and $user!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	$section='';
	if (isset($_GET['section']) and !empty($_GET['section']))
		$section=$_GET['section'];
	$action='';
	if (!empty($_GET['action']))
		$action=$_GET['action'];

	switch ($section) {
		case 'edit_user':
			if ($action=='update') {
				//si le mdp et la vérif ne sont pas identique
				if (!empty($_POST['password']) and $_POST['password']!=$_POST['passverif'])
					header('location:?m=user&section=edit_user&msg=user_update_error');
				//si l'email n'est pas valide
				if (!empty($_POST['email']) and emailValidation($_POST['email']))
					header('location:?m=user&section=edit_user&msg=user_update_error');
				//si le nom n'est pas saisi
				if (empty($_POST['nom']))
					header('location:?m=user&section=edit_user&msg=user_name_empty');
				
				try {
					$bddlog=new PDO($bdd, $bdduser, $bddmdp);
				}
				catch (PDOException $message) {
					die("Erreur de connexion : ".$message->getMessage());
				}
				$req=$bddlog->query('SELECT * FROM giltin_users WHERE id='.$_SESSION['id']);
				$datas=$req->fetch(PDO::FETCH_ASSOC);
				$sql='UPDATE giltin_users SET ';
				$nb=0;
				//création de la rquète paramétrable
				if (!empty($_POST['password']) and $_POST['password']==$_POST['passverif']) {
					$hash=sha1($_POST['password']);
					$sql.='password="'.$hash.'"';
					$nb++;
				}
				if (!empty($_POST['nom']) and $_POST['nom']!=$datas['nom']) {
					if ($nb>0) $sql.=', ';
					$sql.='nom="'.strip_codes($_POST['nom']).'"';
					$nb++;
				}
				if (!empty($_POST['email']) and $_POST['email']!=$datas['email']) {
					if ($nb>0) $sql.=', ';
					$sql.='email="'.strip_codes($_POST['email']).'"';
					$nb++;
				}
				if (isset($_FILES['avatar']) and $_FILES['avatar']['error']==0) {
					$imageName=pathinfo($_FILES['avatar']['name']);
					$extension=$imageName['extension'];
					$extensions_autorisees=array('jpg', 'jpeg', 'gif', 'png');
					if ($_FILES['avatar']['size']<=2097152) {
						if (in_array($extension, $extensions_autorisees)) {
							$nom=md5(uniqid());
							$image_original=$nom.'.'.$extension;
							$image_crop=$nom.'_mini.'.$extension;

							$chemin_original='uploads/'.$_SESSION['login'].'/avatar/'.$image_original;
							$chemin_crop='uploads/'.$_SESSION['login'].'/avatar/'.$image_crop;

							if (move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin_original)) {
								//rognage de l'image
								list($largeur, $hauteur)=getimagesize($chemin_original);
								if ($largeur>$hauteur)
									$ratio=$hauteur;
								else
									$ratio=$largeur;
								cropImage($chemin_original, $chemin_crop, $ratio, $ratio);
								unlink($chemin_original);
								rename($chemin_crop, 'uploads/'.$_SESSION['login'].'/avatar/'.$nom.'.'.$extension);
								//redimentionnement de l'image
								resizeImage($chemin_original, $chemin_crop, 40, 40);
								header('location:?m=user&size='.$imageSize['size']);
								//modification de l'avatar dans la session
								$_SESSION['avatar']=$chemin_original;
								$_SESSION['avatar_mini']=$chemin_crop;
								$sql.='avatar="'.$image_original.'"';
								$nb++;
							}
						}
					}
					else
						header('location:?m=user&size='.$_FILES['avatar']['size']);
				}
				if (!empty($_POST['lang']) and $_POST['lang']!=$datas['lang']) {
					if ($nb>0) $sql.=', ';
					$sql.='lang="'.strip_codes($_POST['lang']).'"';
					$nb++;
				}
				$sql.=' WHERE id='.$_SESSION['id'];
				//si des modification dans le formulaire ont était récupéré on execute la requete
				if ($nb>0) {
					if ($bddlog->exec($sql)) {
						$_SESSION['nom']=$_POST['nom'];
						$_SESSION['email']=$_POST['email'];
						header('location:?m=user&section=view_user&msg=user_updated');
					}
					else {
						header('location:?m=user&section=edit_user&msg=user_update_error');
					}
				}

			}
			else {
				$avatar=$_SESSION['avatar'];
				$nom=$_SESSION['nom'];
				$email=$_SESSION['email'];echo $action;
				echo '
					<section class="tablet tablet_user">
						<img src="templates/images/clip.png" class="clip_task" /><br /><br />
						<section id="list_infos_user_container">
							<form action="?m=user&section=edit_user&action=update" method="post" enctype="multipart/form-data">
								Avatar :<br />
								<input type="file" name="avatar" /><br />
								<input type="text" name="nom" placeholder="Pr&eacute;nom Nom" value="'.$nom.'" /><br />
								<input type="password" name="password" placeholder="Mot de passe" /><br />
								<input type="password" name="passverif" placeholder="V&eacute;rification mot de passe" /><br />
								<input type="email" name="email" placeholder="Email" value="'.$email.'" /><br />
								<select name="lang">';
									foreach ($list_langs as $code => $nom) {
										echo '<option value="'.$code.'">'.$nom.'</option>';
									}
								echo '</select><br />
								<input type="submit" value="Modifier" />
							</form>
						</section>
						<section id="tablet_user_avatar">
							<section id="tablet_user_avatar_container">
								<img src="'.$avatar.'" />
							</section>
						</section>
						<section class="clear"></section><br />
					</section>';
				}
			break;
		default:
			$req=$bddlog->query('SELECT * FROM giltin_users WHERE id='.$_SESSION['id']);
			$datas=$req->fetch(PDO::FETCH_ASSOC);
			$avatar=$_SESSION['avatar'];
			$nom=$datas['nom'];
			$rights=rights($datas['rights']);
			$email=$datas['email'];
			$lang=lang($datas['lang']);

			echo '<section class="panel col-100">
					<section class="panel-header">
						<span><i class="fa fa-history"></i>Mes informations</span>
					</section>
					<section class="panel-body">
						<section class="panel-body-content">
							<button class="button_link"><a href="?m=user&section=edit_user">Modifiez vos informations</a></button>&nbsp;
							<button class="button_link"><a href="?m=gallery">Visitez votre galerie d\'avatars</a></button>&nbsp;
							<button class="button_link"><a href="?m=view_logs">Suivi des connexions</a></button><br /><br />
							<section id="list_infos_user_container">
								<ul id="list_infos_user">
									<li id="login">'.$nom.'</li>
									<li id="rights">'.$rights.'</li>
									<li id="email">'.$email.'</li>
									<li id="lang">'.$lang.'</li>
								</ul>
							</section>
							<section id="tablet_user_avatar">
								<section id="tablet_user_avatar_container">
									<img src="'.$avatar.'" />
								</section>
							</section>
						</section>
					</section>
				</section>';
			break;
	}