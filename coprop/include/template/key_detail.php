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
 * @brief detail of a key
 *included from ajax_add_key or ajax_mod_key
 */
?>
<div class="content" style="margin-left: 20px">
<h1><?=$str_message?></h1>
<h2> Caractéristique</h2>
<table>
	<tr>
		<td>

                        Nom
		</td>
		<td>
			<?=$name->input()?>
		</td>
        </tr>
        <tr>
            <td>
                Total tantième 
            </td>
                <td>
                    <?=$tantieme->input()?>
                </td>
	</tr>
	
</table>
Description
<?=$note->input()?>
<h2>Détail des lots</h2>
<table>
<?
	for ($i=0;$i<count($alot);$i++):
?>
	<tr>
		<td>
			<?=HtmlInput::card_detail($alot[$i]['qcode'],$alot[$i]['name'])?>
			<?=HtmlInput::hidden('f_id[]',$alot[$i]['f_id'])?>
		</td>
                <td>
                    <?=$a_lot[$i]['desc']?>
                </td>
		<td>
			<?
			$num=new INum('part'.$alot[$i]['f_id']);
			$num->value=round($alot[$i]['l_part']);
			echo $num->input();
			?>
		</td>
	</tr>
<?
	endfor;
?>
</table>
Total tantième : <span id="span_tantieme"><?=init_tantieme?></span>
<?
if ( bcsub ($init_tantieme,$tantieme->value) != 0):
?>
<span id="span_diff" style="color:red"><?=bcsub ($init_tantieme,$tantieme->value)?></span>
<?
else:
    ?>
<span id="span_diff" style="color:green"><?=bcsub ($init_tantieme,$tantieme->value)?></span>
<?
endif;

?>
</div>