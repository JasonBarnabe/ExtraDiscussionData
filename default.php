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

	# UI for new post on existing discussion
	public function DiscussionController_AfterBodyField_Handler($Sender) {
		$ShowWhen = $this->Config['ShowFormWhen'];
		if (!$ShowWhen($Sender)) {
			return;
		}
		# Only the OP can change the rating
		$DiscussionUserID = $Sender->Discussion->InsertUserID;
		if (Gdn::Session()->UserID != $DiscussionUserID) {
			return;
		}
		$FormArray = [];
		foreach ($this->Config['Values'] as $Id => $Options) {
			$FormArray[$Id] = $Options['form_markup'];
		}
		echo '<label>'.$this->Config['UpdateLabel'].'</label>';
		$ColumnName = $this->Config['ColumnName'];
		echo $Sender->Form->RadioList($this->Config['ColumnName'], $FormArray, ['Default' => $Sender->Discussion->$ColumnName]);
	}

	# Update existing discussion on new comment
	public function CommentModel_AfterSaveComment_Handler($Sender) {
		$Value = $Sender->EventArguments['FormPostValues'][$this->Config['ColumnName']];
		$DiscussionID = $Sender->EventArguments['FormPostValues']['DiscussionID'];
		if (!isset($Value) || !isset($DiscussionID)) {
			return;
		}
		# Only the OP can change the value
		$DiscussionUserID = $Sender->SQL->Select('InsertUserID')->From('Discussion')->Where('DiscussionID', $DiscussionID)->Get()->FirstRow(DATASET_TYPE_ARRAY)['InsertUserID'];
		if (Gdn::Session()->UserID != $DiscussionUserID) {
			return;
		}
		$Sender->SQL->Update('Discussion', [$this->Config['ColumnName'] => $Value], ['DiscussionID' => $DiscussionID])->Put();
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
