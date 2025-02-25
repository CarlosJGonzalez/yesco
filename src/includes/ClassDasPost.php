<?php 

class Das_Post
{
	private $db;
	private $client;
	private $storeid;
	private $is_store;
	private $budget;
	
	public function __construct($db,$client,$storeid,$budget = 0)
	{
		$this->db = $db;
		$this->client = $client;
		$this->storeid = $storeid;
		$this->budget = $budget;
	}

	public function __get($property) {
      if (property_exists($this, $property) && $property != 'db') {
          return $this->$property;
        }
    }
    
    public function __set($property, $value) {
        if (property_exists($this, $property) && $property != 'db') {
            $this->$property = $value;
        }
    }

    function delImage($id_post,$is_store){

    	if($is_store){
    		return $this->storePostUpdate($id_post,array('img'=>'','image'=>''));
    	}else{
    		return $this->storePostUpdate($id_post,array('image'=>''));
    	}
    }

    function delVideo($id_post,$is_store){
		return $this->storePostUpdate($id_post,array('video'=>'','image'=>'','img'=>''));
    }

    public function coorpPostUpdate($id_post, $data){
    	$this->db->where('id',$id_post);
		if ($this->db->update ('social_media__local_posts', $data))
			return true;
		else
			return false;
    }

    public function storePostUpdate($id_post,$data){

		$this->db->where('id',$id_post)->where('storeid',$this->storeid);
		if ($this->db->update ('social_media_local_posts_store', $data))
			return true;
		else
			return false;

    }

    public function storeUpdCreatePost($id_post,$data = ''){
    	$post = $this->db->where('id',$id_post)->where('storeid',$this->storeid)->getOne('social_media_local_posts_store');

    	if(count($post)){
    		if($data != '' ){
    			$this->storePostUpdate($id_post,$data);	
    		}    		
    	}else{
    		$post = $this->db->where('id',$id_post)->getOne('social_media__local_posts');
    		
    		$post['storeid']=$this->storeid;
			unset($post['notes']);
			unset($post['boost']);
			$id_post= $this->createStorePost($post);
    	}

    	return $id_post;
    }

    public function createStorePost($data_post){
    	return $this->db->insert('social_media_local_posts_store', $data_post);
    }

    public function getLeads($from,$to,$campid='',$all=false){
    	$between = array($from,$to);

    	$lead = array();	
    	if($all){
				$lead =$this->db->where('client',$this->client.'-'.$this->storeid)
					 		->where('created_time',$between,'BETWEEN')
					 		->get("facebook_lead.lead", null, '*');
    	}else{
    		if ($campid != ''){
    			$lead =	$this->db->where('campid',$campid)
    							 ->where('client',$this->client.'-'.$this->storeid)
    							 ->where('date',$between,'BETWEEN')
    							 ->getOne('advtrack.campaign_leads','count(*) as leads');
    		}
    	}

    	return $lead;
    	
    }

    public function getStats($from,$to){
    	$between = array($from,$to);

    	$stats = $this->db->where('client',$this->client.'-'.$this->storeid)
    					 ->where('date',$between,'BETWEEN')
    				 	 ->getOne('advtrack.facebookstats_new','sum(imps) as imps,sum(clicks) as clicks,campid');
		 	 
 	 	if(count($stats) && isset($stats['imps'])){
 	 		if(isset($stats['campid']) && $stats['campid'] != ''){
 	 			$lead = $this->getLeads($from,$to,$stats['campid']);
 	 			$stats['leads'] = count($lead)? $lead['leads']: 0;
 	 		}
 	 	}else{
 	 		$between = array($from.' 00:00:00',$to.' 23:00:00');
 	 		$stats = $this->db->where('client',$this->client.'-'.$this->storeid)
    					 ->where('date',$between,'BETWEEN')
    				 	 ->getOne('advtrack.facebookstats_posts','sum(imps) as imps,sum(total_clicks) as clicks,"0" as campid');
 	 	}

    	return $stats;
    }

    public function getPostLink($link){
    	$location = $this->getLocation();

    	if($location){
    		
    		$location_url = '';
    		$shop_url = '';

    		if( isset($location['url']) && $location['url'] != '' ){
    			$location_url = $location['url'];				
    		}

    		$link = str_replace("[[site_url]]",$location_url,$link);
    		
    		if( strpos($link, '[[shop_url]]') !== false ){
    			if( isset($location['shop_url']) && $location['shop_url'] != '' ){
	    			$shop_url = $location['shop_url'];				
	    		}else{
	    			$shop_url = 'https://fullypromoted.com/locations/'.$location_url;
	    		}

	    		$link = str_replace("[[shop_url]]",$shop_url,$link);
    		}

    		if( strpos($link, '[[promocode]]') !== false ){
    			if( isset($location['promocode']) && $location['promocode'] != '' ){
	    			$promocode = $location['promocode'];				
	    		}elseif( isset($location['shop_url']) && $location['shop_url'] != '' ){
	    			$link = $location['shop_url'];
	    		}else{
	    			$link = 'https://fullypromoted.com/locations/'.$location_url;
	    		}

	    		$link = str_replace("[[promocode]]",$promocode,$link);
    		}
    	}
    	return $link;
    }
	
