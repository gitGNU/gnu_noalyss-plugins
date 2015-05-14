//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 *javascript
 */

/**
 * @deprecated Not used
 * @param {type} p_ctl : input text 
 * @param {type} p_dossier : dossier
 * @param p_plugin_code
 * @param p_ac
 * @call : ajax.php
 * @returns {undefined} 
 */
function fill_customer(p_ctl, p_dossier, p_plugin_code, p_ac)
{
    try
    {
        $(p_ctl).value = $(p_ctl).value.toUpperCase();
        var queryString = {plugin_code: p_plugin_code, gDossier: p_dossier, ac: p_ac, ctl: $(p_ctl).value, act: 'fill_customer'};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function (req)
                    {
                        alert('ici');
                    }
                }

        );
    } catch (e) {
        remove_waiting_box();
        error_message(e.message);
    }
}
/**
 * Remove all the details from the customer
 * @param p_ctl
 */
function empty_customer(p_ctl)
{
    try
    {

    } catch (e) {
        alert(e.message);
    }

}
/**
 * Add a spare part to a RepairCard
 * @param {type} p_dossier
 * @param {type} p_access
 * @param {type} p_plugin_code
 * @param {type} p_repair_card
 * @returns {undefined}
 */
function spare_part_add(p_dossier, p_access, p_plugin_code, p_repair_card)
{
    try {
        waiting_box();
        var material = $('spare_part_id').value;
        var quantity = $('spare_part_quant').value;
        var queryString = {plugin_code: p_plugin_code, gDossier: p_dossier, ac: p_access, act: 'spare_part_add', repair: p_repair_card, qcode: material, quant: quantity};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function (req)
                    {
                        var answer = req.responseXML;
                        console.log(answer);
                        var html = answer.getElementsByTagName('html');
                        if (html.length === 0)
                        {
                            var rec = req.responseText;
                            alert('erreur :' + rec);
                        }
                        
                        var nodeXml = html[0];
                        var code_html = getNodeText(nodeXml);
                        code_html = unescape_xml(code_html);
                        
                        var xml_spare_id=answer.getElementsByTagName('id')[0];
                        var spare_id=getNodeText(xml_spare_id);
                        var xml_error=getNodeText(answer.getElementsByTagName('error')[0]);
                        if ( xml_error == 1001 ) { 
                            remove_waiting_box();
                            alert('Invalide');
                        }
                        else {
                            var table_len=$('spare_part_table_list_id').rows.length-1;
                            var new_row=$('spare_part_table_list_id').insertRow(table_len);
                            new_row.id='spare_part'+spare_id;
                            new_row.innerHTML=code_html;
                            remove_waiting_box();
                             $('spare_part_id').value="";
                             $('spare_part_quant').value="";
                        }
                    }
                });
    } catch (e) {
        remove_waiting_box();
        error_message("spare_part" + e.message);
    }
}
/**
 * Remove a spare_part from a repair card
 * @param {type} p_dossier
 * @param {type} p_access
 * @param {type} p_plugin_code
 * @param {type} p_spare_part_id
 * @returns {undefined}
 */
function spare_part_remove(p_dossier, p_access, p_plugin_code, p_spare_part_id)
{
    try {
        if ( ! confirm('Effacer ?') ) { return; }
        waiting_box();
        var queryString = {plugin_code: p_plugin_code, gDossier: p_dossier, ac: p_access, act: 'spare_part_remove', spare_part_id: p_spare_part_id};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function (req)
                    {
                        $('spare_part' + p_spare_part_id).hide();
                        remove_waiting_box();
                    }
                });
    } catch (e) {
        remove_waiting_box();
        error_message("spare_part" + e.message);
    }
}
/**
 * Add a spare part to a RepairCard
 * @param {type} p_dossier
 * @param {type} p_access
 * @param {type} p_plugin_code
 * @param {type} p_repair_card
 * @returns {undefined}
 */
function workhour_add(p_dossier, p_access, p_plugin_code, p_repair_card)
{
    try {
        waiting_box();
        var hour = $('workhour_quant').value;
        var description = $('workhour_description').value;
        var queryString = {plugin_code: p_plugin_code, gDossier: p_dossier, ac: p_access, act: 'workhour_add', repair: p_repair_card, hour: hour, description: description};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function (req)
                    {
                        var answer = req.responseXML;
                        console.log(answer);
                        var html = answer.getElementsByTagName('html');
                        if (html.length === 0)
                        {
                            var rec = req.responseText;
                            alert('erreur :' + rec);
                        }
                        
                        var nodeXml = html[0];
                        var code_html = getNodeText(nodeXml);
                        code_html = unescape_xml(code_html);
                        
                        var xml_spare_id=answer.getElementsByTagName('id')[0];
                        var spare_id=getNodeText(xml_spare_id);
                        var xml_error=getNodeText(answer.getElementsByTagName('error')[0]);
                        if ( xml_error == 1001 ) { 
                            remove_waiting_box();
                            alert('Invalide');
                        }
                        else {
                            var table_len=$('workhour_table_id').rows.length-1;
                            var new_row=$('workhour_table_id').insertRow(table_len);
                            new_row.id='workhour'+spare_id;
                            new_row.innerHTML=code_html;
                            remove_waiting_box();
                            $('workhour_quant').value='';
                            $('workhour_description').value='';
                        }
                    }
                });
    } catch (e) {
        remove_waiting_box();
        error_message("spare_part" + e.message);
    }
}
/**
 * Remove workhour from a repair card
 * @param {type} p_dossier
 * @param {type} p_access
 * @param {type} p_plugin_code
 * @param {type} p_workhour_id
 * @returns {undefined}
 */
function workhour_remove(p_dossier, p_access, p_plugin_code, p_workhour_id)
{
    try {
        if ( ! confirm('Effacer ?') ) { return; }
        waiting_box();
        var queryString = {plugin_code: p_plugin_code, gDossier: p_dossier, ac: p_access, act: 'workhour_remove', workhour_id: p_workhour_id};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function (req)
                    {
                        $('workhour' + p_workhour_id).hide();
                        remove_waiting_box();
                    }
                });
    } catch (e) {
        remove_waiting_box();
        error_message("workhour " + e.message);
    }
}
/**
 * Create an invoice and show a button to download it
 * @param {type} p_dossier
 * @param {type} p_plugin_code
 * @param {type} p_ac
 * @param {type} p_repair_id
 * @returns {undefined}
 */
function sav_prepare_invoice(p_dossier,p_plugin_code,p_ac,p_repair_id)
{
    
    try {
        waiting_box();
        var update=new Ajax.Updater($('invoice_div_id'),'ajax.php',
        {
            method:'get',
            parameters:{ gDossier:p_dossier,ac:p_ac,plugin_code:p_plugin_code,repair_id:p_repair_id,act:'prepare_invoice'}
        });
        remove_waiting_box();
    } catch (e)
    {
        remove_waiting_box();
        error_message(e.message)
    }
}