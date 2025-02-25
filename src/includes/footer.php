<script src="https://kit.fontawesome.com/96c8375bff.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script src="/js/chosen.jquery.min.js"></script>

<script src="/js/classie.js"></script>
<script>
    var showRightPush = document.getElementById( 'showRightPush' ),
		body = document.body,
		menuRight = document.getElementById( 'cbp-spmenu-s2' );

	showRightPush.onclick = function() {
		classie.toggle( this, 'active' );
		classie.toggle( body, 'cbp-spmenu-push-toleft' );
		classie.toggle( menuRight, 'cbp-spmenu-open' );
		disableOther( 'showRightPush' );
	};
	$('.custom-file input[type=file]').on('change',function(){
		var files = $(this)[0].files,
		label = files[0].name;
		if (files.length > 1) {
			label = files.length + " files selected"
		}
		$(this).next('.custom-file-label').text(label);
		
	});
	$(document).on('click', '.change-location .dropdown-menu', function (e) {
	  e.stopPropagation();
	});
	$(".chosen-select").chosen({width: "100%"});
	
	//Notifications dropdown-menu and settigns
	$("#dropdownMenuNote").click(function(){
		var url_action = location.protocol+'//'+location.hostname+'/admin/notifications/clearNotifications.php';
		var storeid = $(this).data("storeid");
		var user_type = $(this).data("usertype");
		var value = "new";
		$.ajax({
			url: url_action, 
			type:"POST",
			data:{"storeid":storeid, "value":value, "user_type":user_type},
			success: function(result){
				if(result="success")
					$("#dropdownMenuNote span.notifications_number").remove();
			}
		});
	});
	$(".markRead").click(function(e){
		e.preventDefault();

		$('.dropdown-menu.notifications').addClass("keepOpen");
		
		if(confirm("Are you sure you want to proceed?")){
			var url_action = location.protocol+'//'+location.hostname+'/admin/notifications/clearNotifications.php';
			var storeid = $(this).data("storeid");
			var user_type = $(this).data("usertype");
			var value = "unread";
			$.ajax({
				url: url_action, 
				type:"POST",
				data:{"storeid":storeid, "value":value, "user_type":user_type},
				success: function(result){
					if(result="success")
						$( "div.unread" ).each(function() {
						  $( this ).removeClass( "unread" );
						});
				}
			});
		}
	});
	$(".noteItem").click(function(event){
		var url_action = location.protocol+'//'+location.hostname+'/admin/notifications/clearNotifications.php';
		var value = "unread";
		var id = $(this).data("id");
		
		$(this).children("div.notification-status").addClass("notification-clicked");
		
		$.ajax({
			url: url_action, 
			type:"POST",
			data:{"id":id,"value":value, "type":"single"},
			success: function(result){
				if(result="success"){
					$(".notification-clicked").removeClass( "unread" );
					$(".noteItem .notification-clicked").removeClass( "notification-clicked" );
				}
	
			}
		});
	});
	
	$('body').on('click', function (e) {
		if (!$('.dropdown-menu.notifications').is(e.target) && $('.dropdown-menu.notifications').has(e.target).length === 0 && $('.keepOpen').has(e.target).length === 0) {
			$('.dropdown-menu.notifications').removeClass("keepOpen");
		}
	});
	
	$("input[name=notifications_check]").click(function(){
		if($(this).is(":checked")){
			$(this).attr("value", "1");
		}
		else if($(this).is(":not(:checked)")){
			$(this).attr("value", "0");
		}
	});
	
	$(document).on('click','#submitNotificationSett',function(e){
		e.preventDefault();

		var storeid =  $("input[name=storeid_notifications_sett]").val();
		var user_email =  $("input[name=email_notifications_sett]").val();
		var user_view =  $("input[name=user_view_notifications_sett]").val();
		var email_notification =  $("input[name=email_notification]").val();
		var notifications_check =  $("input[name=notifications_check]").val();
		var url_action = location.protocol+'//'+location.hostname+'/admin/notifications/xt_notifications.php';
		var loading_image = location.protocol+'//'+location.hostname+'/img/loading.svg';
		
		$.ajax({
			type: "POST",
			url: url_action,
			data: {"storeid":storeid, "user_email":user_email, "user_view":user_view,"email_notification":email_notification,"notifications_check":notifications_check},
			cache: false,
			beforeSend:function(html){
				$(".loading_data_info").html('<div class="text-center"><img src="'+loading_image+'"></div>');
			},
			success: function(html){
				$(".loading_data_info").html(html);
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  console.log(err.Message);
			} 
		});
	});
	//End Notifications
	
	//this makes the current link containing li of class "active"
	$( document ).ready(function() {
		var activePage = window.location.pathname;
		$('.sidebar .nav .nav-link').each(function () {
			var linkPage = $(this).attr("href");
			if (activePage == linkPage) {
				$(this).closest("li").addClass("bg-dark-blue");
				$(this).parentsUntil("#navbarSupportedContent","div.collapse").collapse('show');
				
			}
		});
	});
</script>