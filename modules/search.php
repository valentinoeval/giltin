<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($search) and $search!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');
	
	if (isset($_POST['name']) and !empty($_POST['name'])) {
		try {
			$bddlog=new PDO($bdd, $bdduser, $bddmdp);
		}
		catch (PDOException $message) {
			die("Erreur de connexion : ".$message->getMessage());
		}

		$_POST['name']=charSpec2code($_POST['name'], "convert");

		//vérification du type DD/MM/AAAA
		if (preg_match("#^date:[0-9]{2}/[0-9]{2}/[0-9]{4}$#", $_POST['name'])) {
			$html='Recherche par <span>DD/MM/AAAA</span><br>';
			$_POST['name']=build_date_en($_POST['name']);
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date="'.$_POST['name'].'"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date="'.$_POST['name'].'"');
			$flag=true;
		}

		//vérification du type MM/AAAA
		elseif (preg_match("#^date:[0-9]{2}/[0-9]{4}$#", $_POST['name'])) {
			list($null, $date)=explode(':', $_POST['name']);die($date);
			list($mois, $annee)=explode('/', $date);
			$html='Recherche des opérations du mois de '.dateInt2String($mois).' '.$annee;
			$_POST['name']=date('Y-m', time($date));
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date like "'.$_POST['name'].'%"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date like "'.$_POST['name'].'%"');
		}

		//vérification du type AAAA
		elseif (preg_match("#^date:[0-9]{4}$#", $_POST['name'])) {
			list($null, $annee)=explode(':', $_POST['name']);
			$html='Recherche des opérations de l\'année <span>'.$annee.'</span><br />'; 
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date like "'.$annee.'%"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date like "'.$_POST['name'].'%"');
			$flag=true;
		}

		//vérification si la chaine est de type #x
		elseif (preg_match("*^id:[0-9]+$*", $_POST['name'])) {
			$html='Cas de recherche par l\'<span>#id</span> de l\'op&eacute;ration bancaire<br>';
			list($null, $id)=explode(":", $_POST['name']);
			$_POST['name']=$id;
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id="'.$_POST['name'].'"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE id="'.$_POST['name'].'"');
			$flag=true;
		}

		//vérification si la chaine est de type alphanumérique
		elseif (preg_match("#^[a-zA-Z0-9][^ ]+$#", $_POST['name'])) {
			$html='Cas de recherche par le <span>nom</span> de l\'op&eacute;ration bancaire<br>';
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE nom="'.$_POST['name'].'"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE nom="'.$_POST['name'].'"');
			$flag=true;
		}

		//vérification si la chaine est de type numérique sans virgule
		elseif (preg_match("#^[0-9]+€$#", $_POST['name'])) {
			$html='Cas de recherche par le <span>montant</span> de l\'op&eacute;ration bancaire<br>';
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE montant="'.$_POST['name'].'"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE montant="'.$_POST['name'].'"');
			$flag=true;
		}

		//vérification si la chaine est de type numérique avec virgule
		elseif (preg_match("#^[0-9]+[.,][0-9]{1,2}€$#", $_POST['name'])) {
			$html='Recherche par le <span>montant</span> de l\'op&eacute;ration bancaire<br>';
			//récupération des opération bancaires
			$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE montant="'.$_POST['name'].'"');
			$req2=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE montant="'.$_POST['name'].'"');
			$flag=true;
		}
		
		//comptage des opérations bancaires
		$nb_op=0;
		if (isset($flag) and $flag==true) {
			$datas2=$req2->fetch(PDO::FETCH_ASSOC);
			$nb_op=$datas['nb_op'];
		}

		if (isset($flag) and $flag==true) {
			echo '<section class="panel col-100">
					<section class="panel-header">
						<span><i class="fa fa-money"></i>Résultat(s) de la recherche</span>
					</section>
					<section class="panel-body">
						<section class="panel-body-header">
							'.$html.'
							Il y a <span>'.$nb_op.'</span> op&eacute;ration';if ($nb_op>1) echo 's'; echo ' bancaire';if ($nb_op>1) echo 's';
							echo ' dans tous vos comptes pour votre recherche.
						</section>
							<table cellspacing="0" cellpadding="0" class="tList">
							<tr class="tHeader">
								<td></td>
								<td class="nom">Nom de l\'op&eacute;ration bancaire</td>
								<td class="date">Date</td>
								<td class="type">Type</td>
								<td class="montant">Montant</td>
								<td class="compte">Compte</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>';
			$x=0;
			while ($datas=$req->fetch(PDO::FETCH_ASSOC)) {
				$x=2-$x;
				//récupération du nom du compte
				$req3=$bddlog->query('SELECT nom FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' AND id_compte='.$datas['id_compte']);
				$datas3=$req3->fetch(PDO::FETCH_ASSOC);
				if ($datas['type']=='c')
					$datas['type']='Cr&eacute;dit';
				elseif ($datas['type']=='d')
					$datas['type']='D&eacute;bit';
				if ($datas['verif']=='1') {
					echo '<tr '; if ($x==0) echo 'class="impair"'; echo '>';
				}
				else {
					echo'<tr class="op_bancaire_not_verif '; if ($x==0) echo ' impair"'; echo '">';
				}
						echo '<td>';
							if ($datas['verif']=='1')
								echo '<img src="templates/images/verified.png" title="Op&eacute;ration bancaire v&eacute;rifi&eacute;e" />';
							else
								echo '<a href="?m=view_op&account='.$_GET['account'].'&verif='.$datas['id'].'"><img src="templates/images/unverified.png" title="V&eacute;rifer l\'op&eacute;ration" /></a>';
						echo '</td>';
						echo '<td class="nom"><span title="id:'.$datas['id'].'">'.charSpec2code($datas['nom'], "deconvert").'</span></td>';
						echo '<td>'.build_date_fr($datas['op_date']).'</td>';
						echo '<td>';
							if ($datas['type']=='Cr&eacute;dit')
								echo '<span class="color_credit">'; 
							else
								echo '<span class="color_debit">';
							echo $datas['type'].'</span>';
						echo '</td>';
						echo '<td>';
							if ($datas['type']=='Cr&eacute;dit')
								echo '<span class="color_credit">'; 
							else
								echo '<span class="color_debit">';
							echo $datas['montant'].' €</span>';
						echo '</td>';
						echo '<td class="compte">'.$datas3['nom'].'</td>';
						echo '<td><div class="newcom"><a href="./" /><img src="templates/images/empty.png" title="Ajouter un commentaire" /></a></div></td>';
						echo '<td><div class="edit"><a href="./" /><img src="templates/images/empty.png" title="Editer" /></a></div></td>';
						echo '<td><div class="del"><a href="./" /><img src="templates/images/empty.png" title="Supprimer" /></a></div></td>';
					echo '</tr>';
			}
			echo '</table>
				</section>';
		}
	}
	else {
		echo print_msg("no_criterion");
	}