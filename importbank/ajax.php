<?php
require_once('class_database.php');
require_once('include/class_bank_item.php');
require_once('bank_constant.php');

extract($_GET);
$cn=new Database($gDossier);
$html='';$extra='';$ctl='';
switch($act) {
case 'show':
  /*
   * Show detail operation and let you save it or remove it
   *array (
	  'gDossier' => '77',
	  'plugin_code' => 'IMPBANK',
	  'act' => 'show',
          'id' => '15967',
	  'ctl' => 'div15967',
	  )
   */
  ob_start();
  $ctl=$_GET['ctl'];
  $bi=new Bank_Item($id);
  $r='';
  global $msg;
  $msg='';

  if (  isset($_GET['remove'] ))
    {
      $msg="Opération a effacer";
      $bi->show_delete($ctl);

      $bi_sql=new Temp_Bank_Sql($cn,$id);
      $bi_sql->status='D';
      $bi_sql->update();
      $extra='{"id":"'.$id.'","msg":"<span style=\"color:red\">Effacer</span>"}';

    }
  else   if (  isset($_GET['recup'] ))
    {
      $msg="Opération récupérée";
      $bi_sql=new Temp_Bank_Sql($cn,$id);
      $bi_sql->status='N';
      $bi_sql->update();
      $bi->show_item($ctl);
      $extra='{"id":"'.$id.'","msg":"<span style=\"color:red\">Récupérer</span>"}';

    }

    else 
      if (isset($_GET['save']))
	{
	  
	  if ($_GET['fiche'.$id] != '')
	    {
	      $bi_sql=new Temp_Bank_Sql($cn,$id);

	      $f_id=$cn->get_value('select f_id from fiche_Detail
					where
					ad_value=upper(trim($1)) and ad_id=23',array($_GET['fiche'.$id]));

	      if ($f_id == '') $f_id=null;
	      $bi_sql->f_id=$f_id;
	      $rec=$_GET['e_concerned'.$id];
	      $bi_sql->tp_rec=(trim($rec) != '')?trim($rec):null;
	      $bi_sql->status='W';
	      $bi_sql->libelle=$_GET['libelle'];
	      $bi_sql->amount=$_GET['amount'];
	      $bi_sql->tp_extra=$_GET['tp_extra'];
	      $bi_sql->tp_third=$_GET['tp_third'];
	      $bi_sql->tp_date=$_GET['tp_date'];

	      $bi_sql->update();
	    }
	  $msg="Attente";
	  $bi->show_item($ctl);
	  $extra='{"id":"'.$id.'","msg":"<span style=\"color:red\">Attente</span>"}';
	}
      else
	$bi->show_item($ctl);
  $r=ob_get_contents();
  ob_clean();
}

$html=escape_xml($r);
$extra=escape_xml($extra);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$html</code>
<extra>$extra</extra>
</data>
EOF;
?>