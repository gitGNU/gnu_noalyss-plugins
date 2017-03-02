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

extract($_GET, EXTR_SKIP);
$cn=Dossier::connect();
$html='';$extra='';$ctl='';
$dossier_id=Dossier::id();
$r="";
switch($act) {
    case 'set_suggest':
    ////////////////////////////////////////////////////////////////////
    // set the selection coming from display_suggest
    ////////////////////////////////////////////////////////////////////
        $id=HtmlInput::default_value_get("id", 0);
        $suggest_id=HtmlInput::default_value_get("suggest_id",0);
       // Retrieve info
        $a_suggest=$cn->get_array("select 
                a.id,
                a.jr_id,
                b.jr_internal,
                a.f_id,
                (select ad_value from fiche_detail where ad_id=23 and f_id=a.f_id) as qcode
            from 
                jrn as b 
                join importbank.suggest_bank as a on (a.jr_id=b.jr_id)
            where 
                a.id=$1", array($suggest_id));
        if ( count($a_suggest)==1) 
        {
            $cn->exec_sql("
            update importbank.temp_bank set status='W',tp_rec=$1,f_id=$2
            where
            id=$3
                ",
                    array
                        (
                        $a_suggest[0]['jr_id'],
                        $a_suggest[0]['f_id'],
                        $id)
                    );
            $a_json=array('tiers'=>HtmlInput::card_detail($a_suggest[0]['qcode']),
                'concop'=>HtmlInput::detail_op($a_suggest[0]['jr_id'],$a_suggest[0]['jr_internal']),
                'status'=>'<span style=\"color:red;background-color:white\">Attente</span>'
                );
            echo json_encode($a_json);
        } else {
            $a_json=array('tiers'=>"",
                'concop'=>"",
                'status'=>'Error'
                );
            echo json_encode($a_json);
            
        }
        return;
        break;
    case 'display_suggest':
    ////////////////////////////////////////////////////////////////////
    // If several rows are found for an import bank (temp_bank) then 
    // display the different possibilities
    ////////////////////////////////////////////////////////////////////
        $id=HtmlInput::default_value_get("id", 0);
        $plugin_code=HtmlInput::default_value_get('plugin_code',"");
        echo HtmlInput::title_box(_("Suggestion"), "display_suggest_box");
        /*
         * Display operation
         */
        $bi_sql=new Temp_Bank_Sql($cn,$id);
        $date=_('date');
        $label=_('Libellé');
        $other_info=_("Autre information");
        $amount=_('Montant');
        $str_tiers=_('Tiers');
        echo "<table>";
        echo "<tr>";
        echo td($date);
        echo td($bi_sql->tp_date);
        echo "</tr>";
        echo "<tr>";
        echo td($str_tiers);
        echo td($bi_sql->tp_third);
        echo "</tr>";
        echo "<tr>";
        echo td($label);
        echo td($bi_sql->libelle);
        echo "</tr>";
        echo "<tr>";
        echo td($amount);
        echo td($bi_sql->amount);
        echo "</tr>";
        
        echo "<tr>";
        echo td($other_info);
        echo td($bi_sql->tp_extra);
        echo "</tr>";
        
        echo "            </table>";
        $a_suggest=$cn->get_array("select 
                a.id,
                a.jr_id,
                b.jr_internal,
                a.f_id,
                b.jr_date,
                b.jr_pj_number,
                b.jr_comment,
                (select ad_value from fiche_detail where ad_id=23 and f_id=a.f_id) as qcode
            from 
                jrn as b 
                join importbank.suggest_bank as a on (a.jr_id=b.jr_id)
            where 
                temp_bank_id=$1", array($id));
        $nb_asuggest=count($a_suggest);
        echo "<ul style=\"list-style:none\">";
        for ($i=0;$i<$nb_asuggest;$i++) {
            echo '<li>'.
                    HtmlInput::button("get{$i}", _('choisir'), sprintf("onclick=\"select_suggest('%s','%s','%s','%s')\"",$dossier_id,$plugin_code,$id,$a_suggest[$i]['id'])).
                    h($a_suggest[$i]['jr_date']).  " " .      
                    HtmlInput::card_detail($a_suggest[$i]['qcode']).
                    h($a_suggest[$i]['jr_pj_number']).  " " .      
                    HtmlInput::detail_op($a_suggest[$i]['jr_id'], $a_suggest[$i]['jr_internal'])." ".
                    h($a_suggest[$i]['jr_comment'])." " .        
                            "</li>";
        }
        echo '</ul>';
        echo '<p style="text-align:center">';
        echo HtmlInput::button_close("display_suggest_box");
        echo '</p>';
        return;
        break;
    case 'check_all':
        /////////////////////////////////////////////////////////////////////
        // Check all the rows or unchecked
        /////////////////////////////////////////////////////////////////////
        require_once 'include/class_import_bank.php';
        $import_id=HtmlInput::default_value_get("import_id", 0);
        $checked=HtmlInput::default_value_get("checked", 0);
        $status=HtmlInput::default_value_get("status", 0);
        $sql=Import_Bank::convert_status_sql($status);
        $cn->exec_sql("
            update importbank.temp_bank set is_checked = $2
            where import_id=$1 $sql",
                array($import_id,$checked));
        $r="ok";
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
        $status=HtmlInput::default_value_get("status", 0);
        echo '<form method="get" id="display_tiers_frm" onsubmit="selected_set_tiers(\'display_tiers_frm\');return false">';
        echo HtmlInput::array_to_hidden(array("ac","gDossier","import_id","plugin_code"), $_GET);
        echo HtmlInput::hidden("sa", "purge");
        echo HtmlInput::hidden("sb", "list");
        echo HtmlInput::hidden("id", $import_id);
        echo HtmlInput::hidden("form_action2", "selected_record");
        echo HtmlInput::hidden("form_action", "selected_record");
        echo HtmlInput::hidden("select_action", "1");
        echo HtmlInput::hidden("fil_status", $status);
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
	  $f_id="";
          $concerned=HtmlInput::default_value_get("e_concerned".$id, NULL);
          $concop_json="";
          $tiers_json="";
          if ($concerned == "" ) $concerned=null;
          // If we introduce a qcode , find the f_id 
	  if ($_GET['fiche'.$id] != '')
	    {
	      $f_id=$cn->get_value('select f_id from fiche_Detail
					where
					ad_value=upper(trim($1)) and ad_id=23',array($_GET['fiche'.$id]));
              $status='W';
              $msg_json='<span style=\"color:red;background-color:white\">Attente</span>';
              $tiers_json=HtmlInput::card_detail(trim(strtoupper($_GET['fiche'.$id])));
              if ($concerned!=null)
                {
                    $internal=$cn->get_value("select jr_internal from jrn where jr_id=$1",
                            array($_GET['e_concerned'.$id]));
                    if ($internal!="")
                    {
                        $concop_json=HtmlInput::detail_op($_GET['e_concerned'.$id],
                                        $internal);
                    }
                }
            }
	  if ($f_id == '') {
              $f_id=null;
              $status='N';
              $msg_json='<span style=\"color:red;background-color:white\">Nouveau</span>';
          } 
          
	  $bi_sql=new Temp_Bank_Sql($cn,$id);
	  $bi_sql->f_id=$f_id;
	  $rec=$_GET['e_concerned'.$id];
	  $bi_sql->tp_rec=(trim($rec) != '')?trim($rec):null;
	  $bi_sql->status=$status;
	  $bi_sql->libelle=$_GET['libelle'];
	  $bi_sql->amount=$_GET['amount'];
	  $bi_sql->tp_extra=$_GET['tp_extra'];
	  $bi_sql->tp_third=$_GET['tp_third'];
	  $bi_sql->tp_date=$_GET['tp_date'];

	  $bi_sql->update();
	      
	  $msg="Attente";
	  $bi->show_item($ctl);
          $a_extra=array("id"=>$id,"msg"=>$msg_json,"tiers"=>$tiers_json,"concop"=>$concop_json);
          $extra=json_encode($a_extra);
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