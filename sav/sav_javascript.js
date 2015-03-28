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
        alert(e.message);
        alert(p_ctl);
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
                        
                        var table_len=$('spare_part_table_list_id').rows.length-1;
                        var new_row=$('spare_part_table_list_id').insertRow(table_len);
                        new_row.id='spare_part'+spare_id;
                        new_row.innerHTML=code_html;
                        remove_waiting_box();
                    }
                });
    } catch (e) {
        alert("spare_part" + e.message);
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
        alert("spare_part" + e.message);
    }
}