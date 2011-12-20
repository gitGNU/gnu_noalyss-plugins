/**
 *javascript
 */

/**
 * Modifier un copropriétaire et les lots qu'il a
 */
function mod_coprop(dossier,copro_qcode,plugin_code)
{
	try
	{
		alert('Modifier un copropriétaire et ses lots');
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
function add_key()
{
	try
	{
		alert('Ajout clef + tantième lot associés');
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * Modifie clef + tantième lot associés
 */
function mod_key()
{
	try
	{
		alert('Modifie clef + tantième lot associés');
	}
	catch(e)
	{
		alert(e.message);
	}
}