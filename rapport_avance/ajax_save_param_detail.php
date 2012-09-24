<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief sauve la nouvelle ligne de détail (paramètre) et renvoie un xml
 * avec code = ok ou nok, p_id pk de formulaire_param et html le code html à afficher : soit une ligne à ajouter à la table
 * soit le message d'erreur à ajouter dans le div
 *
 *
 */
require_once 'class_impress.php';
require_once 'include/class_formulaire_param_detail.php';
$fp_id=0;
switch($tab)
{
	case 'account_tva':
		$acc_tva=new RAPAV_Account_Tva();
		$acc_tva->tva_id=$code_tva;
		$acc_tva->tmp_val=$formtva;
		$acc_tva->jrn_def_type=$code_jrn;
		$acc_tva->p_id=$p_id;
		$acc_tva->type_detail=2;
		$acc_tva->tt_id=$code_base;
		if ($acc_tva->verify() == 1)
		{
			$code='nok';
			$html="Erreur dans la formule ".$acc_tva->errcode;
		}
		else
		{
			$acc_tva->insert();
			$code='ok';
			$fp_id=$acc_tva->fp_id;
			$html='<td>';
			ob_start();
			$acc_tva->display_row();
			$html.=ob_get_contents();
			ob_end_clean();
			$html.= '</td>';
			$html.='<td id="del_'.$acc_tva->fp_id.'">';
			$html.=HtmlInput::anchor("Effacer","",sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
					, $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $acc_tva->fp_id));
			$html.= '</td>';
		}
		break;
	case 'formula':
		$acc_formula=new RAPAV_Formula();
		$acc_formula->fp_formula=$formula_new;
		$acc_formula->p_id=$p_id;
		$acc_formula->type_detail=1;
		if ($acc_formula->verify() == 1)
		{
			$code='nok';
			$html=$acc_formula->errcode;
		}
		else
		{
			$acc_formula->insert();
			$fp_id=$acc_formula->fp_id;
			$code='ok';
			$html='<td>';
			ob_start();
			$acc_formula->display_row();
			$html.=ob_get_contents();
			ob_end_clean();
			$html.= '</td>';
			$html.='<td id="del_'.$acc_formula->fp_id.'">';
			$html.=HtmlInput::anchor("Effacer","",sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
				, $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $acc_formula->fp_id));
			$html.='</td>';

		}
		break;
	case 'compute_id':
		$acc_compute=new RAPAV_Compute();
		$acc_compute->fp_formula=$form_compute;
		$acc_compute->p_id=$p_id;
		$acc_compute->type_detail=3;
		if ($acc_compute->verify() == 1)
		{
			$code='nok';
			$html=$acc_compute->errcode;
		}
		else
		{
			$acc_compute->insert();
			$fp_id=$acc_compute->fp_id;
			$code='ok';
			$html='<td>';
			ob_start();
			$acc_compute->display_row();
			$html.=ob_get_contents();
			ob_end_clean();
			$html.= '</td>';
			$html.='<td id="del_'.$acc_compute->fp_id.'">';
			$html.=HtmlInput::anchor("Effacer","",sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
					, $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $acc_compute->fp_id));
			$html.='</td>';

		}
		break;
}
//echo $html;exit();
$html=escape_xml($html);

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$code</code>
<html>$html</html>
<p_id>$p_id</p_id>
<fp_id>$fp_id</fp_id>
</data>
EOF;
?>
