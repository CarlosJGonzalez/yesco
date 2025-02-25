var campid = '';
if(getUrlParameter('campid')){
	campid = '&campid='+getUrlParameter('campid');
}

$(function () {
	if( $('#reportrange').length || $('.reportrange').length) {

		if($('#reportrange').length ){
			self = $('#reportrange'); 
			parent = $('#reportrange').parent().parent();
		}else{
			self = $('.reportrange');
			parent = $('.reportrange').parent().parent();
		}
		addCheckbox(parent, 'Compare to');
		addDateRangeHtml(parent);

		if(getUrlParameter('from_compare') && getUrlParameter('to_compare')){
			var from_compare = moment(getUrlParameter('from_compare'));
			var to_compare   = moment(getUrlParameter('to_compare'));
		}else{
			var from_compare = moment().subtract(29, 'days');
			var to_compare   = moment();
		}		

		if(getUrlParameter('compare')){
			$('#compare_range').click();
			init_compare_range(from_compare, to_compare);
			initCompareRabge(from_compare, to_compare);
		}

		$('#compare_range').on('change',function () {
			if (this.checked) {			
				init_compare_range(from_compare, to_compare);
				initCompareRabge(from_compare, to_compare);
		    } else {
		        $('#reportrange_compare').addClass( "d-none" );
		        window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD')+campid;
		    }
		});
	}
});

function init_compare_range(start_compare, end_compare) {
	$('#reportrange_compare span').html(start_compare.format('MMMM D, YYYY') + ' - ' + end_compare.format('MMMM D, YYYY'));
}

function initCompareRabge(start_compare,end_compare){	
	$('#reportrange_compare').removeClass( "d-none" );
	a = moment(start),
	b = moment(end),
	c = b.diff(a,"days")

    $('#reportrange_compare').daterangepicker({
		opens: 'left',
		startDate: start_compare,
		endDate: end_compare,
		ranges: {
		   'Previous year': [moment(start).subtract(1, 'year'), moment(end).subtract(1, 'year')],
		   'Previous period': [moment(start).subtract(c + 1, 'days'), moment(start).subtract(1, 'days')]
		}
	}, function(start_compare, end_compare, label) {
		$('#reportrange_compare span').html(start_compare.format('MMMM D, YYYY') + ' - ' + end_compare.format('MMMM D, YYYY'));
		window.location.href='?compare=1&from_compare='+start_compare.format('YYYY-MM-DD')+'&to_compare='+end_compare.format('YYYY-MM-DD')+'&from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD')+campid;		
	});
}

function addCheckbox(element, name) {
	var holder =  $('<div />', { class:'custom-control custom-checkbox mx-2'}).appendTo(element);
   $('<input />', { class:'custom-control-input',type: 'checkbox', id: 'compare_range', value: name }).appendTo(holder);
   $('<label />', { class:'custom-control-label','for': 'compare_range', text: name }).appendTo(holder);
}

function addDateRangeHtml(element){
	element.append('<div id="reportrange_compare" class="d-none rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">'+
	'<i class="far fa-calendar-alt"></i>&nbsp;<span></span> <i class="fa fa-caret-down"></i></div>');
}


