<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($deleter) and $deleter!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');
	
	if ($_POST['module']=='op') {
		if (isset($_POST['account']) and !empty($_POST['account'])) {
			if (isset($_POST['id']) and !empty($_POST['id'])) {
				try {
					$bddlog=new PDO($bdd, $bdduser, $bddmdp);
					$req=$bddlog->query('DELETE FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_POST['account'].' AND id='.$_POST['id']);
					$url=str_replace('-', '&', $_POST['url']);
					header('location:'.$url.'&msg=del_op');
				}
				catch (PDOException $message) {
					die("Echec de la suppression de votre op&eacute;ration bancaire");
				}
			}
		}
	}
	elseif ($_POST['module']=='dump') {
		if (isset($_POST['id']) and !empty($_POST['id'])) {
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);
				$req=$bddlog->query('DELETE FROM giltin_backups WHERE id='.$_POST['id'].' AND id_user='.$_SESSION['id']);
				unlink('uploads/'.$_SESSION['login'].'/dumps/'.$_POST['file'].'.sql');
				$url=str_replace('-', '&', $_POST['url']);
				header('location:'.$url.'&msg=del_dump');
			}
			catch (PDOException $message) {
				die("Echec de la suppression de votre sauvegarde");
			}
		}
	}
	elseif ($_POST['module']=='settings') {
		if (isset($_POST['id']) and !empty($_POST['id'])) {
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);
				//suppression des éventuelles opérations bancaires du comtpe
				$bddlog->exec('DELETE FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_POST['id']);
				//suppression du compte
				$bddlog->exec('DELETE FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' AND id_compte='.$_POST['id']);
				$url=str_replace('-', '&', $_POST['url']);
				header('location:'.$url.'&msg=account_deleted');
			}
			catch (PDOException $message) {
				die("Echec de la suppression de votre sauvegarde");
			}
		}
	}