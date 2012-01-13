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
 * @brief génére écriture comptable pour appel de fond
 *
 */
global $cn, $g_copro_parameter;
require_once 'class_coprop_appel_fond.php';
extract($_GET);
$error = 0;

// Demande génération
if (isset($calc))
{
    try
    {

// Génére écriture comptable dans journal choisi
        switch ($aft)
        {
            case -1:
                throw  new Exception("Choississez le type de calcul");
                break;
            case 1:
                $appel_fond = new Coprop_Appel_Fond();
                $appel_fond->compute_budget($_GET);
                break;
            case 2:
                $appel_fond = new Coprop_Appel_Fond();
                 $appel_fond->compute_amount($_GET);
                break;
        }

        $appel_fond->display_ledger();


        exit();
    }
    catch (Exception $e)
    {
        alert($e->getMessage());
    }
}
// save
if ( isset($_POST['confirm']))
{
	$ledger=new Acc_Ledger($cn,$_POST['p_jrn']);
	$ledger->with_concerned=false;
	$ledger->save($_POST);
	echo "<h2>Opération sauvée</h2>";
	echo HtmlInput::detail_op($ledger->jr_id,$ledger->internal);
	echo $ledger->input($_GET,1);
	$appel_fond = new Coprop_Appel_Fond();
	$appel_fond->id=$_POST['af_id'];
	$appel_fond->confirm();
	exit();
}
// Montre écran confirmation
if ( isset ($_GET['save']))
{
	/**
	 *@todo manque correction
	 */
	echo '<form method="POST">';
	$ledger=new Acc_Ledger($cn,$_GET['p_jrn']);
	$ledger->with_concerned=false;
	echo $ledger->input($_GET,1);
	echo HtmlInput::submit('confirm','Confirmer');
	echo '</form>';
	exit();
}



// Detail : propose de faire un appel de fond
$date = new IDate('p_date');
$date->value=HtmlInput::default_value('p_date',"",$_GET);

$amount = new INum('amount');
$amount->value=HtmlInput::default_value('amount',0,$_GET);

$ledger = new Acc_Ledger($cn, 0);
$led_appel_fond = $ledger->select_ledger('ODS', 3);
$led_appel_fond->selected = (isset($_GET['p_jrn']))?$_GET['p_jrn']:$g_copro_parameter->journal_appel;

$copro = new ICard();
$categorie_appel = new ICard();
$categorie_appel->label = " Appel de fond : " . HtmlInput::infobulle(0);
$categorie_appel->name = "w_categorie_appel";
$categorie_appel->tabindex = 1;
$categorie_appel->value = isset($_GET['w_categorie_appel'])?$_GET['w_categorie_appel']:"";
$categorie_appel->table = 0;
$categorie_appel->selected=(isset($_GET['key']))?$_GET['key']:-1;

// name of the field to update with the name of the card
$categorie_appel->set_attribute('label', 'w_categorie_appel_label');
// Type of card : deb, cred,
$categorie_appel->set_attribute('typecard', $g_copro_parameter->categorie_appel);

$categorie_appel->extra = $g_copro_parameter->categorie_appel;

// Add the callback function to filter the card on the jrn
$categorie_appel->set_callback('filter_card');
$categorie_appel->set_attribute('ipopup', 'ipopcard');
// when value selected in the autcomplete
$categorie_appel->set_function('fill_data');

// when the data change
$categorie_appel->javascript = sprintf(' onchange="fill_data_onchange(\'%s\');" ', $categorie_appel->name);
$categorie_appel->set_dblclick("fill_ipopcard(this);");

$categorie_appel_label = new ISpan();
$categorie_appel_label->table = 0;
$f_categorie_appel_label = $categorie_appel_label->input("w_categorie_appel_label", "");

// Search button for card
$f_categorie_appel_bt = $categorie_appel->search();

$key = new ISelect("key");
$key->value = $cn->make_array("select cr_id,cr_name from coprop.clef_repartition");
$key->selected=HtmlInput::default_value('key',-1,$_GET);

$f_add_button = new IButton('add_card');
$f_add_button->label = _('Nouvelle fiche ');
$f_add_button->set_attribute('ipopup', 'ipop_newcard');
$f_add_button->set_attribute('jrn', -1);
$filter = $g_copro_parameter->categorie_appel;
$f_add_button->javascript = " this.filter='$filter';this.jrn=-1;select_card_type(this);";
$str_add_appel = $f_add_button->input();

// Budget
$budget_sel = new ISelect("b_id");
$budget_sel->value = $cn->make_array("select b_id,b_name from coprop.budget order by b_name");
$budget_sel->selected=HtmlInput::default_value('b_id',-1,$_GET);

// pourcentage
$budget_pct = new INum("bud_pct", 0);
$budget_pct->value=HtmlInput::default_value('bud_pct',0,$_GET);

// select between budget or amount
$appel_fond_type = new ISelect("aft");
$appel_fond_type->value = array(
    array("value" => -1, 'label' => 'Faites votre choix'),
    array("value" => 1, 'label' => 'Appel de fond par budget'),
    array("value" => 2, 'label' => 'Appel de fond par montant')
);
$onchange = " onchange=\"appel_fond_show() \"";
$appel_fond_type->javascript = $onchange;
$appel_fond_type->selected=HtmlInput::default_value('aft',-1,$_GET);

echo '<form method="get">';
echo HtmlInput::request_to_hidden(array('ac', 'plugin_code', 'sa','gDossier'));
require_once 'template/appel_fond.php';
echo HtmlInput::submit('calc', "Calculer");
echo '</form>';
?>
