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
    <?php
/**
 * @file montre les détails d'un budget
 * @see Budget::detail()
 */
global  $g_copro_parameter;

echo HtmlInput::hidden('p_jrn',$g_copro_parameter->journal_appel);
?>
<table class="result">
    <tr>
        <th>Fiche dépense</th>
        <th>Label</th>
        <th>Clef de répartition </th>
        <th>Montant</th>
    </tr>
<?php for ($i=0;$i < count($a_input);$i++):?>
    <tr>
        <td>
            <?php echo $a_input[$i]['card']?>
        </td>
        <td>
            <?php echo $a_input[$i]['card_label']?>
        </td>
        <td>
            <?php echo $a_input[$i]['key']?>

        </td>

        <td>
            <?php echo $a_input[$i]['amount']?>

        </td>
    </tr>

<?php endfor?>

</table>
<p>
	Total <span id="sbud_total"><?php echo nbm($bud_amount)?></span>
</p>
<p>
Différence <span id="span_diff"></span>
</p>