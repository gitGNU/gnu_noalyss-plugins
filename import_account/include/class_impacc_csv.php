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
require_once 'class_impacc_csv_sale.php';
require_once 'class_impacc_csv_purchase.php';
require_once 'class_impacc_csv_misc_operation.php';
require_once 'class_impacc_tool.php';
require_once DIR_IMPORT_ACCOUNT."/database/class_impacc_import_detail_sql.php";

///Used by all Import CSV Operation , contains the setting (delimiter,thousand ...) 
class Impacc_CSV
{

    var $detail; //!< Object Impacc_Import_csv_SQL
    var $errcode; //!< Array of error code will be recorded in import_detail.id_message

    function __construct()
    {
        $cn=Dossier::connect();
        $this->detail=new Impacc_Import_csv_SQL($cn);
        $this->detail->s_delimiter="2";
        $this->detail->s_surround='"';
        $this->detail->jrn_def_id=3;
        $this->detail->s_encoding="utf-8";
        $this->detail->s_decimal='1';
        $this->detail->s_thousand='0';
        $this->detail->s_date_format=1;
        $this->errcode=array(
            "CK_FORMAT_DATE"=>_("Format de date incorrect"),
            "CK_PERIODE_CLOSED"=>_("Période non trouvée"),
            "CK_INVALID_PERIODE"=>_("Période non trouvée"),
            "CK_INVALID_AMOUNT"=>_("Montant invalide"),
            "CK_INVALID_ACCOUNTING"=>_("Poste comptable ou Fiche non existante"),
            "CK_TVA_INVALID"=>_("Code TVA Invalide"),
            "CK_CARD_LEDGER"=>_("Fiche non disponible pour journal")
        );
    }

    /// Display a form to upload a CSV file with operation
    function input_format()
    {
        global $cn, $adecimal, $athousand, $aseparator, $aformat_date;
        $in_delimiter=new ISelect('in_delimiter');
        $in_delimiter->value=$aseparator;
        $in_delimiter->selected=$this->detail->s_delimiter;
        $in_delimiter->size=1;

        $in_surround=new IText('in_surround', $this->detail->s_surround);
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

        $in_date_format=new ISelect("in_date_format");
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
        $this->detail->s_delimiter=HtmlInput::default_value_post("in_delimiter",
                        "");
        $this->detail->s_surround=HtmlInput::default_value_post("in_surround",
                        "");
        $this->detail->jrn_def_id=HtmlInput::default_value_post("in_ledger", "");
        $this->detail->s_encoding=HtmlInput::default_value_post("in_encoding",
                        " ");
        $this->detail->s_decimal=HtmlInput::default_value_post("in_decimal", "");
        $this->detail->s_thousand=HtmlInput::default_value_post("in_thousand",
                        "");
        $this->detail->s_date_format=HtmlInput::default_value_post("in_date_format",
                        4);
    }

