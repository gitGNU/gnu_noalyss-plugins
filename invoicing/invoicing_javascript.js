/* This file is part of NOALYSS and is under GPL see licence.txt */
content[1000]='Si le titre n\'est pas donnée, le sujet sera utilisé comme titre du document';
/**
 *javascript
 */
function add_material(dossier_id,plugin_code,target)
{
    var qs="?gDossier="+dossier_id+'&plugin_code='+plugin_code+'&op=add_mat&t='+target;
    
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				);
    if ( $(target)) 
    {
	removeDiv(target);
    }

    var sx=0;
    if ( window.scrollY)
    {
            sx=window.scrollY+120;
    }
     else
     {
            sx=document.body.scrollTop+120;
     }
    var str_style="top:"+sx+";width:50%;height:70%";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading()};
    
    add_div(div);

}
function display_material(dossier_id,f_id,plugin_code,target)
{
    var qs="?gDossier="+dossier_id+'&plugin_code='+plugin_code+'&op=display_modify&t='+target+'&f='+f_id;
    
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				);
    if ( $(target)) 
    {
	removeDiv(target);
    }

    var sx=0;
    if ( window.scrollY)
    {
            sx=window.scrollY+120;
    }
     else
     {
            sx=document.body.scrollTop+120;
     }
    var str_style="top:"+sx+";width:50%;height:auto";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading()};
    
    add_div(div);

}
function success_add_material(req)
{
    fill_box(req);

}
function error_ajax() {
    alert_box('Erreur ajax AMORTIS');
}

/**
*Answer to a post (or get) in ajax
*/
function save_new_material(obj)
{

    var querystring="?"+$(obj).serialize()+'&op=save_new_material&t=bxmat';

    // Create a ajax request to get all the person
    var action = new Ajax.Request ('ajax.php',
				   {
				       method: 'post',
				       parameters: querystring,
				       onFailure: error_ajax,
				       onSuccess: success_save_new_material
				   }
                                  );

    return false;   
}

function success_save_new_material(req)
{
    fill_box(req);
    $('bxmat').style.height='auto';
}
function save_modify(obj)
{
     var querystring="?"+$(obj).serialize()+'&op=save_modify&t=bxmat';

    // Create a ajax request to get all the person
    var action = new Ajax.Request ('ajax.php',
				   {
				       method: 'post',
				       parameters: querystring,
				       onFailure: error_ajax,
				       onSuccess: success_save_modify
				   }
                                  );

    return false;   
   
}
function success_save_modify(req)
{
    fill_box(req);

}
function remove_mat(g_dossier,plugin_code,a_id)
{
    if ( ! confirm('Vous confirmez EFFACEMENT')) { return false;}
    var qs="?gDossier="+g_dossier+"&plugin_code="+plugin_code+"&a_id="+a_id+"&op=rm&t=bxmat";     
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_ajax,
				      onSuccess:success_add_material
				  }
				);
 

   
}
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
