<?php
	//determination de l'environnement
	if (!file_exists('kvs')) {
		//BASE DE DONNEES KingVal Studio
		$bdd='mysql:host=mysql11.000webhost.com;dbname=a3909572_data';
		$bddmdp='eval1992';
		$bdduser='a3909572_kingval';
	}
	else {
		//BASE DE DONNEES localhost
		$bdd='mysql:host=localhost;dbname=giltin';
		$bdduser='root';
		$bddmdp='eval1992';
	}

	//CONSTANTES D'ACCES AUX MODULES
	$add_op=true;
	$admin=true;
	$calc_prec_sold=true;
	$deleter=true;
	$edit=true;
	$export=true;
	$gallery=true;
	$home=true;
	$pari=true;
	$search=true;
	$settings=true;
	$unverif_op=true;
	$user=true;
	$view_logs=true;
	$view_op=true;
	
	//AUTRES CONSTANTES
	$decalage_h=3600;
	$list_langs=array(
		'fr_FR'=>'Français',
		'en_US'=>'English'
	);
	$colors_categories=array('FFFF33', '7CF2C6', '7C98F2', '80F27C', 'E07CF2', '7CF2EA', 'F29F7C', '', '', '', '', '');

	//FONCTIONS
	//affichage messages informations
	function print_msg($code) {
		//formulaire générique
		if ($code=='empty') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Veuillez saisir les champs</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//formulaire connexion
		elseif ($code=='pass') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Veuillez saisir le mot de passe</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='pass2') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Le mot de passe est incorrect</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='blocked_account') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous ne pouvez pas vous connecter car votre compte est bloqu&eacute;<br />ou n\'est pas encore activ&eacute;</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='load_configs_fail') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Impossible de charger vos configurations</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='edit_configs_fail') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Impossible d\'&eacute;diter vos donn&eacute;es de connexion</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='account_blocked') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Le nombre de tentatives de connexions &eacute;chou&eacute;es est d&eacute;pass&eacute;e<br />vous pourrez vous reconnecter dans maximum 30 minutes</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//formulaire op&eacute;ration bancaire
		elseif ($code=='name_op') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Veuillez saisir un nom à l\'op&eacute;ration bancaire</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_amount') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas sp&eacute;cifi&eacute; de montant</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_nb') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Le montant saisie n\'est pas un nombre</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='add_op') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Op&eacute;ration bancaire enregistr&eacute;e</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_op') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas s&eacute;lectionn&eacute; le compte au pr&eacute;alable</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='empty_date') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas saisi de date</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_account') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas s&eacute;lectionn&eacute; de compte au pr&eacute;alable</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_account1') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas s&eacute;lectionn&eacute; le premier compte</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_account2') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas s&eacute;lectionn&eacute; le deuxième compte</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='not_different') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Les comptes s&eacute;lectionn&eacute;s ne sont pas diff&eacute;rents</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='del_op') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Suppression de l\'op&eacuteration bancaire effectu&eacute;e avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='del_op') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Veuillez choisir une cat&eacute;gorie</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//moteur de recherche
		elseif ($code=='no_criterion') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous n\'avez pas donnez de critère de recherche</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//calcul du solde précédent
		elseif ($code=='ad_sold_prec') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Le calcul du solde du mois pr&eacute;c&eacute;dent à &eacute;t&eacute; effectu&eacute;</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module dump BDD
		elseif ($code=='backup_dump') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Dump de la base de donn&eacute;es effectu&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='restore_dump') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Restauration de la base de donn&eacute;es effectu&eacute;e avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='del_dump') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Suppression du dump effectu&eacute;e avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module index
		elseif ($code=='module_not_exist') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Le module demand&eacute; est invalide ou n\'existe pas</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module validation inscription
		elseif ($code=='account_validate') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Votre compte à &eacute;t&eacute; activ&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='no_id_uid') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Il manque des informations de validation</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='validate_error') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Les informations de validation ne sont pas correctes</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='account_already_create') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous ne pouvez pas d&eacute;bloquer ce compte car il a d&eacute;jà &eacute;t&eacute; activ&eacute;</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='mkdir_error') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Erreur lors de la cr&eacute;ation de vos dossiers personnels</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module settings
		elseif ($code=='no_account_name') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Veuillez saisir  un nom à votre nouveu compte</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='invalid_account_name') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Le nom saisie n\'est pas correct</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='account_create') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Votre nouveau compte à &eacute;t&eacute; cr&eacute;&eacute; et valid&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='account_deleted') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Votre compte bancaire à &eacute;t&eacute; supprim&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='settings_updated') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Vos param&egrave;tres ont &eacute;t&eacute; modifi&eacute;s avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='edit_settings_error') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Erreur lors de la modification de vos param&egrave;tres</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module admin
		elseif ($code=='') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Votre nouveau compte à &eacute;t&eacute; cr&eacute;&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Votre nouveau compte à &eacute;t&eacute; cr&eacute;&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Votre nouveau compte à &eacute;t&eacute; cr&eacute;&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Votre nouveau compte à &eacute;t&eacute; cr&eacute;&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='admin_account_create') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Le compte utilisateur à &eacute;t&eacute; cr&eacute;&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='admin_dump_user') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Les utilisateurs ont &eacute;t&eacute; sauvegard&eacute;s avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='admin_version_updated') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>La version a &eacute;t&eacute; mise &agrave; jour avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module reset
		elseif ($code=='pass_sent') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Votre nouveau mot de passe vous a &eacute;t&eacute; envoy&eacute; à l\'adresse<br />email</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='email_invalid') {
			$message='<div class="msg_bad"><img src="templates/images/done.png" /><span>L\'adresse email n\'est pas valide</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module edit
		elseif ($code=='input_empty') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Veuillez saisir l\'int&eacute;gralit&eacute; des champs</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='empty_update_informations') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Il manque des informations pour modifier votre<br />op&eacute;ration bancaire</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='op_updated') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>L\'op&eacute;ration bancaire à &eacute;t&eacute; modifi&eacute;e avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='account_updated') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Votre compte à &eacute;t&eacute; modifi&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module user
		elseif ($code=='user_update_error') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Erreur lors de la mise à jour de vos informations</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='user_updated') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Vos informations ont &eacute;t&eacute; modifi&eacute;es avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		elseif ($code=='user_name_empty') {
			$message='<div class="msg_bad"><img src="templates/images/fail.png" /><span>Vous devez saisir un nom et pr&eacute;nom</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		//module galerie
		elseif ($code=='avatar_changed') {
			$message='<div class="msg_good"><img src="templates/images/done.png" /><span>Votre avatar a &eacute;t&eacute; chang&eacute; avec succès</span><img id="close_msg" src="templates/images/non_verifie.png" title="Masquer" /><div class="clear"></div></div>';
		}
		return $message;
	}

	//convertion date anglaise en date francaise
	function build_date_fr($date) {
		list($annee, $mois, $jour)=explode("-", $date);
		$newDate=$jour."/".$mois."/".$annee;
		return $newDate;
	}

	//convertion date francaise en date anglaise
	function build_date_en($date) {
		list($jour, $mois, $annee)=explode("/", $date);
		$newDate=$annee."-".$mois."-".$jour;
		return $newDate;
	}

	//convertion numéro de mois en nom du mois
	function nb2month($nb_month) {
		switch ($nb_month) {
			case 01:return "Janvier";
				break;
			case 02:return "F&eacute;vrier";
				break;
			case 03:return "Mars";
				break;
			case 04:return "Avril";
				break;
			case 05:return "Mai";
				break;
			case 06:return "Juin";
				break;
			case 07:return "Juillet";
				break;
			case 8:return "Août";
				break;
			case 9:return "Septembre";
				break;
			case 10:return "Octobre";
				break;
			case 11:return "Novembre";
				break;
			case 12:return "D&eacute;cembre";
				break;
		}
	}

	//
	function charSpec2code($texte, $convert) {
		$chars=array("é","è","ë","à","ç","ö","ô");
		$codes=array("&eacute;","&egrave;","&euml;","&agrave;","&ccedil;","&ouml;","&ocirc;");
		
		if ($convert=="convert")
			$texte=str_replace($chars, $codes, $texte);
		elseif ($convert=="deconvert")
			$texte=str_replace($codes, $chars, $texte);
		return $texte;
	}

	//converti le nombre de bytes en unités de tailles de fichier compréhensible par l'utilisateur
	function human_file_size($bytes, $decimals=2) {
	  	$unit='BKMGTP';
	  	$factor=floor((strlen($bytes)-1)/3);
	  	return sprintf("%.{$decimals}f", $bytes/pow(1024, $factor)).@$unit[$factor];
	}

	//génère un mot de passe aléatoire
	function randomMDP() {
		$nbrCar=10;
		$chaine='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$nb_lettres=strlen($chaine)-1;
		$mdp=null;
		for($i=0; $i<$nbrCar; $i++) {
			$pos=mt_rand(0, $nb_lettres);
			$car=$chaine[$pos];
			$mdp.=$car;
		}
		return $mdp;
	}

	//vérification de la validité de l'adresse email
	function emailValidation($adresse) { 
		//$syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
		$syntaxe='^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$';
		if (preg_match($syntaxe,$adresse)) 
			return true; 
		else 
			return false; 
	}

	//suppresions des tags html et php
	function strip_codes($texte) {
		$clean_texte=strip_tags($texte);
		return $clean_texte;
	}

	//vérification que le solde est bien du type X{1,}, X{1,}.X ou X{1,}.XX
	function is_int_or_float($number) {
		if (preg_match('#^(-)?[0-9]+(.[0-9]{1,2})?$#', $number))
			return true;
		else
			return false;
	}

	//croper une image automatiquement et centré
	function cropImage($file_src, $file_dest, $width, $height) {
		/*
			$file_src : Le chemin de l'image source, l'image qui va être recadrée
			$file_dest : Le chemin de la nouvelle image, qui va être créée. Si vous voulez écraser la première image, mettez le même chemin dans $file_src et $file_dest.
			$x : L'abscisse du coin haut gauche du cadre
			$y : L'ordonnée du coin haut gauche du cadre
			$width : La largeur du cadre
			$height : La hauteur du cadre
		*/

		//destination
		$dest=imagecreatetruecolor($width,$height);
		imagealphablending($dest,false);
		imagesavealpha($dest,true);
		
		//source
		$file_src_infos=pathinfo($file_src);
		$ext=strtolower($file_src_infos['extension']);
		if ($ext=="jpg")
			$ext="jpeg";
		list($largeur, $hauteur)=getimagesize($file_src);
		if ($largeur>$hauteur) {
			$x=($largeur-$hauteur)/2;
			$y=0;
		}
		elseif ($largeur<$hauteur) {
			$x=0;
			$y=($hauteur-$largeur)/2;
		}
		else {
			$x=0;
			$y=0;
		}
		
		// Création de l'image de destination.
		$func="imagecreatefrom".$ext;
		$src=$func($file_src);
		
		imagecopy($dest, $src, 0, 0, $x, $y, $width, $height);
		
		$func="image".$ext;
		$func($dest, $file_dest);
		imagedestroy($dest);
	}

	//redimentionnement de l'avatar
	function resizeImage($file_src, $file_dest, $new_width, $new_height, $proportional=true) {
		/* 
	    	$file_src : Le chemin de l'image source (), l'image qui va être redimensionnée
	    	$file_dest : Le chemin de la nouvelle image, qui va être créée. Si vous voulez écraser la première image, mettez le même chemin dans $file_src et $file_dest.
	    	$new_width : La nouvelle largeur en pixel
	    	$new_height : La nouvelle hauteur en pixel
	    	$proportional : Argument boolean optionnel, si égale à true alors les dimensions de l'image de destination seront proportionnelles à ceux de l'image source, et donc pas forcement $new_width x $new_height,
	    												sinon les dimensions seront exactement $new_width x  $new_height
	    */

		$attr=getimagesize($file_src);
		$fw=$attr[0]/$new_width;
		$fh=$attr[1]/$new_height;
		
		if ($proportional)
			$f=$fw>$fh?$fw:$fh;
		else
			$f=$fw>$fh?$fh:$fw;

		$w=$attr[0]/$f;
		$h=$attr[1]/$f;
        
		$file_src_infos=pathinfo($file_src);
		
		$ext=strtolower($file_src_infos['extension']);
		if ($ext=="jpg")
			$ext="jpeg";
		
		$func="imagecreatefrom".$ext;
		$src=$func($file_src);
		
		// Création de l'image de destination. La taille de la miniature sera wxh 
		$x=0;
		$y=0;
		if ($proportional)
			$dest=imagecreatetruecolor($w,$h);
		else {
			$dest=imagecreatetruecolor($new_width,$new_height);
			$x=($new_width-$w)/2;
			$y=($new_height-$h)/2;
		}

		// Configuration du canal alpha pour la transparence
		imagealphablending($dest,false);
 		imagesavealpha($dest,true);

		// Redimensionnement de src sur dest 
		imagecopyresampled($dest,$src,$x,$y,0,0,$w,$h,$attr[0],$attr[1]);

		$func="image".$ext;
		$func($dest, $file_dest);
		imagedestroy($dest);
		
		return true;		
	}

	//traduction du code de langue en nom
	function lang($code) {
		switch ($code) {
			case 'fr':
				return 'Français';
				break;
			case 'en':
				return 'English';
				break;
		}
	}

	//retourne le nom du statut de l'utilisateur
	function rights($code) {
		switch ($code) {
			case '-2':
				return 'Super Administrateur';
				break;
			case '-1':
				return 'Administrateur';
				break;
			case '0':
				return 'En attente de validation';
				break;
			case '1':
				return 'Compte bloqu&eacute;';
				break;
			case '2':
				return 'Utilisateur';
				break;
		}
	}

	function dateInt2String($int) {
		$mounthInt=array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$mounthStr=array('Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');
		
		return str_replace($mounthInt, $mounthStr, $int);
	}