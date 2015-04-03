<?php
/*
 * * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
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

 * 
 */


/**
 * @brief display detail of a repair card called from Service_After_Sale::display_detail
 * Variables are 
 *  - Sav_Repair_Card_SQL $repair_Card
 *  - $fiche_customer 
 *  - $fiche_received
 *  - $fiche_done
 *  - $repair_card
 */
global $g_sav_parameter;
$style_customer_detail_ul="list-style: none;padding-left: 20px;margin:0px";
$style_customer_date_ul="list-style: none;padding-left: 0px;margin:0px";
$style_date_id='width:50%;float:left';
?>

<div class="content" style="position: absolute" id="sas_display_detail_id">
<form id="sas_display_detail_form" class="print" method="POST">
<?php
/* Hidden value */
echo HtmlInput::request_to_hidden(array('gDossier','sa','sb','ac','plugin_code'));
echo HtmlInput::hidden('repair_card_id',$this->repair_card->id);

$back="?".http_build_query(array('gDossier'=>$gDossier,'ac'=>HtmlInput::default_value_request('ac',''),'plugin_code'=>HtmlInput::default_value_request('plugin_code','')));

if ( $this->repair_card->card_status=='D' ):

    $input_fiche_customer=new ICard('cust_id');
    $input_fiche_customer->set_dblclick('fill_ipopcard(this)');

    $input_fiche_customer->set_function('fill_data');
    //$input_fiche_customer->javascript=sprintf(' onchange="fill_customer(\'%s\',\'%s\',\'%s\',\'%s\');" ', $input_fiche_customer->name, $gDossier, $plugin_code, $ac);
    $sql=' select fd_id from fiche_def where frd_id = '.FICHE_TYPE_CLIENT;
    $filter=$cn->make_list($sql);
    $input_fiche_customer->set_attribute('typecard', $filter);
    $input_fiche_customer->extra=$filter;
    $object=json_encode(array('dossier'=>Dossier::id(), 'jrn'=>-1, 'type_cat'=>FICHE_TYPE_CLIENT));
    $add=sprintf('select_card_type(%s)', $object);
    
   
?>

    <table >
        <tr>
            <td style="display:table-cell">
                <?php echo _('Client '); ?>
            </td>
            <td style="display:table-cell">
                <?php echo $input_fiche_customer->input(); ?>
            </td>
            <td style="display:table-cell">
               <?php echo $input_fiche_customer->search(); ?>
            </td>
            <td style="display:table-cell">
                <?php echo HtmlInput::button("add_customer_id", _('Ajout client'), ' onclick=\''.$add.'\''); ?>
            </td>
        </tr>
    </table>
<?php else : ?>
    <div style="width:50%;float: left">
        <table>
                <tr> 
                    <td>Code </td>
                    <td><?php echo HtmlInput::card_detail($fiche_customer->strAttribut(ATTR_DEF_QUICKCODE), "", ' style="display:inline;text-decoration:underline"') ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('Nom') ?> : </td>
                    <td id="cust_name_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_NAME, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('Prénom') ?> : </td>
                    <td id="cust_firstname_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_FIRST_NAME, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('Adresse') ?>: </td>
                    <td id="cust_address_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_ADRESS, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('Code postal') ?>: </td>
                    <td id="cust_cp_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_CP, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('Ville') ?>: </td>
                    <td id="cust_town_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_CITY, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('Pays') ?>: </td>
                    <td id="cust_country_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_PAYS, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('GSM') ?>: </td>
                    <td id="cust_mobile_id"><?php echo $fiche_customer->strAttribut(27, 0) ?></td>
                </tr>
                <tr>
                    <td><?php echo _('Téléphone') ?>: </td>
                    <td id="cust_mobile_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_TEL, 0) ?></td>
                </tr>
                <tr>
                    <td><?php echo _('Email') ?>: </td>
                    <td id="cust_email_id"><?php echo $fiche_customer->strAttribut(ATTR_DEF_EMAIL, 0) ?></td>
                </tr>
                <tr> 
                    <td><?php echo _('TVA') ?>: </td>
                    <td id="cust_vat_id"><?php echo $fiche_customer->strAttribut(13, 0) ?></td>
                </tr>
            </table>
    </div>
        <div style="<?php echo $style_date_id;?>">
            <table id="date_table_id">
                <tr class="highlight">
                    <td>
                        Numéro de fiche  : 
                    </td>
                    <td>
                        <?php echo h($this->repair_card->id)?>
                    </td>
                </tr> 
        <tr>
            <td> <?php echo _('Date réception')."</td><td> ".$date_received->input();?> </td>
        </tr>
            <?php if ( $this->repair_card->card_status != 'D' ): ?> <tr><td> <?php echo _('Date Début')."</td><td>".$date_start->input();?></td></tr> <?php endif;?>
            <?php if ( $this->repair_card->card_status != 'D' ): ?> <tr><td> <?php echo _('Date Fin')."</td><td>".$date_end->input();?></td></tr><?php endif;?>
            <tr>
                <td>
                    <?php echo _('Etat')."</td><td> ".$status->input()?>
                </td>
            
        </tr>
            </table>
    </div>
    <div style="float:top;clear: both"></div>
