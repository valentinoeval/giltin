$(document).ready(function() {
	//pliage/dépliage notice du moteur de recherche
	$('#button').toggle(function() {
		$('#list').show('slow');
		$('#button_plus').css({
			"-webkit-transform" : "rotate(-180deg)",
			"-moz-transform" : "rotate(-180deg)",
			"-o-transform" : "rotate(-180deg)",
			"transform" : "rotate(-180deg)"
		});
		$('#notice').fadeTo('slow', 1);
	}, function() {
		$('#list').hide('slow');
		$('#button_plus').css({
			"-webkit-transform" : "rotate(0deg)",
			"-moz-transform" : "rotate(0deg)",
			"-o-transform" : "rotate(0deg)",
			"transform" : "rotate(0deg)"
		});
		$('#notice').fadeTo('slow', 0.3);
	});

	//fermeture du bloc du message
	$('#close_msg').click(function() {
		$('.msg_good').hide();
		setTimeout(function() {$('.msg_good').remove();}, 500);
		$('.msg_bad').hide('slow');
		setTimeout(function() {$('.msg_bad').remove();}, 500);
	});
	setTimeout(function() {$('.msg_good').hide('slow');}, 5000);
	setTimeout(function() {$('.msg_bad').hide('slow');}, 5000);

	//animation du changement d'opération simple/virement
	$('#squared').click(function() {
		var virementCheck=$("#squared").find(':checkbox');
		if (this.checked) {
			virementCheck.attr('checked', true);
			$('#account').fadeOut(300);
			$('.slideBox').fadeOut(600);
			setTimeout(function() {$('#virement').fadeIn(300)}, 300);
		}
		else {
			virementCheck.attr('checked', false);
			$('#virement').fadeOut(300);
			$('.slideBox').fadeIn(600);
			setTimeout(function() {$('#account').fadeIn(300)}, 300);
		} 
	});

	//affichage du formulaire de restauration du mot de passe
	$('#showFormReset').click(function() {
		$('#login').slideUp('slow');
		$('#reset').slideDown('slow');
	});
	$('#showFormLogin').click(function() {
		$('#reset').slideUp('slow');
		$('#login').slideDown('slow');
	});
	
	//OVERLAYERS
	//click sur la croix d'une op ouvre le formulaire de confirmation de suppression
	$('.del_link').click(function() {
		$('#wrapper_overLayer').fadeIn(350);
	});
	//click sur le bouton annuler
	$('#btn_cancel').click(function() {
		$('#wrapper_overLayer').fadeOut(350);
	});

	//affichage de la liste de mois de l'année lors du clique sur le bouton
	$('.button_mounth').click(function() {
		var hidden=$('#mois li ul').attr('data-hidden');
		console.log(hidden);
		if (hidden==true) {
			$('#mois li > ul').css({"display" : "block"});
			$('#mois li > ul').attr("data", "false");
		}
	});
	$('.button_mounth').click(function() {
		var hidden=$('#mois li ul').attr('data-hidden');
		if (hidden==false) {
			$('#mois li > ul').css({"display" : "none"});
			$('#mois li > ul').attr("data", "true");
		}
	});
});