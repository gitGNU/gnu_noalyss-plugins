<?php
global $tab;
function show($p_code,$p_desc) {
	global $cn,$tab;
	echo "<h2>".h($p_code)." ".$p_desc."</h2>";
	$plugin_code=$_REQUEST['plugin_code'];
	$dossier=Dossier::id();
	$a_code=$cn->get_array("
			select pi_id, pc.tva_id,tva_label,tva_comment,tva_rate, pcm_val
			from tva_belge.parameter_chld as pc
			left join tva_rate as tv on (pc.tva_id=tv.tva_id)
			where pcode=$1 order by pi_id",
			array($p_code));
	if (sizeof($a_code) == 0) {
		echo '<span class="notice" style="display:block">Aucun paramètre donné </span>';

		echo HtmlInput::button("add_param","Ajout paramètre","onclick=\"show_addparam('$p_code','$plugin_code','$dossier','$tab');\"");
		return;
	}
	$n_max=sizeof($a_code);
	echo "<table class=\"result\">";
	echo "<tr>";
	echo th("Code TVA");
	echo th("Label");
	echo th("Description");
	echo th("Taux");
	echo th("utilisant le poste comptable");
	echo "</tr>";
	for ($i=0;$i<$n_max;$i++)
	{
		echo "<tr>";
		echo td($a_code[$i]['tva_id']);
		echo td($a_code[$i]['tva_label']);
		echo td($a_code[$i]['tva_comment']);
		echo td($a_code[$i]['tva_rate']);
		echo td("Poste comptable :".$a_code[$i]['pcm_val']);
		echo '<td>';
		echo '<form id="f'.$a_code[$i]['pi_id'].'" method="POST">';
		echo HtmlInput::hidden("pi_id",$a_code[$i]['pi_id']);
		echo HtmlInput::hidden("tab",$tab);
		echo HtmlInput::anchor("Effacer","","onclick=\"if ( confirm('Vous confirmez?')) { $('f".$a_code[$i]['pi_id']."').submit(this);} else {return false;}\"");
		echo "</form>";
		echo '</td>';
		echo "</tr>";
	}
	echo "</table>";
	echo HtmlInput::button("add_param","Ajout paramètre","onclick=\"show_addparam('$p_code','$plugin_code','$dossier','$tab');\"");
}
?>
<?php 
	$tab_default=(isset ($_REQUEST['tab']))?$_REQUEST['tab']:'opout';
?>
<a href="javascript:void(0)" class="line" onclick="tva_show_param('opout')">Opération à la sortie</a>&nbsp;
<a href="javascript:void(0)" class="line" style="" onclick="tva_show_param('opin')">Opération à l'entrée </a>&nbsp;
<a href="javascript:void(0)" class="line" onclick="tva_show_param('tvadue')">TVA Due</a>&nbsp;
<a href="javascript:void(0)" class="line" onclick="tva_show_param('tvaded')">TVA Déductible</a>&nbsp;
<a href="javascript:void(0)" class="line" onclick="tva_show_param('lintra')">Listing Client Intracomm.</a>&nbsp;
<a href="javascript:void(0)" class="line" onclick="tva_show_param('assujetti')">Listing Client assujetti.</a>&nbsp;
<a href="javascript:void(0)" class="line" onclick="tva_show_param('divers')">Divers</a>
<form method="POST">
	<?php 
	echo HtmlInput::hidden('tab',$tab_default);
	echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','sa'));
	?>
<div style="display:none" id="opout">
<h1><?php echo _("Opération à la sortie");?></h1>
<?php $tab="opout";?>
<?php echo show("GRIL00",_("Grille 00 : opérations soumises à un régime particulier"))?>
<?php echo show("GRIL01",_("Grille 01 : Opérations pour lesquelles la TVA est due (6%)"))?>
<?php echo show("GRIL02",_("Grille 02 : Opérations pour lesquelles la TVA est due (12%)"))?>
<?php echo show("GRIL03",_("Grille 03 : Opérations pour lesquelles la TVA est due (21%)"))?>
<?php echo show("GRIL44",_("Grille 44 : Opérations pour lesquelles la TVA étrangère est due par le cocontractant"))?>
<?php echo show("GRIL45",_("Grille 45 : Opérations pour lesquelles la TVA est due par le cocontractant"))?>
<?php echo show("GRIL46",_("Grille 46 : Livraisons intracommunautaires exemptées effectuées en Belgique et ventes ABC"))?>
<?php echo show("GRIL47",_("Grille 47 :Autres opérations exemptées et autres opérations effectuées à l’étranger"))?>
<?php echo show("GRIL48",_("Grille 48 : Opérations relatives aux notes de crédit des grilles 44 et 48"))?>
<?php echo show("GRIL49",_("Grille 49 : Opérations relatives aux notes de crédit"))?>
</div>
<div style="display:none" id="opin">
	<?php $tab="opin";?>
