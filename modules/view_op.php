<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($view_op) and $view_op!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	if (!isset($_GET['account']) or empty($_GET['account']))
		header('location:index.php?msg=not_account');

	//appel du module de calcul du solde du mois précédent si changement de mois
	include('modules/calc_prec_sold.php');
	
	//connexion BDD
	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
	}
	catch (PDOException $message) {
		die("Erreur de connexion : ".$message->getMessage());
	}
	
	//récupération des opération bancaires
	if (isset($_GET['mois']) and $_GET['mois']>=1 and $_GET['mois']<=12) {
		if (isset($_GET['annee']) and !empty($_GET['annee']))
			$Y=$_GET['annee'];
		else
			$Y=date("Y");
		$m=$_GET['mois'];
		$date=$Y.'-'.$m;
	}
	else {
		$Y=date("Y");
		$m=date("m");
		$date=$Y.'-'.$m;
	}
	
	$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND op_date LIKE "'.$date.'%" ORDER BY op_date, id ASC');
	$req2=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND op_date LIKE "'.$date.'%"');
	$req3=$bddlog->query('SELECT montant, type FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND op_date>="'.$date.'-01" AND op_date<="'.$date.'-31"');
	$req4=$bddlog->query('SELECT count(*) AS nb_unverif_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$_GET['account'].' AND verif=0');
	$req5=$bddlog->query('SELECT nom FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' AND id_compte='.$_GET['account']);
	$req6=$bddlog->query('SELECT * FROM giltin_categories');

	//calcul du solde du compte du mois courant
	$solde=0;
	while ($datas3=$req3->fetch(PDO::FETCH_ASSOC)) {
		if ($datas3['type']=='c')
			$solde+=$datas3['montant'];
		else
			$solde-=$datas3['montant'];
		if (!is_int_or_float($solde)) $solde=0;
	}
	if ($solde>0)
		$type='<span class="color_credit">cr&eacute;diteur</span>';
	elseif ($solde==0)
		$type='<span class="color_credit">nul</span>';
	else {
		$type='<span class="color_debit">d&eacute;biteur</span>';
		$solde=$solde*(-1);
	}

	//marquer dans la bdd l'opération comme étant vérifiée
	if (isset($_GET['verif']) and !empty($_GET['verif'])) {
		$bddlog->exec('UPDATE giltin_comptes_'.$_SESSION['id'].' SET verif="1" WHERE id_compte='.$_GET['account'].' AND id='.$_GET['verif']);
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	//comptage des opérations bancaires du mois sélectionné
	$nb_op=0;
	while ($datas2=$req2->fetch(PDO::FETCH_ASSOC)) {$nb_op++;}

	//comptage des opérations non vérifiés
	$datas4=$req4->fetch(PDO::FETCH_ASSOC);
	$nb_unverif_op=$datas4['nb_unverif_op'];
	//récupération du nom du compte
	$datas5=$req5->fetch(PDO::FETCH_ASSOC);
	$account_name=$datas5['nom'];
	//affichage des options et informations
	echo '<section class="tablet tablet_user">
			<img src="templates/images/clip.png" class="clip_task" /><br /><br />
			<button class="button_mounth">
				<ul id="mois">
					<li><span id="current_mounth">'.nb2month($m).'<span class="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></span>
						<ul id="sub_mois">
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=01">Janvier</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=02">F&eacute;vrier</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=03">Mars</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=04">Avril</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=05">Mai</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=06">Juin</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=07">Juillet</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=08">Août</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=09">Septembre</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=10">Octobre</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=11">Novembre</a></li>
							<li><a href="?m=view_op&account='.$_GET['account'].'&mois=12">D&eacute;cembre</a></li>
						</ul>
					</li>
				</ul>
			</button>&nbsp;';
		if ($nb_unverif_op>0) {
			echo '<button class="button_link"><a href="?m=unverif_op&account='.$_GET['account'].'">'.$nb_unverif_op.' op&eacute;ration';if ($nb_unverif_op>1) echo 's';echo ' en attente</a></button>';
		}
		echo '<br /><br />
			<div class="conteneur_options">
				Il y a <span class="info">'.$nb_op.'</span> op&eacute;ration';
				if ($nb_op>1) echo 's';
				echo ' bancaire';
				if ($nb_op>1) echo 's';
				echo ' en <span class="info">'.nb2month($m).'</span> pour votre <span class="info">';
				echo $account_name.'</span> - <a href="?m=unverif_op&account='.$_GET['account'].'">'.$nb_unverif_op.' op&eacute;ration';if ($nb_unverif_op>1) echo 's';echo '</a> en attente de v&eacute;rification.<br />
				Voir les op&eacute;rations bancaires du mois de
				Le solde est '.$type.' de '.$solde.'€
			</div>
			<section class="clear"></section><br />
		</section>';

	//affichage des opérations bancaires récupérées de la BDD
	echo '<table cellspacing="0" cellpadding="0" id="tList">
			<tr id="tHeader">
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
				echo '<td>
						<div class="del">
							<img src="templates/images/empty.png" class="del_link" onclick="hydrating_form_op('.$_GET['account'].', '.$datas['id'].', \''.charSpec2code($datas['nom'], "deconvert").'\', \''.$url.'\')" title="Supprimer" />
						</div>
					</td>';
			echo '</tr>';
	}
	echo '</table>
		<section id="wrapper_overLayer">
			<section id="overLayer">
				<section id="contentOverLayer">
					Etes vous sur de vouloir supprimer cette op&eacute;ration bancaire : <span id="op_name"></span>?<br /><br />
					<form action="?m=deleter" method="post" id="del_form">
						<input type="hidden" id="account" name="account" />
						<input type="hidden" id="id" name="id" />
						<input type="hidden" id="url" name="url" />
						<input type="hidden" name="module" value="op" />
						<input type="submit" id="btn_confirm" value="Confirmer" /><input type="reset" id="btn_cancel" value="Annuler" />
					</form>
				</section>
			</section>
		</section>';