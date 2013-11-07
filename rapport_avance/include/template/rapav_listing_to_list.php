

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
        $row=  Database::fetch_array($res, $i);
        $arg = array(
            'gDossier' => Dossier::id(),
            'ac' => $_REQUEST['ac'],
            'pc' => $_REQUEST['plugin_code'],
            'id' => $row['l_id'],
            'cin' => 'listing_tb_id',
            'cout' => 'listing_mod_div');
        $json = 'listing_modify(' . str_replace('"', "'", json_encode($arg)) . ')';
    ?>
    <tr>
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
            Action
        </td>
    </tr>
    <?php
    endfor;
    
    ?>
</table>