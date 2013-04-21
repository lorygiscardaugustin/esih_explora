<?php
	session_start();
	require_once("php/rs.php");
	$conn= new Rs();
?>
<!DOCTYPE html> <!--Designed by L. Giscard AUGUSTIN-->
<html>
<head>
	<!-- En-tête de la page -->
	<title>ACCUEIL</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="CSS/Style.css">
	<link rel='shortcut icon' href='IMAGES/logo.png' type='image/x-icon'/ >
	<?php
		function clean_up($source="offres/")
		{
			// Loop through the folder
			$dir = dir($source);
			while (false !== $entry = $dir->read())
			{
				// Skip pointers
				if ($entry == '.' || $entry == '..')
				{
					continue;
				}
				unlink("offres/".$entry);
			}	
			// Clean up
			$dir->close();
			return true;
		}
	?>
	<script>
		function changeValue()
		{
			var initial = document.getElementById("searchField").value;
			if (initial == 'Entreprise / Domaine Etudes / Compétence')
				document.getElementById("searchField").value = "";
		}
		
		function changeValueBack()
		{
			var initial = document.getElementById("searchField").value;
			if (initial == "")
				document.getElementById("searchField").value = 'Entreprise / Domaine Etudes / Compétence';
		}			
	</script>
</head>
<body>

<!--TOP-->
<div id="header">	
	<center><img src="IMAGES/logo.png" /></center>
	<center><div><font style="color:#000000; font-family:MoolBoran; font-weight:bold; font-size:30px;">Chambre de Commerce et d'Industrie des Professionnels de l'Artibonite</font></div></center>
</div>

