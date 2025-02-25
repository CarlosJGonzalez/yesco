<?php
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\CallRail;
use Das\CallFireRouting;
use Das\CampId;
use Das\MarkUp;


class Das_Campaign
{

	private $db;
	private $client;
	private $storeid;
	private $client_storeid;
	private $callRail;
	private $callFireRouting;
	private $markup;
	private $account = '224437392';
	private $company_id = 'COM27536ea4c4504ac29362c99f1fa17c42';

	function __construct($db,$token,$client,$storeid = 0){
		$this->db = $db;
		$this->storeid = $storeid;
		$this->client = $client;
		$this->callRail = new CallRail($token,$this->account);
		$this->callFireRouting = new CallFireRouting($token);
		$this->campid = new CampId($token);
		$this->markup = new MarkUp($token);

		if($this->storeid == 0){
			$this->client_storeid = $this->client;
		}else{
			$this->client_storeid = $this->client.'-'.$this->storeid;
		}
	}

	function updateCTN($params){
		
		$forward = preg_replace("/[^0-9]/", "", $params['forward']);
		$old_forward_number = preg_replace("/[^0-9]/", "", $params['$old_forward_number']);
		$ctn_number = preg_replace("/[^0-9]/", "", $params['ctn_number']);
		$track = $this->getExistTracker($ctn_number,$old_forward_number);

		if(count($track)){
			$data = array(
							"id" 		  => $track[0]['id'],
							"termnum" 	  => $forward,
							"greeting"    => true,
							"countrycode" => $params['country']
						 );
			$data['greeting_message'] = ( isset($params['greetingrMessage']) && $params['greetingrMessage'] != "") ? $params['greetingrMessage'] : 'This call will be recorded for quality assurance';
			$info = $this->callRail->updateTracker($data);

			if(!$info['is_error']){
				$data = array(
							'id' => $params['id'],
							'terminatingnum' => $forward,
						 );
				$info =  $this->callFireRouting->update($data);
			}else{
				return false;
			}			
		}

		return true;
	}

	function getCampaigns($date = null){
		return $this->db->where("storeid",$this->storeid)->get("campaign_info");
	}

	function getCampaignsEnd($date = null){
		
		if(!isset($date)){
			$date = date('Y-m-d 00:00:00');
		}

		$this->db->where("(end_date <> '0000-00-00 00:00:00' AND end_date < ?)",array($date));
		$this->db->where("storeid",$this->storeid);
		return $this->db->get("campaign_info");
	}

	function getCampaignsOn($date = null){
		
		if(!isset($date)){
			$date = date('Y-m-d 00:00:00');
		}

		$this->db->where("(end_date = '0000-00-00 00:00:00' OR end_date >= ?)",array($date));
		$this->db->where("storeid",$this->storeid);
		return $this->db->get("campaign_info");
	}


	function getCallFireRoutingInfo($id){
		$info = $this->callFireRouting->getCallFireRouting($id);

		if( isset($info['is_error']) && $info['is_error'] ){
			return false;
		}

		return $info['data'];
	}

	function getCallFireRouting($campid = null,$options = null){
		if( isset($campid) ){
			return $this->callFireRouting->getCallFireRoutingByCampId($this->client,$this->storeid,$campid,$options);
		}

		if($this->storeid == 0){
			return $this->callFireRouting->getCallFireRoutingByStoreId($this->client);
		}

		return $this->callFireRouting->getCallFireRoutingByStoreId($this->client,$this->storeid);
		
	}

	function getPortals($campid = null){
		if (isset($campid)) {
			if($this->storeid == 0){
				return $this->campid->getCampId($campid,$this->client)['data'];
			}
			return $this->campid->getCampId($campid,$this->client ,$this->storeid)['data'];
		} 
		
		if($this->storeid == 0){
			return $this->campid->getCampIdByStoreId($this->client)['data'];
		}
		return $this->campid->getCampIdByStoreId($this->client ,$this->storeid)['data'];
	}

	function getChannels(){
		return $this->db->where('type = 0')->get('advtrack.campid_nomenclature',null,'id,name');
	}

	function getSource(){
		return $this->db->where('type = 1')->get('advtrack.campid_nomenclature',null,'name');
	}

	function getMedium($id_parent = null ){
		if( isset( $id_parent ) ) {
			$this->db->where('parent',$id_parent); 
		}
		return $this->db->where('type = 2')->get('advtrack.campid_nomenclature',null,'name');
	}

	function getPayPeriodType(){
		return $this->db->get('payment_info.pay_period_type',null,'name, unique_name');
	}

	function getCampaign($id){
		return $this->db->where("id",$id)->getOne("campaign_info");
	}

	function existCampId($campid){
		return $this->db->where("campid",$campid)->where("client",'Y')->where('client',$this->client_storeid)->getOne("advtrack.campid_data",'count(*) as qtt')['qtt'];
	}

