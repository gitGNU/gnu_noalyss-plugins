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

/**
 * @file
 * @brief display list of formulaire + button
 *
 */
?>
<div id="form_list_div">
<table class="result">
	<tr>
		<th>Nom du formulaire</th>
		<th>Description</th>

		<th></th>
		<th></th>
		<th></th>
	</tr>
<?php 
	for ($i=0;$i<count($alist);$i++):
            $class=($i%2==0)?'class="odd"':'class="even"';
?>
	<tr <?php echo $class; ?>>
		<td>
			<?php echo h($alist[$i]['f_title'])?>
		</td>
		<td>
			<?php echo h($alist[$i]['f_description'])?>
		</td>
		<td>
			<?php echo HtmlInput::anchor('Définition', "",sprintf('onclick="rapav_form_def(\'%s\',\'%s\',\'%s\',\'%s\')"',$_REQUEST['plugin_code'],$_REQUEST['ac'],dossier::id(),$alist[$i]['f_id']))?>
		</td>
		<td>
			<?php echo HtmlInput::anchor('Paramètre', "",sprintf('onclick="rapav_form_param(\'%s\',\'%s\',\'%s\',\'%s\')"',$_REQUEST['plugin_code'],$_REQUEST['ac'],dossier::id(),$alist[$i]['f_id']))?>
		</td>
		<td>
			<?php echo HtmlInput::anchor('Export définition', sprintf("extension.raw.php?plugin_code=%s&ac=%s&gDossier=%s&d_id=%s&act=rapav_form_export",$_REQUEST['plugin_code'],$_REQUEST['ac'],dossier::id(),$alist[$i]['f_id']))?>
		</td>
	</tr>

<?php endfor; ?>
</table>
	<?php 
	echo HtmlInput::button("add_form_bt","Ajout d'un formulaire",'onclick="$(\'add_form_div\').show();$(\'add_form_bt\').hide()"');
echo '<div id="add_form_div" style="display:none">';
echo '<form method="POST">';
echo '<h2> Nouveau formulaire</h2>';
$name=new IText("titre");
$description=new IText("description");
echo '<table>';
echo tr(td("Titre : ").td($name->input()));
echo tr(td('Description').td($description->input()));
echo '</table>';
echo HtmlInput::submit ("add_form","Sauver");
echo '</form>';
echo '<form enctype="multipart/form-data"  method="POST"> ';
echo '<h2> Depuis un fichier</h2>';
$file=new IFile('form');
echo $file->input();
echo HtmlInput::submit ("restore_form","Sauver");
echo '</form>';
?>
</div>
</div>
<div id="form_mod_div">

</div>