<?php
if (count($group))
{
	echo '<ul>';
	foreach($group AS $k => $d)
	{
		echo '<li><a href="' . Router::url('media/browser/type:' . $d->type) . '">' . $d->type . '</a> ('.$d->countType.')</li>';
	}
	
	echo '</ul>';
	
}

if (count($list))
{
	echo '<table class="condensed-table bordered-table zebra-striped">
		<tr>
			<th>filename</th>
			<th>filetype</th>
			<th>filesubtype</th>
			<th>filesize</th>
		</tr>';
	foreach($list AS $k => $d)
	{
		if ($d->filesize == 0) { $d->filesize = filesize('./media/' . $d->mime . '/' . $d->name); }
		echo '<tr>
			<td><a href="' . Router::url('media/fileinfo/id:' . $d->id) .'">' . $d->name .'</a></td>
			<td><a href="' . Router::url('media/browser/type:' . $d->type) . '">' . $d->type . '</a></td>
			<td><a href="' . Router::url('media/browser/type:' . $d->type . '/sub:' . $d->subType) . '">'.$d->subType.'</a></td>
			<td>' . _format_bytes($d->filesize) . '</td>
			</tr>';
	}
	echo '</table>';
	
}
?>