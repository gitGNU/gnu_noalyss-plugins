/* This file is part of NOALYSS and is under GPL see licence.txt */
content[1000]='Si le titre n\'est pas donnée, le sujet sera utilisé comme titre du document';
/**
 *javascript
 */

/**
 * Display list of template of messages
 * @param {type} p_dossier
 * @param {type} p_ac
 * @param {type} p_plugin
 * @returns {undefined}
 */
function inv_get_message(p_dossier,p_ac,p_plugin) {
    waiting_box();
    new Ajax.Request ( 'ajax.php',{ 
        method:'get',
        parameters:{'gDossier':p_dossier,'plugin_code':p_plugin,'ac':p_ac,'act':'inv_get_message'},
        onSuccess : function (req) {
            remove_waiting_box();
            add_div({'id':'display_message_box','style':'position:absolute;top:220px;z-index:5','cssclass':'inner_box','html':req.responseText});
        }
    })
}
/**
 * Select the message and fill the form
 * @param {type} p_dossier
 * @param {type} p_ac
 * @param {type} p_plugin
 * @param {type} mr_id
 * @returns {undefined}
 */
function inv_select_message(p_dossier,p_ac,p_plugin,mr_id) {
    waiting_box();
    new Ajax.Request('ajax.php', {
        method:'get',
        parameters:{'gDossier':p_dossier,'plugin_code':p_plugin,'ac':p_ac,'act':'inv_select_message','mr_id':mr_id},
        onSuccess : function (req) {
            var xml=req.responseXML;
            
            $('email_from').value=getNodeText(xml.getElementsByTagName("sender")[0]);
            $('email_subject').value=getNodeText(xml.getElementsByTagName("subject")[0]);
            $('email_message').value=getNodeText(xml.getElementsByTagName("message")[0]);
            
            removeDiv("display_message_box");
            
            remove_waiting_box();
            
        }
    })
}
/**
 * Display the detail of a message
 * @param {type} p_dossier
 * @param {type} p_ac
 * @param {type} p_plugin
 * @param {type} mr_id
 */
function inv_display_message(p_dossier,p_ac,p_plugin,mr_id) {
    waiting_box();
    new Ajax.Request('ajax.php',
    {
        method:'get',
        parameters:{'gDossier':p_dossier,'plugin_code':p_plugin,'ac':p_ac,'act':'inv_display_message','mr_id':mr_id},
        onSuccess:function (req) {
            add_div({'id':'detail_message_box','style':'position:absolute;top:30%;z-index:8','cssclass':'inner_box','html':req.responseText,'drag':1});
            remove_waiting_box();
        }
    });
}

function inv_delete_message(p_dossier,p_ac,p_plugin,mr_id) {
    smoke.confirm ("Confirmer",function (e) {
    if ( e ) {
        waiting_box();
    
        new Ajax.Request('ajax.php',
        {
            method:'get',
            parameters:{'gDossier':p_dossier,'plugin_code':p_plugin,'ac':p_ac,'act':'inv_delete_message','mr_id':mr_id},
            onSuccess:function (req) {
                if ( $("tr_message"+mr_id) ) { $("tr_message"+mr_id).remove();}
                removeDiv("detail_message_box");
                remove_waiting_box();
            }
        });
        } 
    });
}
