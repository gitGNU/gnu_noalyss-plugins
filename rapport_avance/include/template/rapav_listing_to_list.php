<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<table class="result" id="listing_tb_id">
    <tr>
        <th>
            Nom
        </th>
        <th>
            Description
        </th>
        <th>
            Modèle de document
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
        
        $arg2=array(
            'gDossier' => Dossier::id(),
            'ac' => $_REQUEST['ac'],
            'pc' => $_REQUEST['plugin_code'],
            'id' => $row['l_id'],
            'cin' => 'listing_detail_id',
            'cout' => 'listing_detail_id');
        $modify = 'listing_modify('.str_replace('"',"'",  json_encode($arg2).')');
        $url_document="extension.raw.php?".http_build_query(array(
            'gDossier' => Dossier::id(),
            'ac' => $_REQUEST['ac'],
            'plugin_code' => $_REQUEST['plugin_code'],
            'act'=>'downloadTemplateListing',
            "id"=>$row['l_id']
           ));
    ?>
    <a class="line"href="extension.raw.php?gDossier=10077&amp;ac=rapav&amp;plugin_code=rapav&amp;id=1&amp;act=downloadTemplateListing" >00.m3u</a>
    <a class="line" href="extension.raw.php?gDossier=10077&amp;ac=EXT%2FRAPAV&amp;plugin_code=EXT%2FRAPAV&amp;act=downloadTemplateListing&amp;id=1">00.m3u</a>
    
    	<tr <?php echo $class; ?>>
        <td>
            <?php echo HtmlInput::anchor(h($row['l_name']),'',' onclick="'.$json.'"'); ?>
        </td>
        <td>
            <?php echo h($row['l_description']); ?>
        </td>
        <td>
           <?php echo HtmlInput::anchor(h($row['l_filename']), $url_document);?>
        </td>
        <td>
            <?php echo h($row['fd_label']); ?>
        </td>
        <td>
            <?php echo HtmlInput::anchor(_('Modifie'),'',' onclick="'.$modify.'"'); ?>
            
        </td>
    </tr>
    <?php
    endfor;
    
    ?>
</table>