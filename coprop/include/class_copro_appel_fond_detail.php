<?php

/**
 * @brief manage the table coprop.appel_fond_detail
 */
class Copro_Appel_Fond_Detail
{
    function insert()
    {
        global $cn;
        $this->afd_id=$cn->get_value("insert into coprop.appel_fond_detail
            (af_id,lot_id,key_id,afd_amount,key_tantieme,lot_tantieme)
            values ($1,$2,$3,$4,$5,$6) returning afd_id",
                array(
                    $this->af_id, //1
                    $this->lot_id, //2
                    $this->key_id, //3 
                    $this->afd_amount, //4 
                    $this->key_tantieme, //5 
                    $this->lot_tantieme)); //6
    }
}
?>
