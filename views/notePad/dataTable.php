
$(document).ready( function() {
	$.extend( true, $.fn.dataTable.defaults, {
		"sDom": "<'row'<'col-lg-7'l><'col-lg-5'f>r>t<'row'<'col-lg-4'i><'col-lg-8'p>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_",
			"sSearch": "",
			"sEmptyTable": "No data available in table",
			"sInfo": "Showing _START_ - _END_ (_TOTAL_)",
			"sInfoEmpty": "No entries to show"
		}
	});

	var <?=$var_name?> = $('#dataTable-<?=$id?>').dataTable(<?=$setup;?>);


	// SEARCH - Add the placeholder for Search and Turn this into in-line formcontrol
	var search_input = $('#dataTable-<?=$id?>').closest('.dataTables_wrapper').find('div[id$=_filter] input');
	search_input.attr('placeholder', 'Search')
	search_input.addClass('form-control input-sm')

	// SEARCH CLEAR - Use an Icon
	var clear_input = $('#dataTable-<?=$id?>').closest('.dataTables_wrapper').find('div[id$=_filter] a');
	clear_input.html('<i class="icon-remove-circle icon-large"></i>')
	clear_input.css('margin-left', '5px')

	// LENGTH - Inline-Form control
	var length_sel = $('#dataTable-<?=$id?>').closest('.dataTables_wrapper').find('div[id$=_length] select');
	length_sel.addClass('form-control input-sm');

	// LENGTH - Info adjust location
	var length_sel = $('#dataTable-<?=$id?>').closest('.dataTables_wrapper').find('div[id$=_info]');
	length_sel.css('margin-top', '20px')
});

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
return {
"iStart":         oSettings._iDisplayStart,
"iEnd":           oSettings.fnDisplayEnd(),
"iLength":        oSettings._iDisplayLength,
"iTotal":         oSettings.fnRecordsTotal(),
"iFilteredTotal": oSettings.fnRecordsDisplay(),
"iPage":          oSettings._iDisplayLength === -1 ?
0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
"iTotalPages":    oSettings._iDisplayLength === -1 ?
0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
};
}

/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
"bootstrap": {
"fnInit": function( oSettings, nPaging, fnDraw ) {
var oLang = oSettings.oLanguage.oPaginate;
var fnClickHandler = function ( e ) {
e.preventDefault();
if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
fnDraw( oSettings );
}
};

$(nPaging).addClass('pagination').append(
'<div class="row" style="padding-right: 7px;"> <ul class="pager">'+
	'<li class="previous disabled"><a href="#">'+oLang.sPrevious+'</a></li>'+
	'<li class="next disabled"><a href="#">'+oLang.sNext+'</a></li>'+
	'</ul> </div>'
);
var els = $('a', nPaging);
$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
},

"fnUpdate": function ( oSettings, fnDraw ) {
var iListLength = 5;
var oPaging = oSettings.oInstance.fnPagingInfo();
var an = oSettings.aanFeatures.p;
var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

if ( oPaging.iTotalPages < iListLength) {
iStart = 1;
iEnd = oPaging.iTotalPages;
}
else if ( oPaging.iPage <= iHalf ) {
iStart = 1;
iEnd = iListLength;
} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
iStart = oPaging.iTotalPages - iListLength + 1;
iEnd = oPaging.iTotalPages;
} else {
iStart = oPaging.iPage - iHalf + 1;
iEnd = iStart + iListLength - 1;
}

for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                // Add the new list items and their event handlers
                for ( j=iStart ; j<=iEnd ; j++ ) {
                    sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                    $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                        .insertBefore( $('li:last', an[i])[0] )
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                            fnDraw( oSettings );
                        } );
                }

                // Add / remove disabled classes from the static elements
                if ( oPaging.iPage === 0 ) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }

                if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
} );