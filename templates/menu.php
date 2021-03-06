<?php if (isset($_SESSION['login']) and !empty($_SESSION['login'])) : ?>
	<div class="navbar">
		<ul>
			<li class="logo"><a href="./"><span class="imgMenu"><img alt="Logo" src="templates/images/logo.png" /></span>Giltin'</a></li>
			<li class="extend"><button class="btn-navbar"><i class="fa fa-bars fa-2x"></i></button></li>
			<li><a href="?m=add_op"><span class="imgMenu"><img alt="add" src="templates/images/add.png" /></span>Ajouter</a></li>
			<li><a href="#">Comptes&nbsp;<span class="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<?php while ($datas_accounts_menu=$req_accounts_menu->fetch(PDO::FETCH_ASSOC)) : ?>
						<li><a href="?m=view_op&account=<?php echo $datas_accounts_menu['id_compte']; ?>"><?php echo $datas_accounts_menu['nom']; ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li><a href="#">Options&nbsp;<span class="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<li class="dump"><a href="?m=export">Export comptes</a></li>
					<li class="settings"><a href="?m=settings">Param&egrave;tres</a></li>
				</ul>
			</li>
			<li id="user"><a href="#"><span id="avatarMenu"><img alt="Avatar" src="<?php echo $_SESSION['avatar_mini']; ?>" /></span> <?php echo $_SESSION['nom']; ?>&nbsp;<span class="imgMenuPlus"><img alt="Plus" src="templates/images/plus.png" /></span></a>
				<ul>
					<li><a href="?m=user">Mes informations</a></li>
					<li><a href="?m=aboutus">&Agrave; propos</a></li>
					<li class="logout"><a href="logout.php">D&eacute;connexion</a></li>
				</ul>
			</li>
		</ul>
	</div>
<?php endif; ?>