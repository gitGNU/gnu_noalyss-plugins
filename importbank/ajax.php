<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once('include/class_bank_item.php');
require_once('bank_constant.php');

extract($_GET);
$cn=Dossier::connect();
$html='';$extra='';$ctl='';
switch($act) {
    case 'set_tiers':
        var_dump($_GET);
        return;
    case 'check_all':
        /////////////////////////////////////////////////////////////////////
        // Check all the rows or unchecked
        /////////////////////////////////////////////////////////////////////
        $import_id=HtmlInput::default_value_get("import_id", 0);
        $checked=HtmlInput::default_value_get("checked", 0);

        $cn->exec_sql("
            update importbank.temp_bank set is_checked = $2
            where import_id=$1",
                array($import_id,$checked));
        break;
    case 'save_check':
        ////////////////////////////////////////////////////////////////////
        // Save the checked row into the table temp_bank
        ////////////////////////////////////////////////////////////////////
        $id=HtmlInput::default_value_get("row_id", 0);
        $state=HtmlInput::default_value_get("state", 0);
        if ($id == 0 )return;
        if ( $state == 0 ) {
            //upate in temp_bank
            $cn->exec_sql("update importbank.temp_bank set is_checked = 0 where id= $1  ",
                    array($id));
        } else {
            $cn->exec_sql("update importbank.temp_bank set is_checked = 1 where id= $1  ",
                    array($id));
        }
        return;
    case 'display_tiers':
        ////////////////////////////////////////////////////////////////////
        // Choose a tiers and set it for several operations
        ////////////////////////////////////////////////////////////////////
        echo "<div>";
        // count the number of rows selected 
        $import_id=HtmlInput::default_value_get("import_id", 0);
        $count=$cn->get_value("select count(*) from importbank.temp_bank where "
                . " is_checked = 1 and import_id = $1",
                array($import_id));
        echo HtmlInput::title_box(_("Choix du tiers pour les opérations sélectionnées"), "select_tiers_div");
        if ( $import_id == 0 || $count == 0) {
            echo '<p class="warning">';
            echo (_("Rien n'est sélectionné"));
            echo '</p>';
            echo '<p>';
            echo HtmlInput::button("cancel", _("Fermer"),
                    " onclick=\"removeDiv('select_tiers_div')\"");
            echo '</p>';
            return;
        } else {
            echo '<p>';
            echo _('Nombre de lignes choisies')." ".$count;
            echo '</p>';
        }
        echo '<form method="get" id="display_tiers_frm" onsubmit="selected_set_tiers(\'display_tiers_frm\');return false">';
        echo HtmlInput::array_to_hidden(array("ac","gDossier","import_id","plugin_code"), $_GET);
        echo HtmlInput::hidden("sa", "purge");
        echo HtmlInput::hidden("sb", "list");
        echo HtmlInput::hidden("id", $import_id);
        echo HtmlInput::hidden("form_action2", "selected_record");
        echo HtmlInput::hidden("form_action", "selected_record");
        echo HtmlInput::hidden("select_action", "1");
        $w=new ICard();
	$w->jrn=$cn->get_value("select jrn_def_id from importbank.format_bank as a "
                . "join importbank.import as b on (a.id=b.format_bank_id) "
                . "where b.id=$1",array($import_id));
	$w->name='fiche1000';
	$w->extra='filter';
	$w->typecard='deb';
	$w->set_dblclick("fill_ipopcard(this);");
       	$w->set_attribute('ipopup','ipopcard');
	$w->set_attribute('label','e_third');
	$w->set_attribute('typecard','deb');
	$w->set_callback('filter_card');
	$w->set_function('fill_data');
	$w->set_attribute('inp','fiche');
        $w->autocomplete=1;
        $w->choice="choice_suggest";
        $w->choice_create=0;
        echo HtmlInput::hidden('p_jrn',$w->jrn);
        echo $w->input();
        echo $w->search();
        ?>
<span id="e_third"></span>
<ul style="list-style: none">
    <li style="display:inline;"><?php echo HtmlInput::submit("set_selected", _("Confirmer"));?></li>
    <li style="display:inline;"><?php echo HtmlInput::button("cancel", _("Fermer")," onclick=\"removeDiv('select_tiers_div')\"");?></li>
</ul>
</div>
<div id="div_suggest" style="position:float;float:left;height:30em">
        <h2><?php echo "Suggestion";?></h2>
        <div id="choice_suggest" class="autocomplete_fixed" style="position: static;height:auto" >
            
        </div>
    </div>

<?php        echo '</form>';
        return;
case 'show':
  /*
   * Show detail operation and let you save it or remove it
   *array (
	  'gDossier' => '77',
	  'plugin_code' => 'IMPBANK',
	  'act' => 'show',
          'id' => '15967',
	  'ctl' => 'div15967',
	  )
   */
  ob_start();
  $ctl=$_GET['ctl'];
  $bi=new Bank_Item($id);
  $r='';
  global $msg;
  $msg='';

  if (  isset($_GET['remove'] ))
    {
      $msg="Opération a effacer";
      $bi->show_delete($ctl);

      $bi_sql=new Temp_Bank_Sql($cn,$id);
      $bi_sql->status='D';
      $bi_sql->update();
      $extra='{"id":"'.$id.'","msg":"<span style=\"color:red\">Effacer</span>"}';

    }
  else   if (  isset($_GET['recup'] ))
    {
      $msg="Opération récupérée";
      $bi_sql=new Temp_Bank_Sql($cn,$id);
      $bi_sql->status='N';
      $bi_sql->update();
      $bi->show_item($ctl);
      $extra='{"id":"'.$id.'","msg":"<span style=\"color:red\">Récupérer</span>"}';

    }

    else 
      if (isset($_GET['save']))
	{
	  
	  if ($_GET['fiche'.$id] != '')
	    {
	      $f_id=$cn->get_value('select f_id from fiche_Detail
					where
					ad_value=upper(trim($1)) and ad_id=23',array($_GET['fiche'.$id]));
	    }
	  if ($f_id == '') $f_id=null;
	  $bi_sql=new Temp_Bank_Sql($cn,$id);
	  $bi_sql->f_id=$f_id;
	  $rec=$_GET['e_concerned'.$id];
	  $bi_sql->tp_rec=(trim($rec) != '')?trim($rec):null;
	  $bi_sql->status='W';
	  $bi_sql->libelle=$_GET['libelle'];
	  $bi_sql->amount=$_GET['amount'];
	  $bi_sql->tp_extra=$_GET['tp_extra'];
	  $bi_sql->tp_third=$_GET['tp_third'];
	  $bi_sql->tp_date=$_GET['tp_date'];

	  $bi_sql->update();
	      
	  $msg="Attente";
	  $bi->show_item($ctl);
	  $extra='{"id":"'.$id.'","msg":"<span style=\"color:red;background-color:white\">Attente</span>"}';
	}
      else
	$bi->show_item($ctl);
  $r=ob_get_contents();
  ob_end_clean();
}

$html=escape_xml($r);
$extra=escape_xml($extra);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$html</code>
<extra>$extra</extra>
</data>
EOF;
?>