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
 * @brief génére écriture comptable pour appel de fond
 *
 */
global $cn,$g_copro_parameter;

extract($_GET);
if ( isset($genere ))
{
	// Génére écriture comptable dans journal choisi
} else
{
	$date=new IDate('p_date');
	$amount=new INum('amount');

	$ledger=new Acc_Ledger($cn,0);
	$led_appel_fond=$ledger->select_ledger('ODS',3);
	$led_appel_fond->selected=$g_copro_parameter->journal_appel;
	$poste_appel=new IPoste('poste_appel');
	$poste_appel->set_attribute('gDossier',Dossier::id());
	$poste_appel->set_attribute('jrn',$g_copro_parameter->journal_appel);
	$poste_appel->set_attribute('account','poste_appel');
	$poste_appel->value=$g_copro_parameter->poste_appel;
	$key=new ISelect("key");
	$key->value=$cn->make_array("select cr_id,cr_name from coprop.clef_repartition");

	require_once 'template/appel_fond.php';
}
?>
