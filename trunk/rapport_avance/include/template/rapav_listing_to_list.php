

<table class="result" id="listing_tb_id">
    <tr>
        <th>
            Nom
        </th>
        <th>
            Description
        </th>
        <th>
            Catégorie de fiches
        </th>
    </tr>
    <?php
    $max=Database::num_row($res);
    for ($i=0;$i<$max;$i++):
            $class=($i%2==0)?'class="odd"':'class="even"';

        $row=  Database::fetch_array($res, $i);
     $arg = array(
            'gDossier' => Dossier::id(),
            'ac' => $_REQUEST['ac'],
            'pc' => $_REQUEST['plugin_code'],
            'id' => $row['l_id'],
            'cin' => 'listing_definition_id',
            'cout' => 'listing_definition_div_id');
        $json = 'listing_definition(' . str_replace('"', "'", json_encode($arg)) . ')';
    ?>
    	<tr <?php echo $class; ?>>
        <td>
            <?php echo HtmlInput::anchor(h($row['l_name']),'',' onclick="'.$json.'"'); ?>
        </td>
        <td>
            <?php echo h($row['l_description']); ?>
        </td>
        <td>
            <?php echo h($row['fd_label']); ?>
        </td>
        <td>
            
        </td>
    </tr>
    <?php
    endfor;
    
    ?>
</table>