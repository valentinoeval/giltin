<?php if (isset($_SESSION['login']) and !empty($_SESSION['login'])) : ?>
	<div id="menu">
		<ul>
			<li id="logo"><a href="./"><span id="imgMenu"><img alt="Logo" src="templates/images/logo.png" /></span>Giltin\'</a></li>
			<li><a href="?module=add_op"><span id="imgMenu"><img alt="add" src="templates/images/add.png" /></span>Ajouter</a></li>
			<?php //affichage de tous les comptes dans le menu ?>
			<?php while ($datas_accounts_menu=$req_accounts_menu->fetch(PDO::FETCH_ASSOC)) : ?>
				<li><a href="?module=view_op&account=<?php echo $datas_accounts_menu['id_compte']; ?>"><?php echo $datas_accounts_menu['nom']; ?></a></li>
			<?php endwhile; ?>
			<li><a href="#">Fonctionnalités <span id="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<li><a href="?module=pari">Pari footballistique</a></li>
				</ul>
			</li>
			<li><a href="#">Options <span id="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<?php if ($_SESSION['rights']==-2 or $_SESSION['rights']==-1) : ?>
						<li class="settings"><a href="?module=admin">Administration</a></li>
					<?php endif; ?>
					<li class="dump"><a href="?module=dump">Dump BDD</a></li>
					<li class="settings"><a href="?module=settings">Paramètres</a></li>
				</ul>
			</li>
			<li id="user"><a href="#"><span id="avatarMenu"><img alt="Avatar" src="<?php echo $_SESSION['avatar_mini']; ?>" /></span> <?php echo $_SESSION['nom']; ?></a>
				<ul>
					<li><a href="?module=user">Mes informations</a></li>
					<li><a href="?module=aboutus">A propos</a></li>
					<li class="logout"><a href="logout.php">D&eacute;connexion</a></li>
				</ul>
			</li>
		</ul>
	</div>
<?php endif; ?>