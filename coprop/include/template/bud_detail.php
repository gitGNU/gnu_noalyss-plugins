<?php
/**
 * @file montre les détails d'un budget
 * @see Budget::detail()
 */
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
