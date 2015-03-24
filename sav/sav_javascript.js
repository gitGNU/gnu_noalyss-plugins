//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 *javascript
 */

/**
 * 
 * @param {type} p_ctl : input text 
 * @param {type} p_dossier : dossier
 * @param p_plugin_code
 * @param p_ac
 * @call : ajax.php
 * @returns {undefined} 
 */
function fill_customer(p_ctl,p_dossier,p_plugin_code,p_ac)
{
    try
    {
        $(p_ctl).value=$(p_ctl).value.toUpperCase();
        var queryString={plugin_code:p_plugin_code,gDossier:p_dossier,ac:p_ac,ctl:$(p_ctl).value,act:'fill_customer'};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function(req)
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
        
    }catch (e) {
         alert(e.message);
    }
    
}
function spare_part_add(p_dossier,p_access,p_plugin_code,p_repair_card)
{
    try {
        var material=$('spare_part_id').value;
        var quantity=$('spare_part_quant').value;
        var queryString={plugin_code:p_plugin_code,gDossier:p_dossier,ac:p_access,act:'spare_part_add',repair:p_repair_card,qcode:material,quant:quantity};
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onSuccess: function(req)
                    {
                      
                    }
                });
    } catch (e) {
        alert("spare_part"+e.message);
    }
}