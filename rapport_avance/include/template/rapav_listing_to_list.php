

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
    ?>
    <tr>
        <td>
            <?php echo h($row['l_name']); ?>
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