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
 * @brief modify copro. + lot
 * parameter are
 *@code
 * array
  'plugin_code' => string 'COPROP' (length=6)
  'gDossier' => string '26' (length=2)
  'coprop_id' => string '32' (length=2)
  'ac' => string 'EXT/COPROP' (length=10)
  'act' => string 'modcopro' (length=8)

 *@endcode
 */

$copro=new Fiche($cn);
$copro->id=$coprop_id;

$acurrent=$cn->get_array("select lot.l_id   ,l_fiche_id,
			(select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=1) as fiche_name,
			(select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=23) as fiche_qcode,
			(select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=9) as fiche_desc
			from coprop.lot where coprop_fk=$1",
		array($coprop_id));

$not_assigned=$cn->get_array("select a.f_id,
			(select ad_value from fiche_detail as e where e.f_id=a.f_id and ad_id=1) as fiche_name,
			(select ad_value from fiche_detail as f where f.f_id=a.f_id and ad_id=23) as fiche_qcode,
                        (select ad_value from fiche_detail where f_id=l_fiche_id and ad_id=9) as fiche_desc
			from coprop.lot
			right join fiche as a on (l_fiche_id=a.f_id)
			where
			coalesce(coprop_fk,0) <>$1 and fd_id=$2",
		array($coprop_id,$g_copro_parameter->categorie_lot));

require_once("template/coprop_lot_mod.php")
?>
