<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief gestion des clefs de répartitions
 *
 */
require_once 'class_copro_key.php';
global $cn,$gDossier;

// ajout nouvelle clef
if ( isset($_POST['add_key']))
{
    /**
     * @todo ajout verification double chargement
     */
	$a=new Copro_key();
	$a->insert($_POST);
}

// Mise à jour
if ( isset($_POST['mod_key']))
{
	$a=new Copro_key();
	$a->cr_id=$_POST['cr_id'];
	$a->update($_POST);
}


$sql="select cr_id,cr_name,cr_note,cr_tantieme,
    coalesce(  (select sum(crd_amount) from
		coprop.clef_repartition_detail as crd
		where crd.cr_id=cr.cr_id) ,0) as delta
    from
    coprop.clef_repartition as cr
    order by cr_name";

$a_key=$cn->get_array($sql);
?>
<div id="key_list">
<table class="result">
	<tr>
		<th>
			Nom
		</th>
		<th>
			Note
		</th>
                <th>
                    Tantième
                </th>
                <th>
                    Différence
                </th>
                <th>

                </th>
	</tr>
<?php 
for ($i=0;$i < count($a_key);$i++):
	$js=sprintf("mod_key('%s','%s','%s','%s')",$gDossier,$_REQUEST['plugin_code'],$_REQUEST['ac'],$a_key[$i]['cr_id']);
	$mod_key=HtmlInput::anchor($a_key[$i]['cr_name'],"","onclick=\"$js\"");
?>
	<tr id="row<?php echo $a_key[$i]['cr_id']?>">
		<td>
			<?php echo $mod_key?>
		</td>
		<td>
			<?php echo h($a_key[$i]['cr_note'])?>
		</td>
                <td class="num">
                    <?php echo nbm($a_key[$i]['cr_tantieme'])?>
                </td>
                <td class="num">
                    <?php echo nbm($a_key[$i]['delta'])?>
                </td>
		<td id="col<?php echo $a_key[$i]['cr_id']?>">
                    <?php 
                    $js="onclick=remove_key('".$_REQUEST['plugin_code']."','".$_REQUEST['ac']."','".$_REQUEST['gDossier']."','".$a_key[$i]['cr_id']."')";
			echo HtmlInput::anchor("enlever","",$js);
                        ?>
                </td>

	</tr>
<?php 
endfor;
?>
</table>
	<?php $js=sprintf("add_key('%s','%s','%s')",$gDossier,$_REQUEST['plugin_code'],$_REQUEST['ac']);
 echo HtmlInput::button("add_key","Ajout clef","onclick=\"$js\"");
	?>
 </div>


<div id="keydetail_div"></div>
