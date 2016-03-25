<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * @file
 * @brief sauve la nouvelle ligne de détail (listing) et renvoie un xml
 * avec code = ok ou nok, p_id pk de formulaire_param et html le code html à afficher : soit une ligne à ajouter à la table
 * soit le message d'erreur à ajouter dans le div
 * 
 * Paramètre entrée
 *    - gDossier
 *    - ac
 *    - plugin_code
 *    - p_id 
 *    - tab valeurs : formula, compute_id, new_account_id, new_attribute_id
 *
 * Pour attribut : 
 *    - attribute_card : numérique ad_id
 *
 */
require_once NOALYSS_INCLUDE.'/lib/class_impress.php';
require_once $g_listing_home.'/include/class_rapav_listing_param.php';
require_once $g_listing_home.'/include/class_rapav_condition.php';
$lp_id = HtmlInput::default_value_get('lp_id', -1);
$formula = new RAPAV_Listing_Param_SQL($lp_id);
$attr="ACCOUNT";
$html = "";
$str_cond="";
switch ($tab)
{
    case 'formula':
        $acc_formula = new RAPAV_Formula_Formula($formula);
        $acc_formula->data->setp('formula', $p_formula);
        if ($acc_formula->verify() == 1)
        {
            $code = 'nok';
            $html = $acc_formula->errcode;
            $lp_id = 0;
        } else
        {
            $acc_formula->save($_GET);
            $acc_formula->load();
            $lp_id = $acc_formula->data->getp('lp_id');
            $code = 'ok';
            ob_start();
            echo td($acc_formula->display_code());
            echo td($acc_formula->display_comment());
            $r = $acc_formula->display();
            echo td($r);
            echo td($acc_formula->display_order());
            $html.=ob_get_contents();
            ob_end_clean();
            
         
        }
        break;
    case 'compute_id':
        $compute = new RAPAV_Formula_Compute($formula);
        $compute->data->setp('formula', $form_compute);
        if ($compute->verify() == 1)
        {
            $code = 'nok';
            $html = $compute->errcode;
            $lp_id = 0;
        } else
        {
            $compute->save($_GET);
            $compute->load();
            $lp_id = $compute->data->getp('lp_id');
            $code = 'ok';
            ob_start();
            echo td($compute->display_code());
            echo td($compute->display_comment());
            $r = $compute->display();
            echo td($r);
            echo td($compute->display_order());
            $html.=ob_get_contents();
            ob_end_clean();
            
            
        }
        break;
    case 'new_account_id':
        $compute = new RAPAV_Formula_Account($formula);
        $compute->data->setp('tmp_val', $p_formula);
        if ($compute->verify() == 1)
        {
            $code = 'nok';
            $html = $compute->errcode;
            $lp_id = 0;
        } else
        {
            $compute->save($_GET);
            $compute->load();
            $lp_id = $compute->data->getp('lp_id');
            $code = 'ok';
            ob_start();
            echo td($compute->display_code());
            echo td($compute->display_comment());
            $r = $compute->display();
            echo td($r);
            echo td($compute->display_order());
            $html.=ob_get_contents();
            ob_end_clean();
           
            
        }
        break;
    case 'new_attribute_id':
        ob_start();
        $attr = new RAPAV_Formula_Attribute($formula);
        $attr->save($_GET);
        $attr->load();
        $lp_id = $attr->data->getp("lp_id");
        $code = 'ok';
        $html = "";
        echo td($attr->display_code());
        echo td($attr->display_comment());
        echo td($attr->display());
        echo td($attr->display_order());
        $html.=ob_get_contents();
        ob_end_clean();
       
       
        $attr="ATTR";
        break;
}
$cond=new RAPAV_Condition();
$a_cond=$cond->load_by_listing_param_id($lp_id);
$nb_cond = count($a_cond);
    $str_cond="<td>";
    $and="";
for ($i=0;$i<$nb_cond;$i++) {
    $str_cond .= $a_cond[$i]->get_condition().$and;
    $and=_(' et ');
}
$str_cond.="</td>";
ob_start();
echo $str_cond;
 echo '<td>';
echo HtmlInput::anchor("Effacer", "", sprintf("onclick=\"listing_detail_remove('%s','%s','%s','%s')\"", $_REQUEST['gDossier'], $_REQUEST['plugin_code'], $_REQUEST['ac'], $lp_id));
echo '</td>';
 echo '<td>';
 $obj = new Rapav_Listing_Param($lp_id);
$obj->button_modify();
echo '</td>';
$html.=ob_get_clean();

//echo $html;exit();
$html = escape_xml($html);

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$code</code>
<html>$html</html>
<lp_id>$lp_id</lp_id>
<l_id>$listing_id</l_id>
</data>
EOF;
?>        