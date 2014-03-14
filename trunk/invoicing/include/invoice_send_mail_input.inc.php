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

global $g_user;

$titre = new IText('ag_title');

// Profile in charged of the action
$ag_dest = new ISelect();
$ag_dest->name = "ag_dest";
// select profile
$aAg_dest = $cn->make_array("select  p_id as value, " .
        "p_name as label " .
        " from profile  "
        . "where p_id in (select p_granted from "
            . " user_sec_action_profile where ua_right='W' and p_id=" . $g_user->get_profile() . ") order by 2");
$ag_dest->value = $aAg_dest;

// -- Date
$date = new IDate();
$date->name = "ag_timestamp";
$date->value = date('d.m.Y');

// -- remind date
$remind_date = new IDate();
$remind_date->name = "ag_remind_date";
$remind_date->value = "";

// -- document
$category = new ISelect('dt_id');
$category->value = $cn->make_array("select dt_id,dt_value from document_type order by dt_value");
$category->readOnly = false;

//-- description
$desc = new ITextArea();
$desc->style = ' class="itextarea" style="width:80%;margin-left:0px"';
$desc->name = "ag_comment";
?>
<form method="POST" id="sel_sale_frm" onsubmit="return verify_invoicing()">
<?php
echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code','action'));
echo HtmlInput::request_to_hidden(array('sel_sale'));
echo HtmlInput::hidden('sa', 'send');
?>

    <h2 class="note"><?php echo _('Envoi uniquement à ceux ayant une adresse email et une facture') ?> </h2>
    <p>
        <input type="checkbox" name="pdf"> <?php echo _('Conversion en PDF'); ?>
    </p>
    <p>
        <?php echo _('Email envoyé par'); ?> :
        <input type="text" id="email_from" name="email_from" class="input_text">
        <span class="notice" id="email_from_span"></span>
    </p>

    <p>
        <?php echo _('Sujet') ?> : 
        <input type="text" id="email_subject" name="email_subject" class="input_text">
        <span class="notice" id="email_subject_span"></span>
    </p>
    <p>
        <?php echo _('Message') ?> : 
        <textarea style="vertical-align: top;width:80rem;height:20rem;" name="email_message" class="input_text">               </textarea>
    </p>
    <p>
        <input type="checkbox" name="email_copy"> <?php echo _("Envoyer copie à l'expéditeur"); ?>

    </p>
    <h2 class="note"><?php echo _('Inclure dans le suivi') ?> </h2>
    <p>
        <input type="checkbox" name="save_followup"> <?php echo _("Sauver dans le suivi"); ?>
    </p>
    <table>
        <tr>
            <td>
                <label><?php echo _('Date'); ?> :</label>
            </td>        
            <td>
                <?php echo $date->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label><?php echo _('Date rappel'); ?> :</label>
            </td>
            <td>
                <?php echo $remind_date->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <?php echo _('Profil'); ?> :
                </label>
            </td>
            <td>

                <?php echo $ag_dest->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label><?php echo _('Titre'); ?> : </label>
            </td>
            <td>
                <?php echo $titre->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <?php echo _('Catégorie'); ?> :
                </label>
            </td>
            <td>
                <?php echo $category->input(); ?>
            </td>
        </tr>
    </table>

    <p>
        <?php
        echo HtmlInput::submit('choice_sel', _('Exécuter'));
        ?>
    </p>
</form>
<script>
    function verify_invoicing()
    {
        if ($('invoice_radio').checked) {
            if ($('email_from').value.trim() == "") {
                $('email_from').style.border = "solid 2px red";
                $('email_from_span').innerHTML = " Obligatoire";
                return false;
            } else {
                $('email_from_span').hide();
                $('email_from').style.border = "";
            }
            if ($('email_subject').value.trim() == "") {
                $('email_subject').style.border = "solid 2px red";
                $('email_subject_span').innerHTML = " Obligatoire";
                return false;
            } else {
                $('email_subject_pan').hide();
                $('email_subject').style.border = "";
            }
        }
    }
</script>
<