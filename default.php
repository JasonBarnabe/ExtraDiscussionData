<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['ExtraDiscussionData'] = array(
   'Name' => 'Extra Discussion Data',
   'Description' => 'Allows an additional piece of enumerable data on your discussions, for example ratings.',
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.1'),
   'Author' => "Jason Barnabe",
   'AuthorEmail' => 'jason.barnabe@gmail.com'
);

require_once dirname(__FILE__).'/config.php';

class ExtraDiscussionDataPlugin extends Gdn_Plugin {

	# Include the options on the form
	public function PostController_DiscussionFormOptions_Handler($Sender) {
		$ShowWhen = $this->GetConfig('ShowFormWhen');
		if ($ShowWhen($Sender)) {
			$FormArray = [];
			foreach ($this->GetConfig('Values') as $Id => $Options) {
				$FormArray[$Id] = $Options['form_markup'];
			}
			$Sender->EventArguments['Options'] .= $Sender->Form->RadioList($this->GetConfig('ColumnName'), $FormArray);
		}
	}

	# Individual discussion
	public function DiscussionController_AfterDiscussionTitle_Handler($Sender) {
		$Discussion = $Sender->EventArguments['Discussion'];
		echo $this->GetConfig('Values')[$Discussion->{$this->GetConfig('ColumnName')}]['show_markup'];
	}

	# Discussion list
	public function DiscussionsController_AfterDiscussionTitle_Handler($Sender) {
		$this->DiscussionController_AfterDiscussionTitle_Handler($Sender);
	}

	# Do it in the category discussion list too
	public function CategoriesController_AfterDiscussionTitle_Handler($Sender) {
		$this->DiscussionsController_AfterDiscussionTitle_Handler($Sender);
	}

	private function GetConfig($Name) {
		global $ExtraDiscussionDataConfig;
		return $ExtraDiscussionDataConfig[$Name];
	}
}
