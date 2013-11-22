<div>
    <table>
        <tr>
            <td>
                <label>Code</label> 
            </td>
            <td>
                <?php echo $code->input() ?>
            </tD>
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
                <label>Order apparition</label>
            </td>
            <td>
                <?php echo $order->input(); ?>
            </td>
        </tr>
    </table>
    <table style="width:90%;margin-left:5%" >
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
        </tr>
    </table>
    <div style="width:100%;height:290px;margin:1px">
        <span class="error" id="param_detail_info_div"></span>

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

                    <?php echo HtmlInput::submit('save', 'Sauve') ?>

                </form>
            </div>
        </div>
    </div>
</div>