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
 * @brief liaison entre lot et copropriétaires
 *
 */
require_once 'class_copro_lot.php';
global $cn,$g_copro_parameter;
if ( isset($_POST['copro_new']))
{
	var_dump($_POST);
	$cp=new Copro_Lot();
	try
	{
		$cp->insert($_POST);
	}
	catch(Exception $e)
	{
		echo $e->getTraceAsString();
	}
}
// Ajout de lots
if (isset($_POST['addlot']))
{
	var_dump($_POST);
	$cp=new Copro_Lot();
	try
	{
		$cp->add_lot($_POST);
	}
	catch(Exception $e)
	{
		echo $e->getTraceAsString();
	}
}
// Mise à jour lots existants
if ( isset($_POST['updexist']))
{
		var_dump($_POST);
	$cp=new Copro_Lot();
	try
	{
		$cp->update_lot($_POST);
	}
	catch(Exception $e)
	{
		echo $e->getTraceAsString();
	}
}
//require_once 'include/class_coprop-lot_coprop.php';
/* Add button */
$f_add_button=new IButton('add_card');
$f_add_button->label=_('Créer une nouvelle fiche');
$f_add_button->set_attribute('ipopup','ipop_newcard');
$f_add_button->set_attribute('jrn',-1);
$filter=$g_copro_parameter->categorie_lot.",".$g_copro_parameter->categorie_coprop;
$f_add_button->javascript=" this.filter='$filter';this.jrn=-1;select_card_type(this);";


/*
 * Liste
 */
$sql=" select distinct
	c_fiche_id,
	(select ad_value from fiche_detail where f_id=c_fiche_id and ad_id=1) as copro_name,
	(select ad_value from fiche_detail where f_id=c_fiche_id and ad_id=23) as copro_qcode

	from
	coprop.coproprietaire
	";
/**
 * @todo ajouter tri
 */
$a_copro=$cn->get_array($sql);

$sql_lot=$cn->prepare ("lot","select coprop_fk, (select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=1) as lot_name,
	(select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=23) as lot_qcode,
        (select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=9) as lot_desc
	from coprop.lot where coprop_fk=$1");

echo $f_add_button->input();
echo '<div class="content" id="listcoprolot">';
require_once 'template/coprop_lot_list.php';
echo '</div>';

echo '<div class="content" id="ajoutcopro" style="display:none">';
require_once('template/coprop_lot_add.php');

echo '</div>';
echo '<div id="divcopropmod"></div>';
echo $f_add_button->input();

?>
