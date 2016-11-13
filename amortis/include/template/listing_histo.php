<?php
/*
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
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
?>
<div class="content" style="width:80%;margin-left:10%">
<!-- <h2 class="info">Historique</h2> -->
<form method="POST">
<?php
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('sb',$_REQUEST['sb']);
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
echo _('Cherche').' : '.HtmlInput::filter_table('amortissement_tb','1,2,3,5,6',1);
?>
<table id="amortissement_tb" class="result">
<tr>
<th><?php echo $header->get_header(0)?></th>
<th><?php echo $header->get_header(1)?></th>
<th style="text-align:right"><?php echo $header->get_header(2)?></th>
<th style="text-align:center"><?php echo $header->get_header(3)?></th>
<th><?php echo $header->get_header(4)?></th>
<th><?php echo $header->get_header(5)?></th>

</tr>
<?php

for ($i=0;$i<count($array);$i++) :
        $class=($i%2==0)?"odd":"even";
	echo '<tr class="'.$class.'">';
	echo td($array[$i]['quick_code']);
	echo td($array[$i]['vw_name']);
	echo td(nbm($array[$i]['h_amount']), 'align="right"');
	echo td($array[$i]['h_year'],'align="center"');

	echo td($array[$i]['h_pj']);
	$msg='';
	if ( $array[$i]['jr_internal'] != '' )
	{
	    $jrid=$cn->get_value('select jr_id from jrn where jr_internal=$1',array($array[$i]['jr_internal']));
            $msg=HtmlInput::detail_op($jrid,$array[$i]['jr_internal']);
        }
	echo td($msg);
   $ic=new ICheckBox('p_sel[]');
   $ic->value=$array[$i]['ha_id'];
   echo td($ic->input());



        echo '</tr>';
endfor;
?>

</table>
<?php echo HtmlInput::submit('remove','Effacer la sélection','onclick="confirm(\'Confirmez Effacement ?\')"')?>
</form>
</div>
