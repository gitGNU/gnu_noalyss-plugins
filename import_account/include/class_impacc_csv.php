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


require_once 'class_impacc_csv_bank.php';
require_once 'class_impacc_csv_sale_purchase.php';
require_once 'class_impacc_csv_misc_operation.php';
require_once DIR_IMPORT_ACCOUNT."/database/class_impacc_import_detail_sql.php";

$err_code=array(
    "CK_FORMAT_DATE"=>_("Format de date incorrect"),
    "CK_PERIODE_CLOSED"=>_("Période fermée ou période non trouvée"),
    "CK_INVALID_AMOUNT"=>_("Montant invalide"),
    "CK_INVALID_ACCOUNTING"=>_("Poste comptable ou Fiche non existante"));


///Used by all Import CSV Operation , contains the setting (delimiter,thousand ...) 
class Impacc_CSV
{

    
    var $detail; //!< Object Impacc_Import_csv_SQL

    var $errcode; //!< Array of error code will be recorded in import_detail.id_message

    function __construct()
    {
        $cn=Dossier::connect();
        $this->detail=new Impacc_Import_csv_SQL($cn);
        $this->detail->s_delimiter=",";
        $this->detail->s_surround='"';
        $this->detail->jrn_def_id=3;
        $this->detail->s_encoding="utf-8";
        $this->detail->s_decimal='.';
        $this->detail->s_thousand='';
        $this->detail->s_date_format=4;
        $this->errcode=    array(
            "CK_FORMAT_DATE"=>_("Format de date incorrect"),
            "CK_PERIODE_CLOSED"=>_("Période non trouvée"),
            "CK_INVALID_PERIODE"=>_("Période non trouvée"),
            "CK_INVALID_AMOUNT"=>_("Montant invalide"),
            "CK_INVALID_ACCOUNTING"=>_("Poste comptable ou Fiche non existante"),
            "CK_TVA_INVALID"=>_("Code TVA Invalide")
            );

    }

    /// Display a form to upload a CSV file with operation
    function input_format()
    {
        global $cn ,$adecimal,$athousand,$aseparator,$aformat_date ;
        $in_delimiter=new ISelect('in_delimiter');
        $in_delimiter->value=$aseparator;
        $in_delimiter->selected=$this->detail->s_delimiter;
        $in_delimiter->size=1;

        $in_surround=new IText('in_surround',
                $this->detail->s_surround);
        $in_surround->size=1;

        $ledger=new Acc_Ledger($cn, $this->detail->jrn_def_id);
        $in_ledger=$ledger->select_ledger('ALL', 3);
        $in_ledger->name='in_ledger';

        $in_encoding=new ISelect('in_encoding');
        $in_encoding->value=array(
            array('value'=>"utf-8", 'label'=>_('Unicode')),
            array('value'=>"latin1", 'label'=>_('Latin'))
        );
        $in_encoding->selected=$this->detail->s_encoding;
        
        $in_decimal=new ISelect('in_decimal');
        $in_decimal->value=$adecimal;
        $in_decimal->selected=$this->detail->s_decimal;
        $in_decimal->size=1;

        $in_thousand=new ISelect('in_thousand'); 
        $in_thousand->selected=$this->detail->s_thousand;
        $in_thousand->value=$athousand;
        $in_thousand->size=1;

        $in_date_format = new ISelect("in_date_format");
        $in_date_format->value=$aformat_date;
        $in_date_format->selected=$this->detail->s_date_format;
        
        require_once DIR_IMPORT_ACCOUNT.'/template/upload_operation.php';
    }

    function set_import($p_file_id)
    {
        $this->detail->import_id=$p_file_id;
    }
    //---------------------------------------------------------------------
    ///Get value from post , fill up the Impacc_Import_csv_SQL object
    //---------------------------------------------------------------------
    
    function set_setting()
    {
        $this->detail->s_delimiter=HtmlInput::default_value_post("in_delimiter","");
        $this->detail->s_surround=HtmlInput::default_value_post("in_surround","");
        $this->detail->jrn_def_id=HtmlInput::default_value_post("in_ledger", "");
        $this->detail->s_encoding=HtmlInput::default_value_post("in_encoding","");
        $this->detail->s_decimal=HtmlInput::default_value_post("in_decimal", "");
        $this->detail->s_thousand=HtmlInput::default_value_post("in_thousand","");
        $this->detail->s_date_format=HtmlInput::default_value_post("in_date_format",4);
    }

