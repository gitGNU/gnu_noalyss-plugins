<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<table>
    <tr>
        <td>
            Nom
        </td>
        <td>
            <?php echo $name->input();?>
        </td>
        
    </tr>
    <tr>
        <td>
            Description
        </td>
        <td>
            <?php echo $description->input();?>
        </td>
        
    </tr>
    <tr>
        <td>
            Catégorie de fiche
        </td>
        <td>
            <?php echo $fichedef->input();?>
        </td>
        
    </tr>
    <tr>
        <td>
            Fichier
        </td>
        <td>
            <?php echo $file->input();?>
        </td>
        
    </tr>

</table>
<?php
echo $str_remove;
?>