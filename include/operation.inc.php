<?php

$ledger=new Acc_Ledger($cn,0);
$search_box=$ledger->search_form('ALL',1);
echo '<div class="content">';

echo '<form method="GET">';
echo $search_box;
echo HtmlInput::submit("viewsearch",_("Recherche"));
echo HtmlInput::extension();
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo '</form>';

/*
 * Change accounting
 */
if (isset($_POST['chg_poste']))
  {
       var_dump($_POST);
       change_accounting($cn);
  }
/*
 * Change card
 */
if (isset($_POST['chg_card']))
  {
    var_dump($_POST);
    change_card($cn);
  }
/*
 * Change ledger
 */
if (isset($_POST['chg_ledger']))
  {
    var_dump($_POST);
    change_ledger($cn);
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
    $sql.=' order by jr_date_order asc,substring(jr_pj_number,\'\\\d+$\')::numeric asc  ';

    // Count nb of line
    $max_line=$cn->count_sql($sql);

    require_once('template/search_view.php');

}

echo '</div>';


?>