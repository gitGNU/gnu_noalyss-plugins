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
	<?php echo HtmlInput::button('rlist','Retour liste',' onclick="$(\'key_list\').show();$(\'keydetail_div\').hide()"')?>
<h1><?php echo $str_message?></h1>


<h2> Caractéristique</h2>
<table>
	<tr>
		<td>

                        Nom
		</td>
		<td>
			<?php echo $name->input()?>
		</td>
        </tr>
        <tr>
            <td>
                Total tantième
            </td>
                <td>
                    <?php echo $tantieme->input()?>
                </td>
	</tr>

</table>
Description
<?php echo $note->input()?>
<h2>Détail des lots</h2>
<table class="result">
	<tr>
		<th>QuickCode</th>
		<th>Nom</th>
		<th>Description</th>
		<th>Copropriétaire</th>
		<th>Bâtiment</th>
		<th>Montant</th>
	</tr>
<?php 
	for ($i=0;$i<count($alot);$i++):
		if ( $alot[$i]['qcode'] == "" ) continue;
?>
	<tr>
		<td>
			<?php echo HtmlInput::card_detail($alot[$i]['qcode'],$alot[$i]['qcode'],' class="line"')?>
			<?php echo HtmlInput::hidden('f_id[]',$alot[$i]['f_id'])?>
		</td>
		<td>
			<?php echo $alot[$i]['name']?>
		</td>
                <td>
                    <?php echo $alot[$i]['desc']?>
                </td>
				<td>
					<?php 
						$copro=$cn->get_value ("select ad_value
							from fiche_Detail
							where ad_id=1
							and f_id = (select coprop_id::integer from coprop.summary where lot_id=$1)",array($alot[$i]['f_id']));
						echo h($copro);
					?>
				</td>
				<td>
					<?php 
						$batiment=$cn->get_value ("select ad_value
							from fiche_Detail
							where ad_id=1
							and f_id = (select building_id::integer from coprop.summary where lot_id=$1)",array($alot[$i]['f_id']));
						echo h($batiment);
					?>
				</td>
		<td>
			<?php 
			$num=new INum('part'.$alot[$i]['f_id']);
			$num->javascript='onchange="format_number(this,0);compute_key();"';
			$num->value=round($alot[$i]['l_part'],0);
			echo $num->input();
			?>
		</td>
	</tr>
<?php 
	endfor;
?>
</table>
<p>
Total tantièmes : <span id="span_tantieme"><?php echo round($init_tantieme)?></span>
</p>

<p>
  Différence :

<?php 
 bcscale(0);
if ( bcsub ($init_tantieme,$tantieme->value) != 0):
?>
<span id="span_diff" style="color:red"><?php echo bcsub ($init_tantieme,$tantieme->value)?></span>
<?php 
else:
    ?>
<span id="span_diff" style="color:green"><?php echo bcsub ($init_tantieme,$tantieme->value)?></span>
<?php 
endif;
?>

</p>

</div>