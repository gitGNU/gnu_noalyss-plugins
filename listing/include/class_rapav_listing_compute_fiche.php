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

require_once NOALYSS_INCLUDE.'/lib/class_sendmail.php';
require_once NOALYSS_INCLUDE.'/class/class_follow_up.php';

/**
 * Description of class_rapav_listing_compute_fiche
 *
 * @author dany
 */
class RAPAV_Listing_Compute_Fiche extends RAPAV_Listing_Compute_Fiche_SQL
{
    var $listing_compute;
    var $number;
    
    function set_number($p_id)
    {
        $this->number=$p_id;
    }
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
    /**
     * Generate the document, open the file and replace all the tags,
     * by the found value.
     * Load the file into the db
     */
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
        
        // Add history
        $this->histo($dirname, $file_to_parse, $type);

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
    /**
     * Replace into the file, the tags from the DB
     * @param $p_dir path
     * @param $p_filename filename (modele)
     * @param $p_type mimetype of the file (OOo) 
     * @throws Exception when the type from the DB is not a date, a text or a 
     * numeric
     */
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
                        else 2
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
                        else 2
                    end
                        as type
                    from rapport_advanced.listing_compute_detail where lf_id=$1 "
                    , array($this->lf_id));
        }

        // open the files
        $ifile = fopen($p_dir . '/' . $p_filename, 'r');

        // check if tmpdir exist otherwise create it
        $temp_dir = $_ENV['TMP'];
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
    /**
     * Replace special tags (PERIODE_DECLARATION, TITRE, DOSSIER, NAME... 
     * into the file
     * @param $p_dir path
     * @param $p_filename filename (modele)
     * @param $p_type mimetype of the file (OOo) 
     */
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
        $array[] = array('code' => 'NUM_PIECE', 'value' => $this->number);
        $array[] = array('code' => 'TODAY', 'value' => date('d.m.Y'));

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
    /**
     * Replace into the file the history tags
     *     -  Code _DATE date of operation
     *     -  Code _ECH limit date of operation
     *     -  Code _MONTANT_OPERATION amount of operation
     *     -  Code _PIECE Receipt number
     *     -  Code _INTERNAL Internal number of op
     *     -  Code _HTVA amount without VAT
     *     -  Code _TVA amount VAT
     *     -  Code _TOTAL amount HVAT + TVA
    * @param $p_dir path
     * @param $p_filename filename (modele)
     * @param $p_type mimetype of the file (OOo) 
     */
    private function histo($p_dir, $p_filename, $p_type)
    {
        global $cn, $g_parameter;
        // Retrieve all the code + libelle
        $sql="
        select lc_code,jr_date,jr_ech,jr_comment,jr_rapt,jr_montant,jr_internal,novat,vat,lf_id,jr_pj_number
                from rapport_advanced.listing_compute_detail
                left join rapport_advanced.listing_compute_historique  using (ld_id)
                join rapport_advanced.listing_compute_fiche using (lf_id)
                left join jrn using (jr_id)
                left join (
                                select sum(qs_price) as novat,sum(qs_vat) as vat,qs_internal from quant_sold join jrn on(qs_internal=jr_internal)
                                group by qs_internal
                                union all
                                select sum(qp_price),sum(qp_vat),qp_internal from quant_purchase join jrn on(qp_internal=jr_internal)
                                group by qp_internal) as detail on (detail.qs_internal=jrn.jr_internal)
                where 
             lf_id = $1
             order by 2";
        $array=$cn->get_array($sql,array($this->lf_id));
        
        // Compute the code to replace
        $nb_code=count($array);
        
        if ( $nb_code == 0)
            return;
        
        $a_code=array();
        for ($i=0;$i<$nb_code;$i++)
        {
            $a['date']=array(
                'key'=>$array[$i]['lc_code'].'_DATE',
                'value'=>$array[$i]['jr_date']
                    );
            $a['date_ech']=array(
                'key'=>$array[$i]['lc_code'].'_ECH',
                'value'=>$array[$i]['jr_ech']);
            
            $a['montantop']=array(
                'key'=>$array[$i]['lc_code'].'_MONTANT_OPERATION',
                'value'=>$array[$i]['jr_montant']);
            
            $a['comment']=array(
                'key'=>$array[$i]['lc_code'].'_COMMENT',
                'value'=>$array[$i]['jr_comment']);
            
            $a['piece']=array(
                'key'=>$array[$i]['lc_code'].'_PIECE',
                'value'=>$array[$i]['jr_pj_number']);
            
            $a['internal']=array(
                'key'=>$array[$i]['lc_code'].'_INTERNAL',
                'value'=>$array[$i]['jr_internal']);
            
            $a['novat']=array(
                'key'=>$array[$i]['lc_code'].'_HTVA',
                'value'=>$array[$i]['novat']);
            
            $a['vat']=array(
                'key'=>$array[$i]['lc_code'].'_TVA',
                'value'=>$array[$i]['vat']);
            
            $a['total']=array(
                'key'=>$array[$i]['lc_code'].'_TOTAL',
                'value'=>bcadd($array[$i]['vat'],$array[$i]['novat']));
            $a_code[]=$a;
        }
        // open the files
        $ifile = fopen($p_dir . '/' . $p_filename, 'r');

        // check if tmpdir exist otherwise create it
        $temp_dir = $_env['TMP'];
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
            foreach ($a_code as $key => $value)
            {
                
                // search all key and replace each by a different value
                foreach ($value as $key2=>$value2)
                {
                        if ($p_type == 'OOo')
                        {
                            $replace = '&lt;&lt;' . $value2['key'] . '&gt;&gt;';
                            $fmt_value = $value2['value'];
                            $fmt_value = str_replace('&', '&amp;', $fmt_value);
                            $fmt_value = str_replace('<', '&lt;', $fmt_value);
                            $fmt_value = str_replace('>', '&gt;', $fmt_value);
                            $fmt_value = str_replace('"', '&quot;', $fmt_value);
                            $fmt_value = str_replace("'", '&apos;', $fmt_value);
                        } else
                        {
                            $replace = '<<' . $value2['key'] . '>>';
                            $fmt_value = $value2['value'];
                        }
                        $buffer = preg_replace("/".$replace."/", $fmt_value, $buffer,1);
                }
            }
            /**
             * clean  remainging HISTO tags
             */
            foreach ($a_code as $key => $value)
            {
                
                // search all key and replace each by a different value
                foreach ($value as $key2=>$value2)
                {
                        if ($p_type == 'OOo')
                        {
                            $replace = '&lt;&lt;' . $value2['key'] . '&gt;&gt;';
                            $fmt_value = "";
                        } else
                        {
                            $replace = '<<' . $value2['key'] . '>>';
                            $fmt_value = "";
                        }
                        $buffer = str_replace($replace, $fmt_value, $buffer);
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
    /**
     * Load the document into DB
     */
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
        $fiche = new Fiche($cn, $this->f_id);
        $qcode=$fiche->strAttribut(ATTR_DEF_QUICKCODE);
        $this->lf_filename = $qcode.'-'.$date . '-' . $this->listing_compute->listing->data->l_filename;
        $this->lf_mimetype= $this->listing_compute->listing->data->l_mimetype;
        $this->update();
        $cn->commit();
    }
    /**
     * Create a PDF if GENERATE_PDF is different of NO and load it into
     * DB
     */
    function create_pdf()
    {
        global $cn;
        if (GENERATE_PDF == 'NO') return;
        $cn->start();
        if ( $this->lf_filename=="")return;
        
        $file=$_ENV['TMP']."/".$this->lf_filename;
        
        $cn->lo_export($this->lf_lob,$file);
        
        // remove extension
        $ext=  strrpos($this->lf_filename,".");
        $dst_file=  substr($this->lf_filename, 0, $ext);
        $dst_file.=".pdf";
        passthru(OFFICE.$file,$status);
        // reload it into database
        $this->lf_pdf=$cn->lo_import($_ENV['TMP']."/".$dst_file);
        $this->lf_pdf_filename=$dst_file;
        $this->update();
        $cn->commit();
        
    }
    /**
     * Send email
     * @param $p_from email sender
     * @param $p_subject subject 
     * @param $p_message message
     * @param $p_attach 
     *      - 0 no attach
     *      - 1 PDF document
     *      - 2 document from db
     * @param $p_copy 
     *      - 0 no copy to the sender
     *      - 1 copy to the sender
     */
    function send_mail($p_from, $p_subject, $p_message, $p_attach,$p_copy)
    {
        global $cn;
        $fiche = new Fiche($cn, $this->f_id);

        $email = $fiche->strAttribut(ATTR_DEF_EMAIL);
        $result = "";
        $this->lf_email_send_date = date('Y.m.d H:i');

        if ($email == "" || $email == NOTFOUND )
        {
            $result = $fiche->strAttribut(ATTR_DEF_QUICKCODE) . " n'a pas d'email ";

            $this->lf_email_send_result = $result;
            $this->update();

            return $result;
        }
        $mail = new Sendmail();
        $mail->set_from($p_from);
        $mail->set_message($p_message);
        $mail->set_subject($p_subject);
        if ($p_copy == 1 ) {
            $email.=','.$p_from;
        }
        $mail->mailto($email);
        switch ($p_attach)
        {
            case 0:
                /* no attach */
                break;
            case 1:
                /* -- PDF -- */
                if ($this->lf_pdf_filename == "")
                {
                    $result = $fiche->strAttribut(ATTR_DEF_QUICKCODE) . " n'a pas de document en PDF";
                    $this->lf_email_send_result = $result;
                    $this->update();

                    return $result;
                }
                $cn->start();
                $file = $this->lf_pdf_filename;
                $dir = tempnam($_ENV['TMP'], 'mail');
                unlink($dir);
                mkdir($dir);
                $cn->lo_export($this->lf_pdf, $dir . '/' . $file);
                $ofile = new FileToSend($dir . '/' . $file);
                $mail->add_file($ofile);
                break;
            case 2:
                /* -- Doc généré -- */
                if ($this->lf_filename == "")
                {
                    $result = $fiche->strAttribut(ATTR_DEF_QUICKCODE) . " n'a pas de document généré";
                    $this->lf_email_send_result = $result;
                    $this->update();

                    return $result;
                }
                $cn->start();
                $file = $this->lf_filename;
                $dir = tempnam($_ENV['TMP'], 'mail');
                unlink($dir);
                mkdir($dir);
                $cn->lo_export($this->lf_lob, $dir . '/' . $file);
                $ofile = new FileToSend($dir . '/' . $file);
                $mail->add_file($ofile);
                break;
        }
        $cn->commit();
        try
        {
            $mail->compose();
            $mail->send();
            $result = $fiche->strAttribut(ATTR_DEF_QUICKCODE) . ' message envoyé email : ' . $fiche->strAttribut(ATTR_DEF_EMAIL);
            $this->lf_email_send_result=$result;
            $this->update();
            /**
             * Add parameter copie à soi-même
             */
            return $result;
        } catch (Exception $ex)
        {
            $result = $fiche->strAttribut(ATTR_DEF_QUICKCODE) . " erreur mail " . $ex->getMessage();
            $this->lf_email_send_result=$result;
            $this->update();
            return $result;
        }
    }
    /**
     * Include into FOLLOW
     * @param type $p_array 
     * @see Follow_Up::fromArray
     * @return string with result
     */
    function include_follow($p_array)
    {
        global $cn;
        $action=new Follow_Up($cn);
        if (isDate($p_array['ag_timestamp'])==null) $p_array['ag_timestamp']=date('d.m.Y');
        if ( trim($p_array['ag_title']) == "")$p_array['ag_title']="Ajouté depuis plugin";
        
        $action->fromArray($p_array);
        
        $action->f_id=$this->f_id;
        $fiche = new Fiche($cn, $this->f_id);
        $action->qcode_dest=$fiche->strAttribut(ATTR_DEF_QUICKCODE);
        $_POST['nb_item']=0;
        $action->save();
        /** Copy the document in Follow_UP */
        Document::insert_existing_document($action->ag_id, $this->lf_lob, $this->lf_filename, $this->lf_mimetype);
        return $fiche->strAttribut(ATTR_DEF_QUICKCODE).' inclus dans Suivi';
    }

}
