<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="/css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
	<link href="/css/smart_wizard_theme_arrows.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Add New Location | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-4 p-0">
			<div class="breadcrumbs bg-white px-4 py-1 border-bottom small">
				<a href="/admin/location-details/" class="text-muted">All Locations</a>
				<span class="mx-1">&rsaquo;</span>
				<span class="font-weight-bold text-muted">Add New Location</span>
			</div>
			<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4 mb-4">
				<h1 class="h2 font-light mb-0 text-center text-sm-left">
					<span class="fa-layers fa-fw mr-2">
						<i class="fas fa-map-marker"></i>
						<i class="fas fa-plus-circle fa-inverse" data-fa-transform="shrink-8 up-2"></i>
					 </span>
					 Add New Location</h1>
			</div>
			
			<div class="py-3 px-4">

				<?php if(isset($_SESSION['success'])){ ?>
				<div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
				  <strong>Success!</strong> <?php echo $_SESSION['success'];?>
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<?php unset($_SESSION['success']); } ?>
				<?php if(isset($_SESSION['error'])){ ?>
				<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
				  <strong>Error!</strong> <?php echo $_SESSION['error'];?>
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<?php unset($_SESSION['error']); } ?>

					<?php
						$address = Array("street"=>"9050 Pines blvd",
										"secondary"=>"st 250",
										"city"=>"pembroke pine",
										"state"=>"FL",
										"zipcode"=>"33024");
						
						$formatted_add = ss_getStreetAddress($address);
						echo "<pre>";
						var_dump($formatted_add[0]);
						echo "</pre>";
//						echo $formatted_add[0]->components->primary_number;
				
//						$address = Array("prefix"=>"9050 Pines blvd");
//						$suggestions = ss_getSuggestions($address);
//						echo "<pre>";
//						var_dump($suggestions);
//						echo "</pre>";
					?>
		  </div>
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBd_9Le-U42hY_eEcWOQI6wAkd-WRB6NRw&libraries=places&callback=initAutocomplete" async defer></script>

	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.smartWizard.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#smartwizard').smartWizard({
				theme:"arrows",
				keyNavigation:false,
				useURLhash:false,
				showStepURLhash:false,
				toolbarSettings: {
					toolbarExtraButtons: [
					$('<button></button>').text('Add Location')
					.addClass('btn bg-blue text-white')
					.on('click', function(){ 
						$("#addLocation").submit();
					})
					]
				}
			});
			$( ".datepicker" ).datepicker({
				"minDate":0
			});
			
		});
		$("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
			if($('button.sw-btn-next').hasClass('disabled')){
				$('.sw-btn-group-extra').show(); 
			}else{
				$('.sw-btn-group-extra').hide();				
			}
		});
		
		var placeSearch, autocomplete;
		var componentForm = {
			street_number: 'short_name',
			route: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'short_name',
			country: 'long_name',
			postal_code: 'short_name'
		};

		function initAutocomplete() {
			// Create the autocomplete object, restricting the search to geographical
			// location types.
			autocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */(document.getElementById('address1')),
				{types: ['geocode']});

			// When the user selects an address from the dropdown, populate the address
			// fields in the form.
			autocomplete.addListener('place_changed', fillInAddress);
		}

		function fillInAddress() {
			
			// Get the place details from the autocomplete object.
			var place = autocomplete.getPlace();
console.log(place.address_components);
			for (var component in componentForm) {
			  document.getElementById(component).value = '';
			  document.getElementById(component).disabled = false;
			}

			// Get each component of the address from the place details
			// and fill the corresponding field on the form.
			for (var i = 0; i < place.address_components.length; i++) {
			  var addressType = place.address_components[i].types[0];
			  if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById(addressType).value = val;
			  }
			}
			document.getElementById('address1').value = 
    place.address_components[0]['long_name'] + ' ' +
    place.address_components[1]['long_name'];
		}

	  </script>
  </body>
</html>