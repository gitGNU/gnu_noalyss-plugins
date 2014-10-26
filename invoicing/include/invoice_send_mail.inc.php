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
// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief 
 * @param email_from send of emails
 * @param email_subject subject of email
 * @param email_message text 
 * @param email_copy copy to sender
 * @param pdf convert to pdf
  @endnote
 */
require_once 'class_sendmail.php';
require_once 'class_follow_up.php';
global $cn;

//----- Mail
$from = HtmlInput::default_value_post('email_from', 'null');
$subject = HtmlInput::default_value_post('email_subject', 'null');
$message = HtmlInput::default_value_post('email_message', 'null');
$copy = HtmlInput::default_value_post('email_copy', '-1');
$pdf = HtmlInput::default_value_post('pdf', 'null');

//-- Follow up
$ag_timestamp = HtmlInput::default_value_post('ag_timestamp', date('d.m.Y'));
$ag_title = HtmlInput::default_value_post('ag_title', $message);
$ag_remind_date=HtmlInput::default_value_post('ag_remind_date','');
$ag_dest=HtmlInput::default_value_post('ag_dest',1);

if (isDate($ag_timestamp) == null)
    $ag_timestamp = date('d.m.Y');

if (trim($ag_title) == "")
    $ag_title = $message;

if (isDate($ag_remind_date) == null) {
    $ag_remind_date=null;
}


if ($from == "null")
{
    die(_("Désolé mais il faut donner l'email de celui qui envoie"));
}
if ($subject == "null")
{
    die(_("Le sujet est obligatoire"));
}

if ($message == "null")
{
    $message = $subject;
}
$feedback = array();
$dirname = tempnam($_ENV['TMP'], 'invoice');
unlink($dirname);
umask(0);
mkdir($dirname);
$cn->start();
foreach ($_GET['sel_sale'] as $key => $value)
{
    $a_invoice = $cn->get_array("select jr_id,jr_pj_name,jr_pj,qs_client 
            from jrn join quant_sold on (jr_internal=qs_internal) 
            where jr_id = $1", array($value));
    $invoice = $a_invoice[0];
    $dest = new Fiche($cn, $invoice['qs_client']);
    $dest_mail = $dest->strAttribut(ATTR_DEF_EMAIL);
    $dest_name = $dest->strAttribut(ATTR_DEF_NAME);
    $dest_qcode = $dest->strAttribut(ATTR_DEF_QUICKCODE);

    /**
     * Something to send
     */
    if ($invoice['jr_pj_name'] != "" && $invoice['jr_pj'] != "" && $dest_mail != "" && $dest_mail != NOTFOUND)
    {
        // -- send mail --//
        $file = $dirname . '/' . $invoice['jr_pj_name'];
        $cn->lo_export($invoice['jr_pj'], $file);
        $filetosend = $file;
        /*
         * convert to PDF
         */
        if (GENERATE_PDF != "YES" && $pdf != 'null')
        {
            $feedback[] = _('Pas de conversion en PDF disponible');
            continue;
        }
        if (GENERATE_PDF == 'YES' && $pdf == 'on')
        {
            // convert to PDF
            $cn->lo_export($invoice['jr_pj'], $file);

            // remove extension
            $ext = strrpos($file, ".");
            $filetosend = substr($file, 0, $ext);
            $filetosend .=".pdf";

            passthru(OFFICE . $file, $status);
            if ($status == 1)
            {
                $feedback[] = "Cannot convert to PDF , email not sent for " .
                        $dest_mail . " qcode = " . $dest_qcode . " name = " . $dest_name;
                continue;
            }
        }
        $sendmail = new Sendmail();
        $sendmail->set_from($from);
        $to = $dest_mail;

        if ($copy != '-1')
        {
            $dest_mail = $dest_mail . ',' . $from;
        }
        $sendmail->mailto($dest_mail);
        $sendmail->set_subject($subject);

        if (strlen(trim($message)) == 0)
        {
            $message = $subject;
        }

        $sendmail->set_message($message);
        $ofile = new FileToSend($filetosend);
        $sendmail->add_file($ofile);
        try
        {
            $sendmail->compose();
            $sendmail->send();
            $feedback[] = _('Envoi facture ') . $invoice['jr_pj_name'] . _(' destinataire ') . $dest_qcode . " " . $dest_name . " " . $dest_mail;
            /**
             * Save into follow up
             */
            $action = new Follow_Up($cn);
            $a_follow['ag_dest'] = $ag_dest;
            $ag_comment =  _("Envoi facture") . " " . $invoice['jr_pj_name']." "._(' à ').$dest_mail.PHP_EOL.$message;
            $a_follow['ag_comment']=$ag_comment;
            $a_follow['ag_title']=$ag_title;
            $a_follow['ag_timestamp']=$ag_timestamp;
            $a_follow['ag_remind_date']=$ag_remind_date;
            $a_follow['dt_id']=HtmlInput::default_value_post('dt_id',-1);
            $action->fromArray($a_follow);

            $action->f_id_dest = $invoice['qs_client'];
            $action->qcode_dest = $dest_qcode;
            $_POST['nb_item'] = 0;
            $action->save();
// return $fiche->strAttribut(ATTR_DEF_QUICKCODE) . ' inclus dans Suivi';
        } catch (Exception $e)
        {
            $feedback[] = _('Envoi echoué') . " " . $e->getMessage() . " $dest_qcode $dest_name $dest_mail ";
        }
    } else if ($invoice['jr_pj_name'] == "" || $invoice['jr_pj'] == "")
    {
        $feedback[] = _('Aucune pièce à envoyer') . " $dest_qcode $dest_name $dest_mail";
    } else if ($dest_mail == "" || $dest_mail == NOTFOUND)
    {
        $feedback[] = _('Aucune adresse email trouvée') . " $dest_qcode $dest_name $dest_mail";
    }
}
$cn->commit();
?>
<ol>
    <?php foreach ($feedback as $line): ?>

        <li>
            <?php echo $line; ?>
        </li>
    <?php endforeach; ?>
</ol>


