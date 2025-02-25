<?php
set_time_limit(0); // no limit

class Das_MC
{

	private $apikey;
	private $mc;

	function __construct($apikey)
	{
		$this->apikey= $apikey;
	}

	function addInterest($listid,$intCateid,$parameters){
		$url ='lists/'.$listid.'/interest-categories/'.$intCateid.'/interests';
		return $this->call("POST",$url,$parameters);
	}

	function existInterestName($listid,$intCateid,$name){
		$filter = json_encode(array(
										'fields' => 'interests,total_items',
										'count' => 1000,
								    )
							 );

		$interests = $this->getInterest($listid,$intCateid,$filter);

		if($interests['total_items'] == 0){
			return false;
		}

		$interests = $interests['interests'];
		foreach ($interests as  $interest) {
			if($interest['name'] == $name){
				return $interest;
			}
		}
		return false;
	}

	function getInterest($listid,$intCateid,$parameters=''){
		$url ='lists/'.$listid.'/interest-categories/'.$intCateid.'/interests';
		return $this->call("GET",$url,$parameters);
	}

	function getInterestCategories($listid,$parameters=''){
		$url ='lists/'.$listid.'/interest-categories';

		return $this->call("GET",$url,$parameters);
	}


	function addInterestCategories($listid,$parameters = ''){
		$url ='lists/'.$listid.'/interest-categories';

		return $this->call("POST",$url,$parameters);
	}

	function hasInterest($id,$listid,$email){
		$interest = $this->getMemberInterests($listid,$email,'interests');
		
		if(isset($interest['status'])){
			if($interest['status'] == '404' && $interest['is_error'] == 1 && $interest['msg_error'] == "The requested resource could not be found."){
				return false;
			}
		}
		
		
		$interest =  $interest['interests'];
		if (array_key_exists($id,$interest)){
			return $interest[$id];
		}

		return false;
	}

	
	function getMemberInterests($listid,$email,$fields=false){
		$emailHash = md5($email);
		$url = 'lists/'.$listid.'/members/'.$emailHash;

		if(!$fields){
			$fields = 'id,unique_email_id,interests';
		}

		return $this->call("GET",$url, json_encode(array('fields' => $fields)));
	}

	function addUpdateMember($listid,$email,$parameters = ''){
		$emailHash = md5($email);
		$url = 'lists/'.$listid.'/members/'.$emailHash;

		return $this->call("PUT ",$url,$parameters);
	}

	function getMember($listid,$email){
		
		$emailHash = md5($email);
		$url = 'lists/'.$listid.'/members/'.$emailHash;
		$member= $this->call("GET",$url);

		if($member['is_error']){
			return array('is_error'=>1,'info'=>[],'activity'=>[]);
		}

		$info=[
					'id'=>$member['id'],					
					'email_address'=>$member['email_address'],
					'unique_email_id'=>$member['unique_email_id'],
					'fname' => $member['merge_fields']['FNAME'],
					'lname' => $member['merge_fields']['LNAME'],
					'status'=>$member['status'],
					'interests'=>$member['interests'],
			 ];

		$activity= $this->getMemberActivity($listid,$email);
	 
		return array('info'=>$info,'activity'=>$activity,'is_error'=>0);
	}


	function addMergeFields($listid,$parameters = ''){
		$url ='lists/'.$listid.'/merge-fields';

		return $this->call("POST",$url,$parameters);
	}

