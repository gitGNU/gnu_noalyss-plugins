<?php
require_once('class_database.php');
require_once('class_ext_tva.php');
require_once('class_ibutton.php');
require_once ('class_ext_list_intra.php');
require_once ('class_ext_list_assujetti.php');

extract($_GET);
$cn=new Database($gDossier);
$html='';$extra='';$ctl='';
switch($act) {
case 'dsp_decl':
  /* the hide button */
  $button=new IButton('hide');
  $button->label=_('Retour');
  $button->javascript="$('detail').hide();$('main').show();";
  if ( $type == 1) {
    /* display the declaration of amount */
    $decl=new Ext_Tva($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }
  if ( $type == 3) {
    /* display the declaration of amount */
    $decl=new Ext_List_Intra($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }
  if ( $type == 2) {
    /* display the declaration of amount */
    $decl=new Ext_List_Assujetti($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }

  break;
}

$html=escape_xml($r);

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<html>$html</html>
<extra>$extra</extra>
</data>
EOF;
?>