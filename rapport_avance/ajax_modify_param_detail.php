<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

// require_once '.php';
require_once 'include/class_formulaire_param_detail.php';
$errcode = 0;
$fp_id = HtmlInput::default_value_get('fp_id', -1);
$title="none";$comment="";$tab='none';
if ($fp_id != -1)
{
    $obj = new Formulaire_Param_Detail_SQL($fp_id);
    switch ($obj->type_detail)
    {
        // --- Formula
        case 1:
            $obj = new Rapav_Formula($fp_id);
            $title = "Formule";
            $comment='
            <p>
                Entrez une formule avec des postes comptables, la syntaxe est la même que celle des "rapports"
            </p>
            <p>
                Exemple : [70%]*0.25+[71%]
            </p>';
            $tab='formula';
            break;
        // -- Poste comptable et code
        case 2:
            $obj = new RAPAV_Account_Tva($fp_id);
            $title = "Poste Comptable et code TVA";
            $tab='account_tva';
            $comment='<p>
	Entrez un poste comptable et un code de TVA
	</p>';
            break;
        // -- Calcul sur formulaire
        case 3:
            $obj = new RAPAV_Compute($fp_id);
            $title = "Compute";
            $tab='compute_id';
            $comment='<p>
	Entrez une formule avec des codes utilisés dans ce formulaire
	</p>';
            break;
        // -- Poste comptable
        case 4:
            $obj = new RAPAV_Account($fp_id);
            $comment="";
            $tab='new_account_id';
            $title = "Poste comptable";
            break;
        // -- operation reconciliee
        case 5:
            $comment='';
            $obj = new RAPAV_Reconcile($fp_id);
            $title = "Opérations rapprochées";
            $tab="new_reconcile_id";
            break;
        default:
            $errcode = 1;
            echo HtmlInput::title_box('Erreur', 'param_detail_div');
            echo "Erreur type formule inconnu";
            break;
    }
} else
{
    $errcode = 2;
    echo HtmlInput::title_box('Erreur', 'param_detail_div');
    echo _('Paramètre invalide');
}

if ($errcode == 0)
{
  echo HtmlInput::title_box($title, 'param_detail_div');
  echo '<div class="content" style="padding:10px">';
  echo '<span class="notice" id="param_detail_info_div"></span>';
  echo $comment;
  echo '<form method="post" onsubmit="save_param_detail(\'modify_param_detail_frm\');return false;" id="modify_param_detail_frm">';
  $obj->input();
  echo HtmlInput::hidden('p_id',$obj->p_id);
  echo HtmlInput::hidden('tab',$tab);
  echo HtmlInput::hidden('fp_id',$obj->fp_id);
  echo HtmlInput::hidden('ac',$_REQUEST['ac']);
  echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
  echo Dossier::hidden();
  echo HtmlInput::submit('save_modify_param_detail','Sauve');
  echo '</form>';
  echo '</div>';
}
?>        