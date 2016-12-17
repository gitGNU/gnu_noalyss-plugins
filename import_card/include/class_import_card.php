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
/* $Revision$ */
// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be
/**\file
 * \brief Manage import
 */
require __DIR__."/class_impcard_file_csv_sql.php";
require __DIR__."/class_impcard_format_sql.php";
class Import_Card
{
    private $record;
    private $format;
    function __construct($p_file_id=-1,$p_format_id=-1)
    {
        global $cn;
        $this->record=new Impcard_File_Csv_SQL($cn, $p_file_id);
        $this->format=new Importcard_Format_SQL($cn, $p_format_id);
      
    }
    /**
     * @brief fill the CSV format object Importcard_Format_SQL
     *  with default values
     * @param int $p_fiche_card if 0 then f_position is empty otherwise , f_position
     * contains the attribute of this card category
     */
    function create_format_temp($p_fiche_card)
    {
        global $cn;
        $this->format->f_name='TEMP';
        $this->format->f_unicode_encoding='Y';
        $this->format->f_card_category=$p_fiche_card;
        $this->format->f_skiprow=0;
        $this->format->f_surround='"';
        $this->format->f_delimiter=";";
        $this->format->f_saved=0;
        if ($p_fiche_card == 0 ){
            $this->format->f_position="-1";
        } else {
             $a_attribute=$cn->make_list(
                "select ad_id from jnt_fic_attr join attr_def using(ad_id) where fd_id=$1 
                    order by jnt_order ",
                    array($p_fiche_card));
                    $this->format->f_position=$a_attribute;
        }
    }
	/**
	 * @brief for the form we have here all the hidden variables
	 * @return html string with the hidden dossier, plugin_code,action(sa)
	 */
	function hidden()
	{
		$r = HtmlInput::extension() . Dossier::hidden();
                $r.= HtmlInput::hidden("record", $this->record->id);
                $r.= HtmlInput::hidden("format", $this->format->id);
		return $r;
	}
        /**
         * @brief propose different possibilities to use your files
         */
        function propose_format()
        {
            global $cn;
            ob_start();
            $hidden = $this->hidden() . HtmlInput::hidden('sa', 'test');
            $delimiter = new IText('rdelimiter');
            $delimiter->size = 1;
            $delimiter->value = $this->format->f_delimiter;
            $fd = new ISelect('rfichedef');
            $fd->value = $cn->make_array('select fd_id,fd_label from fiche_def order by 2');
            $fd->selected=$this->format->f_card_category;
            $encodage = new ICheckBox('encodage');
            $encodage->selected = ($this->format->f_unicode_encoding=="Y")?TRUE:FALSE;
            $skip_row=new INum('skip_row');
            $skip_row->value=$this->format->f_skiprow;
            $select_template = new ISelect("select_template");
            $select_template->value=$cn->make_array("select id,f_name from importcard.format order by 2");
            require_once('template/input_format.php');
            $r = ob_get_contents();
            ob_end_clean();
            echo $r;
        }
	/**
	 * @brief show the first screen,
	 * @return html string
	 */
        function new_import()
	{
		global $cn;
		ob_start();
		$hidden = $this->hidden() . HtmlInput::hidden('sa', 'import');
		$fd = new ISelect('rfichedef');
                 $fd->value = $cn->make_array('select fd_id,fd_label from fiche_def order by 2');
		$file = new IFile('csv_file');
		
		require_once('template/input_file.php');
		$r = ob_get_contents();
		ob_end_clean();
		echo $r;
	}
        /**
         * Upload the file and record the default format and the file
         * @global type $cn
         * @return type
         */
        function save_file()
        {
            global $cn;
            if (trim($_FILES['csv_file']['name']) == '')
            {
                    alert('Pas de fichier donné');
                    return -1;
            }
            $this->record->file_name = tempnam($_ENV['TMP'], 'upload_');
            move_uploaded_file($_FILES["csv_file"]["tmp_name"], $this->record->file_name );
            $this->record->save();
            $file_def=HtmlInput::default_value_request('rfichedef', 0);
            $this->create_format_temp($file_def);
            $this->format->f_card_category=$file_def;
            $this->format->save();
            $hidden = $this->hidden() . HtmlInput::hidden('sa', 'record');

            
        }
        /**
         * fill object Importcard_Format_SQL with the data send by post
         */
        function get_post_format()
        {
            global $cn;
            $this->format->f_card_category= HtmlInput::default_value_request('rfichedef',0);
           
            $this->format->f_skiprow=HtmlInput::default_value_request("skip_row",  0);
            $this->format->f_delimiter = HtmlInput::default_value_request("rdelimiter", $this->format->f_delimiter );
            $this->format->f_surround = HtmlInput::default_value_request("rsurround",  $this->format->f_surround);
            /* If not set , give the default of card category */
            $head_col = HtmlInput::default_value_request("head_col", $this->format->f_position);
            
            // If there is no column head set , then load the default order of the card attribute
            if ( is_array($head_col) ) {
                $this->format->f_position = join($head_col,",");
            } else {
                $a_attribute=$cn->make_list(
                "select ad_id from jnt_fic_attr join attr_def using(ad_id) where fd_id=$1 
                    order by jnt_order ",
                    array($this->format->f_card_category));
                    $this->format->f_position=$a_attribute;
            }
            $this->format->f_unicode_encoding= (isset($_REQUEST['encodage'])) ? 'Y' : 'N';
            $this->format->save();
        }

