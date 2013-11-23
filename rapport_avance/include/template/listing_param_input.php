<div>
    <form id="common_frm">
    <?php echo HtmlInput::hidden("listing_id", $p_id); ?>
        <table >
            <tr>
                <td>
                    <label>Code</label> 
                   
                </td>
                <td>
                    <?php echo $code->input() ?>
                </tD>
                <td>
                     <p id="code_id_span" class="error"></p>
                </td>
                
                
            </tr>
            <tr>
                <td>
                    <label>Commentaire</label>
                </td>
                <td>
                    <?php echo $comment->input(); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Ordre d'apparition</label>
                </td>
                <td>
                    <?php echo $order->input(); ?>
                </td>
            </tr>
        </table>
    </form>
    <table id="table_<?php echo $p_id;?>" style="width:90%;margin-left:5%" >
        <tr>
            <td id="new_formula_id_bt" class="tool" style="background:red">
                <a class="mtitle"  href="javascript:void(0)"  onclick="show_listing_formula('new_formula_id')">
                    Formule
                </a>
            </td>
            <td id="new_compute_id_bt"  class="tool" style="background:inherit">
                <a class="mtitle" href="javascript:void(0)"
                   onclick="show_listing_formula('new_compute_id')">
                    Compute
                </a>
            </td>

            <td id="new_account_id_bt"  class="tool" style="background:inherit">
                <a class="mtitle" href="javascript:void(0)"   onclick="show_listing_formula('new_account_id')">
                    Poste comptable
                </a>
            </td>
            <td id="new_attribute_id_bt"  class="tool" style="background:inherit">
                <a class="mtitle" href="javascript:void(0)"   onclick="show_listing_formula('new_attribute_id')">
                    Attribut
                </a>
            </td>
        </tr>
    </table>
    <div style="width:90%;height:290px;margin-left:5%">
        <span class="error" id="info_listing_param_input_div_id"></span>

        <div style="padding: 10px">
            <div id="new_formula_id" style="display:block">
                <p>
                    Entrer une formule avec des postes comptables, la syntaxe est la même que celle des "rapports"
                </p>
                <p>
                    Exemple : [70%]*0.25+[71%]
                </p>

                <form id="new_padef" method="POST" onsubmit="save_param_listing('new_padef');
                        return false">
                          <?php echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'p_id')) ?>
                    <p>
                        <?php echo HtmlInput::hidden('tab', 'formula') ?>
                        <?php echo "formula"; ?>
                    </p>

                    <?php echo HtmlInput::submit('save', 'Sauve') ?>

                </form>
            </div>
            <div id="new_compute_id" style="display:none">
                <p>
                    Entrer une formule avec des codes utilisés dans ce formulaires
                </p>
                <form id="new_padec" method="POST" onsubmit="save_param_listing('new_padec');
                        return false">
                          <?php echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'p_id')) ?>

                    <?php echo HtmlInput::hidden('tab', 'compute_id') ?>
                    <?php echo "compute" ?>
                    <p>
                        <?php echo HtmlInput::submit('save', 'Sauve') ?>
                    </p>

                </form>
            </div>
            <div id="new_account_id" style="display:none">
                <form id="new_paded" method="POST" onsubmit="save_param_listing('new_paded');
                        return false">

                    <?php echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'p_id')) ?>

                    <?php echo HtmlInput::hidden('tab', 'new_account_id') ?>
                    <?php echo "account" ?>

                    <p>
                        <?php echo HtmlInput::submit('save', 'Sauve') ?>
                    </p>

                </form>
            </div>
            <div id="new_attribute_id" style="display:none">
                <form id="new_padeattr" method="POST" onsubmit="save_param_listing('new_padeattr');
                        return false">

                    <?php echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code', 'p_id')) ?>

                    <?php echo HtmlInput::hidden('tab', 'new_attribute_id') ?>
                    <?php echo $attribute->input(); ?>

                    <p>
                        <?php echo HtmlInput::submit('save', 'Sauve') ?>
                    </p>

                </form>
            </div>
        </div>
    </div>
</div>