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
require_once 'class_acc_ledger_sold_generate.php';
$ledger = new Acc_Ledger_Sold_Generate($cn, -1);
$_GET['ledger_type'] = 'VEN';
//var_dump($_GET);
$request = HtmlInput::default_value_get('action', -2);
if ($request <> -2)
{
    if (!isset($_GET['sel_sale']))
    {
        echo h2("Rien n'a été choisi", 'class="notice"');
    } else
    {
        switch ($request)
        {
            case 1:
                // Download zip
                require ('invoice_to_zip.inc.php');
                break;
            case 2:
                // regenerer facture
                require ('invoice_generate.inc.php');
                break;
            case 3:
                // Envoi facture par email
                require('invoice_send_mail.inc.php');
                break;
        }
        exit();
    }
}
echo $ledger->display_search_form();
// Example
// Build the sql
list($sql, $where) = $ledger->build_search_sql($_GET);
// Count nb of line
$max_line = $cn->count_sql($sql);

$offset = 0;
// show a part
list($count, $html) = $ledger->list_operation($sql, $offset, 0);

// --- template Invoice  to generate --- //
$document=new ISelect('document');
$document->value=$cn->make_array("select md_id,md_name from document_modele where md_affect='VEN' order by 2");

$document_to_send=new ISelect('format_document');
$document_to_send->value=array(
    array('value'=>'1','Convertire en PDF'),
    array('value'=>'2','Envoi de la facture sans conversion en PDF')
)
?>
<form method="GET" id="sel_sale_frm" onsubmit="return verify_invoicing()">
    Ajouter dans le form les valeurs de la recherches
    <?php
    echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code'));
    echo HtmlInput::request_to_hidden(array('date_start', 'date_end'));
    echo HtmlInput::request_to_hidden(array('date_paid_start', 'date_paid_end'));
    echo HtmlInput::request_to_hidden(array('amount_min', 'amount_max'));
    echo HtmlInput::request_to_hidden(array('desc', 'qcode', 'accounting'));
    echo HtmlInput::request_to_hidden(array('r_jrn'));
    echo $html;
    ?>
    <ul style="list-style-type: none">
        <li>
            
            <input type="radio" name="action" value="1" 
                   onclick="$('invoice_div').hide();$('send_mail_div').hide();">
            <?php echo _('Télécharger toutes les factures') ?>
        </li>
        <li>
          
            <input type="radio" name="action" value="2" 
                   onclick="$('invoice_div').show();$('send_mail_div').hide();">
              <?php echo _('Générer les factures') ?>
            <div id="invoice_div" style="display:none">
                <?php echo _('Document à générer'); ?> : <?php echo $document->input(); ?>
            </div>
        </li>
        <li>
            
            <input type="radio" name="action" id="invoice_radio" value="3" 
                   onclick="$('invoice_div').hide();$('send_mail_div').show();">
            <?php echo _('Envoi des factures par email') ?>
            <div id="send_mail_div" style="display:none">
                <h2 class="note"><?php echo _('Envoi uniquement à ceux ayant une adresse email et une facture')?> </h2>
                <p>
                    <input type="checkbox" name="pdf"> <?php echo _('Conversion en PDF'); ?>
                </p>
                <p>
                    <?php echo _('Email envoyé par'); ?> :
                     <input type="text" id="email_from" name="email_from" class="input_text">
                     <span class="notice" id="email_from_span"></span>
                </p>
                
                <p>
                    <?php echo _('Sujet')?> : 
                    <input type="text" id="email_subject" name="email_subject" class="input_text">
                    <span class="notice" id="email_subject_span"></span>
                </p>
                <p>
                    <?php echo _('Message')?> : 
                    <textarea style="vertical-align: top;width:23%;height:10%" name="email_message" class="input_text">               </textarea>
                </p>
                <p>
                    <input type="checkbox" name="email_copy"> <?php echo _("Envoyer copie à l'expéditeur"); ?>
                   
                </p>
            </div>
        </li>
    </ul>   
    <p>
        <?php
        echo HtmlInput::submit('choice_sel', 'Exécuter');
        ?>
    </p>
</form>
<script>
    function verify_invoicing()
    {
        if ($('invoice_radio').checked) {
            if ($('email_from').value.trim()=="") {
                $('email_from').style.border="solid 2px red";
                $('email_from_span').innerHTML=" Obligatoire";
                return false;
            } else {
                $('email_from_span').hide();
                $('email_from').style.border="";
            }
            if ($('email_subject').value.trim()=="") {
                $('email_subject').style.border="solid 2px red";
                $('email_subject_span').innerHTML=" Obligatoire";
                return false;
            }else {
                $('email_subject_pan').hide();
                $('email_subject').style.border="";
            }
        }
    }
</script>
    
    