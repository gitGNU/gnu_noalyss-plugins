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

/**
 * @file
 * @brief
 *
 */
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_csv.php";
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_operation.php";
require_once DIR_IMPORT_ACCOUNT."/database/class_impacc_import_file_sql.php";
require_once DIR_IMPORT_ACCOUNT."/database/class_impacc_import_csv_sql.php";

class Impacc_File
{

//--------------------------------------------------
// Will be replaced by Impacc_CSV as soon as the XML
// 
// Import is proposed
//-----------------------------------------------------    

    static $aformat=array(1=>"CSV");
    var $format;
    var $filename;
    var $import_file; //!< Impacc_Import_file_SQL $impid

    /// Save the uploaded file and CSV setting if it is CSV import

    function save_file()
    {
        if (trim($_FILES['file_operation']['name'])=='')
        {
            alert(_('Pas de fichier donné'));
            return -1;
        }
        $format=HtmlInput::default_value_post("format", -1);

        if (!isset(self::$aformat[$format]))
        {
            alert(_("Format inconnu"));
            return -1;
        }
        $this->format=self::$aformat[$format];
        $this->filename=tempnam($_ENV['TMP'], 'upload_');
        if (!move_uploaded_file($_FILES["file_operation"]["tmp_name"],
                        $this->filename))
        {
            throw new Exception(_("Fichier non sauvé"), 1);
        }
        $cn=Dossier::connect();
        $imp=new Impacc_Import_file_SQL($cn);
        $imp->setp('i_tmpname', $this->filename);
        $imp->setp('i_filename', $_FILES['file_operation']['name']);
        $imp->setp("i_type", self::$aformat[$format]);
        $imp->insert();
        $this->import_file=$imp;
        $this->impid=$imp->getp("id");

        // For CSV only
        if ($imp->i_type=="CSV")
        {
            try
            {
                $csv=new Impacc_CSV();
                $csv->set_import($this->impid);
                $csv->set_setting();
                $csv->check_setting();
                $csv->save_setting();
            }
            catch (Exception $ex)
            {
                error_log($ex->getTraceAsString());
                echo _("Format invalide")," : ",$ex->getMessage();
            }
        }
    }
    function load($p_import_id)
    {
        $cn=Dossier::connect();
        $this->import_file=new Impacc_Import_file_SQL($cn,$p_import_id);
        
    }
    /// Load the file in a temporary table
    function record()
    {
        $operation=new Impacc_Operation();
        $operation->record_file($this);
        $cn=Dossier::connect();
        $cn->exec_sql("update impacc.import_file set i_date_transfer=now() where id=$1",
                array($this->import_file->id));
    }

    /// Display the parameters and the file
    /// to upload
    function input_file()
    {

        $file=new IFile("file_operation");
        $format=new ISelect("format");
        $format->id="format_sel";
        $format->value=array(
            array("value"=>0, "label"=>"-"),
            array("value"=>1, "label"=>"CSV")
        );
        $format->javascript="onchange=\"ctl_display()\"";
        require_once DIR_IMPORT_ACCOUNT."/template/input_file.php";
    }

    /// Check the rows of the imported file 
    function check()
    {
        if ($this->import_file->i_type=='CSV')
        {
            $operation=new Impacc_Operation();
            $operation->check($this);
        }
    }

    /// Display the rows of the imported file and a status for each row
    function result()
    {
        if ($this->import_file->i_type=='CSV')
        {
            $operation=new Impacc_CSV();
            $operation->result($this);
        }
    }
    /// Transfer to accountancy
    function transfer()
    {
        if ($this->import_file->i_type=='CSV')
        {
            $operation=new Impacc_CSV();
            $operation->load_import($this->impid);
            $operation->transfer();
        }
    }

    /// Show the result of the file import
    function result_transfer()
    {
        if ($this->import_file->i_type == 'CSV')
        {
            $cn=Dossier::connect();
            // Show first the successfully transfered record
            // Show the failed
            $operation=new Impacc_CSV();
            $operation->load_import($this->import_file->id);
            // Show the target ledger
            $ledger=new Acc_Ledger($cn,$operation->detail->jrn_def_id);
            echo "<h1>";
            printf(_("Importation dans le journal %s"),$ledger->get_name());
            echo "</h1>";
            $operation->result($this);
        }
    }
    function display_list()
    {
        $cn=Dossier::connect();
        $array=$cn->get_array(
                "
                 select   
                 id,
                 i_filename,
                 i_type,
                 to_char(i_date_transfer,'DD.MM.YY HH:MI') as stransfer,
                 to_char(i_date_import,'DD.MM.YY HH:MI') as simport
                 from 
                 impacc.import_file
                 order by i_date_import desc
                "
                );
        require_once DIR_IMPORT_ACCOUNT."/template/history_file.php";
    }
    /// Delete a row in impacc.import_file
    function delete($id)
    {
        $cn=Dossier::connect();
        $cn->exec_sql("delete from impacc.import_file where id=$1",array($id));
        
    }
}

?>
