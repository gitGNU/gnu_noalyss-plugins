<?php

$ledger=new Acc_Ledger($cn,0);
$search_box=$ledger->search_form('ALL',1);
echo '<div class="content">';

echo '<form method="GET">';
echo $search_box;
echo HtmlInput::submit("viewsearch",_("Recherche"));
echo HtmlInput::extension();
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
echo '</form>';

/*
 * Change accounting
 */
if (isset($_POST['chg_poste']))
  {
       change_accounting($cn);
  }
/*
 * Change card
 */
if (isset($_POST['chg_card']))
  {
    change_card($cn);
  }
/*
 * Change ledger
 */
if (isset($_POST['chg_ledger']))
  {
    change_ledger($cn);
  }
/*
 * Change ledger
 */
if (isset($_POST['chg_card_account']))
  {
    change_card_account($cn);
  }

//-----------------------------------------------------
// Display search result
//-----------------------------------------------------
if ( isset ($_GET['viewsearch']))
{

    if (count ($_GET) == 0)
        $array=null;
    else
        $array=$_GET;
    $array['p_action']='ALL';
    list($sql,$where)=$ledger->build_search_sql($array);

    // order
    $sql.=' order by jr_date_order asc,substring(jr_pj_number,\'[0-9]+$\')::numeric asc  ';

    // Count nb of line
    $max_line=$cn->count_sql($sql);
echo HtmlInput::button('accounting_bt','Changer poste comptable','onclick="$(\'div_poste\').show();$(\'div_card\').hide();$(\'div_ledger\').hide();$(\'div_card_account\').hide();"');

echo HtmlInput::button('card_bt','Changer fiche','onclick="$(\'div_poste\').hide();$(\'div_card\').show();$(\'div_ledger\').hide();$(\'div_card_account\').hide();"');

echo HtmlInput::button('ledger_bt','Déplacement dans Journal','onclick="$(\'div_poste\').hide();$(\'div_card\').hide();$(\'div_ledger\').show();$(\'div_card_account\').hide();"');
echo HtmlInput::button('card_acc_bt','Poste > fiche','onclick="$(\'div_poste\').hide();$(\'div_card\').hide();;$(\'div_card_account\').show();$(\'div_ledger\').hide()"');

    require_once('template/search_view.php');

}

echo '</div>';


?>