/*
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
					$("row"+lot_id).style.color="red";
					$("row"+lot_id).style.textDecoration="line-through";
                                        $("col"+lot_id).style.textDecoration="none";
					$("col"+lot_id).innerHTML="Effacé";

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
    $('key_list').hide();
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
					$('keydetail_div').show();
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
    $('key_list').hide();
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
					$('keydetail_div').show();
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

function remove_key(plugin_code,ac,dossier,key_id)
{
	if (! confirm("Vous voulez effacer cette clef ?")) { return;}
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&key_id="+key_id+'&ac='+ac+"&act=removekey";
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
					$("row"+key_id).style.color="red";
					$("row"+key_id).style.textDecoration="line-through";
					$("col"+key_id).innerHTML="";

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
function compute_key()
{
	try
	{
		str="";
		var array=$("fkey").getInputs('text');
		var tot=0;
		for (i=0;i<array.length;i++)
			{
				if ( array[i].name.search(/part/) > -1)
				{
					if (! isNaN(array[i].value)) {
						tot+=parseFloat(array[i].value);
					}
				}
			}
		$("span_tantieme").innerHTML=Math.round(tot);
		if ( ! isNaN($('cr_tantieme').value)) {
			var difference=parseFloat($('cr_tantieme').value)-tot;
			difference=Math.round(difference*100)/100;
			if ( difference != 0 )	{
					$('span_diff').style.backgroundColor="red";
				} else {
					$('span_diff').style.backgroundColor="green";
				}
			$('span_diff').innerHTML=difference;
		}

	}
	catch(e)
	{
		alert(e.message);
	}
}

function budget_detail(plugin_code,ac,dossier,bud_id)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&bud_id="+bud_id+'&ac='+ac+"&act=buddisplay";
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
                                    $('divbuddetail').innerHTML=response.responseText;
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
function compute_budget()
{
	try
	{
		str="";
		var array=$("fbud_update").getInputs('text');
		var tot=0;
		for (i=0;i<array.length;i++)
			{
				if ( array[i].name.search(/bt_amount/) > -1)
				{
					if (! isNaN(array[i].value) && array[i].value!= "") {
						tot+=parseFloat(array[i].value);
					}
				}
			}
		$("sbud_total").innerHTML=Math.round(tot*100)/100;
		if ( ! isNaN($('b_amount').value)) {
			var difference=parseFloat($('b_amount').value)-tot;
			difference=Math.round(difference*100)/100;
			if ( difference != 0 )	{
					$('span_diff').style.backgroundColor="red";
				} else {
					$('span_diff').style.backgroundColor="green";
				}
			$('span_diff').innerHTML=difference;
		}

	}
	catch(e)
	{
		alert(e.message);
	}
}
function budget_add(dossier,plugin_code,ac)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&bud_id=0&ac="+ac+"&act=budadd";
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
                                    $('divbuddetail').innerHTML=response.responseText;
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
function budget_remove(plugin_code,ac,dossier,bud_id)
{
	if (! confirm("Vous voulez effacer ce budget?")) { return;}
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&bud_id="+bud_id+'&ac='+ac+"&act=removebudget";
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
					$("row"+bud_id).style.color="red";
					$("row"+bud_id).style.textDecoration="line-through";
					$("col2"+bud_id).innerHTML="";
					$("col1"+bud_id).innerHTML="";

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

function appel_fond_show()
{
    try
    {
        var aft=$('aft');
        var choice=aft.options[aft.selectedIndex].value;
        if ( choice == -1 )
        {
            $('appel_fond_budget').hide();
            $('appel_fond_amount').hide();
            return;
        }
        if ( choice == 1 )
        {
            $('appel_fond_budget').show();
            $('appel_fond_amount').hide();
            return;
        }
        if ( choice == 2 )
        {
            $('appel_fond_budget').hide();
            $('appel_fond_amount').show();
            return;
        }
    } catch(e)
    {
        alert(e.message);
    }

}