	function deleteCTN( $id ){
		$callFireRouting = $this->getCallFireRoutingInfo( $id );
  
		
		$flag = false;
		if( $callFireRouting ){
			$ctn = $callFireRouting[0]['phone'];
			$terminatingnum = ( isset( $callFireRouting[0]['terminatingnum'] ) && $callFireRouting[0]['terminatingnum'] != '') ? $callFireRouting[0]['terminatingnum'] : false;

			$this->callFireRouting->delete( $callFireRouting[0]['id'] );
			if( $terminatingnum ){
				$callRail = $this->getExistTracker($ctn,$terminatingnum);
			}else{
				$callRail = $this->getExistTracker($ctn);
			}
			

			$callRail = isset($callRail[0]['id']) ? $callRail[0] : false;
			if( $callRail ){
				$callRail_delete = $this->callRail->delete($callRail['id']);
			}
			$flag = true;
		}
		return $flag;
	}

	function deletePortal($id){
		$campid = $this->getCampIdInfo($id);

		$flag = true;
		if($campid){
			$callFireRouting = $this->getCallFireRouting($campid[0]['campid'],array('is_inactive' => 0 ));
			$this->campid->delete($id);
			$callFireRouting = isset($callFireRouting['data'][0]['phone']) ? $callFireRouting['data'][0] : false;
			if( $callFireRouting ){
				$callRail = $this->getExistTracker($callFireRouting['phone'],$callFireRouting['terminatingnum']);
				
				$this->callFireRouting->delete($callFireRouting['id']);
				$callRail = isset($callRail[0]['id']) ? $callRail[0] : false;
				if( $callRail ){
					$callRail_delete = $this->callRail->delete($callRail['id']);
					return $callRail_delete;
				}
			}
		}else{
			$flag = false;
		}
		return $flag;
	}

	function changeCTN($params){

		if(isset($params['id'])){
			$campid = $this->getCampIdInfo($params['id']);
			$ctn =  $this->createCTN($params);
	
			if( !$ctn['is_error'] ){

				if(isset($params['id_callFireRouting']) && $params['id_callFireRouting'] != ''){
					$callFireRouting = $this->getCallFireRoutingInfo($params['id_callFireRouting']);
					
					if($callFireRouting && isset($callFireRouting[0]['terminatingnum'])){

						$callFireRouting = $this->createCallFireRouting(
							$ctn['data'][0]['tracking_number'],
							$callFireRouting[0]['terminatingnum'],
							$params['call_recording'] ,
							$campid[0]['campid'],
							$params['country']
						);
						
						if( $callFireRouting['is_error'] ){
							return $callFireRouting;
						}

						if( isset($params['id_callRail']) && $params['id_callRail'] != '' ){
							$this->callRail->delete($params['id_callRail']);
						}
						
						$callFireRouting = $this->callFireRouting->delete($params['id_callFireRouting']);
						return $callFireRouting;
					}					
				}else{
					$callFireRouting = $this->getCallFireRouting($campid[0]['campid'],array('is_inactive' => 0 ));
					$callFireRouting = isset($callFireRouting['data'][0]['phone']) ? $callFireRouting['data'][0] : false;

					if( $callFireRouting ){
						$this->callFireRouting->delete($callFireRouting['id']);
						$callRail = $this->getExistTracker($callFireRouting['phone'],$callFireRouting['terminatingnum']);

						$callRail = isset($callRail[0]['id']) ? $callRail[0] : false;
						if( $callRail ){
							$callRail_delete = $this->callRail->delete($callRail['id']);
						}
					}

					$callFireRouting = $this->createCallFireRouting(
							$ctn['data'][0]['tracking_number'],
							$params['forward'],
							$params['call_recording'] ,
							$campid[0]['campid'],
							$params['country']
						);
					if( $callFireRouting['is_error'] ){
						return $callFireRouting;
					}
				}
			}else{
				return $ctn;
			}						
		}else{
			return false;
		}

		return true;
	}

 	public function getExistTracker($tracker,$termnum = null){
		$callRail =  $this->callRail->getExistTracker($tracker,$termnum);

		if( isset($callRail['is_error']) && $callRail['is_error'] ){
			return false;
		}

		return $callRail['data'];
	}

	function getCampIdInfo($id){
		$campIdInfo =  $this->campid->getCampIdInfo($id);

		if( isset($campIdInfo['is_error']) && $campIdInfo['is_error'] ){
			return false;
		}

		return $campIdInfo['data'];
	}

	function updatePortal($params){
		if( !isset($params['id'])){
			return false;
		}

		$data = array(
					"client" => $this->client_storeid,
					"name" => $this->db->escape($params['name']),
					"source" => $this->db->escape($params['source']),
					"medium" => $this->db->escape($params['medium']),
					"channel" => $this->db->escape($params['channel']),
					"note" => $this->db->escape($params['note']),
					//"markup" => $this->db->escape($params['markup'])
				);
	
		return $this->db->where("id",$params['id'])->update('advtrack.campid_data',$data);
	}