    function check_setting()
    {
// Check if valid
// 1 sep for thousand and decimal MUST be different
        if ($this->detail->s_thousand==$this->detail->s_decimal)
            throw new Exception(_("Séparateur de décimal et milliers doivent être différent"));
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

        for ($i=0; $i<$nb_array; $i++)
        {
            $and=($array[$i]->id_message=="")?"":",";
            $array[$i]->id_status=0;
            if (trim($array[$i]->id_code_group)=="")
            {
                $array[$i]->id_status=-1;
                $array[$i]->id_message .= $and."CK_CODE_GROUP";
                $and=",";
            }
            //------------------------------------
            //Check date format
            //------------------------------------
            $test=DateTime::createFromFormat($date_format, $array[$i]->id_date);
            if ($test==false)
            {
                $array[$i]->id_status=-1;
                $array[$i]->id_message .= $and."CK_FORMAT_DATE";
                $and=",";
            }
            else
            {
                $array[$i]->id_date_conv=$test->format('d.m.Y');
                $array[$i]->id_date_format_conv=$test->format('Ymd');
                // Check if date exist and in a open periode
                $sql=sprintf("select p_id from parm_periode where p_start <= to_date($1,'%s') and p_end >= to_date($1,'%s') ",
                        $date_format_sql, $date_format_sql);
                $periode_id=$cn->get_value($sql, array($array[$i]->id_date));
                if ($cn->size()==0)
                {
                    $array[$i]->id_message.=$and."CK_INVALID_PERIODE";
                    $and=",";
                }
                else
                // Check that this periode is open for this ledger
                {
                    $per=new Periode($cn, $periode_id);
                    $per->jrn_def_id=$this->detail->jrn_def_id;
                    if ($per->is_open()==0)
                    {
                        $array[$i]->id_message.=$and."CK_PERIODE_CLOSED";
                        $and=",";
                    }
                }
            }
            //----------------------------------------------------------------
            // Check that first id_acc does exist , for ODS it could be an
            // accounting, the card must be accessible for the ledger
            //----------------------------------------------------------------
            $card=Impacc_Verify::check_card($array[$i]->id_acc);
            if ($ledger_type=='ODS'&&$card==false)
            {
                // For ODS it could be an accounting
                $poste=new Acc_Account_Ledger($cn, $array[$i]->id_acc);
                if ($poste->do_exist()==0)
                {
                    $array[$i]->id_message.=$and."CK_INVALID_ACCOUNTING";
                    $and=",";
                }
            }
            if ($ledger_type!='ODS'&&$card==false)
            {
                $array[$i]->id_message.=$and."CK_INVALID_ACCOUNTING";
                $and=",";
            }
            // If card is valid check if belong to ledger
            if ($card instanceof Fiche)
            {
                if ($card->belong_ledger($this->detail->jrn_def_id)!=1)
                {
                    $array[$i]->id_message.=$and."CK_CARD_LEDGER";
                    $and=",";
                }
            }
            //---------------------------------------------------------------
            // Check amount
            // --------------------------------------------------------------

            $array[$i]->id_amount_novat_conv=Impacc_Tool::convert_amount($array[$i]->id_amount_novat,
                            $this->detail->s_thousand, $this->detail->s_decimal);
            if (isNumber($array[$i]->id_amount_novat_conv)==0)
            {
                $array[$i]->id_message.=$and."CK_INVALID_AMOUNT";
                $and=",";
            }

            //----------------------------------------------------------------
            // Test for specific filter
            //----------------------------------------------------------------
            switch ($ledger_type)
            {
                case 'ACH':
                    //-----------------
                    ///- Check Service
                    //-----------------
                    $card=Impacc_Verify::check_card($array[$i]->id_acc_second);
                    if ($card==false)
                    {
                        $array[$i]->id_message=$and."CK_INVALID_ACCOUNTING";
                        $and=",";
                    }
                    if ($card instanceof  Fiche &&  $card->belong_ledger($this->detail->jrn_def_id)!=1)
                    {
                        $array[$i]->id_message.=$and."CK_CARD_LEDGER";
                        $and=",";
                    }
                    Impacc_Csv_Sale_Purchase::check($array[$i], $date_format,
                            $this->detail->s_thousand, $this->detail->s_decimal);
                    break;
                case 'VEN':
                    //-----------------
                    ///- Check Service
                    //-----------------
                    $card=Impacc_Verify::check_card($array[$i]->id_acc_second);
                    if ($card==false)
                    {
                        $array[$i]->id_message=$and."CK_INVALID_ACCOUNTING";
                        $and=",";
                    }
                    if ($card instanceof  Fiche && $card->belong_ledger($this->detail->jrn_def_id)!=1)
                    {
                        $array[$i]->id_message.=$and."CK_CARD_LEDGER";
                        $and=",";
                    }
                    Impacc_Csv_Sale_Purchase::check($array[$i], $date_format,
                            $this->detail->s_thousand, $this->detail->s_decimal);
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

    /// Create the right object for the import id
    /// and throw and exception if the ledger type can not be found
    //\param $p_import_id is the import_file.id
    function make_csv_class($p_import_id)
    {
        $this->load_import($p_import_id);
        $cn=Dossier::connect();
        $ledger=new Acc_Ledger($cn, $this->detail->jrn_def_id);

        switch ($ledger->get_type())
        {
            case 'ACH':
                $obj=new Impacc_Csv_Purchase();
                break;
            case 'VEN':
                $obj=new Impacc_Csv_Sale();
                break;
            case 'ODS':
                $obj=new Impacc_Csv_Misc_Operation();
                break;
            case 'FIN':
                $obj=new Impacc_Csv_Bank();
                break;
            default :
                throw new Exception(_('type journal inconnu'));
        }
        $obj->errcode=$this->errcode;
        return $obj;
    }

    /// Record the given csv file into impacc.import_detail ,
    /// depending of the ledger type a different filter is used to import rows
    //! \param $p_file is an Impacc_File , use to open the temporary file
    function record(Impacc_File $p_file)
    {
        try
        {
            $csv_class=$this->make_csv_class($p_file->impid);
            $csv_class->record($this, $p_file);
        }
        catch (Exception $ex)
        {
            error_log($ex->getTraceAsString());
            echo _("Echec dans record")." ".$ex->getMessage();
            throw $ex;
        }
    }

    /// Display result from the table import_detail for CSV import
    //!\param $importfile is an Impacc_File object
    function result(Impacc_File $importfile)
    {
        try
        {
            $csv_class=$this->make_csv_class($importfile->impid);
        }
        catch (Exception $ex)
        {
            error_log($ex->getTraceAsString());
            echo _("Echec dans result");
            throw $ex;
        }
        $cn=Dossier::connect();
        $display=new Impacc_Import_detail_SQL($cn);
        $ret=$display->seek(" where import_id = $1 order by id",
                array($importfile->impid));
        $nb=Database::num_row($ret);
        require DIR_IMPORT_ACCOUNT."/template/operation_result.php";
    }

    /// Transfer the operation to the right ledger
    function transfer()
    {
        $cn=Dossier::connect();
        try
        {
            ///- Create the right object
            $csv_class=$this->make_csv_class($this->detail->import_id);

            ///- Create the ledger object
            $ledger=Impacc_Tool::ledger_factory($this->detail->jrn_def_id);

            ///- Load only the correct group (all the rows in the group must be valid)
            $sql="
                with rejected as ( SELECT distinct id_code_group 
                                    FROM impacc.import_detail a
                                    where 
                                    import_id=$1
                                    and (id_status != 0 or trim(COALESCE(id_message,'')) !='')
                                    ) 
                select distinct id_code_group ,id_date_format_conv, import_id
                from 
                    impacc.import_detail 
                where 
                    import_id=$1 
                    and id_code_group not in (select coalesce(id_code_group,'') from rejected)
                
                order by id_date_format_conv asc
                ";

            $array=$cn->get_array($sql, array($this->detail->import_id));
            ///- Call the function insert from a child classs
            $csv_class->insert($array, $ledger);
        }
        catch (Exception $ex)
        {
            error_log($ex->getTraceAsString());
            echo _("Echec dans transfer")." ".$ex->getMessage();
            throw $ex;
        }
    }

}
