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
 * @brief ajout clef repartition
 *
 */
$note=new ITextarea('cr_note');
$note->heigh=6;
$note->width=80;
$note->style='style="border:solid blue 1px;vertical-align:text-top;" ';
$name=new IText('cr_name');
$name->size=60;
$tantieme=new INum('cr_tantieme');
$tantieme->javascript='onchange="format_number(this,0);compute_key();"';
$tantieme->value=0;

$str_message="Ajout d'une clef de répartition";
$alot=$cn->get_array("select f_id,vw_name as name,quick_code as qcode, vw_description as desc, 0 as l_part
	from vw_fiche_attr where fd_id=$1 order by 2 ",array($g_copro_parameter->categorie_lot));
$init_tantieme=0;
echo '<form id="fkey" method="post">';
require_once 'template/key_detail.php';
echo HtmlInput::submit("add_key","Ajouter",' onclick="return confirm (\'Vous confirmez?\')"');
echo '</form>';
?>
