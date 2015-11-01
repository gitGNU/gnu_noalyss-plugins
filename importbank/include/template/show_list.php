<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/*!
 * @file
 * @brief : this file is called from Import_Bank::list_record , it lets you
 * set the tiers, remove, transfer the imported operation
 * @see Import_Bank::list_record
 */
?>
<?php
// variable
$dossier_id=Dossier::id();
$a_selected_action = array(
    array("value"=>0,"label"=>_('--choix--')),
    array("value"=>1,"label"=>_('Donner le tiers')),
    array("value"=>2,"label"=>_('Supprimer le tiers')),
    array("value"=>3,"label"=>_('Supprimer réconciliation automatique')),
    array("value"=>4,"label"=>_('Valider réconciliation automatique')),
    array("value"=>5,"label"=>_('Marquer pour suppression')),
    array("value"=>6,"label"=>_('Enlever le marquage de suppression')),
);
// Duplicate database conx
//
$conx2=clone $cn;
$conx3=clone $cn;

// Prepare SQL for suggest
$suggest_sql=$conx2->prepare('get_suggest','
    select id, temp_bank_id,jr_id,f_id 
    from 
    importbank.suggest_bank
    where
    temp_bank_id=$1');
        
?>
<h2 class="info"> 
    
    <?php echo $array[0]['id']." ".$array[0]['i_date']." ".h($array[0]['format_name'])?>
    <?php echo _('Journal')." ".h($jrn_name) ." ".  _('Fiche ').h($jrn_account)?>


</h2>
<form method="get">

Filtrer : <?php echo $filter->input()?>
<?php echo HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id'))?>
<?php echo HtmlInput::submit('refresh','Recharger')?>
</form>
<form method="get" id="show_list_frm2" onsubmit="return confirm_box('show_list_frm2','Vous confirmez?')">
<?php echo HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id',$filter->name))?>
<?php echo HtmlInput::hidden('form_action2','');?>
<?php echo HtmlInput::submit('delete_record',_('Effacer'),
        ' onclick="$(\'form_action2\').value=\'delete_record\';"');?>
<?php echo HtmlInput::submit('transfer_record',_('Transfèrer'),
        ' onclick="$(\'form_action2\').value=\'transfer_record\';"');?>
<?php echo HtmlInput::submit('suggest_record',_('Reconciliation automatique'),
        ' onclick="$(\'form_action2\').value=\'reconcile_auto\';"');?>
<?php echo HtmlInput::submit('selected_record',_('Opérations sélectionnées'),
        ' onclick="$(\'form_action2\').value=\'selected_record\';"');?>
<?php
$select_action=new ISelect('select_action');
$select_action->value=$a_selected_action;
$select_action->selected=0;
echo $select_action->input();
?>
    
</form>
<?php echo HtmlInput::filter_table('record_tb_id','1,2,3,4,5,6,7',1); ?>
<table id="record_tb_id" class="sortable" >
	<TR>
            <th class="no">
                <?php
                $check_all=new ICheckBox('check_all');
                $check_all->javascript=' onclick = "impb_check_all()"';
                echo $check_all->input();
                ?>
            </th>
	<TH>N° opération</TH>
	<th  class=" sorttable_sorted_reverse" >Date <span id="sorttable_sortrevind">&nbsp;&blacktriangle;</span></th>
	<th>Montant</th>
	<th>Etat</th>
	<th>Tiers</th>
        <th>Contrepartie</th>
        <th>Opération liée</th>
	<th>Libellé</th>
	<th>Extra</th>
</TR>
<?php 
	$gdossier=Dossier::id();
	$plugin_code=$_REQUEST['plugin_code'];
	for ($i=0;$i<Database::num_row($ret);$i++):
            $row=Database::fetch_array($ret,$i);
            $suggest=$conx2->execute('get_suggest',array($row['id']));
            $a_suggest = Database::fetch_all($suggest);
            if ( $a_suggest == false ) {$a_suggest = array();}
            $class=($i%2==0)?' class="even"':'class="odd"';

            $javascript="onclick=\"reconcilie('div${row['id']}','$gdossier','${row['id']}','$plugin_code')\"";

?>
<tr <?php echo $class?> >
<td sorttable_customkey="<?php echo $row['is_checked']?>">
    <?php
    // Display a check button and update automatically in ajax the table
    // temp_checked
    $checked_js = sprintf("onclick=\"impb_check_item(%d,'%s','%s')\"",$dossier_id,$plugin_code,$row['id']);
    $check = new ICheckBox(sprintf('temp_bank%s',$row['id']));
    $check->javascript=$checked_js;
    $check->value=$row['is_checked'];
    $check->set_check(1);
    echo $check->input();
    ?>
</td>
<TD>
<?php echo $row['ref_operation']?>
</TD>

<TD sorttable_customkey="<?php echo $row['tp_date']; ?>">
<?php echo HtmlInput::anchor(format_date($row['tp_date']),"",$javascript)?>
</TD>

<td class="num" sorttable_customkey="<?php echo $row['amount']; ?>">
<?php echo HtmlInput::anchor(nbm($row['amount']),"",$javascript)?>
</td>
<td id="<?php echo 'st'.$row['id']?>" <?php echo Import_Bank::color_status($row['status'])?>  >
<?php echo HtmlInput::anchor($row['f_status'],"",$javascript)?>
</td>

<td>
<?php echo HtmlInput::anchor(h($row['tp_third']),"",$javascript)?>
</td>
<td id="tiers<?php echo $row['id']?>">
    <?php
    if ( $row['f_id'] != "") {
        $fiche=new Fiche($conx3,$row['f_id']);
        $qcode= $fiche->get_quick_code();
        if ( $qcode != null ) {
            echo HtmlInput::card_detail($qcode);
        }
    } else {
        if ( count($a_suggest) == 1) {
            $fiche=new Fiche($conx3,$a_suggest[0]['f_id']);
            $qcode= $fiche->get_quick_code();
            if ( $qcode != null ) {
                echo HtmlInput::card_detail($qcode, _('Valide')."?");
            }
        } else  if (count($a_suggest) > 1 ){
            $text= sprintf(_('Possibles %d'),count($a_suggest));
            $js_suggest=sprintf("onclick=\"display_suggest('%s','%s','%s')\"",
                    $dossier_id,$_REQUEST['plugin_code'],$row["id"]);
            echo HtmlInput::anchor($text, "", $js_suggest);
        }
    }
    ?>
</td>
<td id="concop<?php echo $row['id']?>">
    <?php
    if ( $row['tp_rec'] != "") {
        $a_rec=explode(",", $row['tp_rec']);
        $nb_arec=count($a_rec);
        $virg = "";
        for ($j=0  ; $j < $nb_arec;$j++) {
            $ref=$conx3->get_value('select jr_internal from jrn where jr_id=$1',
                    array($a_rec[$j]));
            echo $virg . HtmlInput::detail_op($a_rec[$j],$ref);
            $virg =  " , ";
        }
    } else {
        if ( count($a_suggest) == 1 ) {
            $ref=$conx3->get_value('select jr_internal from jrn where jr_id=$1',
                    array($a_suggest[0]['jr_id']));
            echo HtmlInput::detail_op($a_suggest[0]['jr_id'],_('Valide?').$ref);
        }
    }
    
    ?>
</td>
<td>
<?php echo HtmlInput::anchor(h($row['libelle']),"",$javascript )?>
</td>



<td >
<?php echo HtmlInput::anchor(h($row['tp_extra']),"",$javascript )?>
</td>
</tr>
<?php 
	endfor;
?>

</table>
<form method="get" id="show_list_frm" onsubmit="return confirm_box('show_list_frm','Vous confirmez?')">
<?php echo HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id',$filter->name))?>
<?php echo HtmlInput::hidden('form_action','');?>
<?php echo HtmlInput::submit('delete_record',_('Effacer'),
        ' onclick="$(\'form_action\').value=\'delete_record\';"');?>
<?php echo HtmlInput::submit('transfer_record',_('Transfèrer'),
        ' onclick="$(\'form_action\').value=\'transfer_record\';"');?>
</form>
<script>
    $('select_action').onchange=function(){
        console.debug(this.value);
        if ( this.value == 1 ) {
            waiting_box();
            this.value=0;
            new Ajax.Request('ajax.php',
            {
                method:'get',
                parameters:{'gDossier':<?php echo $dossier_id;?>,
                            'act':'display_tiers',
                            'plugin_code':'<?php echo $plugin_code;?>',
                            'import_id':<?php echo $array[0]['id'];?>,
                            'ac':'<?php echo $_REQUEST['ac']?>'
                },
                onSuccess:function(req,json){
                    remove_waiting_box();
                    var pos=fixed_position(50,100);
                    // Display a div to select the tiers
                    var div1= { "id":"select_tiers_div",
                        "cssclass":"inner_box",
                        "style":pos,
                        "html":req.responseText
                    };
                    try {
                        add_div(div1);
                        req.responseText.evalScripts();
                    } catch (e) {
                        error_message(e.getMessage);
                    }
                    
                }
            })
        }
    }
    function impb_check_all() {
    waiting_box();
    var check=($('check_all').checked)?1:0;
    new Ajax.Request('ajax.php',{
        method:'get',
        parameters : {'gDossier':<?php echo $dossier_id;?>,
            'act':'check_all',
        'plugin_code':'<?php echo $plugin_code; ?>',
        'import_id':<?php echo $array[0]['id']; ?>,
        'ac':'<?php echo $_REQUEST['ac'] ?>',
        'checked':check
        },
       onSuccess:function(e) {
           var tb=document.getElementById('record_tb_id');
           var a=tb.getElementsByTagName('input');
           remove_waiting_box();
            console.debug(a.length);

            var i=0;
            for (i = 0 ; i < a.length;i++) {
              console.debug(a[i].tagName);
              console.debug(a[i].type);
              console.debug(a[i].id);
              if (a[i].id == 'check_all') {continue;}
              if ( check == 0)   {
                  a[i].checked=false;
            } else {
                  a[i].checked=true;
            }
        }
        }
      });
    }
</script>