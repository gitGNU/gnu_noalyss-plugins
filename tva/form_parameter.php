<?php

function show($p_code,$p_desc) {
	global $cn;
	echo "<h2>".h($p_code)." ".$p_desc."</h2>";
	$a_code=$cn->get_array("
			select pi_id, pc.tva_id,tva_label,tva_comment,tva_rate, pcm_val
			from tva_belge.parameter_chld as pc
			left join tva_rate as tv on (pc.tva_id=tv.tva_id)
			where pcode=$1 order by pi_id",
			array($p_code));
	if (sizeof($a_code) == 0) {
		echo '<span class="notice" style="display:block">Aucun paramètre donné </span>';
		echo HtmlInput::button("Ajout paramètre","Ajout paramètre");
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
		echo td("Effacer");
		echo td("modifier");
		echo "</tr>";
	}
	echo "</table>";
	echo HtmlInput::button("Ajout paramètre","Ajout paramètre");
}
?>
<h1><?=_("Opération à la sortie");?></h1>
<?=show("GRIL00",_("Grille 00 : opérations soumises à un régime particulier"))?>
<?=show("GRIL01",_("Grille 01 : Opérations pour lesquelles la TVA est due (6%)"))?>
<?=show("GRIL02",_("Grille 02 : Opérations pour lesquelles la TVA est due (12%)"))?>
<?=show("GRIL03",_("Grille 03 : Opérations pour lesquelles la TVA est due (21%)"))?>
<?=show("GRIL44",_("Grille 44 : Opérations pour lesquelles la TVA étrangère est due par le cocontractant"))?>
<?=show("GRIL45",_("Grille 45 : Opérations pour lesquelles la TVA est due par le cocontractant"))?>
<?=show("GRIL46",_("Grille 46 : Livraisons intracommunautaires exemptées effectuées en Belgique et ventes ABC"))?>
<?=show("GRIL47",_("Grille 47 :Autres opérations exemptées et autres opérations effectuées à l’étranger"))?>
<?=show("GRIL48",_("Grille 48 : Opérations relatives aux notes de crédit des grilles 44 et 48"))?>
<?=show("GRIL49",_("Grille 49 : Opérations relatives aux notes de crédit"))?>

<h1><?=_("Opération à l'entrée");?></h1>
<?=show("GRIL81",_("Grille 81 : Opération sur les marchandises, matières premières..."))?>
<?=show("GRIL82",_("Grille 82 : Opération sur les services et biens divers"))?>
<?=show("GRIL83",_("Grille 83 : Opération sur les biens d'investissements"))?>
<?=show("GRIL84",_("Grille 84 : Montant des notes de crédit reçues et des corrections négatives relatif aux opérations inscrites en grilles 86 et 88"))?>
<?=show("GRIL85",_("Grille 85 : Montant des notes de crédit reçues et des corrections négatives relatif aux autres opérations du cadre III  "))?>
<?=show("GRIL86",_("Grille 86 : Acquisitions intracommunautaires effectuées en Belgique et ventes ABC  "))?>
<?=show("GRIL87",_(" Autres opérations à l'entrée pour lesquelles la T.V.A. est due par le déclarant "))?>
<?=show("GRIL88",_("Services intracommunautaires avec report de perception"))?>

<h1><?=_("TVA Due");?></h1>
<?=show("GRIL54",_("Grille 54 : tva due sur opération grille 01,02 et 03"))?>
<?=show("GRIL55",_("Grille 55 : tva due sur opération grille 86 et 88"))?>
<?=show("GRIL56",_("Grille 56 : tva due sur opération grille 87"))?>
<?=show("GRIL57",_("Grille 57 :T.V.A. relative aux importations avec report de perception "))?>
<?=show("GRIL61",_("Grille 61 :Diverses régularisations T.V.A. en faveur de l'Etat"))?>
<?=show("GRIL63",_("Grille 63 :T.V.A. à reverser mentionnée sur les notes de crédit reçues"))?>

<h1> <?=_("TVA Déductible");?></h1>
<?=show("GRIL59",_("Grille 59 : taxe déductible"))?>
<?=show("GRIL62",_("Grille 62 : Diverses régularisations T.V.A. en faveur du déclarant"))?>
<?=show("GRIL64",_("Grille 56 : T.V.A. à récupérer mentionnée sur les notes de crédit délivrées "))?>

<fieldset>
<legend><?=_('Divers')?></legend>
<TABLE class="result">
<TR>
	<TH><?=_('Poste comptable')?></TH>
	<TH><?=_('Description')?></TH>
	<th></th>
</TR>
<TR>

	<TD>
	<?
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
		<SPAN ID="ATVA_label"><?=h($lib)?></SPAN>
	</td>
	<?=td(_("Poste comptable utilisé pour les avances faites à la TVA"))?>
</tr>
<TR>
	<TD>
	<?
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
		<SPAN ID="CRTVA_label"><?=h($lib)?></SPAN>
	</td>
<?=td(("Poste comptable utilisé pour les créances sur la  TVA"))?>
</tr>
<TR>
<TD>
	<?
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
		<SPAN ID="DTTVA_label"><?=h($lib)?></SPAN>
	</td>
	<?=td(_("Poste comptable utilisé pour les dettes envers la TVA"))?>
</tr>

</TABLE>
</fieldset>
<fieldset><legend><?=_('Aide')?></legend>
<span class="notice">
<?=_('Vous pouvez mettre plusieurs postes comptables séparées par une virgule dans une grille')?>
<?=_("Vous devez ajouter dans paramètre->tva les codes TVA nécessaires pour les cocontractants, les opérations à l'export, les opérations intracommunautaire... Ainsi que les postes comptables dans les paramètres->plan comptable")?>
<br>
<?=_('Exemple')?>
<table>
<tr>
<th>code</th>
<Th>
Label 	</Th><th>Taux</th><th> 	Commentaire 	</th><th>Poste</th>
</tr>
<tr>
<td>4</td>
<TD>
0%	</TD><td>0.0000	</TD><td>Aucune tva n'est applicable	</TD><td>4114,4514	</td></tr>
<tr>
<td>2</td>
<TD>

12%	</TD><td>0.1200	</TD><td>Tva 	</TD><td>4112,4512	</td></tr>
<tr>
<td>1</td>
<TD>
21%	</TD><td>0.2100	</TD><td>Tva applicable à tout ce qui bien et service divers	</TD><td>4111,4511</td></tr>
<tr>
<td>3</td>
<TD>
6%	</TD><td>0.0600	</TD><td>Tva applicable aux journaux et livres	</TD><td>4113,4513	</td></tr>
<tr>
<td> ??? </td>
<TD>
ART44	</TD><td>0.0000	</TD><td>Opérations pour les opérations avec des assujettis à l\'art 44 Code TVA	</TD><td>41143,45143	</td></tr>
<tr>
<td> ??? </td>
<TD>
COC	</TD><td>0.0000	</TD><td>Opérations avec des cocontractants	</TD><td>41144,45144	</td></tr>
<tr>
<td>6</td>
<TD>
EXPORT	</TD><td>0.0000	</TD><td>Tva pour les exportations	</TD><td>41141,45144</td></tr>
<tr>
<td>5</td>
<TD>
INTRA	</TD><td>0.0000	</TD><td>Tva pour les livraisons / acquisition intra communautaires	</TD><td>41142,45142</td></tr>
</table>
</span>
</fieldset>
