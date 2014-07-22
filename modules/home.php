<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($home) and $home!=true)
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

	//graphique affichant le pourcentage des recettes par rapport aux dépenses
	echo '<section class="tablet panel graphic">
			<section class="panel-header"><span><i class="fa fa-history"></i>Graphique d\'activité depuis le début de l\'année</span></section>
			<section class="panel-body">
				<ul class="graphic-legend">
					<li><i class="graphic-legend-income">&nbsp;</i>&nbsp;Recettes</li>
					<li><i class="graphic-legend-spending">&nbsp;</i>&nbsp;Dépenses</li>
				</ul>
				<section class="graphic-container">';
				for ($i=1; $i<=date('m'); $i++) {
					$income=0;
					$spending=0;
					$datas=array();
					if ($i<10)
						$num_mounth='0'.$i;
					else
						$num_mounth=$i;

					$mounths=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte=1 AND op_date LIKE "2014-'.$num_mounth.'%" ORDER BY op_date ASC');
					while ($op=$mounths->fetch(PDO::FETCH_ASSOC)) {
						if ($op['type']=='c') {
							$income+=$op['montant'];
						}
						else {
							$spending+=$op['montant'];
						}
					}
					echo '<section class="graphic-bar-container graphic-m'.$i.'"  title="'.dateInt2String($num_mounth).'">';
					$total=$income+$spending;
					if ($total>0) {
						$percentIncome=(1-($income/$total))*100;
						$percentSpending=(1-($spending/$total))*100;
					}
					else {
						$percentIncome=100;
						$percentSpending=100;
					}
						echo '<section class="graphic-bar-center">';
							echo '<section class="graphic-bar-income"><section class="graphic-bar-empty" style="height:'.$percentIncome.'%;"></section></section>';
							echo '<section class="graphic-bar-spending"><section class="graphic-bar-empty" style="height:'.$percentSpending.'%;"></section></section>';
						echo '</section>';
					echo '</section>';
				}
			echo '</section>
			</section>
		</section>';

	//5 dernières oérations de tous les comptes
	$req=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id']);
	while ($accounts=$req->fetch(PDO::FETCH_ASSOC)) {
		echo '<section class="tablet panel">
				<section class="panel-header">
					<a href="?m=view_op&account='.$accounts['id_compte'].'"" title="Visionnez votre compte \''.$accounts['nom'].'\'">
						<span><i class="fa fa-money"></i>'.$accounts['nom'].' - 5 dernières opérations</span>
					</a>
					</section>
				<section class="panel-body">
					<table cellspacing="0" cellpadding="0" class="tList">
						<tbody>';
						$getOperations=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$accounts['id_compte'].'  ORDER BY op_date DESC LIMIT 5');
						while ($operations=$getOperations->fetch(PDO::FETCH_ASSOC)) {
							echo '<tr><td>'.$operations['nom'].'</td><td>';
							if ($operations['type']=='c')
								echo '<span class="color_credit">'; 
							elseif ($operations['type']=='d')
								echo '<span class="color_debit">';
							echo $operations['montant'].' €</span></td></tr>';
						}
					echo '</tbody>
					</table>
				</section>
			</section>';
	}

	//nombre d'opération du mois courant
	
	$req=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id']);
	while ($accounts=$req->fetch(PDO::FETCH_ASSOC)) {
		echo '<section class="tablet panel">
				<section class="panel-header">
					<span><i class="fa fa-money"></i>'.$accounts['nom'].' - Nombre d\'opérations</span>
					</section>
				<section class="panel-body">
					<section class="panel-body-content nb-op">';
						$reqAccount=$bddlog->query('SELECT count(*) AS nb_op FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte='.$accounts['id_compte'].' AND op_date LIKE "'.date('Y-m').'%"');
						$datas=$reqAccount->fetch(PDO::FETCH_ASSOC);
						echo '<h3>'.$datas['nb_op'].' opération';
						if ($datas['nb_op']>0)
							echo 's';
						echo '</h3>';
				echo '</section>
				</section>
			</section>';
	}

	//répartition des dépenses du mois courant
	echo '<section class="tablet panel">
			<section class="panel-header">
				<span><i class="fa fa-history"></i>Organisation des opérations courantes
			</section>
			<section class="panel-body">
				<section class="panel-body-content">';
					$categories=array();
					$nb_op_total=0;
					$reqCat=$bddlog->query('SELECT * FROM giltin_categories');
					while ($categorys=$reqCat->fetch(PDO::FETCH_ASSOC)) {
						$categories[$categorys['id_category']]=array(
							'id'	=> $categorys['id_category'],
							'nom'	=> $categorys['nom'],
							'nb_op'	=> 0
						);
					}
					$req=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE op_date LIKE "'.date('Y-m').'%" AND id_compte=1');
					while ($op=$req->fetch(PDO::FETCH_ASSOC)) {
						$categories[$op['categorie']]['nb_op']++;
						$nb_op_total++;
					}
					//diagramme
					echo '<section class="diagram">';
					foreach ($categories as $categorie) {
						$percent=$categorie['nb_op']/$nb_op_total*100;
						echo '<section class="diagram-section cat'.$categorie['id'].'" style="width:'.$percent.'%;"></section>';
					}
					echo '</section>';
					//légende
					echo '<ul class="list">';
					foreach ($categories as $categorie) {
						echo '<li><i class="cat'.$categorie['id'].'">&nbsp;</i>'.$categorie['nom'].'</li>';
					}
					echo '</ul>
				</section>
			</section>
		</section>';