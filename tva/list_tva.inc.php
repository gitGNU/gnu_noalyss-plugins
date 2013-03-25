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
require_once('class_iselect.php');
require_once 'class_sort_table.php';

echo '<div id="detail" style="display:none; width:80%;margin-left:10%">';
echo '<image src="image/loading.gif" border="0" alt="Chargement...">';
echo '</div>';
/**\file
 * \brief show all the declaration by date
 */

echo '<div id="main" class="content" style="width:80%;margin-left:10%">';
echo '<form method="get">';
echo HtmlInput::request_to_hidden(array('ac'));
echo _('Filtrer par ');
$choice = new ISelect('type');
$choice->value = array(
	array('label' => 'Toutes', 'value' => 0),
	array('label' => 'Déclarations', 'value' => 1),
	array('label' => 'Listings assujetti', 'value' => 2),
	array('label' => 'Listings intracom', 'value' => 3)
);
$choice->selected = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : 0;
$choice->javascript = ' onchange="submit(this)"';
echo HtmlInput::extension();
echo HtmlInput::hidden('sa', 'ltva');
echo dossier::hidden();
echo $choice->input();
echo HtmlInput::submit('rc', 'Recharger');
echo '</form>';

$url = HtmlInput::request_to_string(array('ac', 'plugin_code', 'gDossier', 'sa', 'type'));
$sort= new Sort_Table();
$sort->add('Date déclaration	', $url, ' order by date_decl asc', ' order by date_decl desc', 'da', 'dd');
$sort->add('Période	', $url, ' order by exercice asc,periode_dec asc', ' order by exercice desc,periode_dec desc', 'pa', 'pd');

$option_order = (isset($_GET['ord'])) ? $_GET['ord'] : 'dd';

$sql_order = $sort->get_sql_order($option_order);

switch ($choice->selected)
{
	case 0:
		$sql = "
select da_id as id, 'Déclaration trim/mens' as type_title,1 as type_decl,to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
case when periodicity ='1' then 'Mensuel'
when periodicity = '2' then 'Trimestriel'
end as fmt_periodicity,
periode_dec,exercice
from tva_belge.declaration_amount
union all
select i_id as id, 'Listing Intracom' as type_title, 3 as type_decl, to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
case when periodicity ='1' then 'Mensuel'
when periodicity = '2' then 'Trimestriel'
when periodicity = '3' then 'Annuel'
end as fmt_periodicity,
periode_dec,exercice
from tva_belge.intracomm
union all
select a_id as id, 'Listing assujetti' as type_title, 2 as type_decl, to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
 'Annuel' as fmt_periodicity,
periode_dec,exercice
from tva_belge.assujetti
";

		break;

	case 1:

		$sql = "
select da_id as id, 'Déclaration trim/mens' as type_title,1 as type_decl,to_char(date_decl,'DD.MM.YYYY') as date_fmt,
case when periodicity ='1' then 'Mensuel'
when periodicity = '2' then 'Trimestriel'
end as fmt_periodicity,
periode_dec,exercice
from tva_belge.declaration_amount 
";
		break;
	case 2:
		$sql = "
select a_id as id, 'Listing assujetti' as type_title, 2 as type_decl, to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
 'Annuel' as fmt_periodicity,
periode_dec,exercice
from tva_belge.assujetti
";
		break;
	case 3:
		$sql = "
select i_id as id, 'Listing Intracom' as type_title, 3 as type_decl, to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
case when periodicity ='1' then 'Mensuel'
when periodicity = '2' then 'Trimestriel'
when periodicity = '3' then 'Annuel'
end as fmt_periodicity,
periode_dec,exercice
from tva_belge.intracomm
";
		break;
}
$sql = $sql . $sql_order;
$res = $cn->get_array($sql);
?>
<table class="result" >
	<tr>
		<th>Type de déclaration</th>
		<th>Periodicité</th>
		<th> <?=$sort->get_header(1)?></th>
		<th><?=$sort->get_header(0)?></th>

		<? for ($i = 0; $i < count($res); $i++):?>
		<tr>
			<?
			$aref = sprintf('<a href="javascript:void(0)" onclick="show_declaration(\'%s\',\'%s\')">', $res[$i]['type_decl'], $res[$i]['id']);
			echo td($aref . $res[$i]['type_title'] . '</a>');
			echo td($aref . $res[$i]['fmt_periodicity'] . '</a>');
			echo td($aref.$res[$i]['periode_dec'].' - '.$res[$i]['exercice'].'</a>');
			echo td($aref.$res[$i]['date_fmt'].'</a>');
			?>
		</tr>
	<? endfor;?>
</table>
</div>


