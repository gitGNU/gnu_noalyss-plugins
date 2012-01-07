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
            $fiche_dep=$cn->make_list("select fd_id from fiche_def where frd_id=2");

			$a_key=$cn->make_array(" select cr_id,cr_name from coprop.clef_repartition order by cr_name");
            for ($i=0;$i<count($array);$i++)
            {
                $card=new ICard('f_id'.$i);
                $card->value=$array[$i]['quick_code'];
                $card->table=0;

                 // name of the field to update with the name of the card
                $card->set_attribute('label','w_card_label'.$i);

                // Type of card : deb, cred,
                $card->set_attribute('typecard',$fiche_dep);

                $card->extra=$fiche_dep;

                // Add the callback function to filter the card on the jrn
                $card->set_callback('filter_card');
                $card->set_attribute('ipopup','ipopcard');
                // when value selected in the autcomplete
                  $card->set_function('fill_data');

                // when the data change

                  $card->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
                            $card->name);
                  $card->set_dblclick("fill_ipopcard(this);");

                  $card_label=new ISpan();
                  $card_label->table=0;
                  $f_card_label=$card_label->input("w_card_label".$i,"");

                // Search button for card
                $f_card_bt=$card->search();

                $amount=new INum("bt_amount[]");
                $amount->value=round($array[$i]['bt_amount'],2);
                $hidden=HtmlInput::hidden("bt_id[]",$array[$i]["bt_id"]);

                $ikey=new ISelect("key[]");
                $ikey->value=$a_key;
                $ikey->selected=$array[$i]['cr_id'];

                $a_input[$i]["amount"]=$amount->input();
                $a_input[$i]["hidden"]=$hidden;
                $a_input[$i]["card"]=$card->input().$f_card_bt;
                $a_input[$i]["card_label"]=$f_card_label;

                $a_input[$i]['key']=$ikey->input();

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
