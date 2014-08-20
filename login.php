<?php
	session_start();
	include('configs.php');

	try {
		$bddlog=new PDO($bdd, $bdduser, $bddmdp);
	}
	catch (PDOException $message) {
		die("Erreur de connexion");
	}

	$ip=$_SERVER['REMOTE_ADDR'];
	//on regarde dans la base si l'ip de l'utilisateur est enregistrée
	$req3=$bddlog->query('SELECT count(*) AS ip_added FROM giltin_attemps_logs WHERE ip="'.$ip.'"');
	$datas3=$req3->fetch(PDO::FETCH_ASSOC);
	//si elle n'a pas été trouvé ou l'ajoute dans la base
	if ($datas3['ip_added']==0) {
		$bddlog->exec('INSERT INTO giltin_attemps_logs VALUES("'.$ip.'", 0)');
	}
	//on récupère le nombre de tentatives de connexions échouées pour l'ip de l'utilisateur
	$req4=$bddlog->query('SELECT nb_attemps FROM giltin_attemps_logs WHERE ip="'.$ip.'"');
	$datas4=$req4->fetch(PDO::FETCH_ASSOC);
	//si le nombre de connections échouées est inférieur à 5 ou si le timestamp enregistré est inférieur à celui actuel
	if ($datas4['nb_attemps']<5 or $datas4['nb_attemps']<time()) {
		//remise à 0 du nombre de tentatives échouées
		if ($datas4['nb_attemps']>5 or $datas4['nb_attemps']<time())
			$bddlog->exec('UPDATE giltin_attemps_logs SET nb_attemps=0');
		if (isset($_POST['login']) and !empty($_POST['login'])) {
			if (isset($_POST['password']) and !empty($_POST['password'])) {
				$req=$bddlog->query('SELECT * FROM giltin_users WHERE login="'.$_POST['login'].'"');
				$datas=$req->fetch(PDO::FETCH_ASSOC);
				if (sha1($_POST['password'])==$datas['password']) {
					if (($datas['rights']>=-2 and $datas['rights']<0) or ($datas['rights']>1 and $datas['rights']<=2)) {
						$nbrCar=100;
						$chaine='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#&*+-/_^£$';
						$nb_lettres=strlen($chaine)-1;
						$key='';
						for($i=0; $i<$nbrCar; $i++) {
							$pos=mt_rand(0, $nb_lettres);
							$car=$chaine[$pos];
							$key.=$car;
						}
						if ($file=fopen('uploads/'.$_POST['login'].'/key', 'w+')) {
							if (fputs($file, $key)) {
								fclose($file);
								//récupération de la date de la dernière connexion
								$req2=$bddlog->query('SELECT date_time, identity FROM giltin_logs WHERE id_user='.$datas['id']);
								$datas2=$req2->fetch(PDO::FETCH_ASSOC);
								//timestamp plus 6h pour avoir le timestamp local
								$time=time()+3600*6;
								$bddlog->exec('UPDATE giltin_logs SET id_log=1, date_time='.$time.', identity="'.$ip.'" WHERE id_user='.$datas['id']);
								//remise à 0 du nombre de tentatives échouées
								$bddlog->exec('UPDATE giltin_attemps_logs SET nb_attemps=0 WHERE ip="'.$ip.'"');
								$_SESSION['id']=$datas['id'];
								$_SESSION['login']=$_POST['login'];
								$_SESSION['key']=$key;
								$_SESSION['rights']=$datas['rights'];
								$_SESSION['nom']=$datas['nom'];
								if ($datas['avatar']!='') {
									$_SESSION['avatar']='uploads/'.$_POST['login'].'/avatar/'.$datas['avatar'];
									list($nom, $ext)=explode('.', $datas['avatar']);
									$_SESSION['avatar_mini']='uploads/'.$_POST['login'].'/avatar/'.$nom.'_mini.'.$ext;
								}
								else
									$_SESSION['avatar']='templates/images/default_avatar.png';
								$_SESSION['email']=$datas['email'];
								$_SESSION['lang']=$datas['lang'];
								//écriture du log
								$logs_file='uploads/'.$_SESSION['login'].'/logs/logs';
								$log=fopen($logs_file, 'a');
								$contenu=file_get_contents($logs_file);
								if ($contenu!=null) {
									$log2=fopen($logs_file, 'w+');
									fputs($log2, $time.':signin:'.$ip."\n");
									fputs($log, $contenu);
								}
								else
									fputs($log, $time.':signin:'.$ip);
								fclose($log);
								fclose($log2);
								if (isset($_COOKIE['last_page']) and !empty($_COOKIE['last_page'])) {
									if (preg_match('logout_done', $_COOKIE['last_page'])===1)
										header('location:./');
									else
										header('location:'.$_COOKIE['last_page']);
								}
								else
									header('location:./');
							}
							else
								header('location:index.php?msg=edit_configs_fail');
						}
						else
							header('location:index.php?msg=load_configs_fail');
					}
					else
						header('location:index.php?msg=blocked_account');
				}
				else {
					//si l'autentification à échouée on incrémente le nombre de tentatives échouées
					$nb_attemps=$datas4['nb_attemps']+1;
					if ($nb_attemps<5) {
						$bddlog->exec('UPDATE giltin_attemps_logs SET nb_attemps='.$nb_attemps.' WHERE ip="'.$ip.'"');
						header('location:index.php?msg=pass2');
					}
					elseif ($nb_attemps=5) {
						$nb_attemps=time()+1800;
						//si le nombre de tentatives est égale à 5 on enregistre le timestamp actuel plus 30 minutes avant d'avoir le droit de se reconnecter
						$bddlog->exec('UPDATE giltin_attemps_logs SET nb_attemps='.$nb_attemps.' WHERE ip="'.$ip.'"');
						header('location:index.php?msg=account_blocked');
					}
				}
			}
			else
				header('location:index.php?msg=pass');
		}
		else
			header('location:index.php?msg=empty');
	}
	else {
		header('location:index.php?msg=account_blocked');
	}