<?php endif; ?>        
<?php
$garantie=new IText('garantie', $this->repair_card->garantie);
echo _('Numéro garantie')." ".$garantie->input();
?>

    <div id="material_id">
        <?php
        if  ( $this->repair_card->card_status == 'D') :
        /* Material can be specified only when new
         * 
         */
        $input_fiche_materiel=new ICard('good_id');
        $input_fiche_materiel->set_dblclick('fill_ipopcard(this)');

        $input_fiche_materiel->set_function('fill_data');
        $sql=' select fd_id from fiche_def where fd_id in ('.$g_sav_parameter->get_material().')';
        $filter=$cn->make_list($sql);
        $input_fiche_materiel->set_attribute('typecard', $filter);
        $input_fiche_materiel->extra=$filter;
        $object=json_encode(array('dossier'=>Dossier::id(), 'jrn'=>-1,'filter'=>$g_sav_parameter->get_material()));
        $add_material=sprintf('select_card_type(%s)', $object);
        echo _('Matériel retourné')." ".$input_fiche_materiel->input()." ".$input_fiche_materiel->search();
        echo HtmlInput::button("add_material_id", _('Ajout Matériel'), ' onclick=\''.$add_material.'\''); 
        
        else :
            echo _('Matériel retourné')." ";
            echo HtmlInput::card_detail($material->strAttribut(ATTR_DEF_QUICKCODE),"",' style="display:inline;text-decoration:underline"')   ; 
            
            
        endif;
        
        ?>

    </div>
    <div style="" id="failure_description_div">
        <?php echo _('Description panne')?>
        <textarea name="description" class="itextarea"><?php echo $this->repair_card->description_failure?></textarea>
    </div>
    <input type='submit' name="save_repair_card" class='button' value="<?php echo _('Sauver')?>">
    <input type='button' class='button' value="<?php echo _('Annuler')?>" onclick='go_back()'>
    </form>

    <hr>
<?php 
//////////////////////////////////////////////////////////////////////////////
// if $p_repair_card_id != -1 it means it is an update
if ( $this->repair_card->id != -1 ) :
?>
    <div id="spare_id" style="<?php echo $style_date_id;?>">
        <h2 class="legend"><?php echo _('Pièces de rechange')?></h2>
        <form method="get" class="print">
        <?php
            $spare->display_list($this->repair_card->id);
            
        ?>
        </form>
    </div>
    <div id="workhour_div" style="<?php echo $style_date_id;?>">
        <h2 class="legend"><?php echo _("Main d'oeuvre")?></h2>
        <form class="print">
        <?php
           $workhour->display_list($this->repair_card->id);
        ?>
        </form>
        
    </div>
        <div style="float:top;clear: both"></div>

    <div id="invoice_div_id">
        <?php echo $this->button_prepare_invoice();?>
    </div>
<?php endif;    ?>
</div>
<script>
    function go_back() {
        window.location='<?php echo $back;?>';
    }
</script>    