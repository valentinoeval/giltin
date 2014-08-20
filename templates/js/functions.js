function hydrating_form_op(account, id, name, url) {
	document.getElementById("account").value=account;
	document.getElementById("id").value=id;
	document.getElementById('op_name').innerHTML=name;
	document.getElementById("url").value=url;
}
//formulaire pour suppression backup
function hydrating_form_dump(id, file, url) {
	document.getElementById("id").value=id;
	document.getElementById("dump_name").innerHTML=file;
	document.getElementById("file").value=file;
	document.getElementById("url").value=url;
}
//formulaire pour suppression compte
function hydrating_form_account(id, name, url) {
	document.getElementById("id").value=id;
	document.getElementById("account_name").innerHTML=name;
	document.getElementById("url").value=url;
}
//formulaire pour modifier l'avatar dans la galerie
function hydrating_form_avatar(avatar, directory) {
	document.getElementById("avatar").value=avatar;
	document.getElementById("avatar_mini").innerHTML='<img src="'+directory+avatar+'" style="width:40px;"/>';
}