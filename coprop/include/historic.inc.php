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
 * @brief show history of call
 *
 */
global $cn;

$sql="select af_id,af_date,to_char(af_date,'DD.MM.YY') as str_date,af_percent,af_amount,af_card,af_ledger,af.jr_internal,
bd.b_id,bd.b_name,bd.b_exercice,bd.b_type,jrn.jr_id,
cr.cr_id,cr_name,
jrn_def_name
from
coprop.appel_fond as af
left join coprop.budget as bd on (af.b_id=bd.b_id)
left join coprop.clef_repartition as cr on (af.cr_id=cr.cr_id)
join jrn_def on (jrn_def_id=af_ledger)
join jrn on (jrn.jr_internal=af.jr_internal)
where af_confirmed='Y'
order by 2
";
$array=$cn->get_array($sql);
require_once 'template/historic.php';
?>
