<?php

/**
 * @file
 * @brief show a list and all of its parameters and detail
 * 
 */

?>
<p>
    Nom listing <?php echo h($this->Data->getp('name')); ?>
</p>
<p>
    Description <?php echo h($this->Data->getp('description')); ?>
</p>
<p>
    Catégorie <?php echo h($this->get_categorie_name()); ?>
    <?php echo h($this->get_categorie_description());  ?>
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
            action
        </th>
    </tr>
<?php 
    $nb=count($this->a_detail);
    for ($i=0;$i<$nb;$i++):
?>
    <tr id="tr_<?php echo $this->a_detail[$i]->Param->getp('lp_id')?>">
        <td>
           <?php echo $this->a_detail[$i]->Param->getp('code'); ?>
        </td>
        <td>
           <?php echo h($this->a_detail[$i]->Param->getp('comment')); ?>
        </td>
        <td>
            <?php 
            $obj=   RAPAV_Listing_Formula::make_object($this->a_detail[$i]->Param);
            echo $obj->display();
            ?>
        </td>
        <td>
            <?php echo $this->a_detail[$i]->Param->getp('order'); ?>
        </td>
        <td>
            Efface / modifie
        </td>
        
    </tr>


<?php
    endfor;
?>   
</table>    