	function addPortal($params){	

		$data = array(
					"client" => $this->client_storeid,
					"campid" => $this->db->escape($params['campid']),
					"name" => $this->db->escape($params['name']),
					"source" => $this->db->escape($params['source']),
					"medium" => $this->db->escape($params['medium']),
					"channel" => $this->db->escape($params['channel']),
					"note" => $this->db->escape($params['note']),
					"markup" => $this->db->escape($params['markup'])
				);
		
		if(isset($params['createCTN'])){
	
			$ctn = $this->createCTN($params);

			if( !$ctn['is_error'] ){
				$callFireRouting = $this->createCallFireRouting($ctn['data'][0]['tracking_number'],$params['forward'],$params['call_recording'] ,$params['campid'],$params['country']);
			}else{
				return $ctn;
			}
		}
		
		$this->addInAdjack($data);
		$id = $this->db->insert("advtrack.campid_data",$data);
		if( $id ){
			$markup = (isset($data['markup']) && $data['markup'] != '') ? $data['markup'] : 0;
			$this->addMarkupHistory($id,$markup);
		}

		return $id;			
	}
	
	function createCallFireRouting($phone,$forward,$recording,$campid,$countrycode){
		$data = array(
					"client" 	     => $this->client_storeid,
					"branch" 		 => (string)$this->storeid,
					"recordcall"     => isset($recording) ? true : false,
					"phone"          => preg_replace("/^\+?{$countrycode}/", '',$phone),
					"terminatingnum" => preg_replace("/[^0-9]/", "", $forward),
					"campid"       	 => $campid,
					"notes"       	 => 'callrail',
				);
		return $this->callFireRouting->create($data);
	}

	function createCTN($params){
		if( isset($params['client']) && $params['country'] && $params['numberName']){
			$data = array(
							"company_id" 	  => $this->company_id,
							"termnum" 		  => preg_replace("/[^0-9]/", "", $params['forward']),
							"recording"       => isset($params['call_recording']) ? true : false,
							"sms_enabled"     => isset($params['sms_enabled']) ? true : false,
							"greeting"        => true,
							"countrycode"     => $params['country'],
							"name"       	  => $params['numberName']
						 );

			$data['is_tollfree'] = isset($params['is_tollfree']) ? true : false;

			if(isset($params['forwardAreacode']) && $params['forwardAreacode'] != ""){
				$data['tracker_areacode'] = (int)$params['forwardAreacode'];
			}

			$data['greeting_message'] = ( isset($params['greetingrMessage']) && $params['greetingrMessage'] != "") ? $params['greetingrMessage'] : 'This call will be recorded for quality assurance';

			if( $data['is_tollfree'] ){				
				$data['tollfree_areacode'] = (isset($params['tollFreeAreaCode']) && $params['greetingrMessage'] != "")  ? $params['tollFreeAreaCode'] : "800";
			}

			if(isset($params['whisper_message']) && $params['whisper_message'] != ""){
				$data['whisper_message'] = $params['whisperMessage'];
			}

			return $this->callRail->createTracker($data);
		}
		return [];
	}

	public function getActiveMarkUpByDate($campid_id,$params){
		return $this->markup->getMarkUpActiveByDate( $campid_id,$params )['data'][0]['markup'];
	}
	
	private function addMarkupHistory($campid_id,$markup){
		if($markup == '0'){
			return true;
		}
		return $this->markup->create( $campid_id,
										array(
												'markup'=> $markup,
												'start'=> (string)strtotime("now")
											 )
									  );
	}

	private function addInAdjack($data){
		unset($data["source"]);
		unset($data["medium"]);
		unset($data["note"]);
		unset($data["markup"]);
		$data['active']='Y';
		$data['type']='S';
		$info[]=$data;
		$data['type']='C';
		$info[]=$data;

		return $this->db->insertMulti("advtrack.campid",$info);
	}

	function addCampaign($params){
		//Escape input fields
		$portal = $this->db->escape($params['portal']);
		$campaign_name = $this->db->escape($params['campaign_name']);
		$start_date = date("Y-m-d",strtotime($params['start_date']));
		$end_date = date("Y-m-d",strtotime($params['end_date']));
		$notes = $this->db->escape($params['notes']);

		$budget = filter_var($params['budget'], FILTER_VALIDATE_FLOAT);
		$payment_period = $this->db->escape($params['payment_period']);
		$campid = $this->db->escape($params['campid']);		    
		
		$data = array(
					"portal" => $portal,
					"campaign_name"=>$campaign_name,
					"start_date"=>$start_date,
					"end_date"=>$end_date,
					"notes"=>$notes,
					"storeid"=>$this->storeid,
					"budget"=>$budget,
					"payment_period"=>$payment_period,
					"campid"=>$campid
				);

		return $this->db->insert("campaign_info",$data);
	}
	function updateCampaign($params){

		$end_date = date("Y-m-d",strtotime($params['end_date']));
		$id = $this->db->escape($params['id']);
		$data = array(
						"end_date"=>$end_date
					);
		
		return $this->db->where("id",$id)->update("campaign_info",$data);
	}
	function deleteCampaign($id){
		//Escape input fields
		$camp_id = $this->db->escape($id);
		return $this->db->where("id",$camp_id)->delete("campaign_info");
	}
	
}
?>