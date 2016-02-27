/* This file is part of NOALYSS and is under GPL see licence.txt */


content[200] = "Le code doit être unique pour ce formulaire";
content[201] = "Formula TODO";
content[203] = "Utiliser le % pour indiquer les postes comptables qui en dépendent ex: 70% pour reprendre tous les comptes commençant par 70";
content[204] = "Filtrer en donnant une date, partie de la description ou de la déclaration";

/**
 *@brief show the definition of a form and let it modify it
 *@param plugin_code code of plugin
 *@param ac code AD
 *@param dossier gDossier
 *@param f_id pk of form_def
 */
function rapav_form_def(plugin_code, ac, dossier, f_id)
{
    try
    {
        waiting_box();
       if ( $('form_list_div') ) $('form_list_div').hide();
        $('form_mod_div').innerHTML = "";
        $('form_mod_div').show();
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=mod_form' + "&f_id=" + f_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        var answer = req.responseText;
                        $('form_mod_div').innerHTML = answer;
                        answer.evalScripts()
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
/**
 * @brief Add a row to the table in formulaire_definition
 * table id = table_formulaire_definition_id
 */
function add_row_definition(plugin_code, ac, dossier)
{
    try
    {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=add_row_definition';
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        var answer = req.responseText;
                        var mytable = g("table_formulaire_definition_id").tBodies[0];
                        var nNumberRow = mytable.rows.length;
                        var oRow = mytable.insertRow(nNumberRow);
                        oRow.innerHTML = answer;
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);
    }
}

/**
 * @brief Add a row to the table in formulaire_parametre
 * table id = table p_id
 */
function add_param_detail(plugin_code, ac, dossier, p_id)
{
    try
    {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=add_param_detail' + "&p_id=" + p_id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        removeDiv('param_detail_div');
                        var nTop = calcy(90);
                        var nLeft = "200px";
                        var str_style = "top:" + nTop + "px;left:" + nLeft + ";width:70em;height:40em";
                        add_div({
                            id: 'param_detail_div',
                            style: str_style,
                            cssclass: 'inner_box',
                            drag: 1
                        });
                        $('param_detail_div').innerHTML = req.responseText;
                        req.responseText.evalScripts();
                    }
                }
        );

    } catch (e)
    {
        alert_box("add_param_detail" + e.message);
    }
}
function modify_param_detail(plugin_code, ac, dossier, fp_id)
{
    try
    {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=modify_param_detail' + "&fp_id=" + fp_id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        removeDiv('param_detail_div');
                        var nTop = calcy(90);
                        var nLeft = "200px";
                        var str_style = "top:" + nTop + "px;left:" + nLeft + ";width:70em;height:auto";
                        add_div({
                            id: 'param_detail_div',
                            style: str_style,
                            cssclass: 'inner_box',
                            drag: 1
                        });
                        $('param_detail_div').innerHTML = req.responseText;
                        req.responseText.evalScripts();
                    }
                }
        );

    } catch (e)
    {
        alert_box("add_param_detail" + e.message);
    }
}
/**
 *@brief display a popup and let you select an existing code
 */
function rapav_search_code(ac, plugin_code, dossier, f_id)
{
    try
    {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_search_code' + "&f_id=" + f_id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        removeDiv('search_code_div');
                        var nTop = calcy(80);
                        var nLeft = "400px";
                        var str_style = "top:" + nTop + "px;left:" + nLeft + ";width:50em;height:auto;z-index:4";
                        add_div({
                            id: 'search_code_div',
                            style: str_style,
                            cssclass: 'inner_box',
                            drag: 1
                        });
                        $('search_code_div').innerHTML = req.responseText;
                        //req.responseText.evalScripts();
                    }
                }
        );

    } catch (e)
    {
        alert_box("add_param_detail" + e.message);
    }
}
/**
 *@brief delete a parameter detail
 */
function delete_param_detail(plugin_code, ac, dossier, fp_id,p_id,child)
{
    try
    {
        smoke.confirm("Confirmez-vous l'effacement ?",function (event)
        {
            if (event) 
            {
                waiting_box();

                var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=delete_param_detail' + "&fp_id=" + fp_id;
                var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        $('tr_' + fp_id).style.textDecoration = "line-through";
                        $('tr_' + fp_id).style.color = "red";
                        $('del_' + fp_id).innerHTML = "";
                        $('mod_' + fp_id).innerHTML = "";
                        if (child == 6) {
                            $('add_row'+p_id).show();
                        }
                    }
                }
                    );
            }   
        });
    } catch (e)
    {
        alert_box(e);
    }
      
}
/**
 * @brief  montre les détails d'un formulaire
 */
