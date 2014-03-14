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
// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief regenerate all the invoices of the selected operations
 * @param type $name Descriptionara
 * @code 
 *  
  'sel_sale' =>
  array
  0 => string '2832' (length=4)
  1 => string '2871' (length=4)
  2 => string '2889' (length=4)
  3 => string '2939' (length=4)
  'action' => string '2' (length=1)
  'document' => string '8' (length=1)
 * @endcode
 */
require_once 'class_acc_ledger_sold_generate.php';
//--- take all the invoices
?>
<h1> Génération de factures</h1>
<ol>

    <?php
    foreach ($_GET['sel_sale'] as $key => $value)
    {
        $operation = new Acc_Operation($cn);
        $operation->jr_id = $value;
        $op_sale = $operation->get_quant();
        $generate = new Acc_Ledger_Sold_Generate($cn, $op_sale->det->jr_def_id);
        $array = $generate->convert_to_array($op_sale);
        $document=HtmlInput::default_value_get("document",-1);
        if ($document <> -1 ){
        ?>
        <li>
            <?php echo $generate->create_document($array, $_GET['document']); ?>
        </li>
        <?php
        } else {
            echo _('Aucun modèle');
            exit();
        }
    }
    ?>
</ol>
<form method="get">
    <?php
    echo HtmlInput::get_to_hidden(array('gDossier', 'ac', 'plugin_code', 'sel_sale'));
    echo HtmlInput::hidden('action','1');
    ?>

    <?php echo HtmlInput::submit('tl',_('Télécharger toutes les factures')); ?>
</form>
