<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($export) and $export!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');
	
	if (isset($_GET['dump']))
		$getDump=$_GET['dump'];
	else
		$getDump='';
	$url=str_replace('&', '-', $_SERVER['REQUEST_URI']);

	switch ($getDump) {
		//backup de la bdd
		case 'backup':
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);

				$ad_date=date("d/m/Y H:i:s", time()+$decalage_h);
				//création du fichier de dump
				$name_file='comptes_'.date("Y-m-d_H-i-s", time()+$decalage_h).'_manu';
				$file=fopen('uploads/'.$_SESSION['login'].'/dumps/'.$name_file.'.sql', 'w+');

				//requete pour récupéré toutes les lignes de la bdd
				$req1=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id']);

				//écriture de toutes les opération du ccp
				while ($datas1=$req1->fetch(PDO::FETCH_ASSOC)) {
					fputs($file, "INSERT INTO `giltin_comptes_".$_SESSION['id']."` VALUES (".$datas1['id'].", '".$datas1['id_compte']."', '".$datas1['nom']."', '".$datas1['categorie']."', '".$datas1['op_date']."', '".$datas1['type']."', ".$datas1['montant'].", ".$datas1['verif'].");\n");
				}
				fputs($file, "\n");
				fclose($file);

				//récupération de la taille du dump en bytes
				$file_size_bytes=filesize('uploads/'.$_SESSION['login'].'/dumps/'.$name_file.'.sql');
				//taille du fichier adapter à l'unité de mesure qui convient
				$file_size=human_file_size($file_size_bytes);
				//ajout dans la table backup du dump de la base
				$bddlog->exec('INSERT INTO `giltin_backups` VALUES ("", "'.$_SESSION['id'].'", "'.$name_file.'", "'.$ad_date.'", "'.$file_size.'")');
				header('location:?m=export&msg=backup_dump');
			}
			catch (PDOException $message) {
				die("<div class='msgbad'>Echec d\'accès à la base de donn&eacute;es</div>");
			};
			break;

		//restauration d'un backup
		case 'restore':
			//connexion BDD
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);
				//$bddlog->exec('TRUNCATE TABLE ccp;TRUNCATE TABLE livret;TRUNCATE TABLE user;');
				$file=file_get_contents('uploads/'.$_SESSION['login'].'/dumps/'.$_GET['file'].'.sql');
				echo $file;
				$bddlog->exec($file);
				header('location:?m=export&msg=restore_dump');
			}
			catch (PDOException $message) {
				die("<div class='msgbad'>Erreur rencontr&eacute;e : ".$message->getMessage()."</div>");
			};
			break;

		//affichage par defaut avec le listing des dumps
		case '':
			//connexion BDD
			try {
				$bddlog=new PDO($bdd, $bdduser, $bddmdp);

				echo '<section class="panel col-60">
						<section class="panel-header">
							<span><i class="fa fa-database"></i>Liste de vos sauvegarde</span>
						</section>
						<section class="panel-body">
							<section class="panel-body-header">
								<div class="conteneur_options">
									<button class="button_link"><a href="?m=export&dump=backup">Faire un backup de vos comptes</a></button>
								</div>
							</section>
					 		<table cellspacing="0" cellpadding="0" class="tList">
								<tr class="tHeader">
									<td>Nom du backup</td>
									<td>Date de cr&eacute;ation</td>
									<td>Taille</td>
									<td></td>
									<td></td>
								</tr>';
				$req5=$bddlog->query('SELECT * FROM giltin_backups WHERE id_user="'.$_SESSION['id'].'" ORDER BY id ASC');
				$x=0;
				$url=str_replace('&', '-', $_SERVER['REQUEST_URI']);
				while ($datas5=$req5->fetch(PDO::FETCH_ASSOC)) {
					$x=2-$x;
					echo '<tr '; if ($x==0) echo 'class="impair"'; echo '>
							<td><span title="#'.$datas5['id'].'">'.$datas5['nom'].'</span></td>
							<td>'.$datas5['ad_date'].'</td>
							<td>'.$datas5['size'].'</td>
							<td><a href="?m=export&dump=restore&file='.$datas5['nom'].'">Restaurer</a></td>
							<td>
								<div class="del">
									<img src="templates/images/empty.png" class="del_link" onclick="hydrating_form_dump('.$datas5['id'].', \''.$datas5['nom'].'\', \''.$url.'\')" title="Supprimer" />
								</div>
							</td>
						</tr>';
				}
					echo '</table>
					</section>
					<section id="wrapper_overLayer">
						<section id="overLayer">
							<section id="contentOverLayer">
								Etes vous sur de vouloir supprimer cette sauvegarde : <span id="dump_name"></span>?<br /><br />
								<form action="?m=deleter" method="post" id="del_form">
									<input type="hidden" id="id" name="id" />
									<input type="hidden" id="file" name="file" />
									<input type="hidden" id="url" name="url" />
									<input type="hidden" name="module" value="dump" />
									<input type="submit" id="btn_confirm" value="Confirmer" /><input type="reset" id="btn_cancel" value="Annuler" />
								</form>
							</section>
						</section>
					</section>';
			}
			catch (PDOException $message) {
				die("<div class='msgbad'>Erreur rencontr&eacute;e : ".$message->getMessage()."</div>");
			};
			break;
	}