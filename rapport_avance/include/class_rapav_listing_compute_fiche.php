<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rapav_listing_compute_fiche
 *
 * @author dany
 */
class RAPAV_Listing_Compute_Fiche extends RAPAV_Listing_Compute_Fiche_SQL
{

    function set_listing_compute(RAPAV_Listing_Compute &$listing_compute)
    {
        $this->listing_compute=$listing_compute;
    }
    private function get_file_to_parse()
    {
        global $cn;
        // create a temp directory in /tmp to unpack file and to parse it
        $dirname = tempnam($_ENV['TMP'], 'rapav_listing');


        unlink($dirname);
        mkdir($dirname);
        chdir($dirname);
        // Retrieve the lob and save it into $dirname
        $cn->start();


        $filename = $this->listing_compute->listing->data->l_filename;
        $exp = $cn->lo_export($this->listing_compute->listing->data->l_lob, $dirname . DIRECTORY_SEPARATOR . $filename);

        if ($exp === false)
            echo_warning(__FILE__ . ":" . __LINE__ . "Export NOK $filename");

        $type = "n";
        // if the doc is a OOo, we need to unzip it first
        // and the name of the file to change is always content.xml
        if (strpos($this->listing_compute->listing->data->l_mimetype, 'vnd.oasis') != 0)
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

        if ($this->listing_compute->listing->data->l_filename == "")
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
            $res = $zip->open($this->listing_compute->listing->data->l_filename, ZipArchive::CREATE);
            if ($res !== TRUE)
            {
                echo __FILE__ . ":" . __LINE__ . "cannot recreate zip";
                exit;
            }
            $zip->add_recurse_folder($dirname . DIRECTORY_SEPARATOR);
            $zip->close();

            ob_end_clean();

            $file_to_save = $this->listing_compute->listing->data->l_filename;
        } else
        {
            $file_to_save = $file_to_parse;
        }

        $this->load_document($dirname . DIRECTORY_SEPARATOR . $file_to_save);
    }

    private function parse_document($p_dir, $p_filename, $p_type)
    {
        global $cn;
        // Retrieve all the code + amount
        if ($p_type == "OOo")
        {
            $array = $cn->get_array("select '&lt;&lt;'||lc_code||'&gt;&gt;' as code,
                    ld_value_numeric,ld_value_text,ld_value_date,
                    case 
                        when ld_value_numeric is not null then 1 
                        when ld_value_text is not null then 2
                        when ld_value_date is not null then 3
                    end
                        as type
                    from rapport_advanced.listing_compute_detail where lf_id=$1 "
                    , array($this->lf_id));
        } else
        {
            $array = $cn->get_array("select '<<'||lc_code||'>>' as code,
                   ld_value_numeric,ld_value_text,ld_value_date, 
                    case 
                        when ld_value_numeric is not null then 1 
                        when ld_value_text is not null then 2
                        when ld_value_date is not null then 3
                    end
                        as type
                    from rapport_advanced.listing_compute_detail where lf_id=$1 "
                    , array($this->lf_id));
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
        $oname = tempnam($temp_dir, "listing_");
        $ofile = fopen($oname, "w+");

        // read ifile
        while (!feof($ifile))
        {
            $buffer = fgets($ifile);
            // for each code replace in p_filename the code surrounded by << >> by the amount (value) or &lt; or &gt;
            foreach ($array as $key => $value)
            {
                switch ($value['type'])
                {
                    case 1:
                        if ($p_type=='OOo')
                        {
                            $searched = 'office:value-type="string"><text:p>' . $value['code'];
                            $replaced = 'office:value-type="float" office:value="' . $value['ld_value_numeric'] . '"><text:p>' . $value['code'];
                            $buffer = str_replace($searched, $replaced, $buffer);
                        }
                    // everybody
                        $buffer = str_replace($value['code'], $value['ld_value_numeric'], $buffer);
                    break;
                    case 2:
                         $buffer = str_replace($value['code'], $value['ld_value_text'], $buffer);
                        break;
                    case 3:
                         $buffer = str_replace($value['code'], $value['ld_value_date'], $buffer);
                        break;
                    default:
                        throw new Exception(__FILE__.':'.__LINE__.' type inconnu');
                        break;
                }
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

    private function special_tag($p_dir, $p_filename, $p_type)
    {
        global $cn, $g_parameter;
        // Retrieve all the code + libelle
        $array[] = array('code' => 'PERIODE_DECLARATION', 'value' => format_date($this->listing_compute->data->l_start) . " - " . format_date($this->listing_compute->data->l_end));
        $array[] = array('code' => 'TITRE', 'value' => $this->listing_compute->listing->data->l_name);
        $array[] = array('code' => 'DOSSIER', 'value' => $cn->format_name($_REQUEST['gDossier'], 'dos'));
        $array[] = array('code' => 'NAME', 'value' => $g_parameter->MY_NAME);
        $array[] = array('code' => 'STREET', 'value' => $g_parameter->MY_STREET);
        $array[] = array('code' => 'NUMBER', 'value' => $g_parameter->MY_NUMBER);
        $array[] = array('code' => 'LOCALITE', 'value' => $g_parameter->MY_COMMUNE);
        $array[] = array('code' => 'COUNTRY', 'value' => $g_parameter->MY_PAYS);
        $array[] = array('code' => 'PHONE', 'value' => $g_parameter->MY_TEL);
        $array[] = array('code' => 'CEDEX', 'value' => $g_parameter->MY_CP);
        $array[] = array('code' => 'FAX', 'value' => $g_parameter->MY_FAX);
        $array[] = array('code' => 'NOTE', 'value' => $this->listing_compute->data->l_description);

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
        $oname = tempnam($temp_dir, "listing_");
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

    private function load_document($p_file)
    {
        global $cn;
        $cn->start();
        $this->lf_lob = $cn->lo_import($p_file);
        if ($this->lf_lob == false)
        {
            echo "ne peut pas importer [$p_file]";
            return 1;
        }
        $this->lf_size = filesize($p_file);
        $date = date('ymd-Hi');
        $this->lf_filename = $date . '-' . $this->listing_compute->listing->data->l_filename;
        $this->lf_mimetype= $this->listing_compute->listing->data->l_mimetype;
        $this->update();
        $cn->commit();
    }

}
