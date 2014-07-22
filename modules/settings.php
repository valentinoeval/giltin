<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($settings) and $settings!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
	}
	catch (PDOException $message) {
		die("Impossible d'accéder à la base de données");
	}

	$url=str_replace('&', '-', $_SERVER['REQUEST_URI']);

	if (isset($_GET['section']) and !empty($_GET['section'])) {
		$section=$_GET['section'];
		switch ($section) {
			case 'new_account':
				if (isset($_POST['nom']) and !empty($_POST['nom'])) {
					if (preg_match('#^[a-zA-Z0-9 -_]+$#', $_POST['nom'])) {
						///comptage du nombre de compte de l'utilisateur
						$req=$bddlog->query('SELECT count(*) AS nb_compte FROM giltin_list_comptes WHERE id_user='.$_SESSION['id']);
						$datas=$req->fetch(PDO::FETCH_ASSOC);
						//incrémentation du nombre de compte pour obtenir l'identifiant du nouveau compte
						$nb_compte=$datas['nb_compte']+1;
						$nom=strip_codes($_POST['nom']);
						//insertion du nouveau compte
						$bddlog->exec('INSERT INTO giltin_list_comptes VALUES('.$_SESSION['id'].', '.$nb_compte.', "'.$nom.'", '.$_POST['categorie'].')');
						header('location:?m=settings&msg=account_create');
					}
					else
						header('location:?m=settings&msg=invalid_account_name');
				}
				else
					header('location:?m=settings&msg=no_account_name');
				break;
		}
	}
?>
<h3>Ajouter un compte</h3>
<form action="?m=settings&section=new_account" method="post">
	<input type="text" name="nom" placeholder="Nom du compte" /><br />
	<select name="categorie">
		<?php
			$req4=$bddlog->query('SELECT * FROM giltin_comptes_categories');
			while ($datas4=$req4->fetch(PDO::FETCH_ASSOC))
				echo '<option value="'.$datas4['id_categorie'].'">'.$datas4['nom'].'</option>';
		?>
	</select><br />
	<input type="submit" value="Ajouter" />
</form>
<table cellspacing="0" cellpadding="0" id="tList">
	<tr id="tHeader">
		<td>Nom compte</td>
		<td>Nombre d'opérations</td>
		<td>Etat</td>
		<td>Solde</td>
		<td></td>
		<td></td>
	</tr>
	<?php
		$req2=$bddlog->query('SELECT id_compte, nom FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' ORDER BY id_compte ASC');
		$x=0;
		while ($datas2=$req2->fetch(PDO::FETCH_ASSOC)) {
			$x=2-$x;
			$solde=0;
			$nb_op=0;
			//comtage du solde
			$date=date("Y").'-'.date("m");
			$req3=$bddlog->query('SELECT montant, type FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$datas2['id_compte'].' AND op_date>="'.$date.'-01" AND op_date<="'.$date.'-31"');
			//calcul du solde du compte du mois courant
			$solde=0;
			while ($datas3=$req3->fetch(PDO::FETCH_ASSOC)) {
				if ($datas3['type']=='c')
					$solde+=$datas3['montant'];
				else
					$solde-=$datas3['montant'];
				if (!is_int_or_float($solde)) $solde=0;
				$nb_op++;
			}
			//colorisation et détermination de l'état du compte
			if ($solde>0) {
				$etat='<span class="color_credit">Cr&eacute;diteur</span>';
				$montant='<span class="color_credit">'.$solde.' €</span>';
			}
			elseif ($solde==0) {
				$etat='<span class="color_credit">Nul</span>';
				$montant='<span class="color_credit">'.$solde.' €</span>';
			}
			else {
				$etat='<span class="color_debit">D&eacute;biteur</span>';
				$montant='<span class="color_debit">'.$solde.' €</span>';
			}

			echo '<tr '; if ($x==0) echo 'class="impair"'; echo '>';
				echo '<td>'.$datas2['nom'].'</td>
					<td>'.$nb_op.'</td>
					<td>'.$etat.'</td>
					<td>'.$montant.'</td>
					<td><div class="edit"><a href="?m=edit&type=account&account='.$datas2['id_compte'].'" /><img src="templates/images/empty.png" title="Editer" /></a></div></td>
					<td>
						<div class="del">
							<img src="templates/images/empty.png" class="del_link" onclick="hydrating_form_account('.$datas2['id_compte'].', \''.$datas2['nom'].'\', \''.$url.'\')" title="Supprimer" />
						</div>
					</td>
				</tr>';
		}
	echo '</table><br />';

	echo '<section id="wrapper_overLayer">
			<section id="overLayer">
				<section id="contentOverLayer">
					Etes vous sur de vouloir supprimer ce compte bancaire : <span id="account_name"></span>?<br /><br />
					<form action="?m=deleter" method="post" id="del_form">
						<input type="hidden" id="id" name="id" />
						<input type="hidden" id="url" name="url" />
						<input type="hidden" name="module" value="settings" />
						<input type="submit" id="btn_confirm" value="Confirmer" /><input type="reset" id="btn_cancel" value="Annuler" />
					</form>
				</section>
			</section>
		</section>';
	?>