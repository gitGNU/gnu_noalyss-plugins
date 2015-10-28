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

/*!\file
 * \brief raw file for PDF ewa
 */
//require_once('amortis_constant.php');

$act = HtmlInput::default_value_get("act", null);

/**
 * export operation in CSV
 */
if ($act == 'export_operation')
{
    // -- parameter
    $p_from = HtmlInput::default_value_get("p_from", null);
    $p_to = HtmlInput::default_value_get("p_to", null);
    
    // -- check date
    if ( $p_from==null || isDate($p_from)==null || $p_to == null || isDate($p_to) == null) {
        die (_('Date invalide'));
    }
    // Security : filter by ledger
    $ledger=$g_user->get_ledger_sql('ALL',3);
   
    // SQL stmt
    $sql = "
          select to_char(j_date,'DD.MM.YYYY') as str_date,
                jrn_def_code,
                jr_internal,
                jr_pj_number,
                j_poste,
                j_qcode,
                case when coalesce(j_text,'')='' then pcm_lib else j_text end as text,
                j_montant, 
                case when j_debit='t' then 'D' else 'C' end as dc,
                comptaproc.get_letter_jnt(j_id) AS lettrage
                
            from jrnx
            join jrn on (j_grpt=jr_grpt_id)
            join tmp_pcmn on j_poste=pcm_val
            join jrn_def on jrn_def_id=jr_def_id
            where 
            j_date between to_date($1,'DD.MM.YYYY') and to_date($2,'DD.MM.YYYY')
            and $ledger order by j_date
            ";
    
    $ret = $cn->exec_sql($sql, array($p_from, $p_to));
    $file_name='export_operation-'.date('ymd').'.csv';
    header('Pragma: public');
    header('Content-type: application/csv');
    header('Content-Disposition: attachment;filename="'.$file_name.'"', FALSE);
    $nb=Database::num_row($ret);
    printf('"%s";',_("Date DMY"));
    printf('"%s";',_("Journal"));
    printf('"%s";',_("n° interne"));
    printf('"%s";',_("piece"));
    printf('"%s";',_("poste"));
    printf('"%s";',_("Code"));
    printf('"%s";',_("texte"));
    printf('"%s";',_("montant"));
    printf('"%s";',_("DC"));
    printf('"%s"',_("lettrage"));
    printf ("\r\n");
    
    for ($i=0;$i < $nb;$i++)
    {
        $row=Database::fetch_array($ret,$i);
        printf('"%s";',$row['str_date']);
        printf('"%s";',$row['jrn_def_code']);
        printf('"%s";',$row['jr_internal']);
        printf('"%s";',$row['jr_pj_number']);
        printf('"%s";',$row['j_poste']);
        printf('"%s";',$row['j_qcode']);
        printf('"%s";',$row['text']);
        printf('%s;',nb($row['j_montant']));
        printf('"%s";',$row['dc']);
        printf('"%s"',$row['lettrage']);
        printf ("\r\n");
    }
}
/**
 * Download receipt
 */
if ($act=='download_receipt')
{
    extract ($_REQUEST);

    $zip_file=HtmlInput::default_value_request('file', 'null');
    if ($zip_file=='null')
    {
        die('No file asked');
    }

    $zip_file=$_ENV['TMP']."/".$zip_file;

    header('Pragma: public');
    header('Content-type: application/zip');
    header('Content-Disposition: attachment; filename="'.$file.'"');
    $h_file=fopen($zip_file, "r");
    if ($h_file!=true)
    {
        die('cannot open file');
    }
    $buffer=fread($h_file, filesize($zip_file));
    echo $buffer;
}
?>
