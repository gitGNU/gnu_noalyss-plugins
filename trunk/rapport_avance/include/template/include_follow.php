<?php echo HtmlInput::title_box("Suivi", "include_follow_result"); ?>
<form method="GET" onsubmit="js_include_follow_save();
        return false;" id="include_follow_save_frm">
    <table>
        <tr>
            <td>
                <label>Date</label>
            </td>        
            <td>
                <?php echo $date->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>Date rappel</label>
            </td>
            <td>
                <?php echo $remind_date->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    Profil
                </label>
            </td>
            <td>

                <?php echo $ag_dest->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>Titre </label>
            </td>
            <td>
                <?php echo $titre->input(); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    Catégorie
                </label>
            </td>
            <td>
                <?php echo $category->input(); ?>
            </td>
        </tr>
    </table>
    <p>
                <label style="vertical-align: top">Commentaire</label>
    </p>
                <?php echo $desc->input(); ?>
    <p>
        <?php
        echo HtmlInput::array_to_hidden(array('gDossier', 'plugin_code', 'ac', 'lc_id'), $_REQUEST);
        echo HtmlInput::hidden('act', 'include_follow_save');
        echo HtmlInput::submit('include_follow_save_sbm', "Sauver", "", "smallbutton");
        echo HtmlInput::button_close('include_follow_result');
        ?>
    </p>

</form>
