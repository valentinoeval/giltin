<?php
	//sécurisation du module
	if (file_exists('uploads/'.$_SESSION['login'].'/key')) {
		if (file_get_contents('uploads/'.$_SESSION['login'].'/key', NULL, NULL, 0, 100)==$_SESSION['key']) {
			if (!isset($pari) and $pari!=true)
				header('location:index.php');
		}
		else
			header('location:index.php');
	}
	else
		header('location:index.php');

	if (isset($_POST['hidden_nb_mise']) and $_POST['hidden_nb_mise']>=1 and $_POST['hidden_nb_mise']<=8) {// 1 pari
		if (isset($_POST['mise']) and !empty($_POST['mise'])) {
			if (isset($_POST['team1']) and !empty($_POST['team1'])) {
				if (isset($_POST['cote1']) and !empty($_POST['cote1'])) {
					if ($_POST['hidden_nb_mise']>1) {// 2 paris
						if (isset($_POST['team2']) and !empty($_POST['team2'])) {
							if (isset($_POST['cote2']) and !empty($_POST['cote2'])) {
								if ($_POST['hidden_nb_mise']>2) {// 3 paris
									if (isset($_POST['team3']) and !empty($_POST['team3'])) {
										if (isset($_POST['cote3']) and !empty($_POST['cote3'])) {
											if ($_POST['hidden_nb_mise']>3) {// 4 paris
												if (isset($_POST['team4']) and !empty($_POST['team4'])) {
													if (isset($_POST['cote4']) and !empty($_POST['cote4'])) {
														if ($_POST['hidden_nb_mise']>4) {// 5paris
															if (isset($_POST['team5']) and !empty($_POST['team5'])) {
																if (isset($_POST['cote5']) and !empty($_POST['cote5'])) {
																	if ($_POST['hidden_nb_mise']>5) {// 6 paris
																		if (isset($_POST['team6']) and !empty($_POST['team6'])) {
																			if (isset($_POST['cote6']) and !empty($_POST['cote6'])) {
																				if ($_POST['hidden_nb_mise']>6) {// 7 paris
																					if (isset($_POST['team7']) and !empty($_POST['team7'])) {
																						if (isset($_POST['cote7']) and !empty($_POST['cote7'])) {
																							if ($_POST['hidden_nb_mise']>7) {// 8 paris
																								if (isset($_POST['team8']) and !empty($_POST['team8'])) {
																									if (isset($_POST['cote8']) and !empty($_POST['cote8'])) {
																										if (isset($_POST['mise8']) and !empty($_POST['mise8'])) {
																											$i=1;
																											$gains=array();
																											$result=1;
																											$mise=$_POST['mise'];
																											while ($i<=$_POST['hidden_nb_mise']) {
																												$team=$_POST['team'.$i];
																												$cote=$_POST['cote'.$i];
																												$choix_pari=$_POST['choix_pari'.$i];
																												if ($choix_pari=='cote'.$i) {
																													$choix_cote=$cote;
																													$type='Gagnante';
																												}
																												elseif ($choix_pari=='coten'.$i) {
																													$choix_cote=$cote;
																													$type='Match nul';
																												}
																												$result=$result*$choix_cote;
																												$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
																												$i++;
																											}
																											$result=$result*$mise;
																											$result_reel=$result-$mise;
																											$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
																										}
																									}
																								}
																							}
																							else {
																								$i=1;
																								$gains=array();
																								$result=1;
																								$mise=$_POST['mise'];
																								while ($i<=$_POST['hidden_nb_mise']) {
																									$team=$_POST['team'.$i];
																									$cote=$_POST['cote'.$i];
																									$choix_pari=$_POST['choix_pari'.$i];
																									if ($choix_pari=='cote'.$i) {
																										$choix_cote=$cote;
																										$type='Gagnante';
																									}
																									elseif ($choix_pari=='coten'.$i) {
																										$choix_cote=$cote;
																										$type='Match nul';
																									}
																									$result=$result*$choix_cote;
																									$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
																									$i++;
																								}
																								$result=$result*$mise;
																								$result_reel=$result-$mise;
																								$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
																							}
																						}
																					}
																				}
																				else {
																					$i=1;
																					$gains=array();
																					$result=1;
																					$mise=$_POST['mise'];
																					while ($i<=$_POST['hidden_nb_mise']) {
																						$team=$_POST['team'.$i];
																						$cote=$_POST['cote'.$i];
																						$choix_pari=$_POST['choix_pari'.$i];
																						if ($choix_pari=='cote'.$i) {
																							$choix_cote=$cote;
																							$type='Gagnante';
																						}
																						elseif ($choix_pari=='coten'.$i) {
																							$choix_cote=$cote;
																							$type='Match nul';
																						}
																						$result=$result*$choix_cote;
																						$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
																						$i++;
																					}
																					$result=$result*$mise;
																					$result_reel=$result-$mise;
																					$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
																				}
																			}
																		}
																	}
																	else {
																		$i=1;
																		$gains=array();
																		$result=1;
																		$mise=$_POST['mise'];
																		while ($i<=$_POST['hidden_nb_mise']) {
																			$team=$_POST['team'.$i];
																			$cote=$_POST['cote'.$i];
																			$choix_pari=$_POST['choix_pari'.$i];
																			if ($choix_pari=='cote'.$i) {
																				$choix_cote=$cote;
																				$type='Gagnante';
																			}
																			elseif ($choix_pari=='coten'.$i) {
																				$choix_cote=$cote;
																				$type='Match nul';
																			}
																			$result=$result*$choix_cote;
																			$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
																			$i++;
																		}
																		$result=$result*$mise;
																		$result_reel=$result-$mise;
																		$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
																	}
																}
															}
														}
														else {
															$i=1;
															$gains=array();
															$result=1;
															$mise=$_POST['mise'];
															while ($i<=$_POST['hidden_nb_mise']) {
																$team=$_POST['team'.$i];
																$cote=$_POST['cote'.$i];
																$choix_pari=$_POST['choix_pari'.$i];
																if ($choix_pari=='cote'.$i) {
																	$choix_cote=$cote;
																	$type='Gagnante';
																}
																elseif ($choix_pari=='coten'.$i) {
																	$choix_cote=$cote;
																	$type='Match nul';
																}
																$result=$result*$choix_cote;
																$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
																$i++;
															}
															$result=$result*$mise;
															$result_reel=$result-$mise;
															$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
														}
													}
												}
											}
											else {
												$i=1;
												$gains=array();
												$result=1;
												$mise=$_POST['mise'];
												while ($i<=$_POST['hidden_nb_mise']) {
													$team=$_POST['team'.$i];
													$cote=$_POST['cote'.$i];
													$choix_pari=$_POST['choix_pari'.$i];
													if ($choix_pari=='cote'.$i) {
														$choix_cote=$cote;
														$type='Gagnante';
													}
													elseif ($choix_pari=='coten'.$i) {
														$choix_cote=$cote;
														$type='Match nul';
													}
													$result=$result*$choix_cote;
													$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
													$i++;
												}
												$result=$result*$mise;
												$result_reel=$result-$mise;
												$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
											}
										}
									}
								}
								else {
									$i=1;
									$gains=array();
									$result=1;
									$mise=$_POST['mise'];
									while ($i<=$_POST['hidden_nb_mise']) {
										$team=$_POST['team'.$i];
										$cote=$_POST['cote'.$i];
										$choix_pari=$_POST['choix_pari'.$i];
										if ($choix_pari=='cote'.$i) {
											$choix_cote=$cote;
											$type='Gagnante';
										}
										elseif ($choix_pari=='coten'.$i) {
											$choix_cote=$cote;
											$type='Match nul';
										}
										$result=$result*$choix_cote;
										$gains[]=array('Pari sur'=>$team, 'Côte'=>$cote, 'Choix'=>$type);
										$i++;
									}
									$result=$result*$mise;
									$result_reel=$result-$mise;
									$gains[]=array('Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
								}
							}
						}
					}
					else {
						$gains=array();
						$team1=$_POST['team1'];
						$cote1=$_POST['cote1'];
						$choix_pari1=$_POST['choix_pari1'];
						$mise=$_POST['mise'];
						if ($choix_pari1=='cote1') {
							$choix_cote1=$cote1;
							$type1='Gagnante';
						}
						elseif ($choix_pari1=='coten1') {
							$choix_cote1=$cote1;
							$type1='Match nul';
						}
						$result=$choix_cote1*$mise;
						$result_reel=$result-$mise;
						$gains[]=array('Pari sur'=>$team1, 'Côte'=>$cote1, 'Choix'=>$type1, 'Mise'=>$mise.'€', 'Gains potentiels'=>$result.'€', 'Gains réels'=>$result_reel.'€');
					}
				}
			}
		}
	}

	echo '<h3>Calcul des gains aux paris footballistiques</h3>';
	if (!isset($_POST['nb_mise']) or empty($_POST['nb_mise'])) {
		echo '
		<form action="?module=pari" method="post">
			<input type="range" name="nb_mise" value="1" min="1" max="8" step="1" /><br />
			<input type="submit" value="Envoyer" />
		</form>';
	}
	else {
		echo '
		<form action="?module=pari" method="post">';
			$i=1;
			while ($i<=$_POST['nb_mise']) {
				echo '
				<input type="text" name="team'.$i.'" placeholder="Nom equipe parié" /><br />
				<select name="choix_pari'.$i.'">
					<option value="cote'.$i.'">Pari sur gagnante</option>
					<option value="coten'.$i.'">Pari sur match nul</option>
				</select><br />
				<input type="text" name="cote'.$i.'" placeholder="Côte" /><br />';
				$i++;
			}
		echo '<select name="mise">
				<option value="1">1€</option>
				<option value="2">2€</option>
				<option value="5">5€</option>
				<option value="10">10€</option>
				<option value="20">20€</option>
				<option value="50">50€</option>
				<option value="75">75€</option>
				<option value="100">100€</option>
			</select><br />
			<input type="hidden" name="hidden_nb_mise" value="'.$_POST['nb_mise'].'" />
			<input type="submit" value="Envoyer" />
		</form>';
	}
	if (isset($gains)) {
		foreach ($gains as $key => $value) {
			foreach ($value as $key2 => $value2) {
				echo $key2.' : '.$value2.'<br />';
			}
		}
	}