	function getMembers($listid,$parameters = ''){
		
		$url ='lists/'.$listid.'/members';

		$members= $this->call("GET",$url,$parameters);	

		$r_members=[];
		
		if(!isset( $members['is_error'] ) || $members['is_error']){
			return $r_members;
		}

		if( isset( $members['total_items'] ) ) {
			$r_members['total_items'] =  $members['total_items'];
		}		

		if(count($members) && isset($members['members'])){
			
			foreach ($members['members'] as $member) {

			$info=[
					'id'=>$member['id'],					
					'email_address'=>$member['email_address'],
					'unique_email_id'=>$member['unique_email_id'],
					'fname' => $member['merge_fields']['FNAME'],
					'lname' => $member['merge_fields']['LNAME'],
					'status'=> $member['status'],
					'timestamp_opt'=> $member['timestamp_opt'],
				   ];

			$r_members[]=array(
								'info'=>$info,
								'activity'=>$this->getMemberActivity($listid,$member['email_address'])
							  );						 
			}
		}
		return $r_members;
	}
	
	function addMember($listid,$parameters = ''){
		$url = 'lists/'.$listid.'/members';

		return $this->call("POST",$url,$parameters);
	}
	function deleteMember($listid,$subscriberHash){
		$url = 'lists/'.$listid.'/members/'.$subscriberHash;
		return $this->call("DELETE",$url);
		
	}
	function addCampaign($parameters,$send=FALSE){
		$url = 'campaigns/';

		$res_camp= $this->call("POST",$url,$parameters);
		
		if($send){
			$this->actionsCampaign($res_camp['id'],"send");
		}

		return $res_camp;
	}
	
	function editCampaign($campaignid,$parameters,$send=FALSE){
		$url = 'campaigns/'.$campaignid;

		$res_camp= $this->call("PATCH",$url,$parameters);
		
		if($send){
			$res_camp = $this->actionsCampaign($res_camp['id'],"send");
		}

		return $res_camp;
	}

	function getCampaign($campaignid,$parameters=""){
		$url = 'campaigns/'.$campaignid;

		return $this->call("GET",$url,$parameters);		
	}

	function getCampaigns(){
		$url = 'campaigns/';

		return $this->call("GET",$url);		
	}
	
	function deleteCampaign($campaignid,$parameters=""){
		$url = 'campaigns/'.$campaignid;

		return $this->call("DELETE",$url,$parameters);	
	}

	/*
	** @$campaignid: campaign
	** 
	** Action Type:
	**				test:          Send a test email (Default)
	**				cancel-send:   Cancel a campaign
	**				create-resend: Resend a campaign
	**				pause:         Pause an RSS-Driven campaign
	**				replicate:     Replicate a campaign
	**				resume:        Resume an RSS-Driven campaign
	**				schedule:      Schedule a campaign
	**				send:          Send a campaign
	**				unschedule:    Unschedule a campaign
	**
	** @$parameters: json encode.
	**
	*/
	function actionsCampaign($campaignid,$action="test",$parameters=""){
		$url ='campaigns/'.$campaignid.'/actions/'.$action;

		return $this->call("POST",$url,$parameters);
	}

	function resendNonOpeners($campaignid,$send=FALSE){
		$res_camp=$this->actionsCampaign($campaignid,"create-resend");
		
		if($send){
			return $this->actionsCampaign($res_camp['id'],"send");
		}
		return $res_camp;
	}


	/*
	**
	**@$parameters for more information: https://developer.mailchimp.com/documentation/mailchimp/reference/batches/
	**
	*/
	function batches($parameters){
		$url ='batches';

		return $this->call("POST",$url,$parameters);
	}

	function addSegment($listid,$static_segment=""){
		$url = 'lists/'.$listid.'/segments/';

		return $this->call("POST",$url,$static_segment);
	}

	/*
	 * Informaction: 
	 * https://developer.mailchimp.com/documentation/mailchimp/reference/lists/segments/members/
	 */	
	function getSegmentMembers($listid,$segmentid,$parameters=''){
		$url = 'lists/'.$listid.'/segments/'.$segmentid.'/members';

		return $this->call("GET",$url,$parameters);
	}

	function addTemplate($parameters){
		$url = 'templates/';

		return $this->call("POST",$url,$parameters);
	}
	
	function getTemplate($templateid,$parameters = ''){
		$url = 'templates/'.$templateid;

		return $this->call("GET",$url,$parameters);
	}

