<?php


class Budget
{
    function to_list()
    {
        global $cn;
        
        $array=$cn->get_array("select b_id, b_name,
                    to_char(b_start,'DD.MM.YYYY') as str_start,
                    to_char(b_end,'DD.MM.YYYY') as str_end,
                    b_amount
                    from coprop.budget
                    order by b_name
                    ");
        
        require_once 'template/budget_list.php';

    }
    function detail()
    {
        global $cn;
        try
        {
            
            if ( ! isset ($this->b_id)|| trim($this->b_id)=='')
                    throw new Exception ("Aucun budget demandé");
            $array=$cn->get_array("select bt_id,bt_amount,f_id,vw_name,quick_code,cr_name,cr_id 
                from coprop.budget_detail
                join coprop.clef_repartition using (cr_id)
                join vw_fiche_attr using (f_id)
                where b_id=$1",array($this->b_id));
            $a_input=array();
            for ($i=0;$i<count($array);$i++)
            {
                $card=new ICard('f_id[]');
                $card->value=$array[$i]['quick_code'];
                $amount=new INum("bt_amount[]");
                $amount->value=round($array[$i]['bt_amount'],2);
                $hidden=HtmlInput::hidden("bt_id[]",$array[$i]["bt_id"]);
                $a_input[$i]["amount"]=$amount->input();
                $a_input[$i]["hidden"]=$hidden;
                $a_input[$i]["card"]=$card->input();
                
            }
            require_once 'template/bud_detail.php';
        }
        catch (Exception $e)
        {
            $e->getTraceAsString();
        }
    }
}



?>