	/**
	 * Test the CSV file, show the choosed delimiter, the CSV parsed,
	 * and replace column header by attribute
	 * @return 0 ok,  -1 error
	 */
	function test_import()
	{
            global $cn;
            $filename=$this->record->file_name;
            $delimiter=$this->format->f_delimiter;
            $surround=$this->format->f_surround;
            $skip_row=$this->format->f_skiprow;
            $fiche_def=$this->format->f_card_category;
            // Column Header are a select
            $a_header=explode(",", $this->format->f_position);
            $sql=sprintf('select ad_id,ad_text from jnt_fic_attr join attr_def using(ad_id) where fd_id=%d order by ad_text ',$fiche_def);
            $header=new ISelect('head_col[]');
            $header->value=$cn->make_array($sql);
            $header->value[]=array('value'=>-1,'label'=>'-- Non Utilisé --');
            $header->selected=-1;
            
            
            $a_attribute=$cn->get_array(
                "select ad_id,ad_text from jnt_fic_attr join attr_def using(ad_id) where fd_id=$1 order by jnt_order ",
                array($fiche_def)
                );
            $t1_valid_header=$cn->make_list("select ad_id from jnt_fic_attr join attr_def using(ad_id) where fd_id=$1 ",array($fiche_def));
            $a_valid_header=explode(",", $t1_valid_header);
            require_once('template/test_file.php');
            return 0;
	}
	/**
	 * @brief record all rows
	 * @param
	 * @return
	 * @note
	 * @see
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
	function record_import()
	{
		global $cn, $g_failed, $g_succeed;
		$fd = fopen($this->record->file_name, 'r');
                if ($fd == FALSE) 
                {
                    throw new Exception(sprintf(_("Ne peut ouvrir le fichier %s"),
                            $this->record->file_name));
                }
		/*
		 * Check the column
		 */
		$valid_col = 0;
		$valid_name = 0;
		$duplicate = 0;
		$valid_qcode = 0;
		$valid_accounting = 0;
                // Data from DB
                $head_col = explode(",",$this->format->f_position);
                
                
		for ($i = 0; $i < count($head_col); $i++)
		{
			if ($head_col[$i] != -1)
				$valid_col++;
			if ($head_col[$i] == 1)
				$valid_name = 1;
			if ($head_col[$i] == ATTR_DEF_QUICKCODE)
				$valid_qcode = 1;
			if ($head_col[$i] == ATTR_DEF_ACCOUNT)
				$valid_accounting = 1;
			for ($e = $i + 1; $e < count($head_col); $e++)
				if ($head_col[$i] == $head_col[$e] && $head_col[$e] != -1)
					$duplicate++;
		}
		if ($valid_col == 0)
		{
			alert(_("Aucune colonne n'est définie"));
			return -1;
		}
		if ($valid_name == 0)
		{
			alert(_("Les fiches doivent avoir au minimum un nom"));
			return -1;
		}
		if ($duplicate != 0)
		{
			alert(_('Vous avez défini plusieurs fois la même colonne'));
			return -1;
		}
		/*
		 * read the file and record card
		 */
		$row_count = 0;
                $skip_row=$this->format->f_skiprow;

