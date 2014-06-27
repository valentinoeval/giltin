<?php
	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
	}
	catch (PDOException $message) {
		die("Erreur de connexion : ".$message->getMessage());
	}
	$req=$bddlog->query('SELECT version FROM giltin_settings');
	$datas=$req->fetch(PDO::FETCH_ASSOC);
	echo '
		<h3>A propos de Giltin\'</h3>
		<p>Giltin\' vous permet de g&eacute;rer tous vos comptes bancaires ind&eacute;pendement du e-relev&eacute; de votre banque.<br />
		Ass&eacute des d&eacute;lais de mise à jour des op&eacute;rations et de la non visibilit&eacute; de votre solde r&eacute;el à l\'instant T?<br />
		Nous vous permettons de g&eacute;rer vous même vos d&eacute;penses et recettes en cr&eacute;ant vos comptes bancaires dans l\'application.
		<h4>S&eacute;curit&eacute;</h4></p>
		v '.$datas['version'];