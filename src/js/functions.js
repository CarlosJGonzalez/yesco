"use strict";	

function getCoordinates(maps_api,address,address2,city,state,zip){
	var latitude = '', longitude = '';
	var url_action = location.protocol+'//'+location.hostname+'/admin/location-details/xt_google_maps.php';
	let coordinatesInfo = [];
	
	$.ajax({
		type: "POST",
		url: url_action,
		data: {"maps_api":maps_api,"address":address,"address2":address2,"city":city,"state":state,"zip":zip},
		cache: false,
		success: function(result){
			latitude = result.latitude; 
			longitude = result.longitude;
		},
		async: false,
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  console.log(err.Message);
		} 
	});
	
	coordinatesInfo[0] = latitude;
	coordinatesInfo[1] = longitude;
	
	return coordinatesInfo;
}

//Validate if an email is valid
function emailIsValid (email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

/**
 *
 * Returns an array of valid or invalid emails
 *
 * @param    array  $emails The array to check
 * @param    string  $type "valid" or "invalid"
 * @return      array
 *
 */
function get_emails_list(emails, type){
	
	var valid_emails = [];
	var invalid_emails = [];
	
	for(var i = 0; i < emails.length; i++){
		
		if (!emailIsValid(emails[i])) {
			invalid_emails.push(emails[i]);
		}else{
			if(valid_emails.includes(emails[i])){
				continue;
			}else{
				valid_emails.push(emails[i]);
			}
		}
	}

	if(type == "valid"){
		return valid_emails;
	}

	if(type == "invalid"){
		return invalid_emails;
	}
}