		echo '<table>';
		ob_start();
		while (($row = fgetcsv($fd, 0, $this->format->f_delimiter, $this->format->f_surround)) !== false)
		{
                   
			$row_count++;
                        if ( $skip_row >= $row_count ) continue;
                        $qcode="";
			$fiche = new Fiche($cn);
			$array = array();
			echo '<tr style="border:solid 1px black">';
			echo td($row_count);
			$count_col = count($row);
			$col_count = 0;
			for ($i = 0; $i < $count_col; $i++)
			{
				if ($head_col[$i] == -1)
					continue;
				$header[$col_count] = $head_col[$i];
				$col_count++;
				echo td($row[$i]);
				$attr = sprintf('av_text%d', $head_col[$i]);
				$array[$attr] = $row[$i];
                                if ( $head_col [$i] == ATTR_DEF_QUICKCODE) {
                                    $qcode=$row[$i];
                                }
			}
			/*
			 * If no quick code is given we compute it ourself
			 */
			if ($valid_qcode == 0)
			{
				$attr = sprintf('av_text%d', ATTR_DEF_QUICKCODE);
				$array[$attr] = '';
                                
			}
			/*
			 * Force the creating of an accounting
			 */
			if ($valid_accounting == 0)
			{
				$attr = sprintf('av_text%d', ATTR_DEF_ACCOUNT);
				$array[$attr] = '';
			}
			try
			{
                            /**
                             * if qcode already exists then update otherwise insert
                             */
                            $msg=(_('Ajout'));
                            if ($valid_qcode == 0 || trim($qcode) == "")
                            {
				$fiche->insert($this->format->f_card_category, $array);
                            } else {
                                // Retrieve the card with the qcode
                                $fiche->get_by_qcode($qcode,false);
                                // if qcode is found update otherwise insert
                                if ( $fiche->id !=0) {
                                    $fiche->update($array);
                                    $msg=(_('Mise à jour'));
                                } else {
                                    $fiche->insert($this->format->f_card_category,$array);
                                }
                            }   
				echo td($g_succeed." ".$msg);
			}
			catch (Exception $e)
			{
				echo td($g_failed);
				echo td($e->getMessage());
			}
			echo '</tr>';
		}
		$table_content = ob_get_contents();
		ob_end_clean();
		echo '<tr>';
		echo th('');
		for ($e = 0; $e < count($header); $e++)
		{
			$name = $cn->get_value('select ad_text from attr_def where ad_id=$1', array($header[$e]));
			echo th($name);
		}
		echo '</tr>';
		echo $table_content;
		echo '</table>';
		$name = $cn->get_value('select fd_label from fiche_def where fd_id=$1', array($this->format->f_card_category));
		$cn->get_value('select comptaproc.fiche_attribut_synchro($1)', array($this->format->f_card_category));
		echo '<span class="notice">';
		printf (_('%d fiches sont insérées dans la catégorie %s') ,$row_count , $name);
		echo '</span>';
		return 0;
	}
        function propose_save_template()
        {
            require_once __DIR__."/template/template_save.php";
        }
        function show_template()
        {
            global $cn;
            $select_template=new ISelect("template_id");
            $select_template->value=$cn->make_array("select id,f_name from importcard.format where f_saved=1 order by f_name");
            require_once __DIR__."/template/template_show.php";
            
        }
}