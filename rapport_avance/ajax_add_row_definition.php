<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<?php
/**
 * @file
 * @brief  Ajoute une ligne dans la definition
 */
$type_row = $cn->make_array("select p_type,p_description from rapport_advanced.type_row order by p_description");
$type_periode = $cn->make_array("select t_id,t_description from rapport_advanced.periode_type order by t_description");
?>
<td>
    Nouv.
</td>
<td>
    <?php echo HtmlInput::hidden('p_id[]', -1) ?>
    <?php
    $p_code = new IText('p_code[]');
    $p_code->size = "10";
    echo $p_code->input();
    ?>
</td>
<td>
    <?php
    $p_libelle = new IText('p_libelle[]');
    $p_libelle->css_size = "100%";
    echo $p_libelle->input();
    ?>
</td>
<td>
    <?php
    $p_type = new ISelect('p_type[]');
    $p_type->value = $type_row;
    echo $p_type->input();
    ?>
</td>
<td>
    <?php
    $p_type_periode = new ISelect('t_id[]');
    $p_type_periode->value = $type_periode;
    echo $p_type_periode->input();
    ?>
</td>
<td>
    <?php
    $p_order = new INum('p_order[]');
    $p_order->prec = 0;
    $p_order->size = 4;
    echo $p_order->input();
    ?>
</td>
