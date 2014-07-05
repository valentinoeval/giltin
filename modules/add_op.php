<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($add_op) and $add_op!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');
	
	//appel du module de calcul du solde du mois précédent si changement de mois
	include('modules/calc_prec_sold.php');

	if (isset($_POST['active']) and $_POST['active']=true) {
		if (isset($_POST['label']) and !empty($_POST['label'])) {
			if (isset($_POST['montant']) and !empty($_POST['montant'])) {
				//remplacement de la virgule par un point pour l'enregistrement dans la base
				$_POST['montant']=str_replace(',', '.', $_POST['montant']);
				if (is_numeric($_POST['montant'])) {
					if ($_POST['date']!='0000-00-00') {
						try {
							$bddlog=new PDO($bdd, $bdduser, $bddmdp);
						}
						catch (PDOException $message) {
							die("Erreur de connexion : ".$message->getMessage());
						}
												
						//si la case virement est coché ont déplace de l'argent d'un compte à l'autre
						if (isset($_POST['virement']) and $_POST['virement']==1) {
							if (isset($_POST['virement_account_1']) and !empty($_POST['virement_account_1']) and $_POST['virement_account_1']!=0) {
								if (isset($_POST['virement_account_2']) and !empty($_POST['virement_account_2']) and $_POST['virement_account_2']!=0) {
									if ($_POST['virement_account_1']!=$_POST['virement_account_2']) {
										//détermination du type d'opération
										if (isset($_POST['slide']) and $_POST['slide']=='unChecked') {
											$type='c';
											$type2='d';
										}
										else {
											$type='d';
											$type2='c';
										}
										//ajout opération dans le compte 1 giltin_comptes_'.$_SESSION['id'].'
										$req='INSERT INTO giltin_comptes_'.$_SESSION['id'].' VALUES("", "'.$_POST['virement_account_1'].'", "'.charSpec2code(strip_codes("Virement ".$_POST['label']), "convert").'", "'.$_POST['categorie'].'", "'.$_POST['date'].'", "'.$type.'", "'.$_POST['montant'].'", "0")';
										$bddlog->exec($req);
										//ajout opération dans le compte 2
										$req2='INSERT INTO giltin_comptes_'.$_SESSION['id'].' VALUES("", "'.$_POST['virement_account_2'].'", "'.charSpec2code(strip_codes("Virement ".$_POST['label']), "convert").'", "'.$_POST['categorie'].'", "'.$_POST['date'].'", "'.$type2.'", "'.$_POST['montant'].'", "0")';
										$bddlog->exec($req2);
										header('location:?m=add_op&msg=add_op');
									}
									else
										header('location:?m=add_op&msg=not_different');
								}
								else
									header('location:?m=add_op&msg=not_account2');
							}
							else
								header('location:?m=add_op&msg=not_account1');
						}
						//sinon on ajoute simplement dans le compte sélectionné l'opération bancaire
						else {
							if (isset($_POST['account']) and !empty($_POST['account'])) {
								if (isset($_POST['categorie']) and !empty($_POST['categorie'])) {
									if (isset($_POST['slide']) and $_POST['slide']=='unChecked') 
										$type='c';
									else
										$type='d';
									$req='INSERT INTO giltin_comptes_'.$_SESSION['id'].' VALUES("", "'.$_POST['account'].'", "'.charSpec2code(strip_codes($_POST['label']), "convert").'", "'.$_POST['categorie'].'", "'.$_POST['date'].'", "'.$type.'", "'.$_POST['montant'].'", "0")';
									$bddlog->exec($req);
									header('location:?m=add_op&msg=add_op');
								}
								else
									header('location:?m=add_op&msg=add_category');
							}
							else
								header('location:?m=add_op&msg=not_account');
						}
					}
					else
						header('location:index.php?m=add_op&msg=empty_date');
				}
				else
					header('location:?m=add_op&msg=not_nb');
			}
			else
				header('location:?m=add_op&msg=not_amount');
		}
		else
			header('location:?m=add_op&msg=name_op');
	}
	$req2=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' GROUP BY id_compte ASC');
	$req3=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' GROUP BY id_compte ASC');
	$req4=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' GROUP BY id_compte ASC');
	$req5=$bddlog->query('SELECT * FROM giltin_categories');
?>
<h3>Ajouter une op&eacute;ration bancaire</h3>
<form action="?m=add_op" method="post">
	<input type="text" name="label" placeholder="Nom de l'op&eacute;ration bancaire" class="saisie" /><br />
	<div class="squared">
		Virement :&nbsp;&nbsp;
		<input type="checkbox" id="squared" name="virement" value="1" />
		<label for="squared"></label>
	</div><br />
	<div id="virement">
		<select name="virement_account_1" class="select_virement">
			<option value="0">-D&eacute;biteur-</option>
		<?php
			while ($datas2=$req2->fetch(PDO::FETCH_ASSOC))
				echo '<option value="'.$datas2['id_compte'].'">'.$datas2['nom'].'</option>';
		?>
		</select> ->
		<select name="virement_account_2" class="select_virement">
			<option value="0">-Cr&eacute;diteur-</option>
			<?php
				while ($datas3=$req3->fetch(PDO::FETCH_ASSOC))
					echo '<option value="'.$datas3['id_compte'].'">'.$datas3['nom'].'</option>';
			?>
		</select>
	</div>
	<select name="account" id="account">
		<?php
			while ($datas4=$req4->fetch(PDO::FETCH_ASSOC))
				echo '<option value="'.$datas4['id_compte'].'">'.$datas4['nom'].'</option>';
		?>
	</select><br />
	<select name="categorie">
		<?php
			while ($datas5=$req5->fetch(PDO::FETCH_ASSOC))
				echo '<option value="'.$datas5['id_category'].'">'.$datas5['nom'].'</option>';
		?>
	</select><br />
	<input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" /><br />
	<input type="text" name="montant" placeholder="Montant" class="montant" /><br />
	<div class="slideBox">	
		<input type="checkbox" value="unChecked" id="slideBox" name="slide" />
		<label for="slideBox"></label>
	</div>
	<input type="hidden" name="active" />
	<input type="submit" value="Ajouter" class="ajouter" />
</form>