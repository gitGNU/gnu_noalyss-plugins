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
$representative = new Transform_Representative();
$representative->fromPost();
$declarant = new Transform_Declarant();
$declarant->fromPost();
$radio = new IRadio('p_inputtype');
$h_tva = new ICheckBox('h_tva[]');
$h_year = new INum('p_year');
$h_year->value = HtmlInput::default_value_post('p_year', $declarant->year);
$h_year->prec = 0;
$h_tva_compute_date = new ISelect('p_compute_date');
$h_tva_compute_date->value = array(
    array('value' => 1, 'label' => _('Par date paiement')),
    array('value' => 2, 'label' => _('Par date opération'))
);
$start_date = new IDate('p_start_date');
$start_date->id = "p_start_date_id";
$start_date->value = HtmlInput::default_value_post('p_start_date', '');
$end_date = new IDate('p_end_date');
$end_date->value = HtmlInput::default_value_post('p_end_date', '');
$end_date->id = "p_end_date_id";
$inputtype = HtmlInput::default_value_post('p_inputtype', -1);
?>
<h2> <?php echo _('Etape 1/3') ?></h2>
<form method="post" enctype="multipart/form-data" onsubmit="return check_form()">
    <div style="width:45%;padding: 15px;float:left">

        <h3><?php echo _('Mandataire'); ?></h3>
        <span class="notice">
            <?php
            echo _("Ne remplissez pas s'il n'y a pas de mandataire");
            ?>
        </span>
        <?php
        $representative->input($error, $errmsg);
        ?>
    </div>
    <div style="width:45%;padding: 15px;float:left">
        <h3><?php echo _('Déclarant'); ?></h3>
        <?php
        $declarant->input($error, $errmsg);
        ?>
    </div>
    <div style="clear:both;"></div>
    <p>
        <?php echo _('Période'), $h_year->input(); ?>
        <span class="notice"><?php
            if ($error == 6)
            {
                echo $errmsg;
            }
            ?></span>
    </p>

    <p>
        <?php $checked = ($inputtype == 1) ? ' checked ' : ""; ?>
        <input type="radio" name="p_inputtype" id="file_radio"  <?php echo $checked; ?>  value="1" onclick="show_file();">
        <?php
        echo _('Par fichier');
        $display = ( $inputtype == 1) ? 'block' : 'none';
        ?>
        <span id="sp_file" style="display:<?php echo $display; ?>">
            <?php
            $file = new IFile('client_assujetti');
            echo $file->input();
            ?>
        </span>
    </p>
    <p>
        <?php $checked = ($inputtype == 2) ? ' checked ' : ""; ?>
        <input type="radio" name="p_inputtype" <?php echo $checked; ?> id="calc_radio" value="2" onclick="show_calc()">
        <?php
        echo _('Par calcul');
        $display = ( $inputtype == 2) ? 'block' : 'none';
        ?>
    <div id="sp_calcul" style="display:<?php echo $display; ?>">
        <p style="margin-left:30px">
            <?php printf(_('Entre les date %s et %s'), $start_date->input(), $end_date->input()); ?>
            <span class="notice"><?php if ($error == 9 || $error == 9.1)
            {
                echo $errmsg;
            } ?></span>
        </p>
        <?php
        $atva = $cn->get_array('select tva_id,tva_rate,tva_comment from tva_rate order by 2');
        $count_atva = count($atva);
        ?>
        <span class="notice"><?php if ($error == 5 || $error == 5.1)
        {
            echo $errmsg;
        } ?></span>
        <ul style="list-style: none">
                <?php
                for ($i = 0; $i < $count_atva; $i++):
                    ?>
                <li>
                <?php
                $h_tva->value = $atva[$i]['tva_id'];
                echo $h_tva->input() . h($atva[$i]['tva_rate']) . " " . h($atva[$i]['tva_comment']);
                ?>
                </li>
                <?php
            endfor;
            ?>
        </ul>

        <span style="margin-left:30px">
    <?php echo _('Opération de vente'), $h_tva_compute_date->input(); ?>
        </span>
    </div>
</p>    
<p>
<?php
echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'sa'));
echo HtmlInput::hidden('st_transf', 1);
echo HtmlInput::submit('send_list', 'Valider');
?>
</p>
</form>    
<script>
    function show_file() {
        $('sp_file').show();
        $('sp_calcul').hide();
    }
    function show_calc() {
        $('sp_file').hide();
        $('sp_calcul').show();
    }
    function check_form()
    {
        if ($('p_year').value == "") {
            alert('Vous avez oublié la période');
            $('p_year').style.borderColor = "red";
            return false;
        }
        if ($('p_start_date_id').value == "" && $('calc_radio').checked) {
            alert('Date incorrect');
            $('p_start_date_id').style.borderColor = "red";
            return false;
        }
        if ($('p_end_date_id').value == "" && $('calc_radio').checked) {
            alert('Date incorrect');
            $('p_end_date_id').style.borderColor = "red";
            return false;
        }
        if (!$('calc_radio').checked && !$('file_radio').checked) {
            alert('Vous devez choisir par fichier ou par calcul');
            return false
        }
        return true;
    }
</script>    