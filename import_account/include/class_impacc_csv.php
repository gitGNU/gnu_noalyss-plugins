<?php

/*
 * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/* * *
 * @file 
 * @brief
 *
 */
require_once 'class_impacc_csv_bank.php';
require_once 'class_impacc_csv_sale_purchase.php';
require_once 'class_impacc_csv_misc_operation.php';
require_once DIR_IMPORT_ACCOUNT."/database/class_impacc_import_detail_sql.php";
class Impacc_CSV
{
    var $delimiter;
    var $surround;
    var $ledger;
    var $encoding;
    var $decimal;
    var $thousand;
    var $detail; //! Impacc_Import_csv_SQL
    var $import_id;
    function __construct()
    {
        $this->delimiter=",";
        $this->surround='"';
        $this->ledger=3;
        $this->encoding="utf-8";
        $this->filename="";
        $this->decimal='.';
        $this->thousand='';
        $this->detail=null;
    }
    /**
     * Display a form to upload a CSV file with operation
     * 
     * @global type $cn Database Connx
     */
    function input_format()
    {
        global $cn;
        $in_delimiter=new IText('in_delimiter',htmlentities($this->delimiter));
        $in_delimiter->size=1;
        
        $in_surround=new IText('in_surround',  htmlentities($this->surround));
        $in_surround->size=1;
        
        $ledger=new Acc_Ledger($cn,$this->ledger);
        $in_ledger=$ledger->select_ledger('ALL', 3);
        $in_ledger->name='in_ledger';
        
        $in_encoding=new ISelect('in_encoding');
        $in_encoding->value= array (
                    array('value'=>"utf-8",'label'=>_('Unicode')),
                    array('value'=>"latin1",'label'=>_('Latin'))
                    );
        
        $in_encoding->selected=$this->encoding;
        $in_decimal=new IText('in_decimal',$this->decimal);
        $in_decimal->size=1;
        
        $in_thousand=new IText('in_thousand',$this->thousand);
        $in_thousand->value=$this->thousand;
        $in_thousand->size=1;
        
        require_once DIR_IMPORT_ACCOUNT.'/template/upload_operation.php';
    }
    function set_import($p_file_id)
    {
        $this->import_id=$p_file_id;
    }
    function set_setting() 
    {
        $this->delimiter=HtmlInput::default_value_post("in_delimiter", "");;
        $this->surround=HtmlInput::default_value_post("in_surround", "");
        $this->ledger=HtmlInput::default_value_post("in_ledger", "");
        $this->encoding=HtmlInput::default_value_post("in_encoding", "");
        $this->decimal=HtmlInput::default_value_post("in_decimal", "");
        $this->thousand=HtmlInput::default_value_post("in_thousand", "");;
        
        
    }
    function check_setting()
    {
        // Check if valid
        // 1 sep for thousand and decimal MUST be different
        
        //2 encoding and delimiter can not be empty
        
        //3 ledger must be writable for user
    }
    function save_setting()
    {
        $cn=Dossier::connect();
        $this->detail=new Impacc_Import_csv_SQL($cn);
        $this->detail->s_decimal=$this->decimal;
        $this->detail->s_surround=$this->surround;
        $this->detail->s_encoding=$this->encoding;
        $this->detail->s_delimiter=$this->delimiter;
        $this->detail->s_thousand=$this->thousand;
        $this->detail->jrn_def_id=$this->ledger;
        $this->detail->import_id=$this->import_id;
        $this->detail->save();
    }
    function get_setting()
    {
        $this->decimal=$this->detail->s_decimal;
        $this->surround=$this->detail->s_surround;
        $this->encoding=$this->detail->s_encoding;
        $this->delimiter=$this->detail->s_delimiter;
        $this->thousand=$this->detail->s_thousand;
        $this->ledger=$this->detail->jrn_def_id;
        $this->import_id=$this->detail->import_id;

    }
  
    function load_import($p_import_id) 
    {
        $cn=Dossier::connect();
        $id=$cn->get_value('select id from impacc.import_csv where import_id=$1',
                array($p_import_id));
        $this->detail=new Impacc_Import_csv_SQL($cn,$id);
        $this->detail->load();
        $this->get_setting();
    }
    function record(Impacc_File $p_file)
    {
        // retrieve the import setting file 
        $this->load_import($p_file->impid);
        $cn=Dossier::connect();
        $ledger=new Acc_Ledger($cn,$this->detail->jrn_def_id );

        switch ($ledger->get_type()) {
            case 'ACH':
                $ach=new Impacc_Csv_Sale_Purchase();
                $ach->record($this,$p_file);
                break;
            case 'VEN':
                $ven=new Impacc_Csv_Sale_Purchase();
                $ven->record($this,$p_file);
                break;
            case 'ODS':
                $ods=new Impacc_Csv_Misc_Operation();
                $ods->record($this,$p_file);
                break;
            case 'FIN':
                $fin=new Impacc_Csv_Bank();
                $fin->record($this,$p_file);
                break;
                
        }

    }
}
