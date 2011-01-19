<?php
require_once('class_database.php');
require_once('class_ext_tva.php');
require_once('class_ibutton.php');
require_once ('class_ext_list_intra.php');
require_once ('class_ext_list_assujetti.php');
require_once('class_acc_ledger.php');

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
    /* display the declaration for INTRACOM */
    $decl=new Ext_List_Intra($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }
  if ( $type == 2) {
    /* display the declaration of customer */
    $decl=new Ext_List_Assujetti($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }

  break;
case 'rw':
  require_once('class_acc_ledger.php');
  $r='<div style="float:right"><a class="mtitle" href="javascript:void(0)" onclick="removeDiv(\'record_write\')">fermer</a></div>';
  $ctl='record_write';
  $ledger=new Acc_ledger($cn,0);
  $sel_ledger=$ledger->select_ledger('ODS',1);
  $r.='<form onsubmit="save_write(this);return false;">';
  $decl=new Ext_Tva($cn);
  $decl->set_parameter('id',$_GET['p_id']);
  $decl->load();
  $date=new IDate('pdate');
  $r.="Date :".$date->input().'<br>';
  $r.="Choix du journal : ".$sel_ledger->input();
  $r.=$decl->propose_form();
  $r.=HtmlInput::hidden('mt',microtime(true));
  $r.=HtmlInput::extension();
  $r.=dossier::hidden();
  $r.=HtmlInput::submit('save','Sauver','onclick="return confirm(\'Vous confirmez ? \')"');
  $r.='</form>';
  break;
case 'sw':
  $ctl='record_write';
  ob_start();
  echo   '<div style="float:right"><a class="mtitle" href="javascript:void(0)" onclick="removeDiv(\'record_write\')">fermer</a></div>';
  extract($_GET);
  try {
    $array=array();
    $array['e_date']=$pdate;
    /* give automatically the periode */
    $periode=new Periode($cn);
    $periode->find_periode($pdate);
    $array['period']=$periode->p_id;
    $nb_item=count($account);
    for ($i=0;$i<count($account);$i++) {
      $array['amount'.$i]=$amount[$i];
      $array['poste'.$i]=$account[$i];
      $array['ld'.$i]='';
      if ( isset($deb[$i])) $array['ck'.$i]=1;
    }
    /* ATVA */
    if ( isset($atva)) {
      $array['poste'.$i]=$atva;
      $array['amount'.$i]=$atva_amount;
      $array['ld'.$i]='';
      if ( isset($atva_ic)) $array['ck'.$i]=1;      
      $i++;
      $nb_item++;
    }
    /* CRED */
   if ( isset($crtva)) {
      $array['poste'.$i]=$crtva;
      $array['amount'.$i]=$crtva_amount;
      $array['ld'.$i]='';
      if ( isset($crtva_ic)) $array['ck'.$i]=1;      
      $i++;
      $nb_item++;
    }
    /* DET */
   if ( isset($dttva)) {
      $array['poste'.$i]=$dttva;
      $array['amount'.$i]=$dttva_amount;
      $array['ld'.$i]='';
      if ( isset($dttva_ic)) $array['ck'.$i]=1;      
      $i++;
      $nb_item++;
    }
    /* solde */
   if ( isset($solde)) {
      $array['poste'.$i]=$solde;
      $array['amount'.$i]=$solde_amount;
      $array['ld'.$i]='';
      if ( isset($solde_ic)) $array['ck'.$i]=1;      
      $i++;
      $nb_item++;
    }
   $array['nb_item']=$nb_item;
   $array['e_pj']='';
   $array['e_pj_suggest']='NONE';

   $array['p_jrn']=$p_jrn;
   $array['mt']=$mt;

   $array['desc']='Extension TVA : écriture générée';
   $ods=new Acc_Ledger($cn,$p_jrn);
   $ods->save($array);
   echo h2info("Sauvée : ajoutez le numéro de pièce");
   echo   HtmlInput::detail_op($ods->jr_id,'détail opération : '.$ods->internal);
   
  } catch(Exception $e) {
    echo alert($e->getMessage());
  }
  $r=ob_get_contents();
  ob_clean();
   break;
default:
  $r=var_export($_REQUEST,true);
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