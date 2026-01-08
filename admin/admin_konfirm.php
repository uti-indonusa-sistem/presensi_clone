<html>
<head>
<script type="text/javascript">
var txt;
var r = confirm("Hapus pertemuan ?");
if (r == true) {
	var url= "admin_hapus_jurnal-<?php echo $_GET['id_jurnal'];?>-<?php echo str_replace("-","_yz_",$_GET['id_kelas']);?>-<?php echo str_replace("-","_yz_",$_GET['id_ptk']);?>.html"; 
	window.location = url;
} else {
  var url="admin_jurnal_perkuliahan-<?php echo $_GET['id_kelas'];?>-<?php echo str_replace("-","_yz_",$_GET['id_ptk']);?>.html";
  window.location = url;
}
</script>
</head>


</html>