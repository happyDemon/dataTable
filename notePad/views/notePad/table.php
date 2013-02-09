<table class="<?=$class;?>" cellpadding="0" cellspacing="0" border="0" id="dataTable-<?=$id;?>">
	<thead>
		<tr>
			<?php foreach($heads as $head) {?>
				<th class="<?=$head['class'];?>"><?=$head['title'];?></th>
			<?php }?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="<?=$head_count;?>" class="dataTables_empty"><em>Loading data from server</em></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="<?=$head_count;?>"><button class="btn btn-success pull-right btn-create"><i class="icon-plus-sign icon-white"></i> Create <?=$title;?></button></td>
		</tr>
	</tfoot>
</table>