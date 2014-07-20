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

	echo '<section class="tablet panel graphic">
			<section class="panel-header"><span><i class="fa fa-history"></i>&nbsp;&nbsp;Graphique d\'activité depuis le début de l\'année</span></section>
			<section class="panel-body">
				<ul class="graphic-legend">
					<li><i class="graphic-legend-income">&nbsp;</i>&nbsp;Recettes</li>
					<li><i class="graphic-legend-spending">&nbsp;</i>&nbsp;Dépenses</li>
				</ul>
				<section class="graphic-container">';
				$mounths=$bddlog->query('SELECT * FROM giltin_comptes_'.$_SESSION['id'].' WHERE id_compte=1 AND op_date LIKE "2014%" ORDER BY op_date ASC');
				$nb_mounth=1;
				$income=0;
				$spending=0;
				$datas=array();
				while ($op=$mounths->fetch(PDO::FETCH_ASSOC)) {
					list($year, $mounth, $day)=explode('-', $op['op_date']);
					$mounth=(int)$mounth;
					if ($nb_mounth<$mounth) {
						$datas[$nb_mounth]=array(
							'income'	=> $income,
							'spending'	=> $spending
						);
						$nb_mounth=$mounth;
						$income=0;
						$spending=0;
					}
					if ($op['type']=='c') {
						$income+=$op['montant'];
					}
					else {
						$spending+=$op['montant'];
					}
				}
				for ($i=1; $i<=12; $i++) {
					echo '<section class="graphic-bar-container graphic-m'.$i.'">';
					if (isset($datas[$i])) {
						$total=$datas[$i]['income']+$datas[$i]['spending'];
						$percentIncome=(1-($datas[$i]['income']/$total))*100;
						$percentSpending=(1-($datas[$i]['spending']/$total))*100;
						echo '<section class="graphic-bar-center">';
							echo '<section class="graphic-bar-income"><section class="graphic-bar-empty" style="height:'.$percentIncome.'%;"></section></section>';
							echo '<section class="graphic-bar-spending"><section class="graphic-bar-empty" style="height:'.$percentSpending.'%;"></section></section>';
						echo '</section>';
					}
					echo '</section>';
				}
			echo '</section>
			</section>
		</section>';

	$req=$bddlog->query('SELECT * FROM giltin_list_comptes WHERE id_user='.$_SESSION['id']);
	while ($accounts=$req->fetch(PDO::FETCH_ASSOC)) {
		echo '<section class="tablet panel">
				<section class="panel-header"><a href="?m=view_op&account='.$accounts['id_compte'].'"" title="Visionnez votre compte \''.$accounts['nom'].'\'"><span><i class="fa fa-money"></i>&nbsp;&nbsp;'.$accounts['nom'].'</span></a></section>
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
	