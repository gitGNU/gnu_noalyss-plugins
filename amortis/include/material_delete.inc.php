<?php 

require 'class_amortissement_sql.php';
echo HtmlInput::title_box('Bien à amortir',$t);
$amrt=new Amortissement_Sql($cn,$a_id);
$amrt->load();
echo '<h2 class="notice">'._('Effacé').'</h2>';
$fiche = new Fiche($cn,$amrt->f_id);
echo $fiche->getName()." ".$fiche->get_quick_code();
$amrt->delete();
echo '<p style="text-align:center">';
echo HtmlInput::button_close($t);
echo '</p>';