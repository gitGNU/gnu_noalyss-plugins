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

/**
 * @file
 * @brief compute all the formulaire_row and save them into rappport_advanced.declaration
 *
 */
require_once 'class_rapav_formulaire.php';
require_once 'class_formulaire_param_detail.php';
require_once 'class_rapport_avance_sql.php';

/**
 * @brief compute, save and display a declaration
 */
class Rapav_Declaration extends RAPAV_Declaration_SQL
{

    function __construct()
    {
        $this->form = new RAPAV_Formulaire();
        parent::__construct();
    }
    function load_formulaire() {
        $this->form->load();
    }
    /**
     * @brief export a declaration to CSV
     * @global $cn database conx
     * @param $p_id pk of rapav_declaration
     * @param $p_orient
     *        value : 
     *           - list : for a list
     *           - table : to have a table (if using step)
     */
    static function to_csv($p_id,$p_orient="list")
    {
        global $cn;
        
        $a_title = $cn->get_array("select d_title
			,to_char(d_start,'DD.MM.YYYY') as start
			,to_char(d_end,'DD.MM.YYYY') as end
			from
			rapport_advanced.declaration
			where
			d_id=$1 ", array($p_id));
        $title = $a_title[0]['d_title'] . "-" . $a_title[0]['start'] . "-" . $a_title[0]['end'];
        $title = mb_strtolower($title, 'UTF-8');
        $title = str_replace(array('/', '*', '<', '>', '*', '.', '+', ':', '?', '!', " ", ";"), "_", $title);
        $out = fopen("php://output", "w");

        header('Pragma: public');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment;filename="' . $title . '.csv"', FALSE);
        if ($p_orient == "list")
        {
            fputcsv($out, $a_title[0], ";");
            
            $a_row = $cn->get_array('select dr_code,dr_libelle,dr_amount,dr_start,dr_end
			from rapport_advanced.declaration_row
			where d_id=$1 order by dr_order,dr_start', array($p_id));

            for ($i = 0; $i < count($a_row); $i++)
            {
                printf('"%s";"%s";%s;"%s";"%s"' . "\r\n", $a_row[$i]['dr_code'], $a_row[$i]['dr_libelle'], nb($a_row[$i]['dr_amount']), format_date($a_row[$i]['dr_start']), format_date($a_row[$i]['dr_end'])
                );
            }
        }
        elseif ($p_orient=="table")
        {
             fputcsv($out, $a_title[0], ";");
             // Only the period
             $a_periode = $cn->get_array('select distinct dr_start,dr_end
			from rapport_advanced.declaration_row
			where d_id=$1 order by dr_start', array($p_id));

             // 2 blank columns
             printf(';');
            for ($i = 0; $i < count($a_periode); $i++)
            {
                printf(';"%s-%s"', format_date($a_periode[$i]['dr_start']), format_date($a_periode[$i]['dr_end']));
            }
            printf("\r\n");
            
            // print each code on one line
             $a_row = $cn->get_array('select dr_code,dr_libelle,dr_amount,dr_start,dr_end
			from rapport_advanced.declaration_row
			where d_id=$1 order by dr_order,dr_start', array($p_id));
            $last_code="";
            for ($i = 0; $i < count($a_row); $i++)
            {
                if ( $last_code != $a_row[$i]['dr_code'])
                {
                    if ($last_code!=""){ printf("\r\n"); }
                    printf('"%s";"%s"', $a_row[$i]['dr_code'],$a_row[$i]['dr_libelle']);
                    $last_code=$a_row[$i]['dr_code'];
                }
                printf(';%s',nb($a_row[$i]['dr_amount']));
            }
             printf("\r\n");
        }
    }

    function get_file_to_parse()
    {
        global $cn;
        // create a temp directory in /tmp to unpack file and to parse it
        $dirname = tempnam($_ENV['TMP'], 'rapav_');


        unlink($dirname);
        mkdir($dirname);
        chdir($dirname);
        // Retrieve the lob and save it into $dirname
        $cn->start();


        $filename = $this->d_filename;
        $exp = $cn->lo_export($this->d_lob, $dirname . DIRECTORY_SEPARATOR . $filename);

        if ($exp === false)
            echo_warning(__FILE__ . ":" . __LINE__ . "Export NOK $filename");

        $type = "n";
        // if the doc is a OOo, we need to unzip it first
        // and the name of the file to change is always content.xml
        if (strpos($this->d_mimetype, 'vnd.oasis') != 0)
        {
            ob_start();
            $zip = new Zip_Extended;
            if ($zip->open($filename) === TRUE)
            {
                $zip->extractTo($dirname . DIRECTORY_SEPARATOR);
                $zip->close();
            } else
            {
                echo __FILE__ . ":" . __LINE__ . "cannot unzip model " . $filename;
            }

            // Remove the file we do  not need anymore
            unlink($filename);
            ob_end_clean();
            $file_to_parse = "content.xml";
            $type = "OOo";
        } else
            $file_to_parse = $filename;

        $cn->commit();
        return array($file_to_parse, $dirname, $type);
    }

    function generate_document()
    {
        global $cn;
        if ($this->d_filename == "")
            return;

        list($file_to_parse, $dirname, $type) = $this->get_file_to_parse();

        // parse the document
        $this->parse_document($dirname, $file_to_parse, $type);

        // Add special tag
        $this->special_tag($dirname, $file_to_parse, $type);

        // if the doc is a OOo, we need to re-zip it
        if ($type == 'OOo')
        {
            ob_start();
            $zip = new Zip_Extended;
            $res = $zip->open($this->d_filename, ZipArchive::CREATE);
            if ($res !== TRUE)
            {
                echo __FILE__ . ":" . __LINE__ . "cannot recreate zip";
                exit;
            }
            $zip->add_recurse_folder($dirname . DIRECTORY_SEPARATOR);
            $zip->close();

            ob_end_clean();

            $file_to_save = $this->d_filename;
        } else
        {
            $file_to_save = $file_to_parse;
        }

        $this->load_document($dirname . DIRECTORY_SEPARATOR . $file_to_save);
    }

    function parse_document($p_dir, $p_filename, $p_type)
    {
        global $cn;
        // Retrieve all the code + amount
        if ($p_type == "OOo")
        {
            $array = $cn->get_array("select '&lt;&lt;'||dr_code||'&gt;&gt;' as code,dr_amount from rapport_advanced.declaration_row where d_id=$1 and dr_type=3", array($this->d_id));
        } else
        {
            $array = $cn->get_array("select '<<'||dr_code||'>>' as code,dr_amount from rapport_advanced.declaration_row where d_id=$1 and dr_type=3", array($this->d_id));
        }

        // open the files
        $ifile = fopen($p_dir . '/' . $p_filename, 'r');

        // check if tmpdir exist otherwise create it
        $temp_dir = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'tmp';
        if (is_dir($temp_dir) == false)
        {
            if (mkdir($temp_dir) == false)
            {
                echo "Ne peut pas créer le répertoire " . $temp_dir;
                exit();
            }
        }
        // Compute output_name
        $oname = tempnam($temp_dir, "rapport_avance_");
        $ofile = fopen($oname, "w+");

        // read ifile
        while (!feof($ifile))
        {
            $buffer = fgets($ifile);
            // for each code replace in p_filename the code surrounded by << >> by the amount (value) or &lt; or &gt;
            foreach ($array as $key => $value)
            {
                if (is_numeric($value['dr_amount']))
                {
                    $searched = 'office:value-type="string"><text:p>' . $value['code'];
                    $replaced = 'office:value-type="float" office:value="' . $value['dr_amount'] . '"><text:p>' . $value['code'];
                    $buffer = str_replace($searched, $replaced, $buffer);
                }
                $buffer = str_replace($value['code'], $value['dr_amount'], $buffer);
            }
            // write to output
            fwrite($ofile, $buffer);
        }

        // copy the output to input
        fclose($ifile);
        fclose($ofile);

        if (($ret = copy($oname, $p_dir . '/' . $p_filename)) == FALSE)
        {
            echo _('Ne peut pas sauver ' . $oname . ' vers ' . $p_dir . '/' . $p_filename . ' code d\'erreur =' . $ret);
        }
        unlink($oname);
    }

    function special_tag($p_dir, $p_filename, $p_type)
    {
        global $cn, $g_parameter;
        // Retrieve all the code + libelle
        $array[] = array('code' => 'PERIODE_DECLARATION', 'value' => format_date($this->d_start) . " - " . format_date($this->d_end));
        $array[] = array('code' => 'TITRE', 'value' => $this->d_title);
        $array[] = array('code' => 'DOSSIER', 'value' => $cn->format_name($_REQUEST['gDossier'], 'dos'));
        $array[] = array('code' => 'NAME', 'value' => $g_parameter->MY_NAME);
        $array[] = array('code' => 'STREET', 'value' => $g_parameter->MY_STREET);
        $array[] = array('code' => 'NUMBER', 'value' => $g_parameter->MY_NUMBER);
        $array[] = array('code' => 'LOCALITE', 'value' => $g_parameter->MY_COMMUNE);
        $array[] = array('code' => 'COUNTRY', 'value' => $g_parameter->MY_PAYS);
        $array[] = array('code' => 'PHONE', 'value' => $g_parameter->MY_TEL);
        $array[] = array('code' => 'CEDEX', 'value' => $g_parameter->MY_CP);
        $array[] = array('code' => 'FAX', 'value' => $g_parameter->MY_FAX);
        $array[] = array('code' => 'NOTE', 'value' => $this->d_description);

        // open the files
        $ifile = fopen($p_dir . '/' . $p_filename, 'r');

        // check if tmpdir exist otherwise create it
        $temp_dir = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'tmp';
        if (is_dir($temp_dir) == false)
        {
            if (mkdir($temp_dir) == false)
            {
                echo "Ne peut pas créer le répertoire " . $temp_dir;
                exit();
            }
        }
        // Compute output_name
        $oname = tempnam($temp_dir, "rapport_avance_");
        $ofile = fopen($oname, "w+");

        // read ifile
        while (!feof($ifile))
        {
            $buffer = fgets($ifile);
            // for each code replace in p_filename the code surrounded by << >> by the amount (value) or &lt; or &gt;
            foreach ($array as $key => $value)
            {
                if ($p_type == 'OOo')
                {
                    $replace = '&lt;&lt;' . $value['code'] . '&gt;&gt;';
                    $fmt_value = $value['value'];
                    $fmt_value = str_replace('&', '&amp;', $fmt_value);
                    $fmt_value = str_replace('<', '&lt;', $fmt_value);
                    $fmt_value = str_replace('>', '&gt;', $fmt_value);
                    $fmt_value = str_replace('"', '&quot;', $fmt_value);
                    $fmt_value = str_replace("'", '&apos;', $fmt_value);
                } else
                {
                    $replace = '<<' . $value['code'] . '>>';
                    $fmt_value = $value['value'];
                }
                $buffer = str_replace($replace, $fmt_value, $buffer);
            }
            // write to output
            fwrite($ofile, $buffer);
        }

        // copy the output to input
        fclose($ifile);
        fclose($ofile);

        if (($ret = copy($oname, $p_dir . '/' . $p_filename)) == FALSE)
        {
            echo _('Ne peut pas sauver ' . $oname . ' vers ' . $p_dir . '/' . $p_filename . ' code d\'erreur =' . $ret);
        }
        unlink($oname);
    }

    function load_document($p_file)
    {
        global $cn;
        $cn->start();
        $this->d_lob = $cn->lo_import($p_file);
        if ($this->d_lob == false)
        {
            echo "ne peut pas importer [$p_file]";
            return 1;
        }
        $this->d_size = filesize($p_file);
        $date = date('ymd-Hi');
        $this->d_filename = $date . '-' . $this->d_filename;
        $this->update();
        $cn->commit();
    }

    /**
     *
     * @global $cn $cn
     * @param type $p_id
     * @param type $p_start
     * @param type $p_end
     * @param type $p_step
     */
    function compute($p_id, $p_start, $p_end, $p_step)
    {
        global $cn;
        $cn->start();
        // Load the parameter from formulaire_param_detail
        // create object_rapav_declaration
        //   compute
        // save the parameter
        $this->form->f_id = $p_id;
        $this->form->load();
        $this->d_title = $this->form->f_title;
        $this->d_start = $p_start;
        $this->d_end = $p_end;
        $this->to_keep = 'N';
        $this->d_lob = $this->form->f_lob;
        $this->d_filename = $this->form->f_filename;
        $this->d_mimetype = $this->form->f_mimetype;
        $this->d_size = $this->form->f_size;
        $this->d_step = $p_step;
        $this->insert();
        /*
         * First we compute the formula and tva_code for each detail
         */
        $array = $cn->get_array("select p_id,p_code,p_libelle,p_type,p_order,f_id,t_id
			from rapport_advanced.formulaire_param
			where
			f_id=$1
			order by p_order", array($p_id));
        /**
         * if step != 0, recompute the date
         */
        if ($p_step == 0)
        {
            // compute each row
            for ($i = 0; $i < count($array); $i++)
            {
                $row = new Rapav_Declaration_Param();
                $row->d_id = $this->d_id;
                $row->dr_id = $cn->get_next_seq('rapport_advanced.declaration_param_seq');
                $row->from_array($array[$i]);
                if ($array[$i]['p_type'] == 3)
                {
                    $row->compute($p_start, $p_end);
                } else
                {
                    $row->amount = 0;
                }
                $row->dr_start = $p_start;
                $row->dr_end = $p_end;
                $row->insert();
            }
        } else
        {
            // compute new date, stored in $this->start and $this->end
            while ($this->compute_interval($p_start, $p_end, $p_step) == 1)
            {
                for ($i = 0; $i < count($array); $i++)
                {
                    $row = new Rapav_Declaration_Param();
                    $row->d_id = $this->d_id;
                    $row->dr_id = $cn->get_next_seq('rapport_advanced.declaration_param_seq');
                    $row->from_array($array[$i]);
                    if ($array[$i]['p_type'] == 3)
                    {
                        $row->compute($this->start, $this->end);
                        $row->dr_start = $this->start;
                        $row->dr_end = $this->end;
                        $row->insert();
                    }
                }
            }
        }
        $cn->commit();
    }

    function compute_interval($p_start, $p_end, $p_step)
    {
        static $s_start = "";
        static $s_count = 0;

        if ($s_start == "")
        {
            $s_start = $p_start;
        }
        $s_count++;
        // initialize datetime object
        $date_start = DateTime::createFromFormat('d.m.Y', $s_start);
        $date_end = DateTime::createFromFormat('d.m.Y', $s_start);
        $date_finish = DateTime::createFromFormat('d.m.Y', $p_end);

        $add = $this->get_interval($p_step);


        if ($s_count > 1)
        {
            $date_start->add($add);
            $date_end->add($add);
        }
        // compute date_end
        $date_end->add($add);
        $date_end->sub(new DateInterval('P1D'));
        // if date_end > date_finish then stop
        if ($date_end > $date_finish)
            return 0;
        $this->start = $date_start->format("d.m.Y");
        $this->end = $date_end->format("d.m.Y");
        $s_start = $this->start;
        return 1;
    }

    function get_interval($p_step)
    {
        $array_interval = array("", "P7D", "P14D", "P1M", "P2M", "P3M");
        return new DateInterval($array_interval[$p_step]);
    }

    function anchor_document()
    {
        $url = HtmlInput::request_to_string(array('gDossier', 'ac', 'plugin_code'));
        $url = 'extension.raw.php' . $url . '&amp;act=export_decla_document&amp;id=' . $this->d_id;
        return HtmlInput::anchor($this->d_filename, $url);
    }

    function display()
    {
        global $cn;
        $array = $cn->get_array('select * from rapport_advanced.declaration_row where d_id=$1 order by dr_order,dr_start', array($this->d_id));
        require_once 'template/declaration_display.php';
    }

    function save()
    {
        global $cn;
        try
        {
            $cn->start();
            $this->to_keep = 'Y';
            $this->update();
            $code = $_POST['code'];
            $amount = $_POST['amount'];
            for ($i = 0; $i < count($code); $i++)
            {
                $cn->exec_sql('update rapport_advanced.declaration_row set dr_amount=$2 where dr_id=$1', array($code[$i], $amount[$i]));
            }
            $cn->commit();
        } catch (Exception $e)
        {
            alert($e->getTraceAsString());
        }
    }

}

/**
 * @brief Match each row of a form, this row can have several details
 *
 */
class Rapav_Declaration_Param
{

    /**
     * @brief insert into rapport_advanced.formulaire_param
     */
    function insert()
    {
        $data = new RAPAV_Declaration_Row_SQL();
        $data->dr_code = $this->param->p_code;
        $data->dr_libelle = $this->param->p_libelle;
        $data->dr_order = $this->param->p_order;
        $data->dr_amount = $this->amount;
        $data->d_id = $this->d_id;
        $data->dr_id = $this->dr_id;
        $data->dr_type = $this->param->p_type;
        $data->dr_start = $this->dr_start;
        $data->dr_end = $this->dr_end;
        $data->insert();
    }

    /**
     * @brief set the attribute param with the content of the array.
     * keys :
     *    - 'p_id',
     *    - 'p_code',
     *    - 'p_libelle',
     *    - 'p_type',
     *    - 'p_order',
     *    - 'f_id',
     *    - 't_id'
     * @param type $p_array
     */
    function from_array($p_array)
    {
        $this->param = new Formulaire_Param();
        foreach (array('p_id', 'p_code', 'p_libelle', 'p_type', 'p_order', 'f_id', 't_id') as $e)
        {
            $this->param->$e = $p_array[$e];
        }
        $this->param->load();
    }

    /**
     * @brief compute the date following the attribute t_id (match rapport_advanced.periode_type and
     * store the result into $this->start and $this-> end
     *   - 1 date from the FORM
     *   - 2 N
     *   - 3 N-1
     *   - 4 N-2
     *   - 5 N-3
     * @param $p_start requested date
     * @param $p_end requested date
     */
    function compute_date($p_start, $p_end)
    {
        global $g_user;
        switch ($this->param->t_id)
        {
            case 1:
                $this->start = $p_start;
                $this->end = $p_end;
                return;
                break;
            case 2:
                list($this->start, $this->end) = $g_user->get_limit_current_exercice();
                return;
                break;
            case 3:
                $exercice = $g_user->get_exercice();
                $exercice--;
                break;
            case 4:
                $exercice = $g_user->get_exercice();
                $exercice-=2;
                break;
            case 5:
                $exercice = $g_user->get_exercice();
                $exercice-=3;
                break;
            case 6:
                list($this->start, $this->end) = $g_user->get_limit_current_exercice();
                $this->end = $p_end;
                return;
                break;
            default:
                throw new Exception('compute_date : t_id est incorrect');
        }
        global $cn;

        // If exercice does not exist then
        // set the date end and start to 01.01.1900

        $exist_exercice = $cn->get_value('select count(p_id) from parm_periode where p_exercice=$1', array($exercice));
        if ($exist_exercice == 0)
        {
            $this->start = '01.01.1900';
            $this->end = '01.01.1900';
            return;
        }
        // Retrieve start & end date
        $periode = new Periode($cn);
        list($per_start, $per_end) = $periode->get_limit($exercice);
        $this->start = $per_start->first_day();
        $this->end = $per_end->last_day();
    }

    /*     * *
     * @brief compute amount of all the detail of apport_advanced.formulaire_param
     * @param $p_start requested start date
     * @param $p_start requested end date
     *
     *
     */

    function compute($p_start, $p_end)
    {
        global $cn;
        bcscale(2);
        $this->amount = "0";

        $array = $cn->get_array("select fp_id,p_id,tmp_val,tva_id,fp_formula,fp_signed,jrn_def_type,tt_id,type_detail,
			with_tmp_val,type_sum_account,operation_pcm_val,jrn_def_id,date_paid
			from rapport_advanced.formulaire_param_detail where p_id=$1", array($this->param->p_id));
        $this->compute_date($p_start, $p_end);
        for ($e = 0; $e < count($array); $e++)
        {
            $row_detail = Rapav_Declaration_Detail::factory($array[$e]);
            $row_detail->dr_id = $this->dr_id;
            $row_detail->d_id = $this->d_id;
            $tmp_amount = $row_detail->compute($this->start, $this->end);
            $this->amount = bcadd("$tmp_amount", "$this->amount");
            $row_detail->insert();
        }
    }

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Compute the detail for each row
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Rapav_Declaration_Detail extends RAPAV_Declaration_Row_Detail_SQL
{

    /**
     * @brief create an object RAPAV_dd_Formula, RAPAV_dd_Account_Tva or Rapav_dd_compute following the idx type_detail
     * @param type $p_array
     * @return object RAPAV_dd_Formula, RAPAV_dd_Account_Tva or Rapav_dd_compute
     * @throws if the type is not known
     */
    static function factory($p_array)
    {
        switch ($p_array['type_detail'])
        {
            case '1':
                $ret = new Rapav_dd_Formula();
                break;
            case '2':
                $ret = new Rapav_dd_Account_Tva();
                break;
            case '3':
                $ret = new Rapav_dd_Compute();
                break;
            case '4':
                $ret = new Rapav_dd_Account();
                break;
            case '5':
                $ret = new Rapav_dd_Reconcile();
                break;
            default:
                throw new Exception("Type inconnu");
        }

        $ret->from_array($p_array);
        $ret->ddr_amount = 0; // cannot be null
        return $ret;
    }

    /**
     * @brief the p_array contains a row from formulaire_param_detail
     * it will be copied into this->form.
     * @param type $p_array match formulaire_param_detail table structure
     */
    function from_array($p_array)
    {
        $this->form = new Formulaire_Param_Detail();
        $attribute = explode(',', 'fp_id,p_id,tmp_val,tva_id,fp_formula,fp_signed,jrn_def_type,tt_id,type_detail,with_tmp_val,type_sum_account,operation_pcm_val,jrn_def_id,date_paid');
        foreach ($attribute as $e)
        {
            $this->form->$e = $p_array[$e];
        }
    }

}

/**
 * @brief compute a formula
 * @see Impress::parse_formula RAPAV_Formula
 */
class Rapav_dd_Formula extends Rapav_Declaration_Detail
{

    /**
     * compute the amount for one detail of type formula
     *  rapport_advanced.formulaire_param_detail
     * @global type $cn database connexion
     * @param $p_start (date) : computed start date
     * @param $p_end (date) computed start date
     * @return numeric amount computed
     */
    function compute($p_start, $p_end)
    {
        global $cn;
        $sql = "";
        if ($this->form->jrn_def_id != null)
        {
            $sql = ' and j_jrn_def =' . $this->form->jrn_def_id;
        }
        if ($this->form->date_paid == 1)
        {
            $sql.=sprintf(" and jr_date_paid >= to_date('%s','DD.MM.YYYY') and jr_date_paid <= to_date ('%s','DD.MM.YYYY')", $p_start, $p_end);
            $p_start = '01.01.1900';
            $p_end = '01.01.2100';
        }
        $amount = Impress::parse_formula($cn, "", $this->form->fp_formula, $p_start, $p_end, true, 1, $sql);
        return $amount['montant'];
    }

}

/**
 * @brief handle the param_detail type Compute
 * @see RAPAV_Compute
 */
class Rapav_dd_Compute extends Rapav_Declaration_Detail
{

    /**
     * compute the amount for one detail
     *  rapport_advanced.formulaire_param_detail
     * the attribute d_id and dr_id must be set before the call
     * @global type $cn database connexion
     * @param $p_start (date) : computed start date not used
     * @param $p_end (date) computed start date not used
     * @return numeric amount computed
     */
    function compute($p_start, $p_end)
    {
        global $cn;
        $amount = 0;
        bcscale(2);

        // copy $this->form->fp_formula to a variable
        $formula = $this->form->fp_formula;

        // split the string from  into smaller piece
        preg_match_all("/\[([A-Z]*[0-9]*)*([0-9]*[A-Z]*)\]/i", $formula, $e);
        $tmp = $e[0];

        foreach ($tmp as $piece)
        {
            // Find the code in the database
            $search = str_replace('[', '', $piece);
            $search = str_replace(']', '', $search);
            $value = $cn->get_value('select coalesce(sum(dr_amount),0) as value
				from rapport_advanced.declaration_row where d_id=$1 and dr_code=$2', array($this->d_id, $search));
            $formula = str_replace($piece, $value, $formula);
        }
        eval('$amount = ' . $formula . ';');
        //
        return $amount;
    }

}

/**
 * @brief handle the param_detail type Account_Tva
 * The t_id gives the type of total
 *   - 0 TVA + Account
 *   - 1 TVA
 *   - 2 Account
 * the jrn_def_type is either ACH or VEN
 *
 * @see RAPAV_Account_Tva
 */
class Rapav_dd_Account_Tva extends Rapav_Declaration_Detail
{

    /**
     * compute the amount of tva using the given account in either the ledger ACH or VEN
     * following the $this->form->jrn_def_type.
     * set the $this->errcode if something wrong has happened
     * @param  $p_start start date
     * @param $p_end end date
     * @return amount
     */
    private function compute_tva($p_start, $p_end)
    {
        $filter_ledger = "";
        if ($this->form->jrn_def_id != "")
        {
            $filter_ledger = " and j_jrn_def = " . sql_string($this->form->jrn_def_id);
        }
        if ($this->form->date_paid == 1)
        {
            $sql_date=" and j_id in 
                (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id)
                    where
                    coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) >= to_date($2,'DD.MM.YYYY')
                    and coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) <= to_date($3,'DD.MM.YYYY')
                 )
                    ";
                    
        }
        else
        {
            $sql_date="and (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))";
        }
        if ($this->form->jrn_def_type == 'ACH')
        {

            $sql = "select coalesce(sum(qp_vat),0) as amount
						from quant_purchase join jrnx using (j_id)
						where qp_vat_code=$1
						$sql_date
						and j_poste::text like ($4) $filter_ledger";
            $amount = $this->cn->get_value($sql, array($this->form->tva_id,
                $p_start,
                $p_end,
                $this->form->tmp_val));
            return $amount;
        }
        if ($this->form->jrn_def_type == 'VEN')
        {
            $sql = "select coalesce(sum(qs_vat),0) as amount
						from quant_sold join jrnx using (j_id)
						where qs_vat_code=$1 and
						$sql_date
						and j_poste::text like ($4)  $filter_ledger";

            $amount = $this->cn->get_value($sql, array($this->form->tva_id,
                $p_start,
                $p_end,
                $this->form->tmp_val));
            return $amount;
        }
        $this->errcode = 'Erreur dans le journal';
        return 0;
    }

    /**
     * compute the amount of account using the given tva_id in either the ledger ACH or VEN
     * following the $this->form->jrn_def_type.
     * Set the $this->errcode if something wrong has happened
     * @param  $p_start start date
     * @param $p_end end date
     * @return amount
     * @param type $p_start
     * @param type $p_end
     * @return \amount|int
     */
    private function compute_amount($p_start, $p_end)
    {
        $filter_ledger = "";
        if ($this->form->jrn_def_id != "")
        {
            $filter_ledger = " and j_jrn_def = " . sql_string($this->form->jrn_def_id);
        }
        if ($this->form->date_paid == 1)
        {
            $sql_date=" and j_id in 
                (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id)
                    where
                    coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) >= to_date($2,'DD.MM.YYYY')
                    and coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) <= to_date($3,'DD.MM.YYYY')
                 )
                    ";
                    
        }
        else
        {
            $sql_date="and (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))";
        }
        if ($this->form->jrn_def_type == 'ACH')
        {
            $sql = "select coalesce(sum(qp_price),0) as amount from quant_purchase join jrnx using (j_id)
					where qp_vat_code=$1 $sql_date
					and j_poste::text like ($4) $filter_ledger";

            $amount = $this->cn->get_value($sql, array($this->form->tva_id,
                $p_start,
                $p_end,
                $this->form->tmp_val));
            return $amount;
        }
        if ($this->form->jrn_def_type == 'VEN')
        {
            $sql = "select coalesce(sum(qs_price),0) as amount from quant_sold
					join jrnx using (j_id)
					where qs_vat_code=$1 $sql_date
					and j_poste::text like ($4) $filter_ledger";
            $amount = $this->cn->get_value($sql, array($this->form->tva_id,
                $p_start,
                $p_end,
                $this->form->tmp_val));
            return $amount;
        }
        $this->errcode = 'Erreur dans le journal';
        return 0;
    }

    /**
     * Compute the amount of TVA or Account, call internally private functions
     * @see Rapav_dd_Account_Tva::computa_amount Rapav_dd_Account_Tva::compute_tva
     * @param $p_start start date
     * @param $p_end end date
     * @return amount computed
     * @throws Exception
     */
    function compute($p_start, $p_end)
    {
        bcscale(2);
        // Retrieve the account for the tva_id, we need the DEB for VEN and CRED for ACH
        //
		// tt_id gives the type of total
        //  - 0 TVA + Account
        //  - 1 TVA
        //  - 2 Account
        switch ($this->form->tt_id)
        {
            case 0:
                $t1_amount = $this->compute_amount($p_start, $p_end);
                $t2_amount = $this->compute_tva($p_start, $p_end);
                $amount = bcadd($t1_amount, $t2_amount);
                break;
            case 1:
                $amount = $this->compute_tva($p_start, $p_end);
                $amount = bcadd($amount, 0);
                break;

            case 2:
                $amount = $this->compute_amount($p_start, $p_end);
                $amount = bcadd($amount, 0);
                break;

            default:
                throw new Exception('Type de total invalide');
                break;
        }
        return $amount;
    }

}

/**
 * @brief handle the param_detail type Account
 * The type_sum_account gives the type of total
 *   - 0 D-C
 *   - 1 C-D
 *   - 2 D
 *   - 4 C
 * it uses tmp_val, with_tmp_val and type_sum_account
 * @see RAPAV_Account
 */
class Rapav_dd_Account extends Rapav_Declaration_Detail
{

    function compute($p_start, $p_end)
    {
        global $cn;
        $filter_ledger = "";
        if ($this->form->jrn_def_id != "")
        {
            $filter_ledger = " and jrn1.j_jrn_def = " . sql_string($this->form->jrn_def_id);
        }
        
        if ($this->form->date_paid == 1)
        {
            $sql_date=" and j_id in 
                (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id)
                    where
                    coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) >= to_date($3,'DD.MM.YYYY')
                    and coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) <= to_date($4,'DD.MM.YYYY')
                 )
                    ";
                    
        }
        else
        {
            $sql_date="and (jrn1.j_date >= to_date($3,'DD.MM.YYYY') and jrn1.j_date <= to_date($4,'DD.MM.YYYY'))";
        }
        bcscale(2);
        switch ($this->form->type_sum_account)
        {
            // Saldo
            case 1:
            case 2:
                // Compute D-C
                $sql = "
                        select sum(jrnx_amount)
                        from (
                                select distinct jrn1.j_id,case when jrn1.j_debit = 't' then jrn1.j_montant else jrn1.j_montant*(-1) end as jrnx_amount
                                from jrnx as jrn1
                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                where
                                jrn1.j_poste like $1
                                and
                                jrn2.j_poste like $2
                                $sql_date
                                $filter_ledger
                                ) as tv_amount
							 ";
                $amount = $cn->get_value($sql, array(
                    $this->form->tmp_val,
                    $this->form->with_tmp_val,
                    $p_start,
                    $p_end
                ));
                // if C-D is asked then reverse the result
                if ($this->form->type_sum_account == 2)
                    $amount = bcmul($amount, -1);
                break;
            // Only DEBIT
            case 3:
                $sql = "
                        select sum(jrnx_amount)
                        from (
                                select distinct jrn1.j_id,jrn1.j_montant as jrnx_amount
                                from jrnx as jrn1
                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                where
                                jrn1.j_poste like $1
                                and
                                jrn2.j_poste like $2
                                and
                                jrn1.j_debit='t'
                                $sql_date
                                $filter_ledger
                                ) as tv_amount
							 ";
                $amount = $cn->get_value($sql, array(
                    $this->form->tmp_val,
                    $this->form->with_tmp_val,
                    $p_start,
                    $p_end
                ));
                break;
            // Only CREDIT
            case 4:
                $sql = "
                        select sum(jrnx_amount)
                        from (
                                select distinct jrn1.j_id,jrn1.j_montant as jrnx_amount
                                from jrnx as jrn1
                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                where
                                jrn1.j_poste like $1
                                and
                                jrn2.j_poste like $2
                                and
                                jrn1.j_debit='f'
                                $sql_date
                                $filter_ledger
                                ) as tv_amount
							 ";
                $amount = $cn->get_value($sql, array(
                    $this->form->tmp_val,
                    $this->form->with_tmp_val,
                    $p_start,
                    $p_end
                ));
                break;

            default:
                if (DEBUG)
                    var_dump($this);
                die(__FILE__ . ":" . __LINE__ . " UNKNOW SUM TYPE");
                break;
        }
        /*
         * 4 possibilities with type_sum_account
         */
        return $amount;
    }

}

