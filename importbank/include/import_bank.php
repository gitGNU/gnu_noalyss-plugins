<?
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
    echo HtmlInput::get_to_hidden(array('gDossier','plugin_code','sa'));
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
$jrn_def->value=$cn->make_array('select jrn_def_id,jrn_def_name from jrn_def where '.$user->get_ledger_sql('FIN',3).' order by jrn_def_name');
$sep_decimal=new ISelect('sep_dec');
$sep_decimal->value=array(
			  array ('value'=>0,'label'=>' '),
			  array ('value'=>1,'label'=>','),
			  array ('value'=>2,'label'=>'.')
			  );
    
$sep_thousand=new ISelect('sep_thous');
$sep_thousand->value=array(
			   array ('value'=>0,'label'=>' '),
			   array ('value'=>1,'label'=>','),
			   array ('value'=>2,'label'=>'.')
			   );
    
$sep_field=new ISelect('sep_field');
$sep_field->value=array(
			array ('value'=>1,'label'=>','),
			array ('value'=>2,'label'=>';')
			);
$col_valid=new INum('col_valid');
    
$format_date=new ISelect('format_date');
$format_date->value=array(
			array ('value'=>1,'label'=>'DD.MM.YYYY'),
			array ('value'=>2,'label'=>'DD/MM/YYYY'),
			array ('value'=>3,'label'=>'DD-MM-YYYY'),
			array ('value'=>4,'label'=>'DD.MM.YY'),
			array ('value'=>5,'label'=>'DD/MM/YY'),
			array ('value'=>6,'label'=>'DD-MM-YY')
			);
    
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
    var_dump($_POST);
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
    $pos_date=$pos_amount=$pos_lib=$pos_operation_nb=-1;

    // Load the order of the header
    if ( $_POST['format'] != 0)
      {
	$format_bank=new Format_Bank_Sql($cn,$_POST ['format']);
	$pos_date=$format_bank->pos_date;
	$pos_amount=$format_bank->pos_amount;
	$pos_lib=$format_bank->pos_lib;
	$pos_operation_nb=$format_bank->pos_operation_nb;
      }

    echo '<div class="content" style="width:80%;margin-left:10%">';
    $sb='confirm';
    require_once ('template/confirm_transfer.php');
    echo '</div>';
    exit();
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
    $format_bank->sep_field=$sep_field->display();
    $format_bank->sep_thousand=$sep_thousand->display();
    $format_bank->sep_decimal=$sep_decimal->display();
    $format_bank->format_date=$format_date->display();
    $format_bank->nb_col=$_POST['nb_col'];
    $format_bank->skip=$_POST['skip'];

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