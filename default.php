<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['ExtraDiscussionData'] = array(
   'Name' => 'Extra Discussion Data',
   'Description' => 'Allows an additional piece of enumerable data on your discussions, for example ratings.',
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.1'),
   'Author' => "Jason Barnabe",
   'AuthorEmail' => 'jason.barnabe@gmail.com',
   'MobileFriendly' => TRUE
);

require_once dirname(__FILE__).'/config.php';

class ExtraDiscussionDataPlugin extends Gdn_Plugin {

	public $Config = null;

	public function __construct() {
		parent::__construct();
		$this->Config = ExtraDiscussionDataPluginConfig();
	}

	# Include the options on the form
	public function PostController_DiscussionFormOptions_Handler($Sender) {
		$ShowWhen = $this->Config['ShowFormWhen'];
		if ($ShowWhen($Sender)) {
			$FormArray = [];
			foreach ($this->Config['Values'] as $Id => $Options) {
				$FormArray[$Id] = $Options['form_markup'];
			}
			$Sender->EventArguments['Options'] .= '<label>'.$this->Config['Label'].'</label>';
			$Sender->EventArguments['Options'] .= $Sender->Form->RadioList($this->Config['ColumnName'], $FormArray);
		}
	}

	# Individual discussion
	public function DiscussionController_AfterDiscussionTitle_Handler($Sender) {
		$Discussion = $Sender->EventArguments['Discussion'];
		echo $this->Config['Values'][$Discussion->{$this->Config['ColumnName']}]['show_markup'];
	}

	# Discussion list
	public function DiscussionsController_AfterDiscussionTitle_Handler($Sender) {
		$this->DiscussionController_AfterDiscussionTitle_Handler($Sender);
	}

	# Do it in the category discussion list too
	public function CategoriesController_AfterDiscussionTitle_Handler($Sender) {
		$this->DiscussionsController_AfterDiscussionTitle_Handler($Sender);
	}

	# And the profile discussion list
	public function ProfileController_AfterDiscussionTitle_Handler($Sender) {
		$this->DiscussionsController_AfterDiscussionTitle_Handler($Sender);
	}

}
