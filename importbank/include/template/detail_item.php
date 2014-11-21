<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>

<div id="<?php echo $ctl?>">
<?php echo HtmlInput::anchor_close($ctl)?>
<?php
echo h2('Détail opération','class="title"');
	if ($bi->id=='')
	{
		echo h2('Opération effacée','class="notice"');
		return;
	}
echo "<span style=\"float:right\" class=\"notice\">$msg</span>";
?>
<form method="get" onsubmit="save_bank_info(this);return false;">
<?php
echo HtmlInput::request_to_hidden(array('id','ctl','gDossier','plugin_code','act'));
echo HtmlInput::hidden('p_jrn',$bi->jrn_def_id);
?>
    <div style="position:float;float:left; ">
<table>
<TR>
<TD>Date</TD><td><?php echo $date->input()?></td></tr>
<tr>
	<TD>Journal
	</TD>
	<td><span class="highlight"><?php echo $jrn?></span>
	</td>
</tr>
<tr>
	<TD>Contrepartie
	</TD>
	<td><?php echo $w->input()?><?php echo $w->search()?>
	</td>
</tr>
<tr>
    <td></td>
    <td class="highlight">
        <span id="e_third"><?php echo h($name)?></span>
    </td>
</tr>   

<tr><td>n° opération </td><td><?php echo h($bi->ref_operation)?></td></tr>
<tr><TD>
	Tiers
    </TD>
    <td>
	<?php echo $third->input()?>
    </td>
</tr>
<tr>
	<TD>Montant
	</TD>
	<td><?php echo $amount->input()?>
	</td>
</tr>
<tr>
	<TD>Libelle
	</TD>
	<td><?php echo $libelle->input()?>
	</td>
</tr>
<tr>
	<TD>Autre information
	</TD>
	<td><?php echo $extra->input()?>
	</td>
</tr>
<tr>
	<TD>reconciliation
	</TD>
	<td><?php echo $wConcerned->input();?>
	</td>
</tr>
<?php $style=($bi->status == 'E') ? 'style="color:red;font-weight:bold"' : '';?>

<tr>
	<TD>statut</TD>
	<td <?php echo $style?> ><?php echo $status?></td>
</tr>
<?php if ($bi->status != 'D') : ?>
<tr>
	<TD>A effacer</TD>
	<td><?php echo $remove->input();?>
</td>
</tr>
<?php else :?>
<tr>
	<TD>A ne pas effacer</TD>
	<td><?php echo $recup->input();?>
</td>
</tr>
<?php endif; ?>
</table>
<?php echo HtmlInput::submit('save','Sauve');?>
<?php echo HtmlInput::button_close($ctl)?>
    </div>
</form>
    <div id="div_suggest_<?php echo $ctl;?>" style="position:float;float:left;">
        <h2><?php echo "Suggestion";?></h2>
        <div id="choice_suggest<?php echo $ctl;?>" class="autocomplete_fixed" style="position: static;" >
            
        </div>
    </div>
</div>
