<?php

/* 
 * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//\file
/// Display the table result for a import 
//\see Impacc_CSV::result
//\param Impacc_File $importfile
//\param $displa Impacc_Import_detail_SQL
//\param $ret result of Impacc_Import_detail_SQL->seek
//\param nb number of records
global $g_succeed;
switch (get_class($csv_class))
{
    case 'Impacc_Csv_Sale':
        $counterpart=_("Client");
        $type="V";
        $amount=_("Montant HTVA");
        break;
    case 'Impacc_Csv_Purchase':
        $counterpart=_("Fournisseur");
        $amount=_("Montant HTVA");
        $type="A";
        break;
    case 'Impacc_Csv_Misc_Operation':
        $counterpart=_("Quick code ou poste comptable");
        $amount=_("Montant");
        $type="O";
        break;
    case 'Impacc_Csv_Bank':
        $type="F";
        $amount=_("Montant");
        $counterpart=_("Quick code");
        break;
        throw new Exception(_('Objet inconnu dans '.__FILE__.":".__LINE__));
}

?>
<table class="result">
    <tr>
        <th>
            <?php echo _("Date");?>
        </th>
        <th>
            <?php echo _("Groupe"); ?>
        </th>
        <th>
            <?php echo _("Pièce")?>
        </th>
        <th>
            <?php echo $counterpart?>
        </th>
        <th>
            <?php echo _("Libellé opération")?>
        </th>
<?php
// ----------------------------------------------------------------------
// For sale and purchase only
// ----------------------------------------------------------------------
       if ( $type=="V" || $type == 'A'):
           ?>
         <th>
            <?php echo _('Date paiement');?>
        </th>
         <th>
            <?php echo _('Echéance');?>
        </th>
         <th>
            <?php echo _('Service');?>
        </th>
         <th>
            <?php echo _('Quantité');?>
        </th>
<?php endif;?>              
        <th>
            <?php echo $amount;?>
        </th>
<?php
// ----------------------------------------------------------------------
// For sale and purchase only
// ----------------------------------------------------------------------
       if ( $type=="V" || $type == 'A'):
           ?>
         <th>
            <?php echo _('Code TVA');?>
        </th>
         <th>
            <?php echo _('TVAC / TTC');?>
        </th>
<?php endif;?>              
        <th>
            <?php echo _("Status")?>
        </th>    
    </tr>
    <?php
        for ($i=0;$i< $nb;$i++) {
            $row=$display->next($ret,$i);
            $class=($i%2==0)?" even ":" odd ";
    ?>
    <tr class="<?php echo $class?>">
        <td>
            <?php echo h($row->id_date_conv); ?>
        </td>
        
        <td>
            <?php echo h($row->id_code_group); ?>
        </td>
        
        <td>
            <?php
            if ( $row->id_message == "" && $row->id_status ==2) {
                $internal=$cn->get_value("select jr_pj_number from jrn where jr_id=$1",array($row->jr_id));
                echo HtmlInput::detail_op($row->jr_id,$internal);
            }else {
            echo h($row->id_pj);}
            ?>
        </td>
        <td>
            <?php echo h($row->id_acc); ?>
        </td>
        <td>
            <?php echo h($row->id_label); ?>
        </td>
<?php
// ----------------------------------------------------------------------
// For sale and purchase only
// ----------------------------------------------------------------------
       if ( $type=="V" || $type == 'A'):
           ?>
         <td>
            <?php echo h($row->id_date_limit_conv);?>
        </td>
         <td>
            <?php echo h($row->id_date_payment_conv);?>
        </td>
         <td>
            <?php echo h($row->id_acc_second);?>
        </td>
         <td>
            <?php echo h($row->id_quant_conv);?>
        </td>
<?php           
       endif;

?>      
        <td>
            <?php echo h($row->id_amount_novat_conv);?>
        </td>
<?php
// ----------------------------------------------------------------------
// For sale and purchase only
// ----------------------------------------------------------------------
       if ( $type=="V" || $type == 'A'):
           ?>
         <td>
            <?php echo h($row->tva_code);?>
        </td>
        <td>
            <?php echo h($row->id_amount_vat_conv);?>
            
        </td>
<?php           
       endif;

       ?>    
        <td>
            <?php
            if ( $row->id_message == "" && $row->id_status !=2) {
                echo $g_succeed,_('Valide et non transférré');
            } else
            if ( $row->id_message == "" && $row->id_status ==2) {
                $internal=$cn->get_value("select jr_internal from jrn where jr_id=$1",array($row->jr_id));
                echo HtmlInput::detail_op($row->jr_id,$internal);
                echo $g_succeed;
                
            } else
                {
                    if ( $row->id_message != "")
                    {
                        $msg_all=explode(",", $row->id_message);
                        $msg=array_unique($msg_all);
                        $nb_msg=count($msg);
                        echo "<ol>";
                        for ($e=0;$e<$nb_msg;$e++)
                        {
                            $idx=$msg[$e];
                            if ( isset($this->errcode[$idx]))
                            {
                                echo '<li>',h($this->errcode[$idx]),'</li>';
                            } else {
                                echo '<li>',
                                        _("ERREUR")," : ",
                                        $idx,
                                        '</li>';
                            }
                        }
                        echo "</ol>";
                    } else if ($row->id_status==-1) {
                        echo _("Erreur importation");
                        echo h($row->id_message);
                    }
            }
            
            ?>
        </td>      
    </tr>
    <?php
        } //end loop i
    ?>
</table>