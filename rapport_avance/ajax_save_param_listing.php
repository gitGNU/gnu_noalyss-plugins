<?php

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
require_once 'class_impress.php';
require_once 'include/class_rapav_listing_param.php';
$formula = new RAPAV_Listing_Param_SQL();
// var_dump($_GET);
switch ($tab)
{
    case 'formula':
        $acc_formula = new RAPAV_Formula_Formula($formula);
        $acc_formula->data->setp('formula', $p_formula);
        if ($acc_formula->verify() == 1)
        {
            $code = 'nok';
            $html = $acc_formula->errcode;
            $lp_id=0;
        } else
        {
            $acc_formula->save($_GET);
            $acc_formula->load();
            $lp_id = $acc_formula->data->getp('lp_id');
            $code = 'ok';
            ob_start();
            echo td($acc_formula->display_code());
            echo td($acc_formula->display_comment());
            $r=$acc_formula->display();
            echo td($r);
            echo td($acc_formula->display_order());
            $html.=ob_get_contents();
            ob_end_clean();
            $html.='<td id="del_' . $lp_id . '">';
            $html.=HtmlInput::anchor("Effacer", "", sprintf("onclick=\"listing_detail_remove('%s','%s','%s','%s')\""
                                    ,  $_REQUEST['gDossier'],$_REQUEST['plugin_code'], $_REQUEST['ac'], $lp_id));
            $html.='</td>';
        }
        break;
    /*  case 'compute_id':
      $acc_compute = new RAPAV_Compute();
      $acc_compute->fp_formula = $form_compute;
      $acc_compute->p_id = $p_id;
      $acc_compute->type_detail = 3;
      $acc_compute->jrn_def_id = null;
      if ($acc_compute->verify() == 1)
      {
      $code = 'nok';
      $html = $acc_compute->errcode;
      } else
      {
      $acc_compute->insert();
      $fp_id = $acc_compute->fp_id;
      $code = 'ok';
      $html = '<td>';
      ob_start();
      $acc_compute->display_row();
      $html.=ob_get_contents();
      ob_end_clean();
      $html.= '</td>';
      $html.='<td id="del_' . $acc_compute->fp_id . '">';
      $html.=HtmlInput::anchor("Effacer", "", sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
      , $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $acc_compute->fp_id));
      $html.='</td>';
      }
      break;
      case 'new_account_id':
      $acc_account = new RAPAV_Account();
      $acc_account->tmp_val = $account_first;
      $acc_account->with_tmp_val = $account_second;
      $acc_account->p_id = $p_id;
      $acc_account->type_detail = 4;
      $acc_account->type_sum_account = $account_sum_type;
      $acc_account->jrn_def_id = $p_ledger;
      $acc_account->date_paid = (isset($p_paid)) ? 1 : 0;
      if ($acc_account->verify() == 1)
      {
      $code = 'nok';
      $html = $acc_account->errcode;
      } else
      {
      $acc_account->insert();
      $fp_id = $acc_account->fp_id;
      $code = 'ok';
      $html = '<td>';
      ob_start();
      $acc_account->display_row();
      $html.=ob_get_contents();
      ob_end_clean();
      $html.= '</td>';
      $html.='<td id="del_' . $acc_account->fp_id . '">';
      $html.=HtmlInput::anchor("Effacer", "", sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
      , $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $acc_account->fp_id));
      $html.='</td>';
      }
      break; */
    case 'new_attribute_id':
        ob_start();
        $attr = new RAPAV_Formula_Attribute($formula, $listing_id);
        $attr->save($_GET);
        $attr->load();
        $lp_id = $attr->data->getp("lp_id");
        $code = 'ok';
        $html = "";
        echo td($attr->display_code());
        echo td($attr->display_comment());
        echo td($attr->display_order());
        echo td($attr->display());
        $html.=ob_get_contents();
        ob_end_clean();
        $html.='<td id="del_' . $lp_id . '">';
        $html.=HtmlInput::anchor("Effacer", "", sprintf("onclick=\"listing_detail_remove('%s','%s','%s','%s')\"",
                                 $_REQUEST['gDossier'], $_REQUEST['plugin_code'], $_REQUEST['ac'], $lp_id));
        $html.='</td>';
        break;
}

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