<?php
/**
 * Liste tous les budgets inclus
 * @see Budget::to_list
 */
?>

<table class="result">
    <tr>
        <th> Nom </th>
        <th> Date début </th>
        <th> Date Fin </th>
        <th> Montant </th>
    </tr>
<?
for ($i=0;$i<count($array);$i++):
    $class=($i%2==0)?' class="evend" ':' class="odd" ';
    $str_js=sprintf(" onclick=\"budget_detail('%s','%s','%s','%s')\" ",
            $_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$array[$i]['b_id']);
    $js=HtmlInput::anchor("Détail",$str_js);
?>
    <tr <?=$class?> >
        <td>
            <?=$array[$i]['b_name']?>
        </td>
         <td>
            <?=$array[$i]['str_start']?>
        </td>
        <td>
            <?=$array[$i]['str_end']?>
        </td>
        <td>
            <?=nbm($array[$i]['b_amount'])?>
        </td>
        <td>
            <?=$js?>
        </td>
    </tr>  
<?
endfor;
?>
    
</table>
<div id="divbuddetail">

</div>