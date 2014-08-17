<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**\file
 * \brief import Accountancy plan
 */
echo '<div style="width:80%;margin-left:10%;border-collapse: separate;border-spacing:  5px;">';
if (!isset($_POST['imp']) && !isset($_GET['confirm']))
{
	?>


	<h2> Importer le plan comptable</h2>
	Comment votre fichier doit être ?
	<ul>
		<li>Il faut 4 champs</li>
		<li>Les champs sont séparés par des points virgules</li>
	</ul>
	Les champs sont
	<ol>
		<li>Le poste comptable</li>
		<li>Le libellé du poste</li>
		<li>Le poste parent</li>
	    <li>Le type de poste : PAS pour passif, ACT pour actif, PRO pour produit, CHA pour charge, CON suivant contexte, pour les comptes inversés ajouter INV à la fin, exemple PROINV (produit compte inversé)</li>
	</ol>
	<?php 
	echo ' <form method="post" enctype="multipart/form-data" >';
	echo HtmlInput::hidden('sa', $_GET ['sa']);
	echo HtmlInput::extension();
	echo dossier::hidden();
	$file = new IFile('plan');

	echo '<p>';
	echo $file->input();
	echo '</p>';

	$latin = new ICheckBox('latin');
	$overwrite = new ICheckBox('over');
	echo "Le fichier n'est pas en unicode mais en latin1" . $latin->input() . '<br>';
	echo "Supprimer le plan comptable uniquement si vous n'avez entré aucune opération" . $overwrite->input() . "</br>";

	echo HtmlInput::submit('imp', 'Importation');
	?>


	</form>
	<?php 
}

// Import the file and ask to confirm
if (isset($_POST['imp']))
{

	if (trim($_FILES['plan']['name']) == '')
	{
		alert('Pas de fichier donné');
		return -1;
	}

	$filename = tempnam($_ENV['TMP'], 'upload_');
	move_uploaded_file($_FILES["plan"]["tmp_name"], $filename);
	$fplan = fopen($filename, 'r');
	echo '<table>';
	$row_count = 0;
	while (($row = fgetcsv($fplan, 0, ';')) !== false)
	{
		$row_count++;
		echo '<tr style="border:solid 1px black">';
		$count_col = count($row);
		if ($count_col == 4)
		{
			echo td($row_count);
			echo td($count_col);
			for ($i = 0; $i < 4; $i++)
			{
				echo td($row[$i], 'style="border:solid 1px black"');
			}
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '<form method="get">';
	echo HtmlInput::hidden('file', $filename);
	echo dossier::hidden();
	echo HtmlInput::hidden('sa', $_REQUEST['sa']);
	echo HtmlInput::extension();
	if (isset($_POST['latin']))
		echo HtmlInput::hidden('latin', $_POST['latin']);
	if (isset($_POST['over']))
		echo HtmlInput::hidden('over', $_POST['over']);
	echo HtmlInput::hidden('ac', $_REQUEST['ac']);
	echo HtmlInput::submit('confirm', 'Confirmez');
	echo '</form>';
}
/*
 * delete and insert into tmp_pcmn
 */
if (isset($_GET['confirm']))
{
	$cn->start();
	global $g_failed, $g_succeed;
	if (isset($_GET ['over']))
	{
		$cn->exec_sql('delete from tmp_pcmn');
	}
	$fplan = fopen($_GET['file'], 'r');
	echo '<table>';
	$row_count = 0;
	while (($row = fgetcsv($fplan, 0, ';')) !== false)
	{

		$count_col = count($row);
		if ($count_col == 4)
		{
			$dup = $cn->get_value('select * from tmp_pcmn where pcm_val=$1', array($row[0]));
			// check duplicate
			if ($dup == 0)
			{
				// insert
				if (isset($_GET['latin']))
				{
					$cn->exec_sql("insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent,pcm_type) values ($1,$2,$3,$4)", array(
						$row[0],
						utf8_encode($row[1]),
						$row[2],
						$row[3])
					);
				}
				else
				{
					$cn->exec_sql("insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent,pcm_type) values ($1,$2,$3,$4)", array(
						$row[0],
						$row[1],
						$row[2],
						$row[3]
							)
					);
				}
				$ok = 1;
			}
			else
				$ok = 0;
			echo '<tr style="border:solid 1px black">';
			$row_count++;

			echo td($row_count);
			for ($i = 0; $i < 4; $i++)
			{
				echo td($row[$i], 'style="border:solid 1px black"');
			}
			if ($ok == 1)
				echo '<td>' . $g_succeed . '</td>';
			else
				echo '<td>' . $g_failed . '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	echo "Nombre de postes insérés " . $row_count;
	$cn->commit();
}
echo '</div>';

