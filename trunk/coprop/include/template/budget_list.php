<?php
/**
 * Liste tous les budgets inclus
 * @see Budget::to_list
 */
?>

<table class="result">
    <tr>
        <th> Nom </th>
        <th> Exercice </th>
        <th> Type de budget </th>
        <th style="text-align: right"> Montant </th>
    </tr>
<?php 
for ($i=0;$i<count($array);$i++):
    $class=($i%2==0)?' class="evend" ':' class="odd" ';
    $str_js=sprintf(" onclick=\"budget_detail('%s','%s','%s','%s');  \"",
            $_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$array[$i]['b_id']);
    $js=HtmlInput::anchor("Détail","",$str_js);
	$str_js_del=sprintf(" onclick=\"budget_remove('%s','%s','%s','%s');\" ",
            $_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$array[$i]['b_id']);
    $js_del=HtmlInput::anchor("Effacer","",$str_js_del);
?>
    <tr id="row<?php echo $array[$i]['b_id']?>" <?php echo $class?> >
        <td>
            <?php echo $array[$i]['b_name']?>
        </td>
         <td>
            <?php echo $array[$i]['b_exercice']?>
        </td>
        <td>
            <?php echo $array[$i]['str_type']?>
        </td>
        <td CLASS="num">
            <?php echo nbm($array[$i]['b_amount'])?>
        </td>
        <td  id="col1<?php echo $array[$i]['b_id']?>">
            <?php echo $js?>
        </td>
        <td  id="col2<?php echo $array[$i]['b_id']?>">
            <?php echo $js_del?>
        </td>
    </tr>
<?php 
endfor;
?>

</table>