   	public function replaceVariable($post){
    	$location = $this->getLocation();

    	$post = html_entity_decode($post,ENT_QUOTES | ENT_IGNORE, "UTF-8");

    	if($location){
    		$post = str_replace("[[site_name]]", $location['companyname'], $post);	
    		$post = str_replace("[[city]]", $location['city'],$post);

    		$replace = array(" ", "_", "-", "(", ")",".",",");

   			$post = str_replace("[[hashtag_name]]", str_replace($replace,"",strtolower($location['local_hashtag'])),$post);
    		$post = str_replace("[[hashtag_city]]", str_replace($replace,"",strtolower($location['city'])),$post);
    	}

	    $post = strip_tags($post); 
	    $post = stripslashes($post); 
    	return $post;
    }


	public function getLocation(){
		$info=$this->db->where('storeid',$this->storeid)->getOne('locationlist');
		return isset($info['storeid']) ? $info: false;
	}

	public function getMediaPost($post,$post_type){
		if($this->is_store){
			if (in_array($post_type, [0,1])) {
				if(isset($post['img']) && $post['img'] != ''){
					return array('image' => $post['img']);
				}else{
					return array('image' => $post['image']);
				}
			}else{
				return array('video' => $post['video'],'url' => $post['image']);
			}
		}

		if (in_array($post_type, [0,1])) {
			return array('image' => $post['image']);
		}else{
			return array('video' => $post['video'],'url' => $post['image']);
		}

		return [];
	}

	/**
	 * Post Boost is out or not
	 * 
	 * @param $id_post Id Post.
	 * 
	 * @return true or false.
	 */
	public function isOutBoost($id_post){
		$info=$this->db->where('storeid',$this->storeid)->where('id',$id_post)->getOne('social_media_local_boots_optout','boostout');
		return isset($info['boostout']) ? true : false;
	}

	/**
	 * Available Amount Options
	 * 
	 * @param $cycle_start (date) First day $cycle
	 * @param $cycle_end (date) Last day $cycle
	 * 
	 * @return array Available Amount Options
	 */
	function getAvailableOptions($cycle_start,$cycle_end){
	    $outBoostPost = $this->inBoostPost($cycle_start,$cycle_end);
	    $store_boost  = $this->getAmountChangeBoost($cycle_start,$cycle_end);
	    $default_cost = $this->getIdDefaultPost($cycle_start,$cycle_end);

	    $store_boost_amount =0;
	    $store_boost_qtt    =0;
	   

	    if(isset($store_boost['boost_amount'])){
	        $store_boost_amount = $store_boost['boost_amount'];
	        $store_boost_qtt    = $store_boost['qtt'];
	    }

	    $cost = 0;
	    $default_qtt  = 0;
	    if(count($default_cost)){
	        $cost = $this->budget/count($default_cost);
	        $default_qtt = count($default_cost);
	    }

	     $qtt_out=count($outBoostPost) ? count($outBoostPost) : $default_qtt;
	 
	    $qty = ((( $qtt_out - $store_boost_qtt) * $cost) + $store_boost_amount) - $cost;

	    if($qty >= $this->budget){
	        return array();
	    }

	    $types = array(5,10,15,20,25,30,35,40,45,50,55,70,75,85,100);
	    $rtn = array();
	    foreach ($types as $type) {
	        if (($this->budget - $qty ) >= $type){
	            $rtn[]=$type;
	        }       
	    }
	    return $rtn;
	}

	/**
	 * Post is default boost
	 * 
	 * @param $id_post Id Post.
	 * @return true or false.
	 *
	 */
	public function isDefaultBoost($id_post){
		$info=$this->db->where('id',$id_post)->getOne('social_media__local_posts','boost');
		return (isset($info['boost']) && $info['boost'])? true : false;
	}

	/**
	 * Post is out or not
	 * 
	 * @param $id_post Id Post.
	 * @return true or false.
	 *
	 */	
	public function isOut($id_post){
		$info=$this->db->where('storeid',$this->storeid)->where('id',$id_post)->getOne('social_media_local_posts_optout','optout');
		return isset($info['optout']) ? true : false;
	}

	/**
	 * Get all id post boost add default
	 * 
	 * @param $cycle_start (date) First day $cycle
	 * @param $cycle_end (date) Last day $cycle
	 * @param $noin (true or false) convert return default:false
	 * 
	 * @return array ids or text format "1,2,3"
	 */	
	public function getIdDefaultPost($cycle_start,$cycle_end,$noin=false){

		$inf =   $this->db->where("portal = 'facebook'")
					->where('date',array(date("Y-m-d 00:00:00", strtotime($cycle_start)),date("Y-m-d 23:59:59", strtotime($cycle_end))),'BETWEEN')
					->where('boost = 1')
					->get('social_media__local_posts',null,'id');

        return $this->convertArray($inf,$noin);
    }

