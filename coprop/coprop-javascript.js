/**
 *javascript
 */

/**
 * Modifier un copropriétaire et les lots qu'il a
 */
function mod_coprop(dossier,coprop_id,plugin_code,ac)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&coprop_id="+coprop_id+'&ac='+ac+"&act=modcopro";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$('listcoprolot').hide();
					$('ajoutcopro').hide();
					$('divcopropmod').innerHTML=response.responseText;
					//response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
function remove_lot(plugin_code,ac,dossier,lot_id)
{
	if (! confirm("Vous voulez enlever ce lot ?")) { return;}
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&lot_id="+lot_id+'&ac='+ac+"&act=removelot";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					alert("lot_id="+lot_id);
					$("row"+lot_id).style.color="red";
					$("row"+lot_id).style.textDecoration="line-through";
					$("col"+lot_id).innerHTML="Enlevé";

					//response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * Ajout un lien entre copropriétaire et lot
 */
function add_coprop()
{
	try
	{
		$('listcoprolot').hide();
		$('ajoutcopro').show();
	}
	catch(e)
	{
		alert(e.message);
	}
}
function copro_show_list()
{
	try
	{
		$('listcoprolot').show();
		$('ajoutcopro').hide();
	}
	catch(e)
	{
		alert(e.message);
	}

}
/**
 * Ajout clef + tantième lot associés
 */
function add_key(dossier,plugin_code,ac)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+'&ac='+ac+"&act=addkey";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$('keydetail_div').innerHTML=response.responseText;
					response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * Modifie clef + tantième lot associés
 */
function mod_key(dossier,plugin_code,ac,key_id)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&key_id="+key_id+'&ac='+ac+"&act=modkey";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$('keydetail_div').innerHTML=response.responseText;
					response.responseText.evalScripts();

				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
function del_key(plugin_code,ac,dossier,lot_id)
{

}