    function check_setting()
    {
// Check if valid
// 1 sep for thousand and decimal MUST be different
//2 encoding and delimiter can not be empty
//3 ledger must be writable for user
//4 Check Date format
    }
    /// Check that upload file is correct
    function check(Impacc_File $p_file)
    {
        global $aformat_date;
            
        $this->load_import($p_file->impid);
        $cn=Dossier::connect();
        $ledger=new Acc_Ledger($cn, $this->detail->jrn_def_id);
        $ledger_type=$ledger->get_type();
        
         // connect to DB
        $cn=Dossier::connect();
        
        // load all rows where status != -1
        $t1=new Impacc_Import_detail_SQL($cn);
        $array=$t1->collect_objects(" where import_id = $1 and coalesce(id_status,0) <> -1 ",
                array($p_file->impid));
        // for each row check 
        $nb_array=count($array);
        $date_format=$aformat_date[$this->detail->s_date_format-1]['format'];
        $date_format_sql=$aformat_date[$this->detail->s_date_format-1]['label'];
        
        for ($i =0 ; $i<$nb_array;$i++)
        {
            $array[$i]->id_status=0;
            //------------------------------------
            //Check date format
            //------------------------------------
            $test=DateTime::createFromFormat($date_format, $array[$i]->id_date);
            if ( $test == false) {
                $array[$i]->id_status=-1;
                $and=($array[$i]->id_message=="")?"":",";
                $array[$i]->id_message .= "CK_FORMAT_DATE";
            } else {
                // Check if date exist and in a open periode
                  $sql=sprintf("select p_id from parm_periode where p_start <= to_date($1,'%s') and p_end >= to_date($1,'%s') ",
                          $date_format_sql,$date_format_sql);
                  $periode_id=$cn->get_value($sql,array($array[$i]->id_date));

                  if ( $cn->size() == 0) {
                      $and=($array[$i]->id_message=="")?"":",";
                      $array[$i]->id_message.=$and."CK_INVALID_PERIODE";
                  } else 
                    // Check that this periode is open for this ledger
                  {
                      $per=new Periode($cn, $periode_id);
                      $per->jrn_def_id=$this->detail->jrn_def_id;
                      if ( $per->is_open() == 0 ) {
                        $and=($array[$i]->id_message=="")?"":",";
                        $array[$i]->id_message.=$and."CK_PERIODE_CLOSED";
                      }
                  }
                }
            //----------------------------------------------------------------
            // Check that first id_acc does exist , for ODS it could be an
            // accounting, the card must be accessible for the ledger
            //----------------------------------------------------------------
            $card=new Fiche($cn);
            $card->get_by_qcode($array[$i]->id_acc,false);
            if ( $card->id == 0 ) {
                
                if (  $ledger_type != 'ODS' )
                {
                    $and=($array[$i]->id_message=="")?"":",";
                    $array[$i]->id_message.=$and."CK_INVALID_ACCOUNTING";
                }else 
                {
                    // If ods we must check for Accounting
                    $poste=new Acc_Account_Ledger($cn,$array[$i]->id_acc);
                    if ($poste->do_exist() == 0) {
                            $and=($array[$i]->id_message=="")?"":",";
                            $array[$i]->id_message.=$and."CK_INVALID_ACCOUNTING";
                    }
                }
            }
            //---------------------------------------------------------------
            // Check amount
            // --------------------------------------------------------------
            if (isNumber($array[$i]->id_amount_novat) == 0) {
                            $and=($array[$i]->id_message=="")?"":",";
                            $array[$i]->id_message.=$and."CK_INVALID_AMOUNT";
            }
            
            //----------------------------------------------------------------
            // Test for specific filter
            //----------------------------------------------------------------
            switch ($ledger_type)
            {
                case 'ACH':
                    Impacc_Csv_Sale_Purchase::check($array[$i],$date_format);
                    break;
                case 'VEN':
                    Impacc_Csv_Sale_Purchase::check($array[$i],$date_format);
                    break;
                case 'ODS':
                    break;
                case 'FIN':
                    break;
                default :
                    throw new Exception(_('type journal inconnu'));

            }
            // update status
            $array[$i]->update();
        }
                
        
    }
    ///Save the Impacc_Import_csv_SQL object into db
    function save_setting()
    {
        $this->detail->save();
    }

    /// Thank the import_file.id we find the corresponding record from import_csv
    /// and we load id
    //! \param $p_import_id is impacc.import_file.id
    function load_import($p_import_id)
    {
        try
        {
            $cn=Dossier::connect();
            $id=$cn->get_value('select id from impacc.import_csv where import_id=$1',
                    array($p_import_id));
            $this->detail=new Impacc_Import_csv_SQL($cn, $id);
            $this->detail->load();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }
    /// Record the given csv file into impacc.import_detail ,
    /// depending of the ledger type a different filter is used to import rows
    //! \param $p_file is an Impacc_File , use to open the temporary file
    function record(Impacc_File $p_file)
    {
// retrieve the import setting file 
        $this->load_import($p_file->impid);
        $cn=Dossier::connect();
        $ledger=new Acc_Ledger($cn, $this->detail->jrn_def_id);

        switch ($ledger->get_type())
        {
            case 'ACH':
                $ach=new Impacc_Csv_Sale_Purchase();
                $ach->record($this, $p_file);
                break;
            case 'VEN':
                $ven=new Impacc_Csv_Sale_Purchase();
                $ven->record($this, $p_file);
                break;
            case 'ODS':
                $ods=new Impacc_Csv_Misc_Operation();
                $ods->record($this, $p_file);
                break;
            case 'FIN':
                $fin=new Impacc_Csv_Bank();
                $fin->record($this, $p_file);
                break;
            default :
                throw new Exception(_('type journal inconnu'));
                
        }
    }

}