     /**
     *
     * 
     * @return Array all Post Boost Store Change.
     */
	public function getChangeBoost($cycle_start,$cycle_end,$postid=false){
	
		$sql = "select smlps.* from social_media_local_posts_store smlps LEFT JOIN social_media__local_posts smlp on smlp.id = smlps.id where smlps.date between '".date("Y-m-d 00:00:00", strtotime($cycle_start))."' and '".date("Y-m-d 23:59:59", strtotime($cycle_end))."' and smlps.portal = 'facebook' and smlps.boost_end <> '0000-00-00' and smlp.boost = 1 and smlps.boost_store = 0 and smlps.storeid ='".$this->storeid."'";		

        $sql= ($postid) ? $sql." and smlps.id = $postid" :$sql;
        $inf = $this->db->rawQuery($sql);
		
		return (count($inf) > 0)? $inf : []; 
	}

	/**
     *
     * 
     * @return Array Get Amount All Stores Post Boost.
     */
	public function getAmountChangeBoost($cycle_start,$cycle_end){
	
		$sql = "select SUM(smlps.boost_amount) as boost_amount,count(smlps.id) as qtt from social_media_local_posts_store smlps LEFT JOIN social_media__local_posts smlp on smlp.id = smlps.id where smlps.date between '".date("Y-m-d 00:00:00", strtotime($cycle_start))."' and '".date("Y-m-d 23:59:59", strtotime($cycle_end))."' and smlps.portal = 'facebook' and smlps.boost_end <> '0000-00-00' and smlp.boost = 1 and smlps.boost_store = 0 and smlps.storeid ='".$this->storeid."'";		

		 $inf = $this->db->rawQuery($sql);		
		return (count($inf) > 0)? $inf[0] : [];
	}

    /**
     *
     * 
     * @return Array all Post Boost not out.
     */    
	public function inBoostPost($cycle_start,$cycle_end,$postid=false){

		$sql = "select * from social_media__local_posts  where portal = 'facebook' and date between '".date("Y-m-d 00:00:00", strtotime($cycle_start))."' and '".date("Y-m-d 23:59:59", strtotime($cycle_end))."' and boost = 1 and id not in (Select id from social_media_local_boots_optout where storeid  ='".$this->storeid."' and date between '".date("Y-m-d", strtotime($cycle_start))."' and '".date("Y-m-d", strtotime($cycle_end))."')";		

        $sql= ($postid) ? $sql." and id = $postid" :$sql;

         $inf = $this->db->rawQuery($sql);
		
		return (count($inf) > 0)? $inf : [];
	}


    /**
     *
     * Convert in string format "1,2,3" or array(1,2,3)
     *
     */    
    private function convertArray($inf,$noin=false){
        $rtn =array();
        foreach ($inf as  $value) {
           $rtn[]= $value['id'];
        }
        return $noin ? implode(',',$rtn) : $rtn;
    }

    function getPost($id_post){
    	$this->is_store =  true;
		$row=$this->db->where('id',$id_post)->where('storeid',$this->storeid)->getOne('social_media_local_posts_store');

		if(!isset($row['id'])){
			$row=$this->db->where('id',$id_post)->getOne('social_media__local_posts');
			$this->is_store=  false;
		}
		return $row;
    }

    function isStore($id_post){
    	$this->is_store =  true;
		$row=$this->db->where('id',$id_post)->where('storeid',$this->storeid)->getOne('social_media_local_posts_store','id');

		if(!isset($row['id'])){
			$this->is_store=  false;
		}
		return $this->is_store;
    }

    /**
	 * 
	 * Return Type Post
	 * 
	 * @param $post Information about the Post
	 * 
	 * @return integer 0 is Image | 1 is Carrusel | 2 is Video
	 */
    function getPostType($post){
        if($this->is_store){
            if(isset($post['image']) && $post['image'] !=""){
                if ((strpos($post['image'], "vimeo") !== false || strpos($post['image'], "youtube") || strpos($post['image'], "http") !== false) && isset($post['image'])){
                    return 2;
                }
            }

            $image=isset($post['image']) ? explode(';', ltrim($post['image'],';')):[];
            $img=isset($post['img']) ? explode(';', ltrim($post['img'],';')):[];
           	
            if((isset($img) && count($img) > 1) || (isset($image)  && count($image) > 1)){
                return 1;
            }
        }else{
            if(isset($post['image']) && $post['image'] !=""){
                if ((strpos($post['image'], "vimeo") !== false || strpos($post['image'], "youtube") || strpos($post['image'], "http") !== false) && isset($post['image'])){
                    return 2;
                }
            }
            $image=isset($post['image'])?explode(';', ltrim($post['image'],';')):[];
            if(isset($image)  && count($image) > 1){
                return 1;
            }
        }
        return 0;
    }


    public function delContains($array1, &$array2){
		foreach ($array1 as $key1 => $array1_value) {
			foreach ($array2 as $key2 => $array2_value) {
				if($array1_value['id'] == $array2_value['id']){
					unset($array2[$key2]);
				}
			}
		}
	}
}

?>