<h1><?php echo _("Opération à l'entrée");?></h1>
<?php echo show("GRIL81",_("Grille 81 : Opération sur les marchandises, matières premières..."))?>
<?php echo show("GRIL82",_("Grille 82 : Opération sur les services et biens divers"))?>
<?php echo show("GRIL83",_("Grille 83 : Opération sur les biens d'investissements"))?>
<?php echo show("GRIL84",_("Grille 84 : Montant des notes de crédit reçues et des corrections négatives relatif aux opérations inscrites en grilles 86 et 88"))?>
<?php echo show("GRIL85",_("Grille 85 : Montant des notes de crédit reçues et des corrections négatives relatif aux autres opérations du cadre III  "))?>
<?php echo show("GRIL86",_("Grille 86 : Acquisitions intracommunautaires effectuées en Belgique et ventes ABC  "))?>
<?php echo show("GRIL87",_(" Autres opérations à l'entrée pour lesquelles la T.V.A. est due par le déclarant "))?>
<?php echo show("GRIL88",_("Services intracommunautaires avec report de perception"))?>
</div>
<div style="display:none" id="tvadue">
<?php $tab="tvadue";?>
<h1><?php echo _("TVA Due");?></h1>
<?php echo show("GRIL54",_("Grille 54 : tva due sur opération grille 01,02 et 03"))?>
<?php echo show("GRIL55",_("Grille 55 : tva due sur opération grille 86 et 88"))?>
<?php echo show("GRIL56",_("Grille 56 : tva due sur opération grille 87"))?>
<?php echo show("GRIL57",_("Grille 57 :T.V.A. relative aux importations avec report de perception "))?>
<?php echo show("GRIL61",_("Grille 61 :Diverses régularisations T.V.A. en faveur de l'Etat"))?>
<?php echo show("GRIL63",_("Grille 63 :T.V.A. à reverser mentionnée sur les notes de crédit reçues"))?>
</div>
<div style="display:none" id="tvaded">
	<?php $tab="tvaded";?>
<h1> <?php echo _("TVA Déductible");?></h1>
<?php echo show("GRIL59",_("Grille 59 : taxe déductible"))?>
<?php echo show("GRIL62",_("Grille 62 : Diverses régularisations T.V.A. en faveur du déclarant"))?>
<?php echo show("GRIL64",_("Grille 56 : T.V.A. à récupérer mentionnée sur les notes de crédit délivrées "))?>
</div>
<div style="display:none" id="divers">
	<?php $tab="divers";?>
<fieldset>
<legend><?php echo _('Divers')?></legend>


<TABLE class="result">
<TR>
	<TH><?php echo _('Poste comptable')?></TH>
	<TH><?php echo _('Description')?></TH>
	<th></th>
</TR>
<TR>

	<TD>
	<?php 
	$atva=$cn->get_value("select pcm_val from tva_belge.parameter_chld where pcode='ATVA'");
	$ip_tva=new IPoste('ATVA',$atva);
	$ip_tva->set_attribute('gDossier',Dossier::id());
	$ip_tva->set_attribute('jrn',0);
	$ip_tva->set_attribute('account','ATVA');
	$ip_tva->set_attribute('label','ATVA_label');
	echo $ip_tva->input();
	$lib=$cn->get_value("select pcm_lib from tmp_pcmn where pcm_val=$1",array($atva));
	?>
	</TD>
	<TD>
		<SPAN ID="ATVA_label"><?php echo h($lib)?></SPAN>
	</td>
	<?php echo td(_("Poste comptable utilisé pour les avances faites à la TVA"))?>
</tr>
<TR>
	<TD>
	<?php 
	$crtva=$cn->get_value("select pcm_val from tva_belge.parameter_chld where pcode='CRTVA'");
	$ip_tva=new IPoste('CRTVA',$crtva);
	$ip_tva->set_attribute('gDossier',Dossier::id());
	$ip_tva->set_attribute('jrn',0);
	$ip_tva->set_attribute('account','CRTVA');
	$ip_tva->set_attribute('label','CRTVA_label');
	echo $ip_tva->input();
	$lib=$cn->get_value("select pcm_lib from tmp_pcmn where pcm_val=$1",array($crtva));
	?>
	</TD>
	<TD>
		<SPAN ID="CRTVA_label"><?php echo h($lib)?></SPAN>
	</td>
<?php echo td(("Poste comptable utilisé pour les créances sur la  TVA"))?>
</tr>
<TR>
<TD>
	<?php 
	$dttva=$cn->get_value("select pcm_val from tva_belge.parameter_chld where pcode='DTTVA'");
	$ip_tva=new IPoste('DTTVA',$dttva);
	$ip_tva->set_attribute('gDossier',Dossier::id());
	$ip_tva->set_attribute('jrn',0);
	$ip_tva->set_attribute('account','DTTVA');
	$ip_tva->set_attribute('label','DTTVA_label');
	echo $ip_tva->input();
	$lib=$cn->get_value("select pcm_lib from tmp_pcmn where pcm_val=$1",array($dttva));
	?>
	</TD>
	<TD>
		<SPAN ID="DTTVA_label"><?php echo h($lib)?></SPAN>
	</td>
	<?php echo td(_("Poste comptable utilisé pour les dettes envers la TVA"))?>
</tr>

</TABLE>
	<?php echo HtmlInput::submit("save_misc","Sauver")?>
</fieldset>
</div>
<div style="display:none" id="lintra">
	<?php $tab="lintra";?>
<fieldset><legend>Listing intracommunautaires</legend>
<?php echo show("CLINTRA",_("Code TVA pour clients  Intracommunataire"))?>
</fieldset>
</div>
	<div style="display:none" id="assujetti">
	<?php $tab="assujetti";?>
<fieldset><legend>Listing Client nationaux</legend>
<?php echo show("ASSUJETTI",_("Code TVA pour clients  nationaux"))?>
</fieldset>
</div>
	</form>
<script charset="UTF-8" lang="javascript">
	tva_show_param('<?php echo $tab_default?>');
	</script>
