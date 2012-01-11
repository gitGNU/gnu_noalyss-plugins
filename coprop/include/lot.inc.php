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
global $cn,$g_copro_parameter;
}
//require_once 'include/class_coprop-lot_coprop.php';
/* Add button */
$f_add_button=new IButton('add_card');
$f_add_button->label=_('Créer une nouvelle fiche');
$f_add_button->set_attribute('ipopup','ipop_newcard');
$f_add_button->set_attribute('jrn',-1);
$filter=$g_copro_parameter->categorie_lot.",".$g_copro_parameter->categorie_coprop.",".$g_copro_parameter->categorie_immeuble;
$f_add_button->javascript=" this.filter='$filter';this.jrn=-1;select_card_type(this);";



/**
 * @todo ajouter tri
 */

// liste Immeuble

$a_immeuble=$cn->get_array(" select f_id,vw_name,quick_code
    from vw_fiche_attr
    where
    fd_id=$1
    ",array($g_copro_parameter->categorie_immeuble));

/*
 * Liste coprop par immeuble
 */
$coprop=$cn->prepare("coprop"," select coprop_id,
	vw_name as copro_name,
        vw_first_name as copro_first_name,
	quick_code as copro_qcode
	from
	coprop.summary as s join vw_fiche_attr using (f_id)
	where
	s.building_id=$1
	");
/*
 * Liste coprop par immeuble
 */
$lot=$cn->prepare("lot"," select lot_id,
	vw_name as lot_name,
	quick_code as lot_qcode,
        vw_description as lot_desc
	from
	coprop.summary as s join vw_fiche_attr using (f_id)
	where
	s.building_id=$1 and s.coprop_id=$2
	");
/*
 * Lot sans immeuble or coprop
 */
$a_undef_lot=$cn->get_array(" select lot_id,
	vw_name as lot_name,
	quick_code as lot_qcode,
        vw_description as lot_desc
	from
	coprop.summary as s join vw_fiche_attr using (f_id)
	where
	coalesce(s.building_id,'')='' or coalesce(s.coprop_id,'')=''
	");

echo $f_add_button->input();



echo $f_add_button->input();

?>
