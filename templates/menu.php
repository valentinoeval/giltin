<?php if (isset($_SESSION['login']) and !empty($_SESSION['login'])) : ?>
	<div id="menu">
		<ul>
			<li id="logo"><a href="./"><span id="imgMenu"><img alt="Logo" src="templates/images/logo.png" /></span>Giltin'</a></li>
			<li><a href="?m=add_op"><span id="imgMenu"><img alt="add" src="templates/images/add.png" /></span>Ajouter</a></li>
			<li><a href="#">Comptes&nbsp;<span class="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<?php while ($datas_accounts_menu=$req_accounts_menu->fetch(PDO::FETCH_ASSOC)) : ?>
						<li><a href="?m=view_op&account=<?php echo $datas_accounts_menu['id_compte']; ?>"><?php echo $datas_accounts_menu['nom']; ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li><a href="#">Options&nbsp;<span class="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<?php if ($_SESSION['rights']==-2 or $_SESSION['rights']==-1) : ?>
						<li class="settings"><a href="?m=admin">Administration</a></li>
					<?php endif; ?>
					<li class="dump"><a href="?m=dump">Dump BDD</a></li>
					<li class="settings"><a href="?m=settings">Paramètres</a></li>
				</ul>
			</li>
			<li id="user"><a href="#"><span id="avatarMenu"><img alt="Avatar" src="<?php echo $_SESSION['avatar_mini']; ?>" /></span> <?php echo $_SESSION['nom']; ?></a>
				<ul>
					<li><a href="?m=user">Mes informations</a></li>
					<li><a href="?m=aboutus">A propos</a></li>
					<li class="logout"><a href="logout.php">D&eacute;connexion</a></li>
				</ul>
			</li>
		</ul>
	</div>
<?php endif; ?>