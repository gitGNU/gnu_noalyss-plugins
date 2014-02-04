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
/**
 * @brief manage the table intervat_client
 */
class Transform_Client
{

    /**
     * vat_amount_sum  VAT amount in the listing
     */
    var $vat_amount_sum;

    /**
     * turnoversum amount without vat in the listing
     */
    var $turnoversum;

    /**
     * nb number of customer in the listing
     */
    var $nb;

    /**
     * Array of table intervat_client
     */
    var $array;

    /**
     * compute value from declarant
     * @param integer id of declarant (intervat_declarant.d_id)
     */
    function compute_value($p_declarant)
    {
        global $cn;
        $this->correct_data($p_declarant);
        $this->array = $cn->get_array('select * from transform.intervat_client where d_id=$1', array($p_declarant));
        $this->nb = count($this->array);
        
        $this->vat_amount_sum = $cn->get_value('select sum(c_amount_vat::numeric) from 
            transform.intervat_client where d_id=$1',array($p_declarant));
        
        $this->turnoversum = $cn->get_value('select sum(c_amount_novat::numeric) from 
            transform.intervat_client where d_id=$1',array($p_declarant));
    }
    private function correct_data($p_declarant)
    {
        global $cn;
        $cn->exec_sql("update transform.intervat_client set c_amount_vat=replace(c_amount_vat,',','.'),
            c_amount_novat=replace(c_amount_novat,',','.') where d_id=$1",array($p_declarant));
    }

}

?>
