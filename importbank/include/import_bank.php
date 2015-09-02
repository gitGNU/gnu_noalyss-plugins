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

/*
 * Step 1/4
 */
if ( ! isset ($_REQUEST ['sb']))
  {
    echo '<div class="content" style="width:80%;margin-left:10%">';
    echo '<form method="get">';
    $iselect=new ISelect('format');
    $iselect->value=$cn->make_array('select id,format_name from importbank.format_bank order by format_name');
    $new=array('value'=>0,'label'=>'--nouveau--');
    $iselect->value[]=$new;
    require_once('template/import_new.php');
    echo HtmlInput::submit('select_submit','Valider');
    echo HtmlInput::get_to_hidden(array('gDossier','plugin_code','sa','ac'));
    echo HtmlInput::hidden('sb','select_form');
    echo '</form>';
    echo '</div>';
    exit();
  }

/*
 * Initialize all the fields
 */
$format=new IText('format_name');
$jrn_def=new ISelect('jrn_def');
$jrn_def->value=$cn->make_array('select jrn_def_id,jrn_def_name from jrn_def where '.$g_user->get_ledger_sql('FIN',3).' order by jrn_def_name');
$sep_decimal=new ISelect('sep_dec');
$sep_decimal->value=$adecimal;

$sep_thousand=new ISelect('sep_thous');
$sep_thousand->value=$athousand;

$sep_field=new ISelect('sep_field');
$sep_field->value=$aseparator;

$col_valid=new INum('col_valid');

$format_date=new ISelect('format_date');
$format_date->value=$aformat_date;

$file=new IFile('import_file');
$skip=new INum('skip');
$skip->value=0;
$skip->size=5;

$nb_col=new INum('nb_col');

/*
 * Step 2 : show the selected format and upload the file
 */
if ( $_REQUEST ['sb'] == 'select_form')
  {

    if ( $_GET['format'] != '0')
      {
	$format_bank=new Format_Bank_Sql($cn,$_GET['format']);
	if ($cn->size() == 1)
	  {
	    $format->value=$format_bank->format_name;
	    $jrn_def->selected=$format_bank->jrn_def_id;
	    $sep_field->selected=$format_bank->sep_field;
	    $sep_thousand->selected=$format_bank->sep_thousand;
	    $sep_decimal->selected=$format_bank->sep_decimal;
	    $format_date->selected=$format_bank->format_date;
	    $nb_col->value=$format_bank->nb_col;
	    $skip->value=$format_bank->skip;
	  }
    else
      {
	throw new Exception('Nombre de ligne trouvé incorrect');
      }
      }
    echo '<div class="content" style="width:80%;margin-left:10%">';
    $sb='upload_file';
    require_once ('template/show_field.php');
    echo '</div>';
    exit();
  }

/*
 * Step 3: upload the file, show it and let change the values of the format
 */
if ( $_POST['sb']=='upload_file')
  {
    /*
     * First time or the format is not corrected
     */
    if ( ! isset($_POST['correct_format']))
      {
	$format->value=$_POST['format_name'];
	$jrn_def->selected=$_POST['jrn_def'];
	$sep_field->selected=$_POST['sep_field'];
	$sep_thousand->selected=$_POST['sep_thous'];
	$sep_decimal->selected=$_POST['sep_dec'];
	$format_date->selected=$_POST['format_date'];
	$nb_col->value=$_POST['nb_col'];
	$skip->value=$_POST['skip'];

	if ( trim($_FILES['import_file']['name']) == '')
	  {
	    alert('Pas de fichier donné');
	    return -1;
	  }
	$filename=tempnam($_ENV['TMP'],'upload_');
	move_uploaded_file($_FILES["import_file"]["tmp_name"],$filename);
	$fbank=fopen($filename,'r');
	$pos_date=$pos_amount=$pos_lib=$pos_operation_nb=$pos_third=$pos_extra=-1;

	// Load the order of the header
	if ( $_POST['format'] != 0)
	  {
	    $format_bank=new Format_Bank_Sql($cn,$_POST ['format']);
	    $pos_date=$format_bank->pos_date;
	    $pos_amount=$format_bank->pos_amount;
	    $pos_lib=$format_bank->pos_lib;
	    $pos_operation_nb=$format_bank->pos_operation_nb;
	    $pos_third=$format_bank->pos_third;
	    $pos_extra=$format_bank->pos_extra;

	  }

	echo '<div class="content" style="width:80%;margin-left:10%">';
	$sb='confirm';
	require_once ('template/confirm_transfer.php');
	echo '</div>';
	exit();
      }
    else
      {
	$format->value=$_POST['format_name'];
	$jrn_def->selected=$_POST['jrn_def'];
	$sep_field->selected=$_POST['sep_field'];
	$sep_thousand->selected=$_POST['sep_thous'];
	$sep_decimal->selected=$_POST['sep_dec'];
	$format_date->selected=$_POST['format_date'];
	$nb_col->value=$_POST['nb_col'];
	$skip->value=$_POST['skip'];


	$filename=$_POST['filename'];

	$fbank=fopen($filename,'r');
	$pos_date=$pos_amount=$pos_lib=$pos_operation_nb=-1;

	$pos_date=$pos_amount=$pos_lib=$pos_operation_nb=$pos_third=$pos_extra=-1;

	// Load the order of the header
	if ( $_POST['format'] != 0)
	  {
	    $format_bank=new Format_Bank_Sql($cn,$_POST ['format']);
	    $pos_date=$format_bank->pos_date;
	    $pos_amount=$format_bank->pos_amount;
	    $pos_lib=$format_bank->pos_lib;
	    $pos_operation_nb=$format_bank->pos_operation_nb;
	    $pos_third=$format_bank->pos_third;
	    $pos_extra=$format_bank->pos_extra;

	  }

	echo '<div class="content" style="width:80%;margin-left:10%">';
	$sb='confirm';
	require_once ('template/confirm_transfer.php');
	echo '</div>';
	exit();
      }
  }
