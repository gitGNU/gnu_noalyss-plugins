<?php

$ret=$cn->exec_sql($sql);
$nb_row=$cn->size();
if ( $nb_row == 0 ) exit();
$checkbox=new ICheckBox('jr_id[]');

?>
<form method="POST" id="form1" onsubmit="return confirm('Vous confirmez?')">
<?
	echo HtmlInput::extension();
	echo HtmlInput::get_to_hidden(array('sa','ac'));
	echo Dossier::hidden();
?>

<div id="div_poste" class="inner_box" style="top:230;margin:5;overflow:visible;display:none;">
	<h2 class="info">Poste comptable </h2>
	<p class="notice">Changera dans les opérations sélectionnées le poste comptable.Attention, cela ne changera pas la fiche, soyez prudent</p>
<?
	$target=new IPoste('tposte');
	$source=new IPoste('sposte');
	$target->set_attribute('gDossier',Dossier::id());
	$target->set_attribute('jrn',0);
	$target->set_attribute('account','tposte');
	$target->set_attribute('label','tposte_label');
	$target->table=0;

	$source->set_attribute('gDossier',Dossier::id());
	$source->set_attribute('jrn',0);
	$source->set_attribute('account','sposte');
	$source->set_attribute('label','sposte_label');
	$source->table=0;
?>
<table>
	<TR><TD>Changer le poste comptable </TD>
	<TD>
		<?=$source->input()?>
 	<td> <span id="sposte_label"></span></td>
	<td>Par le poste comptable</td>
	<td><?=$target->input()?></td>
	<td> <span id="tposte_label"></span>'</td>
	</TR>
	<tr><TD><?=HtmlInput::submit('chg_poste','Changer le poste comptable')?></TD>
<td><?
echo  HtmlInput::button('accounting_hide_bt','Annuler','onclick="$(\'div_poste\').hide();"');
?>
</TD>
</tr>
</table>
</div>

<div id="div_card" class="inner_box" style="top:230;margin:5;overflow:visible;display:none;">
<h2 class="info">Changer  la fiche </h2>
	<p class="notice">Attention, dans les opérations sélectionnées cela changera la fiche et le poste comptable: ce sera celui de la fiche qui sera utilisé, soyez prudent</p>
<?
	$csource=new ICard('csource');
  	$csource->set_attribute('label','csource_label');
	$csource->set_dblclick("fill_ipopcard(this);");
    	$csource->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
			       $csource->name);
    	$csource->set_function('fill_data');
	$csource->set_attribute("typecard",'all');

	$ctarget=new ICard('ctarget');
	$ctarget->typecard='all';
  	$ctarget->set_attribute('label','ctarget_label');
	$ctarget->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
			       $ctarget->name);
	$ctarget->set_dblclick("fill_ipopcard(this);");
	$ctarget->set_attribute("typecard",'all');


?>
<table>
<TR>
	<TD>Changer</TD>
	<TD><?=$csource->input();?></TD>
	<td><?=$csource->search()?></td>
	<td><span id="csource_label"></span></td>
</tr>
<tr>
	<TD>par </TD>

	<TD><?=$ctarget->input()?></TD>
	<td><?=$ctarget->search()?></td>
	<td><span id="ctarget_label"></span></td>
</tr>
</table>
	<? echo HtmlInput::submit('chg_card','Changer la fiche'); ?>
<?
echo  HtmlInput::button('card_hide_bt','Annuler','onclick="$(\'div_card\').hide();"');
?>

</div>

<div id="div_ledger" class="inner_box" style="top:230;margin:5;overflow:visible;display:none;">
<h2 class="info">Déplacer dans un autre journal </h2>
	<p class="notice">Attention, pour les opérations sélectionnées,cela  transfèrera les opérations vers le journal choisi mais il faut que ce journal soit de même type (achat vers achat, vente vers vente...), les pièces justificatives ne seront pas mises à jour, soyez prudent</p>

<?
	$sql="select jrn_def_id, '('||jrn_def_type||') '||jrn_def_name from jrn_def order by jrn_def_name asc";
$aledger=$cn->make_array($sql);
$sledger=new ISelect('sledger');
$sledger->value=$aledger;
$tledger=new ISelect('tledger');
$tledger->value=$aledger;
?>
<p>
Les opérations appartenant au journal <?=$sledger->input()?> vers le journal <?=$tledger->input()?>
</p>
	<? echo HtmlInput::submit('chg_ledger','Déplacer dans le journal'); ?>
<?
echo  HtmlInput::button('ledger_hide_bt','Annuler','onclick="$(\'div_ledger\').hide();"');
?>

</div>
<table class="result">
<?php
echo HtmlInput::extension();
echo Dossier::hidden();
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
?>
<TR>
	<TH>Date</TH>
	<TH>interne</TH>
	<TH>N°pièce</TH>
	<TH>Journal</TH>
	<TH>Libellé</TH>
	<TH class="num">Montant</TH>
	<TH><INPUT TYPE="CHECKBOX" onclick="toggle_checkbox('form1')"></TH>
</TR>
<?
for ($i = 0;$i < $nb_row;$i++) :
	$row=$cn->fetch_array($ret,$i);
	$odd=($i%2==0)?' class="odd"':' class="even"';
	$checkbox->value=$row['jr_id'];
?>




	<TR <?=$odd?> >
	<TD><?=$row['jr_date']?></TD>
	<td><?=HtmlInput::detail_op($row['jr_id'],$row['jr_internal'])?></td>
	<td><?=$row['jr_pj_number']?></td>
	<td><?=$row['jrn_def_name']?></td>
	<td><?=$row['jr_comment']?></td>

<?php
$positive=0;
// check sign for fin
     if (  $row['jrn_def_type'] == 'FIN' )
            {
                $positive = $cn->get_value("select qf_amount from quant_fin where jr_id=$1",
                                                 array($row['jr_id']));
		if ( $cn->count() != 0)
		  $positive=($positive < 0)?1:0;
            }

?>
	<td class="num"><?=( $positive != 0 )?"<font color=\"red\">  - ".nbm($row['jr_montant'])."</font>":nbm($row['jr_montant'])?> </td>
	<td><?=$checkbox->input();?></td>
	</TR>
<?php
endfor;
?>
</table>
</form>
<script type="text/javascript">
new Draggable('div_card',{zindex:2});
new Draggable('div_ledger',{zindex:2});
new Draggable('div_poste',{zindex:2});
</script>