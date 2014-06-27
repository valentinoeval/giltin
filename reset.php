<?php
	include('configs.php');

	$login=$_POST['login'];
	$email=$_POST['email'];

	if (isset($login) and !empty($login)) {
		if (isset($email) and !empty($email) and emailValidation($email)) {
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);
			}
			catch (PDOException $message) {
				die("Erreur de connexion : ".$message->getMessage());
			}

			// création du mot de passe généré aléatoirement selon les caractères disponibles spécifiés dans la fonction
			$newMDP=randomMDP();
			$hash=sha1($newMDP);
			
			$bddlog->exec('UPDATE giltin_users SET password="'.$hash.'" WHERE login="'.$login.'" AND email="'.$email.'"');

			$expediteur="Giltin'";
			$reponse="kingval.studio@gmail.com";
			$messageSend="<html><body>".
					"<h1 style=\"color:#a1a1a1;\">R&eacute;initialistion de votre mot de passe</h1>".
					"<br />Bonjour,<br />".
					"Vous avez demand&eacute; à ce que votre mot de passe soit r&eacute;initialis&eacute; pour cause de perte.<br />".
					"Votre nouveau mot de passe est <b>".$newMDP."</b><br />".
					"Vous pourrez ensuite le modifier dans votre page membre. Tachez de ne plus l'oublier ;)<br /><br />".
					"<i>Staff de Giltin'</i>.".
					"</body></html>";
			mail($email, "R&eacute;initialisation du mot de passe", $messageSend, "From: $expediteur\r\nReply-To: ".$reponse."\r\nContent-Type: text/html; charset=\"iso-utf8\"\r\n");
			header('location:index.php?msg=pass_sent');
		}
		else
			header('location:index.php?msg=email_invalid');
	}
	else
		header('location:index.php?msg=empty');