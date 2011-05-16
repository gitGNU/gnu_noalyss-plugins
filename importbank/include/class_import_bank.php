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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief Manage import
 */
require_once('class_format_bank_sql.php');


class Import_Bank
{
  /**
   *@brief for the form we have here all the hidden variables
   *@return html string with the hidden dossier, plugin_code,action(sa)
   */
  static function hidden()
  {
    $r=HtmlInput::extension().Dossier::hidden();
    return $r;
  }
  /**
   *@brief show the first screen, you must here enter the date format
   * the file, the card category,
   *@return html string
   */
  static function new_import()
  {
    global $cn;
    ob_start();
    $hidden=self::hidden().HtmlInput::hidden('sa','test');
    $delimiter=new IText('rdelimiter');
    $delimiter->size=1;
    $delimiter->value=',';

    $fd=new ISelect('rfichedef');
    $fd->value=$cn->make_array('select fd_id,fd_label from fiche_def order by 2');
    $file=new IFile('csv_file');
    $encodage=new ICheckBox('encodage');
    $encodage->selected=true;

    require_once('template/input_file.php');
    $r=ob_get_contents();
    ob_clean();
    echo $r;

  }
  /**
   *Test the CSV file, show the choosed delimiter, the CSV parsed, 
   * and replace column header by attribute
   *@return 0 ok,  -1 error
   */
  static function test_import()
  {
    global $cn;
    $hidden=self::hidden().HtmlInput::hidden('sa','record');

    if ( trim($_FILES['csv_file']['name']) == '')
      {
	alert('Pas de fichier donné');
	return -1;
      }
    $filename=tempnam($_ENV['TMP'],'upload_');
    move_uploaded_file($_FILES["csv_file"]["tmp_name"],$filename);

    $file_cat=$cn->get_value('select fd_label from fiche_def where fd_id=$1',array($_POST['rfichedef']));
    $encoding=(isset ($_REQUEST['encodage']))?'Unicode':'latin1';
    require_once('template/test_file.php');
    return 0;
  }
  /**
   *@brief record all rows
   *@param
   *@return
   *@note
   *@see
@code
array
  'plugin_code' => string 'IMPCARD' (length=7)
  'gDossier' => string '30' (length=2)
  'sa' => string 'record' (length=6)
  'rfichedef' => string '17' (length=2)
  'rdelimiter' => string ',' (length=1)
  'encodage' => string '' (length=0)
  'record_import' => string 'Valider' (length=7)
  'head_col' => 
    array
      0 => string '15' (length=2)
      1 => string '14' (length=2)
      2 => string '-1' (length=2)
      3 => string '-1' (length=2)
      4 => string '-1' (length=2)
      5 => string '-1' (length=2)

@endcode
   */
  static function record_import()
  {
    global $cn;
    extract ($_POST);
    $fd=fopen($filename,'r');
    /*
     * Check the column
     */
    $valid_col=0;$valid_name=0;$duplicate=0;$valid_qcode=0;$valid_accounting=0;
    for ($i=0;$i<count($head_col);$i++)
      {
	if ($head_col[$i] != -1 )$valid_col++;
	if ($head_col[$i] == 1 ) $valid_name=1;
	if ( $head_col[$i] == ATTR_DEF_QUICKCODE ) $valid_qcode=1;
	if ( $head_col[$i] == ATTR_DEF_ACCOUNT ) $valid_accounting=1;

	for ($e=$i+1;$e<count($head_col);$e++)
	  if ($head_col[$i]==$head_col[$e] && $head_col[$e] != -1)
	    $duplicate++;
      }

    if ( $valid_col==0)
      {
	alert("Aucune colonne n'est définie");
	return -1;
      }
    if ( $valid_name==0)
      {
	alert("Les fiches doivent avoir au minimum un nom");
	return -1;
      }
    if ( $duplicate != 0)
      {
	alert('Vous avez défini plusieurs fois la même colonne');
	return -1;
      }

      
    /*
     * read the file and record card
     */

    $row_count=0;

    echo '<table>';

    ob_start();
    while (($row=fgetcsv($fd,0,$_POST['rdelimiter'],$_POST['rsurround'])) !== false)
      {
	$fiche=new Fiche($cn);
	$array=array();
	$row_count++;
	echo '<tr style="border:solid 1px black">';
	echo td($row_count);
	$count_col=count($row);
	$col_count=0;
	for ($i=0;$i<$count_col;$i++)
	  {
	    if ( $head_col[$i]==-1) continue;

	    $header[$col_count]=$head_col[$i];
	    $col_count++;

	    echo td ($row[$i]);
	    $attr=sprintf('av_text%d',$head_col[$i]);
	    $array[$attr]=$row[$i];
	  }
	/*
	 * If no quick code is given we compute it ourself
	 */
	if ( $valid_qcode == 0)
	  {
	    $attr=sprintf('av_text%d',ATTR_DEF_QUICKCODE);
	    $array[$attr]='FID';
	  }
	/*
	 * Force the creating of an accounting
	 */
	if ($valid_accounting==0)
	  {
	    $attr=sprintf('av_text%d',ATTR_DEF_ACCOUNT);
	    $array[$attr]='FID';
	  }
	$fiche->insert($rfichedef,$array);
	echo '</tr>';
      }
    $table_content=ob_get_contents();
    ob_clean();
    echo '<tr>';
    echo th('');
    for ($e=0;$e<count($header);$e++)
      {
	$name=$cn->get_value('select ad_text from attr_def where ad_id=$1',array($header[$e]));
	echo th($name);
      }
    echo '</tr>';
    echo $table_content;

    echo '</table>';
    $name=$cn->get_value('select fd_label from fiche_def where fd_id=$1',array($rfichedef));
    $cn->get_value('select comptaproc.fiche_attribut_synchro($1)',array($rfichedef));
    echo '<span class="notice">';
    echo $row_count.' fiches sont insérées dans la catégorie '.$name;
    echo '</span>';
    return 0;
  }
}