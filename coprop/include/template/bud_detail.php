<?php
/**
 * @file montre les détails d'un budget
 */
?>
<table class="result">
    <tr>
        <th>Fiche dépense</th>
        <th>Clef de répartition </th>
        <th>Montant</th>
    </tr>
<? for ($i=0;$i < count($a_input);$i++):?>
    <tr>
        <td>
            <?=$a_input[$i]['card']?>
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
