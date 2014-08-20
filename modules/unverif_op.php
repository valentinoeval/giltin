<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($unverif_op) and $unverif_op!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	//connexion BDD
	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
	}
	catch (PDOException $message) {
		die("Erreur de connexion : ".$message->getMessage());
	}
	
	$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND verif=0');
	$req2=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND verif=0');
	$req3=$bddlog->query('SELECT montant, type FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND verif=0');
	$req4=$bddlog->query('SELECT nom FROM giltin_list_comptes WHERE id_compte='.$_GET['account']);
	
	//calcul du solde du compte du mois courant
	$solde=0;
	while ($datas3=$req3->fetch(PDO::FETCH_ASSOC)) {
		if ($datas3['type']=='c')
			$solde+=$datas3['montant'];
		else
			$solde-=$datas3['montant'];
	}
	if ($solde>=0)
		$type='<span style="color:#11c71e">cr&eacute;diteur</span>';
	else {
		$type='<span style="color:#ce2020">d&eacute;biteur</span>';
		$solde=$solde*(-1);
	}

	//marquer dans la bdd l'opération comme étant vérifiée
	if (isset($_GET['verif']) and !empty($_GET['verif'])) {
		$bddlog->exec('UPDATE ccp SET verif="1" WHERE id="'.$_GET['verif'].'"');
		//header('location:?m=view_op&account='.$_GET['account']);
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	//comptage des opérations bancaires du mois sélectionné
	$nb_op=0;
	while ($datas2=$req2->fetch(PDO::FETCH_ASSOC)) {$nb_op++;}
	//récupération du nom du compte
	$datas4=$req4->fetch(PDO::FETCH_ASSOC);
	$account_name=$datas4['nom'];
	
	//affichage des options et informations
	echo '<section class="panel col-100">
			<section class="panel-header">
				<span><i class="fa fa-money"></i>Opérations à vérifier</span>
			</section>
			<section class="panel-body">
				<section class="panel-body-header">
					<div class="conteneur_options">
						Il y a <span class="info">'.$nb_op.'</span> op&eacute;ration';
						if ($nb_op>1) echo 's';
						echo ' bancaire';
						if ($nb_op>1) echo 's'; echo ' non v&eacute;rifi&eacute;e';
						if ($nb_op>1) echo 's';
						echo ' pour votre <span class="info">'.$account_name.'</span>
					</div>
				</section>';

			//affichage des opérations bancaires récupérées de la BDD
			echo '<table cellspacing="0" cellpadding="0" class="tList">
					<tr class="tHeader">
						<td></td>
						<td class="nom">Nom de l\'op&eacute;ration bancaire</td>
						<td class="date">Date</td>
						<td class="type">Type</td>
						<td class="montant">Montant</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>';
	$x=0;
	$url=str_replace('&', '-', $_SERVER['REQUEST_URI']);
	while ($datas=$req->fetch(PDO::FETCH_ASSOC)) {
		$x=2-$x;
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
				echo '<td class="nom"><span title="#'.$datas['id'].'">'.charSpec2code($datas['nom'], "deconvert").'</span></td>';
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
				echo '<td><div class="newcom"><a href="./" /><img src="templates/images/empty.png" title="Ajouter un commentaire" /></a></div></td>';
				echo '<td><div class="edit"><a href="?m=edit&type=op&account='.$_GET['account'].'&id='.$datas['id'].'" /><img src="templates/images/empty.png" title="Editer" /></a></div></td>';
				echo '<td><div class="del"><a href="?m=del_op&account='.$_GET['account'].'&id='.$datas['id'].'&url='.$url.'" /><img src="templates/images/empty.png" title="Supprimer" /></a></div></td>';
			echo '</tr>';
	}
	echo '</table>
		</section>';