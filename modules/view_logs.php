<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($view_logs) and $view_logs!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');
	$action_src=array('signin', 'signout');
	$action_print=array('Connexion', 'D&eacute;connexion');

	echo '<section class="panel col-40">
			<section class="panel-header">
				<span><i class="fa fa-plus"></i>Liste des connexions relatives à votre compte</span>
			</section>
			<section class="panel-body">
				<table cellspacing="0" cellpadding="0" id="tList">
					<tr id="tHeader">
						<td>Date et heure</td>
						<td>Action</td>
						<td>Adresse IP</td>
					</tr>';
	$x=0;
	$logs_file='uploads/'.$_SESSION['login'].'/logs/logs';
	if (file_exists($logs_file)) {
		$logs=fopen($logs_file, 'r');
		while (!feof($logs)) {
			$x=2-$x;
			$log=fgets($logs);
			list($datetime, $action, $ip)=explode(':', $log);
			if ($x==0)
				$impair=' class="impair"';
			else
				$impair=NULL;
			echo '<tr'.$impair.'>
					<td>'.date('d/m/Y H:i:s', $datetime).'</td>
					<td>'.str_replace($action_src, $action_print, $action).'</td>
					<td>'.$ip.'</td>
				</tr>';
		}
		fclose($logs);
	}
			echo '</table>
			</section>
		</section>';