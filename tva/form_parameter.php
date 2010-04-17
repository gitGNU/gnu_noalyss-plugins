<?php

function show($p_res,$p_code,$p_desc) {
static $a=0;
$a++;

 for ($i=0;$i<count($p_res);$i++) {
	if ( $p_res[$i]['pcode']==$p_code ) {
		$cn=new Database(dossier::id());
		echo '<td>';
		echo HtmlInput::hidden('code[]',$p_code);
		echo "$p_code</td>";
		echo "<td>";
		if ($p_code != 'ATVA') {
			$text=new ITva_Popup('value['.$i.']');
			$text->value=$p_res[$i]['pvalue'];
			$text->add_label("code".$i,$cn);
			$text->js='onchange="set_tva_label(this);"';

			$text->with_button(true);
			echo $text->input();
		} else {
		echo HtmlInput::hidden('value['.$i.']','');
		}
		$msg="";



		echo '</td>';

		echo '<td>';
		$text=new IPoste('account['.$i.']');
		$text->value=$p_res[$i]['paccount'];
		$text->set_attribute('ipopup','ipop_account');
		$text->set_attribute('gDossier',Dossier::id());
		$text->set_attribute('phpsessid',$_REQUEST['PHPSESSID']);
		$text->set_attribute('jrn',0);
		$text->set_attribute('account','account['.$i.']');
		$tvap=new Tva_Parameter($cn);
		if (strpos($p_res[$i]['paccount'],',') != 0 ) {
			$aPoste=split(',',$p_res[$i]['paccount']);
			foreach ($aPoste as $e) {
				if ( $tvap->exist_pcmn($e) ==0 ) {
					$msg='<span class="notice">'.$e._(" n'existe pas dans PCMN")."</span>";
				} else
					if ( $tvap->exist_pcmn($p_res[$i]['pvalue']) ==0 ) {
						$msg='<span class="notice">'.$p_res[$i]['pvalue']._(" n'existe pas dans PCMN")."</span>";
				}

			}
		}
		echo $text->input();
		echo '</td>';

		echo "<td>$p_desc $msg</td>";
		break;
	}
    }
}

/**
 *@brief show parameters need a TVA code instead of an accounting
 *@param $p_res (result of Tva_Parameter.display
 *@param $p_code (GRILxx)
 *@param $p_desc description of the account
 *@return string with HTML code
 */
function show_account($p_res,$p_code,$p_desc) {
 for ($i=0;$i<count($p_res);$i++) {
	if ( $p_res[$i]['pcode']==$p_code ) {
		echo '<td>';
		echo HtmlInput::hidden('code[]',$p_code);
		echo HtmlInput::hidden('value[]','');
		echo "$p_code</td>";
		echo "<td>";
		// The popup a

		$text=new IPoste('account['.$i.']');
		$text->value=$p_res[$i]['paccount'];
		$text->set_attribute('ipopup','ipop_account');
		$text->set_attribute('gDossier',Dossier::id());
		$text->set_attribute('phpsessid',$_REQUEST['PHPSESSID']);
		$text->set_attribute('jrn',0);
		$text->set_attribute('account','account['.$i.']');

		echo $text->input();
		$cn=new Database(Dossier::id());
		$tvap=new Tva_Parameter($cn);
		$msg='';
		if (strpos($p_res[$i]['paccount'],',') != 0 ) {
			$aPoste=split(',',$p_res[$i]['paccount']);
			foreach ($aPoste as $e) {

				if ( trim($e) != '' && strpos('%',$e) === false && $tvap->exist_pcmn($e) ==0 ) {
					$msg.='<span class="notice">'.$e._(" n'existe pas dans PCMN")."</span>";
				}

			}
		}else
//var_dump(trim($p_res[$i]['paccount']) != '',strpos("%",$p_res[$i]['paccount']) == false ,$tvap->exist_pcmn($p_res[$i]['paccount'])==0);
			if ( trim($p_res[$i]['paccount']) != '' && strpos("%",$p_res[$i]['paccount']) == false && $tvap->exist_pcmn($p_res[$i]['paccount']) ==0 ) {
			$msg.='<span class="notice">'.$p_res[$i]['paccount']._(" n'existe pas dans PCMN")."</span>";
		}
		echo '</td>';
		echo '<td>';
		echo 'Donnez un poste comptable';
		echo '</td>';
		echo "<td>$p_desc $msg</td>";
		break;
	}
    }
}
?>
<span class="notice">
<?=_('Vous pouvez mettre plusieurs postes comptables séparés par une virgule dans une grille')?>
</span>

<fieldset> <legend><?=_("Opération à la sortie");?></legend>
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

<fieldset><legend> <?=_("Opération à l'entrée");?></legend>
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

<fieldset><legend> <?=_("TVA Due");?></legend>
<TABLE class="result">
<TR>
	<TH><?=_("code")?></TH>
	<TH><?=_('Poste comptable')?></TH>
	<TH><?=_('Poste comptable Montant')?></TH>
	<TH><?=_('Description')?></TH>
</TR>
<TR>
<?=show($res,"GRIL54",_("Grille 54 : tva due sur opération grille 01,02 et 03"))?>
</tr>
<TR>
<?=show($res,"GRIL55",_("Grille 55 : tva due sur opération grille 86 et 88"))?>
</tr>
<TR>
<?=show($res,"GRIL56",_("Grille 56 : tva due sur opération grille 87"))?>
</tr>
<TR>
<?=show($res,"GRIL57",_("Grille 57 :T.V.A. relative aux importations avec report de perception "))?>
</tr>
<TR>
<?=show($res,"GRIL61",_("Grille 61 :Diverses régularisations T.V.A. en faveur de l'Etat"))?>
</tr>
<TR>
<?=show($res,"GRIL63",_("Grille 63 :T.V.A. à reverser mentionnée sur les notes de crédit reçues"))?>
</tr>


</TABLE>
</fieldset>
<fieldset><legend> <?=_("TVA Déductible");?></legend>
<TABLE class="result">
<TR>
	<TH><?=_("code")?></TH>
	<TH><?=_('Poste comptable')?></TH>
	<TH><?=_('Poste comptable Montant')?></TH>
	<TH><?=_('Description')?></TH>
</TR>
<TR>
<?=show($res,"GRIL59",_("Grille 59 : taxe déductible"))?>
</tr>
<TR>
<?=show($res,"GRIL62",_("Grille 62 : Diverses régularisations T.V.A. en faveur du déclarant"))?>
</tr>
<TR>
<?=show($res,"GRIL64",_("Grille 56 : T.V.A. à récupérer mentionnée sur les notes de crédit délivrées "))?>
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
<?=show_account($res,"ATVA",_("Poste comptable utilisé pour les avances faites à la TVA"))?>
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