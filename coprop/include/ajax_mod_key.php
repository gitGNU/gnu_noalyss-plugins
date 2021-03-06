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

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief modifie clef
 *
 */
$key=$cn->get_array("SELECT cr_name, cr_note,cr_tantieme
  FROM coprop.clef_repartition where cr_id=$1",array($key_id));

$str_message="Modification ".h($key[0]['cr_name']);

$note=new ITextarea('cr_note');
$note->value=$key[0]['cr_note'];

$note->heigh=6;
$note->width=80;
$note->style='style="border:solid blue 1px;vertical-align:text-top;" ';

$name=new IText('cr_name');
$name->value=$key[0]['cr_name'];
$name->size=60;

$tantieme=new INum('cr_tantieme');
$tantieme->javascript='onchange="format_number(this,0);compute_key();"';
$tantieme->value=round($key[0]['cr_tantieme']);

$alot=$cn->get_array("select lot_fk as f_id,
	(select ad_value from fiche_detail where f_id=lot_fk and ad_id=1) as name,
	(select ad_value from fiche_detail where f_id=lot_fk and ad_id=23) as qcode,
	(select ad_value from fiche_detail where f_id=lot_fk and ad_id=9) as desc,
	crd_amount as l_part
	from
		coprop.clef_repartition_detail
		where cr_id=$1
	union
select
f_id,vw_name as name,quick_code as qcode,vw_description as desc, 0 as l_part
	from vw_fiche_attr where fd_id=$2
	and f_id not in (select lot_fk from coprop.clef_repartition_detail where cr_id=$1)",array($key_id,$g_copro_parameter->categorie_lot));

$init_tantieme=$cn->get_value("select sum(crd_amount) from
		coprop.clef_repartition_detail
		where cr_id=$1",array($key_id));

echo '<form id="fkey" method="post">';
echo HtmlInput::hidden('cr_id',$key_id);
require_once 'template/key_detail.php';
echo HtmlInput::submit("mod_key","Modifier",' onclick="return confirm (\'Vous confirmez?\')"');
echo HtmlInput::button('rlist','Retour liste',' onclick="$(\'key_list\').show();$(\'keydetail_div\').hide()"');
echo '</form>';
?>
