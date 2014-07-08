<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($edit) and $edit!=true)
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
		die("Erreur de connexion : ".$message->getMessage());
	}

	$type=$_GET['type'];
	$account=$_GET['account'];
	$id=$_GET['id'];
	if (!empty($_GET['action']))
		$action=$_GET['action'];

	switch ($type) {
		case 'op':
			if (isset($action) and $action=='update') {
				if (isset($_POST['nom']) and !empty($_POST['nom'])) {
					if (isset($_POST['montant']) and !empty($_POST['montant'])) {
						//remplacement de la virgule par un point pour l'enregistrement dans la base
						$_POST['montant']=str_replace(',', '.', $_POST['montant']);
						if (is_numeric($_POST['montant'])) {
							//convertion des caractères spéciaux en code html
							$nom=charSpec2code(strip_codes($_POST['nom']), 'convert');
							// s'il s'agit d'un calcul de solde précédent on identique que celui ci à été modifié en concaténant à la fin le suffixe (Corrigé)
							if ($nom=="Solde pr&eacute;c&eacute;dent")
								$nom.=" (Corrig&eacute;)";
							//détermination du type d'opération
							if (isset($_POST['slide']) and $_POST['slide']=='unChecked')
								$type='c';
							else
								$type='d';
							//remplacement de la virgule par un point pour l'enregistrement dans la base
							$_POST['montant']=str_replace(',', '.', $_POST['montant']);
							$bddlog->exec('UPDATE giltin_comptes_'.$_SESSION['id'].' SET id_compte="'.$_POST['account'].'", nom="'.$nom.'", categorie="'.$_POST['categorie'].'", op_date="'.$_POST['op_date'].'", type="'.$type.'", montant='.$_POST['montant'].' WHERE id_compte='.$_POST['id_compte'].' AND id='.$_POST['id']);
							header('location:?m=view_op&account='.$_POST['id_compte'].'&msg=op_updated');
						}
						else
							header('location:?m=edit&type=op&account='.$_POST['id_compte'].'&id='.$_POST['id'].'&msg=not_nb');
					}
					else
						header('location:?m=edit&type=op&account='.$_POST['id_compte'].'&id='.$_POST['id'].'&msg=not_amount');
				}
				else
					header('location:?m=edit&type=op&account='.$_POST['id_compte'].'&id='.$_POST['id'].'&msg=name_op');
			}
			elseif (!isset($action) or $action==null) {
				$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$account.' AND id='.$id);
				$req2=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' GROUP BY id_compte ASC');
				$req3=$bddlog->query('SELECT * FROM giltin_categories');

				$datas=$req->fetch(PDO::FETCH_ASSOC);

				if ($datas['type']=="c")
					$checked="checked";
				else
					$checked='';
				//bloquage du champ nom s'il s'agit du calcul automatique du solde précédent
				/*if ($datas['nom']=='Solde pr&eacute;c&eacute;dent' or $datas['nom']=='Solde pr&eacute;c&eacute;dent (Corrig&eacute;)')
					$disabled='disabled';
				else
					$disabled='';*/
				echo '
					<h3>Modification de l\'op&eacute;ration bancaire</h3>
					<form action="?m=edit&type=op&action=update" method="post">
						<input type="text" '.$disabled.' name="nom" placeholder="Nom de l\'op&eacute;ration bancaire" value="'.$datas['nom'].'" /><br />
						<select name="account">';
							$i=1;
							while ($datas2=$req2->fetch(PDO::FETCH_ASSOC)) {
								echo '<option value="'.$datas2['id_compte'].'"';
								if ($i==$datas['id_compte'])
									echo ' selected';
								echo '>'.$datas2['nom'].'</option>';
								$i++;
							}
						echo '</select><br />
						<select name="categorie">';
							$i=1;
							while ($datas3=$req3->fetch(PDO::FETCH_ASSOC)) {
								echo '<option value="'.$datas3['id_category'].'"';
								if ($i==$datas['categorie'])
									echo ' selected';
								echo '>'.$datas3['nom'].'</option>';
								$i++;
							}
						echo '</select><br />
						<input type="date" '.$disabled.' name="op_date" placeholder="Nom de l\'op&eacute;ration bancaire" value="'.$datas['op_date'].'" /><br />
						<input type="text" name="montant" placeholder="Montant" class="montant" value="'.$datas['montant'].'" /><br />
						<div class="slideBox">	
							<input type="checkbox" value="unChecked" '.$checked.' id="slideBox" name="slide" />
							<label for="slideBox"></label>
						</div>
						<input type="hidden" name="id_compte" value="'.$account.'" />
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="submit" value="Modifier" />
					</form>';
			}
			break;
		case 'account':
			if ($action=='update') {
				if (isset($_POST['nom']) and !empty($_POST['nom'])) {
					//convertion des caractères spéciaux en code html
					$nom=charSpec2code(strip_codes($_POST['nom']), 'convert');
					$bddlog->exec('UPDATE giltin_list_comptes SET nom="'.$nom.'" WHERE id_user="'.$_SESSION['id'].'" AND id_compte="'.$_POST['id_compte'].'"');
					header('location:?m=settings&msg=account_updated');
				}
				else
					header('location:');
			}
			elseif (!isset($action) or $action==null) {
				$req=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' AND id_compte='.$account);
				$datas=$req->fetch(PDO::FETCH_ASSOC);
				echo '
					<h3>Modification du compte</h3>
					<form action="?m=edit&type=account&action=update" method="post">
						<input type="text" name="nom" placeholder="Nom du compte" value="'.$datas['nom'].'" /><br />
						<input type="hidden" name="id_compte" value="'.$account.'" />
						<input type="submit" value="Modifier" />
					</form>';
			}
			break;
		default:
			header('location:index.php');
			break;
	}