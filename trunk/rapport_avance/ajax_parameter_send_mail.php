<?php
/**
 * Display form to enter parameters
 */
require_once 'include/class_rapav_listing_compute.php';
require_once 'class_fiche_def.php';

ob_start();
$compute=new RAPAV_Listing_Compute();
$compute->load($_GET['lc_id']);
$fiche_def=new Fiche_Def($cn,$compute->listing->data->getp('fiche_def_id'));
if ( $fiche_def->HasAttribute(ATTR_DEF_EMAIL) == false) {
    echo '<p class="notice">';
    echo _("Cette catégorie n'a pas d'attribut email");
    echo '</p>';
} else {
    echo HtmlInput::title_box(_('Envoi par email'), "parameter_send_mail_input");
    $subject=new IText('p_subject');
    $from=new IText('p_from');
    $message=new ITextarea('p_message');
    $attach=new ISelect('p_attach');
    $attach->value=array (
            array('value'=>0,'label'=>_('Aucun document')),
            array('value'=>1,'label'=>_('Document en PDF')),
            array('value'=>2,'label'=>_('Document généré'))
    );
    $copy=new ICheckBox('copy');
    require_once 'include/template/parameter_send_mail_input.php';
    
}
$response = ob_get_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
</data>
EOF;
?>        