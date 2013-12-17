<form onsubmit="send_email(); return false;" id="parameter_send_email_input_frm">
<?php
    echo HtmlInput::array_to_hidden(array('gDossier','plugin_code','ac','lc_id'), $_REQUEST);
    echo HtmlInput::hidden('act','send_mail');
?>
<p>
    <label>De </label><?php echo $from->input();?>
</p>
<p>
    <label>Sujet</label><?php echo $subject->input();?>
</p>
<p>
    <label>Message</label><?php echo $message->input();?>
</p>

<p>
    <label>Attache</label><?php echo $attach->input();?>
</p>
<?php
echo HtmlInput::submit("send_mail", _('Envoi'), '', 'smallbutton');
echo HtmlInput::button_close('parameter_send_mail_input');
?>
</form>