<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Copyright Author Dany De Bontridder danydb@aevalys.eu

require_once 'class_transform_representative.php';
require_once 'class_transform_declarant.php';
$representative=new Transform_Representative();
$representative->fromPost();
$declarant=new Transform_Declarant();
$declarant->fromPost();
$radio=new IRadio('p_inputtype');
$h_tva=new ICheckBox('h_tva[]');
$h_year=new INum('p_year');
$h_year->prec=0;
$h_tva_compute_date=new ISelect('p_compute_date');
$h_tva_compute_date->value=array(
    array('value'=>1,'label'=>_('Par date paiement')),
    array('value'=>2,'label'=>_('Par date opération'))
    );
$start_date=new IDate('p_start_date');
$start_date->value=HtmlInput::default_value_post('p_start_date','');
$end_date=new IDate('p_end_date');
$end_date->value=HtmlInput::default_value_post('p_end_date','');
?>

<form method="post" enctype="multipart/form-data">
    <h2><?php echo _('Mandataire');?></h2>
<?php
$representative->input();
?>
    <h2><?php echo _('Déclarant');?></h2>
<?php
$declarant->input();
?>
    <p>
        <?php echo _('Période'),$h_year->input();?>
    </p>
   
    <p>
    <?php
    
    $radio->value=1;
    echo $radio->input()._('Par fichier');
    $file = new IFile('client_assujetti');
    echo $file->input();
    ?>
    </p>
    <p>
        <?php
        $radio->value=2;
        echo $radio->input()._('Par calcul');
        $atva=$cn->get_array('select tva_id,tva_rate,tva_comment from tva_rate order by 2');
        $count_atva=count($atva);
        ?>
    <ul style="list-style: none">
        <?php
        for ($i=0;$i<$count_atva;$i++):
        ?>
        <li>
            <?php
                $h_tva->value=$atva[$i]['tva_id'];
                echo $h_tva->input().h($atva[$i]['tva_rate'])." ".h($atva[$i]['tva_comment']);
            ?>
        </li>
        <?php
        endfor;
        ?>
    </ul>
     <p style="margin-left:30px">
        <?php printf(_('Entre les date %s et %s'),$start_date->input(),$end_date->input());?>
    </p>
    <span style="margin-left:30px">
    <?php echo _('Opération de vente'),$h_tva_compute_date->input();?>
    </span>
    </p>    
    <p>
        <?php
        echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'sa'));
        echo HtmlInput::hidden('st_transf',1);
        echo HtmlInput::submit('send_list', 'Valider');
        ?>
    </p>
</form>    
