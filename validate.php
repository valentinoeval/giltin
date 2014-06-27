<?php
	include('configs.php');

	if (isset($_GET['validate']) and $_GET['validate']="on") {
		if (isset($_GET['id']) and !empty($_GET['id'])) {
			if (isset($uid) and !empty($uid)) {
				try {
					$bddlog=new PDO($bdd, $bdduser, $bddmdp);
				}
				catch (PDOException $error) {
					die("Erreur de connexion : ".$error->getMessage());
				}

				//requête SQL
				$req=$bddlog->query('SELECT * FROM giltin_users WHERE id='.$_GET['id'].' AND uid="'.$_GET['uid'].'"');
				//execution de la requête
				$datas=$req->fetch(PDO::FETCH_ASSOC);
				$req->closeCursor();
				if ($_GET['id']==$datas['id'] and $_GET['uid']==$datas['uid']) {
					if ($datas['rights']!=1) {
						//création du dossier perso
						if (mkdir('uploads/'.$datas['login'].'/dumps', 0755, true)) {
							if (mkdir('uploads/'.$datas['login'].'/avatar', 0755, true)) {
								if (mkdir('uploads/'.$datas['login'].'/logs', 0744, true)) {
									$bddlog->exec('UPDATE giltin_users SET rights=2 WHERE id='.$_GET['id'].' AND uid="'.$_GET['uid'].'"');
									$bddlog->exec('
										CREATE TABLE `giltin_comptes_'.$_GET['id'].'` (
											`id` int(11) NOT NULL AUTO_INCREMENT,
											`id_compte` int(11) NOT NULL,
											`nom` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
											`categorie` int(11) NOT NULL,
											`op_date` date NOT NULL,
											`type` varchar(1) NOT NULL,
											`montant` float(8,2) NOT NULL,
											`verif` tinyint(1) NOT NULL,
											PRIMARY KEY (`id`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45');
									$bddlog=null;
									//création du fichier log de l'utilisateur
									$log=fopen('uploads/'.$_SESSION['login'].'/logs/logs', 'a+');
									fclose($log);
									header('location:index.php?msg=account_validate');
								}
								else
									header('location:validate.php?msg=mkdir_error');
							}
							else
								header('location:validate.php?msg=mkdir_error');
						}
						else
							header('location:validate.php?msg=mkdir_error');
					}
					else
						header('location:validate.php?msg=account_already_create');
				}
				else 
					header('location:validate.php?msg=validate_error');
			}
			else
				header('location:validate.php?msg=no_id_uid');
		}
		else
			header('location:validate.php?msg=no_id_uid');
	}