<?php 
include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");
if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
	$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
	header('location: /');
	exit;
}

//Only for test purpose
$active_location['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
$active_location['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';

if(empty($active_location['constant_contact_api_key']) || empty($active_location['constant_contact_access_token'])){
	$_SESSION['error'] = "Please enter a valid api key and token.";
	header('location: /settings/promote/');
	exit;
}else{
	$cc_api_key = $active_location['constant_contact_api_key'];
	$cc_access_token = $active_location['constant_contact_access_token'];
}

//ClassDasConstantContact 
$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);

/*$accountInfo = $cc->getAccountSummaryInformation();
echo '<pre>'; print_r($accountInfo); echo '</pre>';*/

/*$accountParams = Array("website"=>"");
$accountInfoUpdate = $cc->updateAccountInformation($accountParams);
echo '<pre>'; print_r($accountInfoUpdate); echo '</pre>';*/

$emailCampaigns = $cc->getCampaigns();
echo '<pre>'; print_r($emailCampaigns); echo '</pre>';

//$emailCampaign = $cc->getCampaign('1133888581324');
//echo '<pre>'; print_r($emailCampaign); echo '</pre>';

/*$previewOfEmailCampaign = $cc->previewOfEmailCampaign('1133925197352');
print_r($previewOfEmailCampaign);
print_r($previewOfEmailCampaign['preview_email_content']);*/

/*$emailCampaign = $cc->getCampaign('1133809582084');
echo '<pre>'; print_r($emailCampaign); echo '</pre>';

$previewOfEmailCampaign = $cc->previewOfEmailCampaign('1133809582084');
print_r($previewOfEmailCampaign);
print_r($previewOfEmailCampaign['preview_email_content']);*/
/*
$accountEmailAddressParamsAll = Array("status"=>"ALL");
$verifiedEmailAddresses = $cc->verifiedEmailAddresses($accountEmailAddressParamsAll);
echo '<pre>'; print_r($verifiedEmailAddresses); echo '</pre>';

$accountEmailAddressParamsUnconfirmed = Array("status"=>"UNCONFIRMED");
$verifiedEmailAddresses = $cc->verifiedEmailAddresses($accountEmailAddressParamsUnconfirmed);
echo '<pre>'; print_r($verifiedEmailAddresses); echo '</pre>';

$accountEmailAddressParamsConfirmed = Array("status"=>"CONFIRMED");
$verifiedEmailAddresses = $cc->verifiedEmailAddresses($accountEmailAddressParamsConfirmed);
echo '<pre>'; print_r($verifiedEmailAddresses); echo '</pre>';
echo '<pre>'; print_r($verifiedEmailAddresses[0]['email_address']); echo '</pre>';
*/
/*$contactLists = $cc->getLists();
echo '<pre>'; print_r($contactLists); echo '</pre>';*/
/*
$list_name = 'Hot Opportunities';
$params = Array("name"=>$list_name,
				"status"=>"ACTIVE"
				);
$createdList = $cc->addList($params);

if($createdList['is_error']){
	echo '<pre>'; print_r($createdList['error_info']['error_message']); echo '</pre>';
}else{
	echo '<pre>'; print_r($createdList); echo '</pre>';
}*/

$list = $cc->getList('1974280493');
echo '<pre>'; print_r($list); echo '</pre>';

$contactsFromList = $cc->getContactsFromList('1974280493');
echo '<pre>'; print_r($contactsFromList); echo '</pre>';

/*$paramsNewContact = Array("lists"=>[[
							   "id"=>'1202943877'
						   ]],
						   "email_addresses"=>[[
							   "email_address"=>"jessicas@das-group.com"
						   ]],
						   "first_name"=>'Jessica',
						   "last_name"=>'Style',
						   );
						
//$newContact = $cc->addContact($paramsNewContact);
//echo '<pre>'; print_r($newContact); echo '</pre>';

$contact = $cc->getContact('1084566184');
echo '<pre>'; print_r($contact); echo '</pre>';


$paramsUpdatedContact = Array("lists"=>[[
							   "id"=>'1202943877'
						   ]],
						   "email_addresses"=>[[
							   "email_address"=>"jessi@das-group.com"
						   ]],
						   "first_name"=>'Jessi',
						   "last_name"=>'Styles',
						   );
//$updatedContact = $cc->updateContact('1105405132', $paramsUpdatedContact);
//echo '<pre>'; print_r($updatedContact); echo '</pre>';

$list_name = 'Hot Opportunities';
$list_status = 'ACTIVE';
$paramsUpdatedList = Array("name"=> $list_name,
						   "status"=> $list_status
						   );
//$updatedList = $cc->updateList('1429384382', $paramsUpdatedList);
//echo '<pre>'; print_r($updatedList); echo '</pre>';
*/
/*$paramsNewContact = Array("lists"=>[[
							   "id"=>'1439649033'
						   ]],
						   "email_addresses"=>[[
							   "email_address"=>"sicwing@hotmail.com"
						   ]],
						   "first_name"=>'Sicwing',
						   "last_name"=>'Wu',
						   );
*/						
//$newContact = $cc->addContact($paramsNewContact);
//echo '<pre>'; print_r($newContact); echo '</pre>';

/*$allContacts = $cc->getContacts();
echo '<pre>'; print_r($allContacts); echo '</pre>';*/

//Create campaign
/*$campaignParams = Array("name"=>"Test campaign final",
						"subject"=>"Subject Test final",
						"from_name"=>"Location Name final",
						"from_email"=>"sicwing@das-group.com",
						"reply_to_email"=>"sicwing@das-group.com",
						"is_permission_reminder_enabled"=>true,
						"permission_reminder_text"=>"As a reminder, you're receiving this email because you have expressed an interest in MyCompany. Don't forget to add from_email@example.com to your address book so we'll be sure to land in your inbox! You may unsubscribe if you no longer wish to receive our emails.",
						"is_view_as_webpage_enabled"=>true,
						"view_as_web_page_text"=> "View this message as a web page",
						"view_as_web_page_link_text"=>"Click Here",
						"greeting_salutations"=>"Hello",
						"greeting_name"=>"FIRST_NAME",
						"greeting_string"=>"Dear ",
						"email_content"=>"<html><body><p>This is text of the email message.</p></body></html>",
						"text_content"=>"This is the text-only content of the email message for mail clients that do not support HTML.",
						"email_content_format"=>"HTML",
						"style_sheet"=>"",
						"sent_to_contact_lists"=>[
						  [
							   "id"=>'1202943877' 
						   ]
						],
						);					
*/
//$newCampaign = $cc->addCampaign($campaignParams);
//echo '<pre>'; print_r($newCampaign); echo '</pre>';

/*$scheduleCampaignParams = Array("scheduled_date"=>'2020-02-29T13:15:00-05:00');

$scheduleCampaign = $cc->scheduleCampaign('1133888846829',$scheduleCampaignParams);
echo '<pre>'; print_r($scheduleCampaign); echo '</pre>';*/

exit;
?>