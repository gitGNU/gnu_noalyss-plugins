<?php 
$cn->exec_sql('delete from amortissement.amortissement where a_id=$1',
	      array($a_id));
echo HtmlInput::anchor_close($t);
echo h2info('Bien à amortir');
echo '<h2 class="notice">'.'Effacé'.'</h2>';
echo HtmlInput::button('close','Fermer',"onclick=\"removeDiv('$t');\"");
?>