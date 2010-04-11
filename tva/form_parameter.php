<?php

function show($p_res,$p_code,$p_desc) {
 for ($i=0;$i<count($p_res);$i++) {
	if ( $p_res[$i]['pcode']==$p_code ) {
		echo '<td>';
		echo HtmlInput::hidden('code[]',$p_code);
		echo "$p_code</td>";
		echo "<td>";
		$text=new IText('value[]',$p_res[$i]['pvalue']);
		$msg="";
		$cn=new Database(dossier::id());
		$tvap=new Tva_Parameter($cn);
		if (strpos($p_res[$i]['pvalue'],',') != 0 ) {
			$aPoste=split(',',$p_res[$i]['pvalue']);
			foreach ($aPoste as $e) {
				if ( $tvap->exist_pcmn($e) ==0 ) {
				$msg='<span class="notice">'.$e._(" n'existe pas dans PCMN")."</span>";
		}
			}
		} else
		if ( $tvap->exist_pcmn($p_res[$i]['pvalue']) ==0 ) {
			$msg='<span class="notice">'.$p_res[$i]['pvalue']._(" n'existe pas dans PCMN")."</span>";
		}
		echo $text->input();
		echo '</td>';
		if ( $p_code != 'ATVA') {
			echo '<td>';
			$text=new IText('account[]',$p_res[$i]['paccount']);
			if (strpos($p_res[$i]['paccount'],',') != 0 ) {
				$aPoste=split(',',$p_res[$i]['paccount']);
				foreach ($aPoste as $e) {
					if ( $tvap->exist_pcmn($e) ==0 ) {
					$msg='<span class="notice">'.$e._(" n'existe pas dans PCMN")."</span>";
			}
				}
			} else
			if ( $tvap->exist_pcmn($p_res[$i]['paccount']) ==0 ) {
				$msg='<span class="notice">'.$p_res[$i]['paccount']._(" n'existe pas dans PCMN")."</span>";
			}
			echo $text->input();
			echo '</td>';
		}
		echo "<td>$p_desc $msg</td>";
		break;
	}
    }
}
?>
<span class="notice">
<?=_('Vous pouvez mettre plusieurs postes comptables séparés par une virgule dans une grille')?>
</span>
<fieldset> <legend><?=_("Opération à l'entrée");?></legend>
<TABLE class="result">
<TR>
	<TH><?=_("code")?></TH>
	<TH><?=_('Poste comptable TVA')?></TH>
	<TH><?=_('Poste comptable Montant')?></TH>
	<TH><?=_('Description')?></TH>
</TR>
<TR>
<?=show($res,"GRIL00",_("Grille 00 : opérations soumises à un régime particulier"))?>
</tr>
<TR>
<?=show($res,"GRIL01",_("Grille 01 : Opérations pour lesquelles la TVA est due (6%)"))?>
</tr>
<TR>
<?=show($res,"GRIL02",_("Grille 02 : Opérations pour lesquelles la TVA est due (12%)"))?>
</tr>
<TR>
<?=show($res,"GRIL03",_("Grille 03 : Opérations pour lesquelles la TVA est due (21%)"))?>
</tr>
<TR>
<?=show($res,"GRIL44",_("Grille 44 : Opérations pour lesquelles la TVA étrangère est due par le cocontractant"))?>
</tr>
<TR>
<?=show($res,"GRIL45",_("Grille 45 : Opérations pour lesquelles la TVA est due par le cocontractant"))?>
</tr>
<TR>
<?=show($res,"GRIL46",_("Grille 46 : Livraisons intracommunautaires exemptées effectuées en Belgique et ventes ABC"))?>
</tr>
<TR>
<?=show($res,"GRIL47",_("Grille 47 :Autres opérations exemptées et autres opérations effectuées à l’étranger"))?>
</tr>
<TR>
<?=show($res,"GRIL48",_("Grille 48 : Opérations relatives aux notes de crédit des grilles 44 et 48"))?>
</tr>
<TR>
<?=show($res,"GRIL49",_("Grille 49 : Opérations relatives aux notes de crédit"))?>
</tr>
</TABLE>
</fieldset>

<fieldset><legend> <?=_("Opération à la sortie");?></legend>
<TABLE class="result">
<TR>
	<TH><?=_("code")?></TH>
	<TH><?=_('Poste comptable')?></TH>
	<TH><?=_('Poste comptable Montant')?></TH>
	<TH><?=_('Description')?></TH>
</TR>
<TR>
<?=show($res,"GRIL81",_("Grille 81 : Opération sur les marchandises, matières premières..."))?>
</tr>
<TR>
<?=show($res,"GRIL82",_("Grille 82 : Opération sur les services et biens divers"))?>
</tr>
<TR>
<?=show($res,"GRIL83",_("Grille 83 : Opération sur les biens d'investissements"))?>
</tr>
<TR>
<?=show($res,"GRIL84",_("Grille 84 : Montant des notes de crédit reçues et des corrections négatives relatif aux opérations inscrites en grilles 86 et 88"))?>
</tr>
<TR>
<?=show($res,"GRIL85",_("Grille 85 : Montant des notes de crédit reçues et des corrections négatives relatif aux autres opérations du cadre III  "))?>
</tr>

<TR>
<?=show($res,"GRIL86",_("Grille 86 : Acquisitions intracommunautaires effectuées en Belgique et ventes ABC  "))?>
</tr>

<TR>
<?=show($res,"GRIL87",_(" Autres opérations à l'entrée pour lesquelles la T.V.A. est due par le déclarant "))?>
</tr>

<TR>
<?=show($res,"GRIL88",_("Services intracommunautaires avec report de perception"))?>
</tr>

</TABLE>
</fieldset>
<fieldset>
<legend><?=_('Divers')?></legend>
<TABLE class="result">
<TR>
	<TH><?=_("code")?></TH>
	<TH><?=_('Poste comptable')?></TH>
	<TH><?=_('Description')?></TH>
</TR>
<TR>
<?=show($res,"ATVA",_("Poste comptable utilisé pour les avances faites à la TVA"))?>
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
<tr><Th>
Label 	</Th><th>Taux<th><th> 	Commentaire 	</th><th>Poste</th>
</tr>
<tr><TD>
0%	</TD><td>0.0000	</TD><td>Aucune tva n'est applicable	</TD><td>4114,4514	</td></tr>
<tr><TD>

12%	</TD><td>0.1200	</TD><td>Tva 	</TD><td>4112,4512	</td></tr>
<tr><TD>
21%	</TD><td>0.2100	</TD><td>Tva applicable à tout ce qui bien et service divers	</TD><td>4111,4511</td></tr>
<tr><TD>
6%	</TD><td>0.0600	</TD><td>Tva applicable aux journaux et livres	</TD><td>4113,4513	</td></tr>
<tr><TD>
ART44	</TD><td>0.0000	</TD><td>Opérations pour les opérations avec des assujettis à l\'art 44 Code TVA	</TD><td>41143,45143	</td></tr>
<tr><TD>
COC	</TD><td>0.0000	</TD><td>Opérations avec des cocontractants	</TD><td>41144,45144	</td></tr>
<tr><TD>
EXPORT	</TD><td>0.0000	</TD><td>Tva pour les exportations	</TD><td>41141,45144</td></tr>
<tr><TD>
INTRA	</TD><td>0.0000	</TD><td>Tva pour les livraisons / acquisition intra communautaires	</TD><td>41142,45142</td></tr>
</table>
</span>
</fieldset>