function rapav_form_param(plugin_code, ac, dossier, f_id)
{
    try
    {
        waiting_box();
        if ( $('form_list_div') ) $('form_list_div').hide();
        $('form_mod_div').innerHTML = "";
        $('form_mod_div').show();
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=mod_param' + "&f_id=" + f_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        var answer = req.responseText;
                        $('form_mod_div').innerHTML = answer;
                        answer.evalScripts()
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
/**
 * @brief montre le div contenant le type de formule pour les rapports
 */
function show_rapport_formula(p_toshow)
{
    try
    {
        var div = ['new_account_tva_id', 'new_formula_id', 'new_compute_id', 'new_account_id', 'new_reconcile_id'];
        for (var r = 0; r < div.length; r++) {
            $(div[r]).hide();
            $(div[r] + '_bt').style.backgroundColor = "inherit";

        }
        $(p_toshow).show();
        $(p_toshow + '_bt').style.backgroundColor = "red";
    } catch (e)
    {
        alert_box(e.message);
    }

}
/**
 * @brief montre le div contenant le type de formule pour les listings
 */
function show_listing_formula(p_toshow)
{
    try
    {
        var div = ['new_formula_id', 'new_compute_id', 'new_account_id', 'new_attribute_id'];
        for (var r = 0; r < div.length; r++) {
            $(div[r]).hide();
            $(div[r] + '_bt').style.backgroundColor = "inherit";

        }
        $(p_toshow).show();
        $(p_toshow + '_bt').style.backgroundColor = "red";
    } catch (e)
    {
        alert_box(e.message);
    }

}
/**
 * @brief sauve les données pour nouvelle formule, code,...
 */
function save_param_detail(p_form_id)
{
    try
    {
        var qs = $(p_form_id).serialize() + '&act=save_param_detail';
        var child=$(p_form_id)['child'];
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: error_get_predef,
                    onSuccess: function infodiv(req, json) {
                        try {
                            remove_waiting_box();
                            var answer = req.responseXML;
                            var acode = answer.getElementsByTagName('code');
                            var ap_id = answer.getElementsByTagName('p_id');
                            var html = answer.getElementsByTagName('html');

                            if (acode.length == 0) {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var code = acode[0].firstChild.nodeValue;
                            var code_xml = getNodeText(html[0]);
                            var code_html = unescape_xml(code_xml);
                            if (code == 'ok')
                            {
                                var afpid = answer.getElementsByTagName('fp_id');
                                var fp_id = afpid[0].firstChild.nodeValue;
                                var p_id = ap_id[0].firstChild.nodeValue;
                                var update = $('tr_' + fp_id);
                                if (update == undefined)
                                {
                                    // Ajoute une ligne avec résultat
                                    var mytable = g("table_" + p_id).tBodies[0];
                                    var nNumberRow = mytable.rows.length;
                                    var oRow = mytable.insertRow(nNumberRow);
                                    oRow.id = "tr_" + fp_id;
                                    oRow.innerHTML = code_html;
                                } else {
                                    $(update).innerHTML = code_html;
                                }
                                removeDiv('param_detail_div');
                                if ( child) {
                                    $('add_row'+p_id).hide();
                                }
                            }
                            if (code == 'nok')
                            {
                                // montre erreur
                                $('param_detail_info_div').innerHTML = code_html;
                            }
                        }
                        catch (e) {
                            alert_box("save_param_detail " + e.message);
                        }
                        try {
                            code_html.evalScripts();
                        }
                        catch (e) {
                            alert_box("save_param_detail Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
    }

    return false;
}
function rapav_declaration_display(plugin_code, ac, dossier, d_id)
{
    try
    {
        $('declaration_list_div').hide();
        $('declaration_display_div').innerHTML = "";
        $('declaration_display_div').show();
        waiting_box();
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_declaration_display' + "&d_id=" + d_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        var answer = req.responseText;
                        $('declaration_display_div').innerHTML = answer;
                        answer.evalScripts()
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
function rapav_declaration_delete(plugin_code, ac, dossier, d_id)
{
    try
    {
        if (confirm('Confirmez-vous l\'effacement ?') == false) {
            return;
        }
        waiting_box();
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_declaration_delete' + "&d_id=" + d_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        $('tr_' + d_id).style.textDecoration = "line-through";
                        $('tr_' + d_id).style.color = "red";
                        $('del_' + d_id).innerHTML = "";
                        $('mod_' + d_id).innerHTML = "";
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
/**
 * @brief export of a form
 * @param plugin_code code of plugin
 * @param ac code AD
 * @param dossier gDossier
 * @param d_id id
 * @returns {undefined}
 */
function rapav_form_export(plugin_code, ac, dossier, d_id)
{
    try {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_form_export' + "&d_id=" + d_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: null
                }
        );

    } catch (e)
    {
        alert_box(e.message);
    }
}
/**
 * @brief Remove a template
 * @param plugin_code code of plugin
 * @param ac code AD
 * @param dossier gDossier
 * @param f_id pk of form_def
 *
 * @note sprintf("rapav_remove_doc_template('%s','%s','%s','%s')",
 $_REQUEST['plugin_code'],
 $_REQUEST['ac'],
 $_REQUEST['gDossier'],
 $this->f_id
 */
function rapav_remove_doc_template(plugin_code, ac, dossier, f_id)
{
    if (!confirm("Confirmez-vous l'effacement de ce modèle ?"))
    {
        return;
    }
    try {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_remove_doc_template' + "&f_id=" + f_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function() {
                        $('rapav_template').style.textDecoration = 'line-through';
                        $('rapav_template').style.color = 'red';
                        $('rapav_template_ctl').innerHTML = '';
                        $('rapav_new_file').style.display = 'block';
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);
    }
}
/**
 * Receive a json object and display a window to add a new listing
 *    - gDossier is the dossier id
 *    - ac is the AC CODE
 *    - pc is the plugin CODE
 *    - cin the table to be updated after save
 *    - cout the div to display
 *    
 */
function listing_modify(json)
{
    try {
        var querystring = 'plugin_code=' + json.pc + '&ac=' + json.ac + '&gDossier=' + json.gDossier + '&act=listing_modify' + "&cin=" + json.cin + '&cout=' + json.cout + '&id=' + json.id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        try {
                            var answer = req.responseXML;
                            var a = answer.getElementsByTagName('ctl');
                            var html = answer.getElementsByTagName('code');
                            if (a.length == 0) {
                                var rec = req.responseText;
                                throw 'cannot find ctl element';
                            }
                            remove_waiting_box();
                            var code_html = getNodeText(html[0]);
                            code_html = unescape_xml(code_html);
                            var position = fixed_position(35, 217)+";width:auto";
                            add_div({'id': json.cout, 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                            $(json.cout).innerHTML = code_html;
                        } catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);

    }
}
/**
 * @brief Check that the name is not empty before saving a new listing
 * @param {type} form_id
 * @returns {Boolean}
 */
function check_listing_add(form_id)
{
    var str = $(form_id)['name'].value;
    if (str.trim() == '') {
        error_message('Le nom est obligatoire');
        return false;
    }
    return true;
}
/**
 * @brief Remove a document from listing
 * @param {type} json
 *    - gDossier is the dossier id
 *    - ac is the AC CODE
 *    - pc is the plugin CODE
 *    - cin not used
 *    - cout span id where is the name of file 
 */
function listing_remove_modele(json)
{
    if ( ! confirm('Effacer ? ') ) { return; }
    try {
        var querystring = 'plugin_code=' + json.pc + '&ac=' + json.ac + '&gDossier=' + json.gDossier + '&act=listing_remove_modele' + "&cin=" + json.cin + '&cout=' + json.cout + '&id=' + json.id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        try {
                            var answer = req.responseXML;
                            var a = answer.getElementsByTagName('ctl');
                            var html = answer.getElementsByTagName('code');
                            if (a.length == 0) {
                                var rec = req.responseText;
                                throw 'cannot find ctl element';
                            }
                            remove_waiting_box();
                            var code_html = getNodeText(html[0]);
                            code_html = unescape_xml(code_html);
                            $(json.cout).innerHTML = code_html;
                        } catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);

    }
}
/**
 * Show the definition of a list in a div
 * @param json json object 
 * Attributes are 
 *     - pc
 *     - ac
 *     - gDossier
 *     - act
 *     - cin
 *     - cout
 *
 */
function listing_definition(json)
{
    try {
        var querystring = 'plugin_code=' + json.pc + '&ac=' + json.ac + '&gDossier=' + json.gDossier + '&act=listing_display_definition' + "&cin=" + json.cin + '&cout=' + json.cout + '&id=' + json.id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        try {
                            var answer = req.responseXML;
                            var a = answer.getElementsByTagName('ctl');
                            var html = answer.getElementsByTagName('code');
                            if (a.length == 0) {
                                var rec = req.responseText;
                                throw 'cannot find ctl element';
                            }
                            remove_waiting_box();
                            var code_html = getNodeText(html[0]);
                            code_html = unescape_xml(code_html);
                            $(json.cout).innerHTML = code_html;
                        } catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);

    }
}
function listing_detail_modify(json)
{
    try {
        var querystring = 'plugin_code=' + json.pc + '&ac=' + json.ac + '&gDossier=' + json.gDossier + '&act=listing_detail_modify' + "&cin=" + json.cin + '&id=' + json.id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        try {
                            var answer = req.responseXML;
                            var a = answer.getElementsByTagName('ctl');
                            var html = answer.getElementsByTagName('code');
                            if (a.length == 0) {
                                var rec = req.responseText;
                                throw 'cannot find ctl element';
                            }
                            remove_waiting_box();
                            var code_html = getNodeText(html[0]);
                            code_html = unescape_xml(code_html);
                            var position = fixed_position(451, 217) + ';width:50%';
                            add_div({'id': json.cin, 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                            $(json.cin).innerHTML = code_html;

                        } catch (e) {
                            alert_box(e.message);
                        }
                        code_html.evalScripts();
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);

    }
}
/**
 * @brief display a form for adding a parameter to the current listing
 * @param  json
 * Attributes are 
 *     - cin      the div to fill
 *     - tb_id    the table to update
 *     - gDossier dossier 
 *     - ac       the AC code
 *     - pc       plugin_code
 */
function listing_detail_add(json)
{
    try {
        var querystring = 'plugin_code=' + json.pc + '&ac=' + json.ac + '&gDossier=' + json.gDossier + '&act=listing_detail_add' + "&cin=" + json.cin + '&id=' + json.id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        try {
                            var answer = req.responseXML;
                            var a = answer.getElementsByTagName('ctl');
                            var html = answer.getElementsByTagName('code');
                            if (a.length == 0) {
                                var rec = req.responseText;
                                throw 'cannot find ctl element';
                            }
                            remove_waiting_box();
                            var code_html = getNodeText(html[0]);
                            code_html = unescape_xml(code_html);
                            var position = fixed_position(244, 217) + ';width:60%;height:490px';
                            add_div({'id': json.cin, 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                            $(json.cin).innerHTML = code_html;
                            code_html.evalScripts();

                        } catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);

    }
}
/**
 * @brief sauve les données pour nouvelle formule, code,...
 * @param p_form_id id du form
 */
function save_param_listing(p_form_id)
{
    /**
     * code_id can not be empty
     */
    if ($('code_id').value.trim() == '') {
        $('code_id').style.border = "solid 2px red";
        $('code_id_span').innerHTML = ' Code ne peut pas être vide';
        return false;
    }
    try
    {
        var qs = $(p_form_id).serialize() + '&act=save_param_listing&' + $('common_frm').serialize();
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: error_get_predef,
                    onSuccess: function(req, json) {
                        try {
                            remove_waiting_box();
                            var answer = req.responseXML;
                            var acode = answer.getElementsByTagName('code');
                            var ap_id = answer.getElementsByTagName('l_id');
                            var html = answer.getElementsByTagName('html');

                            if (acode.length == 0) {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var code = acode[0].firstChild.nodeValue;
                            var code_xml = getNodeText(html[0]);
                            var code_html = unescape_xml(code_xml);
                            if (code == 'ok')
                            {
                                var afpid = answer.getElementsByTagName('lp_id');
                                var fp_id = afpid[0].firstChild.nodeValue;
                                var p_id = ap_id[0].firstChild.nodeValue;
                                var update = $('tr_' + fp_id);
                                if (update == undefined)
                                {
                                    // Ajoute une ligne avec résultat
                                    var mytable = g("definition_tb_id").tBodies[0];
                                    var nNumberRow = mytable.rows.length;
                                    var oRow = mytable.insertRow(nNumberRow);
                                    oRow.id = "tr_" + fp_id;
                                    oRow.innerHTML = code_html;
                                }
                                else {
                                    update.innerHTML = code_html;
                                }
                                removeDiv('listing_param_input_div_id');

                            }
                            if (code == 'nok')
                            {
                                // montre erreur
                                $('info_listing_param_input_div_id').innerHTML = code_html;
                            }
                        }
                        catch (e) {
                            alert_box("save_param_detail " + e.message);
                            return false;
                        }
                        try {
                            code_html.evalScripts();
                        }
                        catch (e) {
                            alert_box("save_param_detail Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
        return false;
    }

    return false;
}
function listing_detail_remove(dossier, plugin_code, ac, id)
{
    smoke.confirm('Confirmez-vous effacer ce détail ?',function (event) {
        if (event ) {
            try {
        var query = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=listing_detail_remove' + "&id=" + id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: query,
                    onFailure: error_get_predef,
                    onSuccess: function(req, json) {
                        try {
                            remove_waiting_box();
                            var answer = req.responseXML;
                            var acode = answer.getElementsByTagName('code');
                            var lp_id = answer.getElementsByTagName('lp_id');

                            if (acode.length == 0) {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var code = acode[0].firstChild.nodeValue;

                            if (code == 'ok')
                            {
                                var row = $('tr_' + id);
                                row.style.color = "red";
                                row.style.textDecoration = 'line-through';
                                row.cells[row.cells.length - 1].innerHTML = "";
                                row.cells[row.cells.length - 2].innerHTML = "";

                            }
                            if (code == 'nok')
                            {
                                // montre erreur
                                alert_box('effacement non possible');
                            }
                        }
                        catch (e) {
                            alert_box("callback : listing_detail_remove " + e.message);
                        }

                    }
                }
        );
    } catch (e)
    {
        alert_box(e.message);
    }
        }
    });
    
}

/**
 *@brief display a popup and let you select an existing code
 */
function listing_search_code(ac, plugin_code, dossier, f_id)
{
    try
    {
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=listing_search_code' + "&f_id=" + f_id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        removeDiv('search_code_div');
                        var nTop = calcy(80);
                        var nLeft = "400px";
                        var str_style = "top:" + nTop + "px;left:" + nLeft + ";width:50em;height:auto;z-index:4";
                        add_div({
                            id: 'search_code_div',
                            style: str_style,
                            cssclass: 'inner_box',
                            drag: 1
                        });
                        $('search_code_div').innerHTML = req.responseText;
                    }
                }
        );

    } catch (e)
    {
        alert_box("listing_search_code" + e.message);
    }
}
function rapav_listing_display(plugin_code, ac, dossier, d_id)
{
    try
    {
        $('declaration_list_div').hide();
        $('declaration_display_div').innerHTML = "";
        $('declaration_display_div').show();
        waiting_box();
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_listing_display' + "&d_id=" + d_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        var answer = req.responseText;
                        $('declaration_display_div').innerHTML = answer;
                        answer.evalScripts()
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
function rapav_listing_delete(plugin_code, ac, dossier, d_id)
{
    try
    {
        if (confirm('Confirmez-vous l\'effacement ?') == false) {
            return;
        }
        waiting_box();
        var querystring = 'plugin_code=' + plugin_code + '&ac=' + ac + '&gDossier=' + dossier + '&act=rapav_listing_delete' + "&d_id=" + d_id;
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        $('tr_' + d_id).style.textDecoration = "line-through";
                        $('tr_' + d_id).style.color = "red";
                        $('del_' + d_id).innerHTML = "";
                        $('mod_' + d_id).innerHTML = "";
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
        return false;
    }
}
function parameter_send_mail()
{
    try {
        var qs = $('parameter_send_mail_frm').serialize(false);
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: ajax_misc_failure,
                    onSuccess: function(req) {
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        var html = answer.getElementsByTagName('code');
                        if (a.length == 0) {
                            var rec = req.responseText;
                            throw 'cannot find ctl element';
                        }
                        remove_waiting_box();
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        var position = fixed_position(451, 217) + ';width:50%';
                        add_div({'id': 'parameter_send_mail_input', 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                        $('parameter_send_mail_input').innerHTML = code_html;
                        remove_waiting_box();
                    }
                }
        );
        return false;
    } catch (e)
    {
        alert_box(e.message);
        return false;

    }
}
function send_email()
{
    if (trim($('p_from').value) == "") {
        alert_box('champs obligatoire manquant');
        $('p_from').style.border = "solid 2px red";
        return false;
    }
    if (trim($('p_subject').value) == "") {
        alert_box('champs obligatoire manquant');
        $('p_subject').style.border = "solid 2px red";
        return false;
    }
    if (trim($('p_attach').value) == "") {
        alert_box('champs obligatoire manquant');
        $('p_attach').style.border = "solid 2px red";
        return false;
    }
    try {
        var qs = $('parameter_send_email_input_frm').serialize(false);
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: ajax_misc_failure,
                    onSuccess: function(req) {
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        var html = answer.getElementsByTagName('code');
                        if (a.length == 0) {
                            var rec = req.responseText;
                            throw 'cannot find ctl element';
                        }
                        remove_waiting_box();
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        var position = fixed_position(451, 217) + ';width:50%';
                        add_div({'id': 'parameter_send_mail_result', 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                        remove_waiting_box();
                        $('parameter_send_mail_result').innerHTML = code_html;
                        removeDiv('parameter_send_mail_input');
                    }
                }
        );
        return false;
    } catch (e)
    {
        alert_box(e.message);
        return false;

    }
}
function js_include_follow()
{
    try {
        var qs = $('include_follow_frm').serialize(false);
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: ajax_misc_failure,
                    onSuccess: function(req) {
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        var html = answer.getElementsByTagName('code');
                        if (a.length == 0) {
                            var rec = req.responseText;
                            throw 'cannot find ctl element';
                        }
                        remove_waiting_box();
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        var position = fixed_position(451, 217) + ';width:50%';
                        add_div({'id': 'include_follow_result', 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                        $('include_follow_result').innerHTML = code_html;
                        remove_waiting_box();
                    }
                }
        );
        return false;
    } catch (e)
    {
        alert_box(e.message);
        return false;

    }
}
function js_include_follow_save()
{
    try {
        var qs = $('include_follow_save_frm').serialize(false);
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: ajax_misc_failure,
                    onSuccess: function(req) {
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        var html = answer.getElementsByTagName('code');
                        if (a.length == 0) {
                            var rec = req.responseText;
                            throw 'cannot find ctl element';
                        }
                        remove_waiting_box();
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        var position = fixed_position(451, 217) + ';width:50%';
                        add_div({'id': 'include_follow_save_result', 'cssclass': 'inner_box', 'drag': 1, 'style': position});
                        remove_waiting_box();
                        $('include_follow_save_result').innerHTML = code_html;
                        removeDiv('include_follow_result');
                    }
                }
        );
        return false;
    } catch (e)
    {
        alert_box(e.message);
        return false;

    }
}
/**
 * @brief save the description of a listing
 */
 function modify_listing_description(plugin_code, ac, dossier, d_id)
    {

       var obj={'gDossier':dossier
                ,'plugin_code':plugin_code
                ,'ac':ac
                ,'d_id':d_id
                ,'act':'modify_listing_description'
                ,'comment':$('description').value
            };
      waiting_box();
      new Ajax.Request('ajax.php',{
          parameters :obj,
          method:'GET',
           onSuccess:function ()
          {
              remove_waiting_box();
              
          }
      })
    }
/**
 * @brief save the description of a listing
 */
 function modify_rapav_description(plugin_code, ac, dossier, d_id)
    {

       var obj={'gDossier':dossier
                ,'plugin_code':plugin_code
                ,'ac':ac
                ,'d_id':d_id
                ,'act':'modify_rapav_description'
                ,'comment':$('description').value
            };
      waiting_box();
      new Ajax.Request('ajax.php',{
          parameters :obj,
          method:'GET',
          onSuccess:function ()
          {
              remove_waiting_box();
              
          }
      })
    }