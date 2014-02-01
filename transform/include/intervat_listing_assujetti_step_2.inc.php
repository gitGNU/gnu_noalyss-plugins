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

// Copyright Author Dany De Bontridder danydb@aevalys.eu
var_dump($_POST);
/**
 * save data into db and display them before creating the XML
 */
global $cn;
require_once 'class_transform_sql.php';
require_once 'class_transform_representative.php';
require_once 'class_transform_declarant.php';

$inputtype = HtmlInput::default_value_post('p_inputtype', null);
$year = HtmlInput::default_value_post('p_year', NULL);
$atva = HtmlInput::default_value_post('h_tva', null);
$compute_date = HtmlInput::default_value_post('p_compute_date', null);

// If inputtype is null not choice between file or compute
if ($inputtype == null)
{
    throw new Exception(_('Vous devez choisir par fichier ou par calcul'), 4);
}
// if inputtype is by computing (=2) then year must existe as exercice 
// and tva_id must not be empty
if ($inputtype == 2)
{
    if ($atva == null)
    {
        throw new Exception(_('Vous devez choisir au moins un taux TVA'), 5);
    }
    if ($year == null)
    {
        throw new Exception(_("Vous devez donner l'année"), 6);
    }
    if ($compute_date == null || isNumber($compute_date) == 0)
    {
        throw new Exception(_("Date de calcul incorrect"), 7);
    }
    if ($compute_date > 3000 && $compute_date < 1940)
    {
        throw new Exception(_("Date de calcul incorrect"), 8);
    }
    foreach ($atva as $tva)
    {
        if (isNumber($tva) == 0)
        {
            throw new Exception(_("ID Tva incorrect: [$tva]"), 9);
        }
    }
}
/**
 * Save a new request
 */
$request = new Transform_Request_SQL();
$request->r_type = 'intervat';

$request->insert();

$representative = new Transform_Representative();
$representative->data = new Intervat_Representative_SQL;
$representative->data->r_id = $request->r_id;
$representative->fromPost();
$representative->insert();

$declarant = new Transform_Declarant();
$declarant->data = new Intervat_Declarant_SQL;
$declarant->data->r_id = $request->r_id;
$declarant->fromPost();
$declarant->insert();

/* * ****************************************************************************
 * Save the file
 * ***************************************************************************** */
if ($inputtype == 1)
{
    if (count($_FILES) == 0)
        throw new Exception(_('Aucun fichier donné'), 1);

    $name = $_FILES['client_assujetti']['name'];

    if (strlen($_FILES['client_assujetti']['tmp_name'][0]) != 0)
    {
        $new_name = tempnam($_ENV['TMP'], 'client_assujetti');
        if (!move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], $new_name))
        {
            throw new Exception(_('Impossible de sauver ce fichier'), 2);
        }
        $file = fopen($new_name, "r");
        $i = 0;
        $o_data = array();
        try
        {
            $cn->start();
            while ($data = fgetcsv($file, 0, ";"))
            {
                $i++;
                if (count($data) != 4 || $i == 1)
                {
                    $o_data[$i] = _('Ligne non importée') . join(' - ', $data);
                    continue;
                }
                /*
                 * insert into transform.intervat_client
                 */
                $o_data[$i] = new Intervat_Client_SQL();
                $o_data[$i]->d_id = $declarant->data->d_id;
                $o_data[$i]->c_name = $data[0];
                $o_data[$i]->c_issuedby = "BE";
                $o_data[$i]->c_vatnumber = $data[1];
                $o_data[$i]->c_amount_vat = $data[2];
                $o_data[$i]->c_amount_novat = $data[3];
                $o_data[$i]->insert();
            }
            $cn->commit();
        } catch (Exception $ex)
        {
            $cn->rollback();
            throw new Exception(_('Ne peut pas ajouter ') . h($o_data[$i]->c_name) . '-' . h($o_data[$i]->c_vatnumber), 3);
        }
    }
}
//******************************************************************************
//----- Compute the data and insert them into the date
//******************************************************************************
if ($inputtype == 2)
{
    var_dump($atva);
    $ltva = "(" . implode(',', $atva) . ")";
    //------ Operation date ----------------
    if ($compute_date == 1)
    {
        $sql = "
        with  c as 
        (select qs_client,
         sum(qs_vat) as vat_amount,
         sum(qs_price) as amount 
         from quant_sold  
         where 
         qs_vat_code in $ltva 
         and j_id in (select j_id 
                        from jrnx 
                        where 
                        j_tech_per in 
                            (select  p_id 
                                from parm_periode 
                                    where p_exercice=$1) ) 
        group by qs_client)
        ,f_name as 
        (select f_id,ad_value 
            from fiche_detail 
            where ad_id=1)  ,
        f_tvanum as 
        (select f_id,ad_value 
            from fiche_detail 
            where ad_id=13) 
select f_name.ad_value as name,
    f_tvanum.ad_value as tvanumb, 
   vat_amount,amount
from 
    c join f_name on (qs_client=f_name.f_id)
    join f_tvanum on (qs_client=f_tvanum.f_id)
    ";
    } elseif ($compute_date == 2) //------ Payment date ----------------
    {
        $sql = "
        with  c as 
        (select qs_client,
            sum(qs_vat) as vat_amount,
            sum(qs_price) as amount 
        from 
            quant_sold join jrnx on (jrnx.j_id=quant_sold.j_id) 
        where 
        qs_vat_code=1 
        and j_grpt in (select jr_grpt_id 
                        from jrn 
                        where 
                        jr_date_paid >= (select min(p_start) from parm_periode where p_exercice=$1 ) 
                        and jr_date_paid <=  (select max(p_start) from parm_periode where p_exercice=$1 ))
         group by qs_client)
    ,f_name as 
    (select f_id,
        ad_value 
        from 
        fiche_detail 
        where 
        ad_id=1)  ,
    f_tvanum as 
    (select f_id,
        ad_value 
        from fiche_detail 
        where 
        ad_id=13) 
select f_name.ad_value as name,
      f_tvanum.ad_value as tvanumb, 
      vat_amount,amount
from 
 c join f_name on (qs_client=f_name.f_id)
   join f_tvanum on (qs_client=f_tvanum.f_id)
";
    }
    $a_listing = $cn->get_array($sql, array($year));
    
    /**
     * Save data into Intervat_Client
     */
    $o_data = array();
        try
        {
            $cn->start();
            $nb=count($a_listing);
            for ($i=0;$i <$nb; $i++)
            {
                /*
                 * insert into transform.intervat_client
                 */
                $o_data[$i] = new Intervat_Client_SQL();
                $o_data[$i]->d_id = $declarant->data->d_id;
                $o_data[$i]->c_name = $a_listing[$i]['name'];
                $o_data[$i]->c_issuedby = "BE";
                $o_data[$i]->c_vatnumber = $a_listing[$i]['tvanumb'];
                $o_data[$i]->c_amount_vat =  $a_listing[$i]['vat_amount'];
                $o_data[$i]->c_amount_novat =  $a_listing[$i]['amount'];
                $o_data[$i]->insert();
            }
            $cn->commit();
        } catch (Exception $ex)
        {
            $cn->rollback();
            throw new Exception(_('Ne peut pas ajouter ') . h($o_data[$i]->c_name) . '-' . h($o_data[$i]->c_vatnumber), 3);
        }
    
    /**
     * Show the result 
     */
}

?>
