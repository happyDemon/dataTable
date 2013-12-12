var data<?=$var_name;?>;
$(document).ready( function() {
	var dataSetup = <?=$setup;?>;
	var dataId = $('#dataTable-<?=$id?>').data('id');

	if (!isNaN(dataId))
	{
		dataSetup.sAjaxSource += '/'+dataId;
	}

	data<?=$var_name;?> = $('#dataTable-<?=$id?>').dataTable(dataSetup);
});