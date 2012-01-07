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
<? for ($i=0;$i < count($a_input);$i++):?>
    <tr>
        <td>
            <?=$a_input[$i]['card']?>
        </td>
        <td>
            <?=$a_input[$i]['card_label']?>
        </td>
        <td>
            <?=$a_input[$i]['key']?>

        </td>

        <td>
            <?=$a_input[$i]['amount']?>

        </td>
    </tr>

<? endfor?>

</table>
<p>
	Total <span id="sbud_total"><?=nbm($bud_amount)?></span>
</p>
<p>
Différence <span id="span_diff"></span>
</p>