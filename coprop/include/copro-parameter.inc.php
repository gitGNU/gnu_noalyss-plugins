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
 * @brief table parametre pour les copropriétés
 *
 */
require_once 'class_copro_parameter.php';
global $cn,$g_copro_parameter;

if ( isset ($_POST['save']))
{
	$cn->start();
	try {
		/*$g_copro_parameter->save('categorie_lot',$_POST['categorie_lot']);
		$g_copro_parameter->save('categorie_coprop',$_POST['categorie_coprop']);*/
		$g_copro_parameter->save('journal_appel',$_POST['journal_appel']);
		$g_copro_parameter->save('categorie_appel',$_POST['categorie_appel']);
		$g_copro_parameter->save('categorie_charge',$_POST['categorie_charge']);
		//$g_copro_parameter->save('categorie_immeuble',$_POST['categorie_immeuble']);
	}
	catch(Exception $e)
	{
		$cn->rollback();
		echo $e->getTraceAsString();
	}
	$cn->commit();
}
$g_copro_parameter=new Copro_Parameter();
/*
 * Liste paramètres
 */
$cat_lot=new ISelect('categorie_lot');
$cat_lot->value=$cn->make_array("select fd_id,fd_label from fiche_def order by fd_label");
$cat_lot->selected=$g_copro_parameter->categorie_lot;
$cat_lot->readOnly=true;

$cat_coprop=new ISelect('categorie_coprop');
$cat_coprop->value=$cn->make_array("select fd_id,fd_label from fiche_def order by fd_label ");
$cat_coprop->selected=$g_copro_parameter->categorie_coprop;
$cat_coprop->readOnly=true;

$journal_appel=new ISelect('journal_appel');
$journal_appel->value=$cn->make_array("select jrn_def_id,jrn_def_name from jrn_def where jrn_def_type='ODS' order by 2");
$journal_appel->selected=$g_copro_parameter->journal_appel;

$categorie_appel=new ISelect('categorie_appel');
$categorie_appel->value=$cn->make_array("select  fd_id,fd_label from fiche_def order by fd_label ");
$categorie_appel->selected=$g_copro_parameter->categorie_appel;

$categorie_charge=new ISelect('categorie_charge');
$categorie_charge->value=$cn->make_array("select  fd_id,fd_label from fiche_def order by fd_label ");
$categorie_charge->selected=$g_copro_parameter->categorie_charge;

$categorie_immeuble=new ISelect('categorie_immeuble');
$categorie_immeuble->value=$cn->make_array("select  fd_id,fd_label from fiche_def order by fd_label ");
$categorie_immeuble->selected=$g_copro_parameter->categorie_immeuble;
$categorie_immeuble->readOnly=true;
?>
<form method="POST">
<table>
	<tr>
		<td>
			Catégorie de fiches pour les immeubles
		</td>
		<td>
			<?=$categorie_immeuble->input()?>
		</td>
	</tr>
		<tr>
		<td>
			Catégorie de fiches pour les copropriétaires
		</td>
		<td>
			<?=$cat_coprop->input();?>
		</td>
	</tr>
	<tr>
		<td>
			Catégorie de fiches pour les lots
		</td>
		<td>
			<?=$cat_lot->input()?>
		</td>
	</tr>
		<tr>
		<td>
			Catégorie de fiches pour les appels de fond
		</td>
		<td>
			<?=$categorie_appel->input();?>
		</td>
	</tr>
		<tr>
		<td>
			Catégorie de fiches pour les charges
		</td>
		<td>
			<?=$categorie_charge->input();?>
		</td>
	</tr>
	<tr>
		<td>
			Journal Appel de fond
		</td>
		<td>
			<?=$journal_appel->input();?>
		</td>
	</tr>
</table>
	<?=HtmlInput::submit("save","Sauver")?>
</form>