/**
 * @brief handle the param_detail type Account
 * The type_sum_account gives the type of total
 *   - 0 D-C
 *   - 1 C-D
 *   - 2 D
 *   - 4 C
 * it uses tmp_val, with_tmp_val and type_sum_account
 * @see RAPAV_Account
 */
class Rapav_dd_Reconcile extends Rapav_Declaration_Detail
{

    function compute($p_start, $p_end)
    {
        global $cn;
        bcscale(2);
        $filter_ledger = "";
        if ($this->form->jrn_def_id != "")
        {
            $filter_ledger = " and jrn1.j_jrn_def = " . sql_string($this->form->jrn_def_id);
        }
        switch ($this->form->type_sum_account)
        {
            // Saldo
            case 1:
            case 2:
                // Compute D-C
                $sql = "
                        select sum(tv_amount.jrnx_amount)
                                from (
                                        select distinct jrn1.j_id,j1.jr_id,
                                        case when jrn1.j_debit = 't' then jrn1.j_montant else jrn1.j_montant*(-1) end as jrnx_amount
                                        from jrnx as jrn1
                                        join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                        join jrn as j1 on (jrn1.j_grpt=j1.jr_grpt_id)
                                        where
                                        jrn1.j_poste like $1
                                        and	jrn2.j_poste like $2
                                        $filter_ledger
                                        ) as tv_amount
                                join jrn_rapt as rap1 on (rap1.jr_id=tv_amount.jr_id or rap1.jra_concerned=tv_amount.jr_id)
                                join (select distinct jrn3.j_id,j2.jr_id
                                        from jrnx as jrn3
                                        join jrn as j2 on (j2.jr_grpt_id=jrn3.j_grpt)
                                        where
                                (jrn3.j_date >= to_date($3,'DD.MM.YYYY') and jrn3.j_date <= to_date($4,'DD.MM.YYYY')) and
                                         jrn3.j_poste like $5) as reconc on (rap1.jr_id=reconc.jr_id or rap1.jra_concerned=reconc.jr_id)

							 ";
                $amount = $cn->get_value($sql, array(
                    $this->form->tmp_val,
                    $this->form->with_tmp_val,
                    $p_start,
                    $p_end,
                    $this->form->operation_pcm_val
                ));
                // if C-D is asked then reverse the result
                if ($this->form->type_sum_account == 2)
                    $amount = bcmul($amount, -1);
                break;
            // Only DEBIT
            case 3:
                $sql = "
                        select sum(tv_amount.jrnx_amount)
                                from (
                                        select distinct jrn1.j_id,j1.jr_id,
                                        jrn1.j_montant as jrnx_amount
                                        from jrnx as jrn1
                                        join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                        join jrn as j1 on (jrn1.j_grpt=j1.jr_grpt_id)
                                        where
                                        jrn1.j_poste like $1
                                        and	jrn2.j_poste like $2
                                        and jrn1.j_debit='t'
                                        $filter_ledger
                                        ) as tv_amount
                                join jrn_rapt as rap1 on (rap1.jr_id=tv_amount.jr_id or rap1.jra_concerned=tv_amount.jr_id)
                                join (select distinct jrn3.j_id,j2.jr_id
                                        from jrnx as jrn3
                                        join jrn as j2 on (j2.jr_grpt_id=jrn3.j_grpt)
                                        where
                                (jrn3.j_date >= to_date($3,'DD.MM.YYYY') and jrn3.j_date <= to_date($4,'DD.MM.YYYY')) and
                                         jrn3.j_poste like $5) as reconc on (rap1.jr_id=reconc.jr_id or rap1.jra_concerned=reconc.jr_id)

							 ";
                $amount = $cn->get_value($sql, array(
                    $this->form->tmp_val,
                    $this->form->with_tmp_val,
                    $p_start,
                    $p_end,
                    $this->form->operation_pcm_val
                ));
                break;
            // Only CREDIT
            case 4:
                $sql = "
                        select sum(tv_amount.jrnx_amount)
                                        from (
                                                select distinct jrn1.j_id,j1.jr_id,
                                                jrn1.j_montant  as jrnx_amount
                                                from jrnx as jrn1
                                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                                join jrn as j1 on (jrn1.j_grpt=j1.jr_grpt_id)
                                                where
                                                jrn1.j_poste like $1
                                                and	jrn2.j_poste like $2
                                                and jrn1.j_debit='f'
                                                $filter_ledger
                                                ) as tv_amount
                                        join jrn_rapt as rap1 on (rap1.jr_id=tv_amount.jr_id or rap1.jra_concerned=tv_amount.jr_id)
                                        join (select distinct jrn3.j_id,j2.jr_id
                                                from jrnx as jrn3
                                                join jrn as j2 on (j2.jr_grpt_id=jrn3.j_grpt)
                                                where
                                        (jrn3.j_date >= to_date($3,'DD.MM.YYYY') and jrn3.j_date <= to_date($4,'DD.MM.YYYY')) and
                                                 jrn3.j_poste like $5) as reconc on (rap1.jr_id=reconc.jr_id or rap1.jra_concerned=reconc.jr_id)

							 ";
                $amount = $cn->get_value($sql, array(
                    $this->form->tmp_val,
                    $this->form->with_tmp_val,
                    $p_start,
                    $p_end,
                    $this->form->operation_pcm_val
                ));
                break;

            default:
                if (DEBUG)
                    var_dump($this);
                die(__FILE__ . ":" . __LINE__ . " UNKNOW SUM TYPE");
                break;
        }
        /*
         * 4 possibilities with type_sum_account
         */
        return $amount;
    }

}

?>
