<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($calc_prec_sold) and $calc_prec_sold!=true)
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

	//mois courant
	$m=date("m");
	//année courante
	$Y=date('Y');
	//si le moi courant est supérieur à janvier on crée une date du mois précédent de l'année courante
	if (date("m")>1) {
		$m--;
		$date=$Y.'-'.$m;
	}
	//sinon si le mois courant est janvier on crée la datedu mois dernier et de l'année dernière
	elseif (date("m")==1) {
		$Y--;
		$date=$Y.'-12';
	}
	//on crée la date courante
	$date2=date('Y').'-'.date('m');
	$nb_calc=0;

	//requète pour récupérer tous les comptes de l'utilisateur
	$req_accounts=$bddlog->query('SELECT id_compte FROM giltin_list_comptes WHERE id_user='.$_SESSION['id'].' ORDER BY id_compte ASC');

	//enrigistrement du solde du mois précédent comme première opération du mois en cours
	while ($datas_account=$req_accounts->fetch(PDO::FETCH_ASSOC)) {
		//comptage du nombre d'opération du compte en cours pour savoir si on le traite ou pas
		$req=$bddlog->query('SELECT count(*) AS nb_operation FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$datas_account['id_compte']);
		$datas=$req->fetch(PDO::FETCH_ASSOC);
		
		if (isset($datas['nb_operation']) and $datas['nb_operation']>0) {
			//requete pour rechercher la dernière opération du compte en cours de traitement
			$req1=$bddlog->query('SELECT op_date FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$datas_account['id_compte'].' AND op_date=(SELECT MAX(op_date) FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$datas_account['id_compte'].')');
			$datas1=$req1->fetch(PDO::FETCH_ASSOC);
			
			if ($datas1['op_date']<$date2.'-01') {
				$req2=$bddlog->query('SELECT montant, type FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$datas_account['id_compte'].' AND op_date>="'.$date.'-01" AND op_date<="'.$date.'-31"');
				$solde=0;
				while ($datas2=$req2->fetch(PDO::FETCH_ASSOC)) {
					if ($datas2['type']=='c')
						$solde+=$datas2['montant'];
					else
						$solde-=$datas2['montant'];
				}
				if ($solde>=0)
					$type='c';
				else {
					$type='d';
					$solde=$solde*(-1);
				}
				$bddlog->exec('INSERT INTO giltin_comptes_'.$_SESSION['id'].' VALUES("", '.$datas_account['id_compte'].', "Solde pr&eacute;c&eacute;dent", 12, "'.$date2.'-01", "'.$type.'", '.$solde.', 1)');
				$nb_calc++;
			}
		}
	}

	//redirection vers la page index avec affichage d'un message prévenant du calcul du solde de fin du mois précédent
	if ($nb_calc>0) header('location:?m=add_op&msg=ad_sold_prec');