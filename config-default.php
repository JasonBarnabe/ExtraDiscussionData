<?php if (!defined('APPLICATION')) exit();

// Copy this file to config.php and customize!
function ExtraDiscussionDataPluginConfig() {
	$Config = [];

	// Name of the column to use in GDN_Discussion
	$Config['ColumnName'] = 'Grade';

	// When creating or editing discussions, should the form to set extra data show? It will when this function returns true.
	$Config['ShowFormWhen'] = function($Sender) {
		// Some logic based on $Sender->Discussion here
		return true;
	};

	$Config['Label'] = 'Grade';

	// Markup to show on add/edit and show pages. Key is the value stored in the DB, value is an hash containing form_markup and show_markup. form_markup is added after the input tag when adding or editing a discussion. show_markup shows after the discussion name in the discussion list and discussion page.
	$Config['Values'] = [
		'' => [
			'form_markup' => 'No Grade',
			'show_markup' => ''
		],
		'A' => [
			'form_markup' => 'A - Excellent!',
			'show_markup' => 'Grade: A'
		],
		'B' => [
			'form_markup' => 'B - OK!',
			'show_markup' => 'Grade: B'
		]
	];

	return $Config;
}

?>
