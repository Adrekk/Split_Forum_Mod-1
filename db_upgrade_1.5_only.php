<?php
global $db_prefix, $smcFunc, $sourcedir, $subforum_tree;
global $boardurl, $cookiename, $mbname, $language, $boarddir;

$SSI_INSTALL = false;
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$SSI_INSTALL = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
db_extend('packages');

// Figure out where the attachment path(s) are:
$tblchk = $smcFunc['db_query']('', 'show tables like "%sp_blocks"', array());
while ($row = $smcFunc['db_fetch_row']($tblchk))
{
	if (str_replace('sp_blocks', '', $row[0]) <> $db_prefix)
		continue;
	
	// Insert forumid column into categories table to associate each category with a particular forum:
	$smcFunc['db_add_column']('{db_prefix}sp_blocks', array('name' => 'forums', 'type' => 'varchar', 'size' => 64));
	
	$forums = array(0);
	if (is_array($subforum_tree))
	{
		foreach ($subforum_tree as $id => $subforum)
			$forums[] = $id;
	}

	$request = $smcFunc['db_query']('','
		UPDATE {db_prefix}sp_blocks SET forums = {raw:forums}',
		array(
			'forums' => implode(',', array_unique($forums)),
		)
	);
	break;
}
$smcFunc['db_free_result']($tblchk);

// Echo that we are done if necessary:
if ($SSI_INSTALL)
	echo 'DB Changes should be made now...';
?>