<div id="bcgrnd">
	
	<!--=====================================================-->
	
	<div id="table_menu">
		<table border="0" id="menu">
			<tr>
				<td style="text-align:center; margin-top:25px; font-family:MoolBoran; font-size:30px; overflow:hidden;">
						La solution à toutes vos quêtes d'emplois !!!
				</td>
			</tr>
		</table>
	</div>
	
		<!--=====================================================-->
		
		<!--<center><table><tr><td><a href="login.php"><img src="IMAGES/exit.png" border="0" height="30px" width="30px" title="Connexion / Deconnexion" style="margin-left:10px;" /></a></td><td><font style="color:#FFFFFF; font-family:MoolBoran; font-size:25px;">Offres de nos Entreprises affiliées.</font></td></tr></table></center>-->
	<table border="0"><tr><td width="1000" align="right"><a href="admin/"><img src="IMAGES/wheel.png" title="Administration" border="0" width="30" height="30" style="" /></a></td></tr></table>
	<div id="body" style="margin-top:-8px;">
		<div id="outer_include" style="height:660px;">
			<form autocomplete="off" method="post" action="index.php" enctype="multipart/form-data">
				<fieldset style="margin-top:20px;">	
					<legend>Poste(s) vacants chez nos Entreprises affiliées.</legend>
					<!--div style="overflow-y:scroll; height:650px;"-->
						<?php
								echo
									'<center><div>
										<div>
											  <input name="searchField" onFocus="changeValue()" onBlur="changeValueBack()" type="text" id="searchField" style="width:250px;" value="Entreprise / Domaine Etudes / Compétence" />
											  <input name="searchSubmit_o" type="submit" id="searchSubmit" value="" /> 
											  <div align="right"><input type="submit" name="all_offre" id="all_offre" class="sub_buttons" value="Afficher tous" /></div>
										</div>
									</div></center><hr /><br />';
								
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
								
								if (isset($_POST['searchSubmit_o']))
								{								
									// Affichage des offres correespondant a la recherche
									echo '<center><fieldset style="border:none;"><table cellspacing="20"><tr style="color:#FF0000;"><td>TITRE	</td>	<td>STATUT	</td>	<td>ENTREPRISE</td>	<td width="200">DOMAINE	</td>	<td>COMPETENCE(S)	</td><td width="50">DETAILS</td></tr>';
									//clean_up();
									$reponse= array();
									$reponse = $conn->Select('SELECT distinct o.Titre, o.IDOFFRE, o.Description, o.Domaine, o.Statut, e.Sigle, e.NomEntreprise from offreemploi as o inner join entreprises as e using(NumeroEntreprise) inner join competence using(IDOFFRE) where o.Statut="vacant" and (lower(Sigle) like \'%'.strtolower($_POST['searchField']).'%\' or lower(NomEntreprise) like \'%'.strtolower($_POST['searchField']).'%\' or lower(Domaine) like \'%'.strtolower($_POST['searchField']).'%\' or lower(Competence) like \'%'.strtolower($_POST['searchField']).'%\') order by o.Titre, e.Sigle');
									$i = 0;
									foreach ($reponse as $donnees)
									{
										$path = "offres/".$donnees['IDOFFRE'].".pdf";
										file_put_contents($path, $donnees['Description']);
										$i = $i + 1;
										echo '<tr><td>'.$donnees['Titre'].'</td><td>'.$donnees['Statut'].'</td><td><a href="admin/detail.php?var='.$donnees['NomEntreprise'].'" target="_blank">'.$donnees['Sigle'].'</a></td><td>'.$donnees['Domaine'].'</td><td>';		
										$reponse1= array();
										$reponse1 = $conn->Select('SELECT Competence from competence where IDOFFRE ='.$donnees['IDOFFRE']);
										echo '<table border="0">';
										
										foreach ($reponse1 as $donnees1)
										{
											echo '<tr><td>-'.$donnees1['Competence'].'</td></tr>';
										}
										echo
										 '</table></td><td><a href="'.$path.'" target="_blank"><img border="0" src="IMAGES/zoom.png" width="30" height="30" title="Vue détaillée" /></a></td></tr><tr><td colspan="6"><hr /></td></tr>';
									}
									echo '</table>';
									if (!$i)
										echo '<tr><td colspan="5" align="center">Aucun postulant correspondant</td></tr>';
								}
									
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
								if (isset($_POST['all_offre']))
								{
									$reponse= array();
									$reponse = $conn->Select('SELECT count(*) as count FROM offreemploi where statut="vacant"');
									foreach ($reponse as $donnees)
									{
										$count = $donnees['count'];
									}
							
									echo
									'<br /><center><label>Listing des <font color="#FF0000">'.$count.'</font> Postes Vacants enregistrés</label></center> <br />
										<center><fieldset style="border:none;">		
											<table cellspacing="20">
												<tr style="color:#FF0000;">
													<td>TITRE	</td>	<td>STATUT	</td>	<td>ENTREPRISE</td>	<td width="200">DOMAINE	</td>	<td>COMPETENCE(S)	</td><td width="50">DETAILS</td>
												</tr>';
													$reponse= array();
													$reponse = $conn->Select('SELECT o.Titre, o.IDOFFRE, o.Description, o.Domaine, o.Statut, e.Sigle, e.NomEntreprise from offreemploi as o inner join entreprises as e using(NumeroEntreprise) where o.Statut="vacant" order by o.Statut DESC, o.Titre, e.Sigle');
													foreach ($reponse as $donnees)
													{
														$path = "offres/".$donnees['IDOFFRE'].".pdf";
														file_put_contents($path, $donnees['Description']);
														echo '<tr><td>'.$donnees['Titre'].'</td><td>'.$donnees['Statut'].'</td><td><a href="admin/detail.php?var='.$donnees['NomEntreprise'].'" target="_blank">'.$donnees['Sigle'].'</a></td><td>'.$donnees['Domaine'].'</td><td>';
														
														
														$reponse1= array();
														$reponse1 = $conn->Select('SELECT Competence from competence where IDOFFRE ='.$donnees['IDOFFRE']);
														echo '<table border="0">';
														
														foreach ($reponse1 as $donnees1)
														{
															echo '<tr><td>-'.$donnees1['Competence'].'</td></tr>';
														}
														echo
														 '</table></td><td><a href="'.$path.'" target="_blank"><img border="0" src="IMAGES/zoom.png" width="30" height="30" title="Vue détaillée" /></a></td></tr><tr><td colspan="6"><hr /></td></tr>';
													}
												
											echo '</table></fieldset></center>';
								}
							?>
						<!--/div-->
				</fieldset>
			</form>
		</div>
	</div>

</div>

<!--Pied-->
	<div id="footer">
		<center>Copyright© / V 2.0</center>
	</div>
	<center><img style="margin-top:-20px;" src="IMAGES/logo_.png" border="0" /></center>

	
</body>
</html>