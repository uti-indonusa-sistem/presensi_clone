<table class="table table-striped table-bordered table-hover" id="dataTables-example">
<thead>

<tr>
<th>No</th>
<th>Matakuliah</th>
<th>Semester</th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
<?php for($a=1;$a<=$i;$a++){?>
<tr>
<td></td>
<td><?php echo $dataJadwal['matkul']; ?></td>
<td><?php echo $value[$a]; ?></td>
</tr>
<?php } ?>
</tbody>
</table>