	function updateTemplate($templateid,$parameters){
		$url = 'templates/'.$templateid;

		return $this->call("PATCH",$url,$parameters);
	}
	
	function deleteTemplate($templateid,$parameters = ''){
		$url = 'templates/'.$templateid;

		return $this->call("DELETE",$url,$parameters);
	}

	function addList($parameters){
		$url = 'lists/';

		return $this->call("POST",$url,$parameters);
	}
	
	function getList($listid,$parameters=""){
		$url = 'lists/'.$listid; 

		return $this->call("GET",$url,$parameters);		
	}
	
	function getLists($parameters=""){
		$url = 'lists'; 

		return $this->call("GET",$url,$parameters);	
	}
	
	function deleteList($listid,$parameters=""){
		$url = 'lists/'.$listid; 

		return $this->call("DELETE",$url,$parameters);		
	}

	function getBatchStatus($batchid){
		$url = 'batches/'.$batchid;

		$result = $this->call("GET",$url);
		$rtn=[
				'id'=>$result['id'],
				'status'=>$result['status'],
				'errored_operations'=>$result['errored_operations'],
				'success_operations'=>$result['finished_operations'] - $result['errored_operations'],
				'finished_operations'=>$result['finished_operations'],
			];

		return $rtn;
	}

	function deleteBatch($batchid){
		$url = 'batches/'.$batchid;
		return $this->call("DELETE",$url);
	}

	function getMemberActivity($listid,$email){
		
		$url = 'lists/'.$listid.'/members/'.md5($email).'/activity';
		
		$member_activity = $this->call("GET",$url);
		
		$rt_info=[];
		if(isset($member_activity['total_items']) && $member_activity['total_items'] > 0){
			$activitys=$member_activity['activity'];
			foreach ($activitys as  $activity) {	
				if(isset($rt_info[$activity['action']])){
					$rt_info[$activity['action']] +=1;
				}else{
					$rt_info[$activity['action']] = 1;
				}			
					
			}
			return $rt_info;
		}
		return [];
	}
	
	function getReport($campaignid){
		$url = 'reports/'.$campaignid;

		return $this->call("GET",$url);	
	}
	
	// https://mailchimp.com/developer/reference/root/
	public function getAccountInfo(){
		return $this->call("GET",'/');	
	}
	
	public function addDomainToAccount($parameters){
		$url = 'verified-domains/';

		return $this->call("POST",$url,$parameters);
	}
	
	public function verifyDomain($domain_name, $parameters){
		//https://us6.api.mailchimp.com/3.0/verified-domains/das-group.com/actions/verify?code=cce29a9469eb
		$url = 'verified-domains/'.$domain_name.'/actions/verify/';

		return $this->call("POST",$url,$parameters);
	}

	private function call($method, $url, $json=""){
		$apiKey=$this->apikey;
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/'.$url;
		
		if( $json && $method == 'GET' )
			$url .= '?' . http_build_query(json_decode($json));

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if($json && $method != 'GET'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 
		}

		

		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);

		if(is_array($result)){
			return $this->is_error($result);
		}

		return $result;		
	}

	private function is_error($data){

		if(isset($data['status']) && $data['status'] >= 400){
			$msg = $this->getErrorMsg($data);
			return array_merge($data,['is_error'=>1,'msg_error'=>$msg]);
		}

		return array_merge($data,['is_error'=>0,'msg_error'=>false]);
	}

	private function getErrorMsg($data){
		$msg='';
		if(isset($data['errors']) && count($data['errors'])){
			foreach ($data['errors'] as  $value) {
				$field = isset($value['field']) ? $value['field'] : '';
				$message = isset($value['message']) ? $value['message'] : '';
				$msg .= $field.':'.$message.'.';
			}
		}else{
			$msg = isset($data['detail']) ? $data['detail'] : '';
		}
		return $msg;
	}


}
?>
