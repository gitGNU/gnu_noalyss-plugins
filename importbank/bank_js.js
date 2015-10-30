/* This file is part of NOALYSS and is under GPL see licence.txt */
function reconcilie(target,dossier_id,p_id,plugin_code,tiers)
{
    var qs="gDossier="+dossier_id+'&plugin_code='+plugin_code+'&act=show&id='+p_id+'&ctl='+target+"&target="+tiers;

    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_box,
				      onSuccess:success_box
				  }
				);
    if ( $(target))
    {
	removeDiv(target);
    }
    var sx=calcy(120);
    
    var str_style="top:"+sx+"px;";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading(),drag:1};

    add_div(div);
}

function save_bank_info(obj)
{
    var query_string=obj.serialize();
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:query_string,
				      onFailure:error_box,
				      onSuccess:success_bank_info
				  });

    return false;
}

function success_bank_info(req,json)
{
    try {
	var answer=req.responseXML;
	var a=answer.getElementsByTagName('extra');
	var name_ctl=a[0].firstChild.nodeValue;
	var ob=name_ctl.evalJSON(true);
	$('st'+ob.id).innerHTML=unescape_xml(ob.msg);
	var div=answer.getElementsByTagName('ctl');
	var div_ctl=div[0].firstChild.nodeValue;
	removeDiv(div_ctl);
    }
    catch(e) {
	alert_box('Erreur success_box_info '+e.message);
    }
}
/**
 * @brief if a row is checked or unchecked, save it into the table temp_bank.is_checked
 * @param p_dossier
 * @param p_plugin_code
 * @param p_row_id temp_bank.id
 */
function impb_check_item(p_dossier,p_plugin_code,p_row_id)
{
    var name_checkbox='temp_bank';
    console.debug($(name_checkbox+p_row_id).checked)
    var status=($(name_checkbox+p_row_id).checked)?1:0;
    console.debug(status);
    waiting_box();
    new Ajax.Request('ajax.php', {
        method:'get',
        parameters : {'gDossier':p_dossier,
                      'plugin_code':p_plugin_code,
                      'act' : 'save_check',
                      'row_id':p_row_id,
                      'state':status
                      
                    },
       onSuccess:function(req) {
            remove_waiting_box();
        }
                    
       });
}
/**
 * Select the same tiers for several rows. The information 
 * import_id .. are in the form
 * @param {type} p_form_id string id of the form
 * @returns {undefined}
 */
function selected_set_tiers(p_form_id) {
try {
console.debug($(p_form_id).serialize());
        smoke.confirm('Confirmez',
                function(e) { 
                    if (e) {
                        console.debug('confirm');
                        $(p_form_id).submit();
                        new Ajax.Request('ajax.php',
                            {
                            method:'get',
                                    parameters: {
                                    'gDossier':$(p_form_id)['gDossier'].value,
                                            'act':'set_tiers',
                                            'import_id':$(p_form_id)['import_id'].value,
                                            'plugin_code':$(p_form_id)['plugin_code'].value,
                                            'ac':$(p_form_id)['ac'].value,
                                            'fiche':$(p_form_id)['fiche1000'].value
                                    }
                        });
                    } else {
                        console.debug('Cancel');
                    }
                });
} catch (e) {
alert(e.message);
}
return false;
        }