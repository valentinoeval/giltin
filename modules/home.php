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

	echo '<section class="tablet panel graphic">
			<section class="panel-header"><span><i class="fa fa-history"></i>&nbsp;&nbsp;Graphique d\'activité depuis le début de l\'année</span></section>
			<section class="panel-body">
				<section class="graphic-container">
				[ DIAGRAMME ]<br /><br />[ DIAGRAMME ]<br /><br />[ DIAGRAMME ]<br /><br />[ DIAGRAMME ]<br /><br />[ DIAGRAMME ]<br /><br />[ DIAGRAMME ]<br /><br />[ DIAGRAMME ]
				</section>
			</section>
		</section>';

	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
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
	}
	catch (PDOException $message) {
		die("Impossible d'accéder à la base de données");
	}