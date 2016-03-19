<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 * @file
 * @brief show a list and all of its parameters and detail
 * 
 */
?>
<p>
    Nom listing <?php echo h($this->data->getp('name')); ?>
</p>
<p>
    Description <?php echo h($this->data->getp('description')); ?>
</p>
<p>
    Catégorie <?php echo h($this->get_categorie_name()); ?>
<?php echo h($this->get_categorie_description()); ?>
</p>
    <?php echo $button->input(); ?>
<table class="result" id="definition_tb_id">
    <tr>
        <th>
            Code
        </th>
        <th>
            Commentaire
        </th>
        <th>
            Formules
        </th>
        <th>
            Ordre
        </th>
        <th>
            Condition
        </th>
        <th>
            action
        </th>
    </tr>
<?php
$nb = count($this->a_detail);
for ($i = 0; $i < $nb; $i++):
    $class=($i%2==0)?' class="even"':'class="odd"';
    ?>
        <tr id="tr_<?php echo $this->a_detail[$i]->Param->getp('lp_id') ?>" <?php echo $class?>>
            <td>
    <?php echo $this->a_detail[$i]->Param->getp('code'); ?>
            </td>
            <td>
    <?php echo h($this->a_detail[$i]->Param->getp('comment')); ?>
            </td>
            <td>
    <?php
    $obj = RAPAV_Listing_Formula::make_object($this->a_detail[$i]->Param);
    echo $obj->display();
    ?>
            </td>
            <td>
    <?php echo $this->a_detail[$i]->Param->getp('order'); ?>
            </td>
            <td>
    <?php  
        $condition=new RAPAV_Condition();
        $a_cond=$condition->load_by_listing_param_id($this->a_detail[$i]->Param->getp('lp_id'));
        $nb_cond=count($a_cond);
        for ( $j = 0;$j < $nb_cond ; $j++)
            echo $a_cond[$j]->get_condition();
    ?>
            </td>
            <td>
    <?php
    echo $this->a_detail[$i]->button_delete();
    ?>
            </td>
            <td>
    <?php
    echo $this->a_detail[$i]->button_modify();
    ?>
            </td>

        </tr>


                <?php
            endfor;
            ?>   
</table> 
