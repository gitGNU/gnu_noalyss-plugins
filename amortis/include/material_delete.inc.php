<?php 
$cn->exec_sql('delete from amortissement.amortissement where a_id=$1',
	      array($a_id));
echo HtmlInput::anchor_close($t);
echo HtmlInput::title_box('Bien à amortir',$t);
echo '<h2 class="notice">'.'Effacé'.'</h2>';