/*
 * Step 4
 * The file is now uploaded, we put in temp and show what has be done, and save the format (or update
 *  if already exist)
 */
if ( $_POST['sb'] == 'confirm')
  {
    $id=($_POST['format'] == 0)?-1:$_POST['format'];

    $format->value=$_POST['format_name'];
    $jrn_def->selected=$_POST['jrn_def'];
    $sep_field->selected=$_POST['sep_field'];
    $sep_thousand->selected=$_POST['sep_thous'];
    $sep_decimal->selected=$_POST['sep_dec'];
    $format_date->selected=$_POST['format_date'];
    $nb_col->value=$_POST['nb_col'];
    $skip->value=$_POST['skip'];

    $format_bank=new Format_Bank_Sql($cn,$id);
    $format_bank->format_name=$_POST['format_name'];
    $format_bank->jrn_def_id=$_POST['jrn_def'];
    $format_bank->sep_field=$_POST['sep_field'];
    $format_bank->sep_thousand=$_POST['sep_thous'];
    $format_bank->sep_decimal=$_POST['sep_dec'];
    $format_bank->format_date=$_POST['format_date'];
    $format_bank->nb_col=$_POST['nb_col'];
    $format_bank->skip=$_POST['skip'];
     /**
    * Check that the destination ledger is well configured and the accounting
    * is properly set and existing
    */
    $jrn_def_id= $jrn_def->selected;

    if ($jrn_def_id == 0 ) {
       alert(_('Journal financier mal configuré'));
       return;
    }

    // Check if the accounting is correct and exist
    $exist = Import_Bank::check_bank_account($jrn_def_id);

    if ( $exist == 0 ) {
       alert(_('Poste comptable de la fiche banque est incorrect'));
       return;
    }
    /*
     * Verify that we have at least date + amount, and not duplicate
     */
    $check=Import_Bank::is_valid_header($_POST['header']);
    if ( $check != '' )
      {
	alert($check);
	/*
	 * Back to step 3 !
	 */
	$filename=$_POST['filename'];

	$fbank=fopen($filename,'r');
	$pos_date=$pos_amount=$pos_lib=$pos_operation_nb=-1;

	// Load the order of the header
	if ( $_POST['format'] != 0)
	  {
	    $format_bank=new Format_Bank_Sql($cn,$_POST ['format']);
	    $pos_date=$format_bank->pos_date;
	    $pos_amount=$format_bank->pos_amount;
	    $pos_lib=$format_bank->pos_lib;
	    $pos_operation_nb=$format_bank->pos_operation_nb;
	    $pos_third=$format_bank->pos_third;
	    $pos_extra=$format_bank->pos_extra;
	  }

	echo '<div class="content" style="width:80%;margin-left:10%">';
	$sb='confirm';
	require_once ('template/confirm_transfer.php');
	echo '</div>';
	exit();
      }

    /*
     * save the column position for the date, amount,...
     */
    for($i=0;$i<count($_POST['header']);$i++)
      {
	switch($_POST['header'][$i])
	  {
	  case 0:
	    $format_bank->pos_date=$i;
	    break;
	  case 1:
	    $format_bank->pos_amount=$i;
	    break;
	  case 2:
	    $format_bank->pos_lib=$i;
	    break;
	  case 3:
	    $format_bank->pos_operation_nb=$i;
	    break;
	  case 4:
	    $format_bank->pos_third=$i;
	    break;
	  case 5:
	    $format_bank->pos_extra=$i;
	    break;
	  }
      }
    $format_bank->save();

    /*
     *read file and save it into importbank.temp_bank
     */
    $fbank=fopen($_REQUEST['filename'],'r');
    echo '<div class="content" style="width:80%;margin-left:10%">';
    require_once('template/show_transfer.php');
    echo '</div>';
  }
?>