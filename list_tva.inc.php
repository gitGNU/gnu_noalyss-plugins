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
echo '<script language="javascript">';
require_once('js_scripts.js');
echo '</script>';

echo '<div id="detail" style="display:none">';
echo '<image src="image/loading.gif" border="0" alt="Chargement...">';
echo '</div>';
/*!\file
 * \brief show all the declaration by date
 */
echo '<div id="main" class="content">';
echo '<form method="get">';
echo _('Filtrer par ');
$choice=new ISelect('type');
$choice->value=array(
		     array('label'=> 'Toutes','value'=> 0 ),
		     array('label'=> 'Déclarations','value'=> 1 ),
		     array('label'=> 'Listings assujetti','value'=> 2),
		     array('label'=> 'Listings intracom','value'=> 3)
		     );
$choice->selected=(isset($_REQUEST['type']))?$_REQUEST['type']:0;
$choice->javascript=' onchange="submit(this)"';
echo HtmlInput::phpsessid();
echo HtmlInput::extension();
echo HtmlInput::hidden('sa','ltva');
echo dossier::hidden();
echo $choice->input();
echo '</form>';
switch($choice->selected) {
case 0:
 $sql="
select da_id as id, 'Déclaration trim/mens' as type_title,1 as type_decl,to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
case when periodicity ='1' then 'Mensuel'  
when periodicity = '2' then 'Trimestriel'
end as fmt_periodicity,
periode_dec
from tva_belge.declaration_amount 
union all
select i_id as id, 'Listing Intracom' as type_title, 3 as type_decl, to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
case when periodicity ='1' then 'Mensuel'  
when periodicity = '2' then 'Trimestriel'
when periodicity = '3' then 'Annuel'
end as fmt_periodicity,
periode_dec
from tva_belge.intracomm


order by date_decl desc
";

  break;

case 1:
 
  $sql="
select da_id as id, 'Déclaration trim/mens' as type_title,1 as type_decl,to_char(date_decl,'DD.MM.YYYY') as date_fmt,
case when periodicity ='1' then 'Mensuel'  
when periodicity = '2' then 'Trimestriel'
end as fmt_periodicity,
periode_dec
from tva_belge.declaration_amount order by date_decl desc
";
  break;
case 3:
$sql="
select i_id as id, 'Listing Intracom' as type_title, 3 as type_decl, to_char(date_decl,'DD.MM.YYYY') as date_fmt,date_decl,
case when periodicity ='1' then 'Mensuel'  
when periodicity = '2' then 'Trimestriel'
when periodicity = '3' then 'Annuel'
end as fmt_periodicity,
periode_dec
from tva_belge.intracomm
order by date_decl desc
";
  break;
}
$res=$cn->get_array($sql);
echo '<table class="result" style="margin-left:25%;width:50%">';
echo tr(th('Type de déclaration').th('Periodicité').th('Mois/année').th('Date'));
for ($i=0;$i<count($res);$i++){
  $aref=sprintf('<a href="javascript:void(0)" onclick="show_declaration(\'%s\',\'%s\')">',
		$res[$i]['type_decl'],$res[$i]['id']);
  $row=td($aref.$res[$i]['type_title'].'</a>');
  $row.=td($aref.$res[$i]['fmt_periodicity'].'</a>');
  $row.=td($aref.$res[$i]['periode_dec'].'</a>');
  $row.=td($aref.$res[$i]['date_fmt'].'</a>');
  echo tr($row);
}
echo '</table>';
echo '</div>';


