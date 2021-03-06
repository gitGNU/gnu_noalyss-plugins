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
 * @brief show the history of the saved declaration
 * take data from rapport_avance.declaration and display via ajax
 */
global $cn;
$cn->exec_sql("delete from rapport_advanced.declaration where to_keep='N' and d_generated < now() - interval '5 hours'");
$cn->exec_sql("delete from rapport_advanced.listing_compute where l_keep='N' and l_timestamp < now() - interval '5 hours'");
$data=$cn->get_array("select d_id,d_title,
		d_start,d_end,to_char(d_generated,'DD/MM/YY HH24:MI') as fmt_generated,
		d_description,d_generated,
		to_char(d_start,'YYMMDD') as fmt_start,
		to_char(d_end,'YYMMDD') as fmt_end,
		to_char(d_generated,'YYMMDDHH24MI') as fmt_order_generated,
                1 as type
		from rapport_advanced.declaration
		where to_keep='Y'
            
order by d_generated desc,d_title");
?>
<div id="declaration_list_div">
<?php
echo '<span style="display:block">';
	echo _('Cherche').HtmlInput::infobulle(204);
	echo HtmlInput::filter_table("t_declaration", "0,1,2,3","1");
	echo '</span>';
?>
<table id="t_declaration" class="sortable">
	<tr>
		<th class=" sorttable_sorted_reverse">
			Date début <?php echo HtmlInput::infobulle(17);?>
		</th>
		<th>
			Date Fin
		</th>
		<th>
			Déclaration
		</th>
		<th>
			Description
		</th>
		<th>
			Date génération
			<span id="sorttable_sortrevind"><img src="image/down.gif"></span>
		</th>
		<th>

		</th>
		<th>

		</th>
	</tr>
	<?php for ($i=0;$i<count($data);$i++) :?>
        <?php $class=($i%2==0)?'class="even"':' class="odd" '; ?>
	<tr id="tr_<?php echo $data[$i]['d_id']?>" <?php echo $class;?> >
		<td sorttable_customkey="<?php echo $data[$i]['fmt_start']?>">

			<?php echo format_date($data[$i]['d_start'])?>
		</td>
		<td sorttable_customkey="<?php echo $data[$i]['fmt_end']?>">
			<?php echo format_date($data[$i]['d_end'])?>
		</td>
		<td>
			<?php echo h($data[$i]['d_title'])?>
		</td>
		<td>
			<?php echo h($data[$i]['d_description'])?>
		</td>
		<td sorttable_customkey="<?php echo $data[$i]['fmt_order_generated']?>">
			<?php echo h($data[$i]['fmt_generated'])?>
		</td>
<?php if  ($data[$i]['type']==1): ?>

		<td  id="mod_<?php echo $data[$i]['d_id']?>">
			<?php echo HtmlInput::anchor("Afficher","",sprintf("onclick=\"rapav_declaration_display('%s','%s','%s','%s')\"",$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$data[$i]['d_id']))?>
		</td>
		<td  id="del_<?php echo $data[$i]['d_id']?>">
			<?php echo HtmlInput::anchor("Efface","",sprintf("onclick=\"rapav_declaration_delete('%s','%s','%s','%s')\"",$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$data[$i]['d_id']))?>
		</td>
<?php endif; ?>
<?php if  ($data[$i]['type']==2): ?>

		<td  id="mod_<?php echo $data[$i]['d_id']?>">
			<?php echo HtmlInput::anchor("Afficher","",sprintf("onclick=\"rapav_listing_display('%s','%s','%s','%s')\"",$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$data[$i]['d_id']))?>
		</td>
		<td  id="del_<?php echo $data[$i]['d_id']?>">
			<?php echo HtmlInput::anchor("Efface","",sprintf("onclick=\"rapav_listing_delete('%s','%s','%s','%s')\"",$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$data[$i]['d_id']))?>
		</td>
<?php endif; ?>                
	</tr>
	<?php endfor; ?>
</table>
</div>
<div id="declaration_display_div">

</div>