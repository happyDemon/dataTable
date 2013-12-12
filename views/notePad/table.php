<div class="table-responsive">
	<table class="table <?=$class;?>" id="dataTable-<?=$id;?>"<? if($data != null) echo ' data-id="'.$data.'"';?>>
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
			<td colspan="<?=$head_count;?>"><button class="btn btn-success pull-right btn-create"><i class="fa fa-plus-circle"></i> <?=ucfirst(Inflector::singular(Inflector::humanize($title)));?></button></td>
		</tr>
		</tfoot>
	</table>
</div>