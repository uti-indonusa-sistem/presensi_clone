<?php // if(($_COOKIE['simpreskul_nik']=='') AND ($_COOKIE['simpreskul_admin']=='')){header("Location:login_dosen.html");} ?>
<?php
// Debug via cookie: set document.cookie = "debug_presensi=1; path=/" in browser console
$debug_enabled = 0;
?>

<style>
.btn-check-all {
    padding: 5px 10px;
    margin: 2px;
    font-size: 11px;
    background-color: #5cb85c;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}
.btn-check-all:hover {
    background-color: #4cae4c;
}
.btn-uncheck-all {
    padding: 5px 10px;
    margin: 2px;
    font-size: 11px;
    background-color: #d9534f;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}
.btn-uncheck-all:hover {
    background-color: #c9302c;
}
</style>

<script>
function checkAll(columnClass, checkIt) {
    var inputs = document.querySelectorAll('.' + columnClass);
    for(var i = 0; i < inputs.length; i++){
        if(inputs[i].type == 'checkbox'){
            inputs[i].checked = checkIt;
        }
    }
}

function checkAllStudents(checkIt) {
    var inputs = document.querySelectorAll('input[type="checkbox"]');
    for(var i = 0; i < inputs.length; i++){
        inputs[i].checked = checkIt;
    }
}

// Clear form data on page load to prevent browser form restoration
window.addEventListener('load', function() {
    // Uncheck ALL checkboxes first
    var allInputs = document.querySelectorAll('input[type="checkbox"]');
    for(var i = 0; i < allInputs.length; i++) {
        allInputs[i].checked = false;
    }
    
    // Reset form to server state (not browser cached state)
    var form = document.getElementById('selectForm');
    if(form) {
        form.reset();
    }
    
    // Re-check only those that have checked attribute in HTML
    // This ensures browser restore doesn't override server values
    allInputs = document.querySelectorAll('input[type="checkbox"][checked]');
    for(var i = 0; i < allInputs.length; i++) {
        allInputs[i].checked = true;
    }
});
</script>

<form action="dosen_input_kehadiran2-<?php echo $_GET['id_kelas']?>-<?php echo $_GET['id_ptk']?>.html" method="POST" id="selectForm">

<div style="margin-bottom: 15px;">
    <button type="button" class="btn-check-all" onclick="checkAllStudents(true)">✓ Pilih Semua</button>
    <button type="button" class="btn-uncheck-all" onclick="checkAllStudents(false)">✗ Hapus Semua</button>
    <input type="submit" class="btn btn-primary" value="Simpan Presensi" name="simpan_presensi" style="margin-left:10px;">
</div>

<table border="1" style="border-color:#DDDDDD; border:1px solid #DDDDDD">
    <thead>
        <tr>
            <th width="1%" rowspan="3" style="vertical-align:center"><center>No</center></th>
            <th width="2%" rowspan="3"><center>NIM</center></th>
            <th width="20%" rowspan="3"><center>Nama</center></th>
            <?php
            // Preload pertemuan journals and dates (avoid querying inside loop)
            $id_kls = str_replace("_yz_","-",$_GET['id_kelas']);
            $id_ptk = str_replace("_yz_","-",$_GET['id_ptk']);
            $pertemuanJournals = array();
            $pertemuanDates = array();
            
            // Build query untuk pertemuan
            $cek_kls = cek_gabungan($_GET['id_kelas']);
            // Handle empty cek_gabungan result - if empty, query by xid_kls directly
            if(empty($cek_kls)){
                $cek_kls = "presensi_jurnal_perkuliahan.xid_kls='".$id_kls."'";
            }
            
            $sqlPertemuanStr = "SELECT presensi_jurnal_perkuliahan.pertemuan_ke, presensi_jurnal_perkuliahan.id_jurnal, DATE_FORMAT(presensi_jurnal_perkuliahan.tanggal,'%d-%m-%Y') AS tanggal FROM presensi_jurnal_perkuliahan WHERE (" . $cek_kls . ") AND id_ptk='".$id_ptk."' AND presensi_jurnal_perkuliahan.pertemuan_ke BETWEEN 1 AND 16 ORDER BY pertemuan_ke ASC";
            
            // Debug: show SQL and result
            if($debug_enabled){
                echo "<!-- PERTEMUAN QUERY: ".$sqlPertemuanStr." -->\n";
            }
            
            $sqlPertemuan = mysqli_query($connection, $sqlPertemuanStr);
            if(!$sqlPertemuan){
                echo "<div style='background:#ffcccc; border:1px solid red; padding:8px; margin:10px 0;'>";
                echo "ERROR: Query pertemuan gagal: " . mysqli_error($connection);
                echo "</div>\n";
            } else {
                while($rP = mysqli_fetch_array($sqlPertemuan)){
                    $k = (int)$rP['pertemuan_ke'];
                    $pertemuanJournals[$k] = trim((string)$rP['id_jurnal']); // Normalize to string
                    $pertemuanDates[$k] = $rP['tanggal'];
                }
                if($debug_enabled){
                    echo "<!-- PERTEMUAN RESULT: ".count(array_filter($pertemuanJournals))." rows -->\n";
                }
            }
            for($i=1;$i<=16;$i++){
                $dt = isset($pertemuanDates[$i]) ? $pertemuanDates[$i] : '';
            ?>
                <th width="3%" style="transform: rotate(-45deg); vertical-align:center">
                    <font style="font-size:12px;"><center><?php echo $dt; ?></center></font>
                </th>
            <?php } ?>
            <th rowspan="3" width="1%" style="transform: rotate(-45deg); vertical-align:center"><center><font style="font-size:7pt">Presentase</font><br>%</center></th>
        </tr>
        <tr>
            <?php for($i=1;$i<=16;$i++){?>
                <th width="3%"><center><?php echo "<font style='font-size:12px'><center>$i</center></font>"; ?></center></th>
            <?php } ?>
        </tr>
        <tr>
            <?php
            // Preload attendance and present counts for all journals to avoid per-student queries
            $allJurnalIds = array_values($pertemuanJournals);
            if (empty($allJurnalIds)) {
                $allJurnalIds = array(0);
            }
            $idsList = implode(',', array_map('intval',$allJurnalIds));
            $attendance = array();
            $presentCount = array();
            if (!empty($allJurnalIds) && $allJurnalIds[0] != 0) {
                // Match attendance by id_jurnal and id_ptk (filter by current lecturer)
                // Normalize nim to handle string/int variants
                $sqlAttend = mysqli_query($connection, "SELECT nim, id_jurnal FROM presensi_rekap WHERE id_jurnal IN (".$idsList.") AND id_ptk='".$id_ptk."'");
                if($sqlAttend) while($a = mysqli_fetch_array($sqlAttend)){
                    $jid = isset($a['id_jurnal']) ? trim((string)$a['id_jurnal']) : '';
                    $nimRaw = isset($a['nim']) ? trim((string)$a['nim']) : '';
                    if($jid === '' || $nimRaw === '') continue;
                    // store raw and int-cast versions
                    $attendance[$jid][$nimRaw] = true;
                    $nimInt = (string)intval($nimRaw);
                    if($nimInt !== $nimRaw) $attendance[$jid][$nimInt] = true;
                    // use raw as canonical presentCount key
                    if (!isset($presentCount[$nimRaw])) $presentCount[$nimRaw] = 0;
                    $presentCount[$nimRaw]++;
                }
            }
            $jumlahAllPertemuan = count(array_filter($pertemuanJournals));
            // Optional debug output: append ?debug_presensi=1 to URL to inspect loaded journals and attendance
            if((isset($_GET['debug_presensi']) && $_GET['debug_presensi']=='1') || (isset($_COOKIE['debug_presensi']) && $_COOKIE['debug_presensi']=='1')){
                $debugOutput = '';
                $debugOutput .= "pertemuanJournals:\n".print_r($pertemuanJournals,true)."\n";
                $debugOutput .= "pertemuanDates:\n".print_r($pertemuanDates,true)."\n";
                $debugOutput .= "idsList: ".$idsList."\n";
                $debugOutput .= "attendance:\n".print_r($attendance,true)."\n";
                $debugOutput .= "presentCount:\n".print_r($presentCount,true)."\n";
                // also fetch raw presensi_rekap rows for these journals
                $debugOutput .= "presensi_rekap rows:\n";
                if(!empty($idsList)){
                    $sqlDbg = mysqli_query($connection, "SELECT * FROM presensi_rekap WHERE id_jurnal IN (".$idsList.")");
                    if($sqlDbg){
                        while($rDbg = mysqli_fetch_assoc($sqlDbg)){
                            $debugOutput .= print_r($rDbg,true)."\n";
                        }
                    } else {
                        $debugOutput .= "presensi_rekap query failed\n";
                    }
                }
                // write to file for offline inspection
                @file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'debug_presensi.txt',$debugOutput);
                echo '<pre>'.htmlspecialchars($debugOutput).'</pre>';
                exit;
            }
            // inline simple check when debug enabled
            if($debug_enabled){
                echo "<!-- DEBUG: jumlahAllPertemuan=".$jumlahAllPertemuan." idsList=".$idsList." attendance rows: ".count($attendance)." -->\n";
            }
            // Always show visible debug info
            if($debug_enabled){
                echo "<div style='background:#ffffcc; border:1px solid orange; padding:8px; margin:10px 0; font-size:11px;'>";
                echo "id_kls: ".$id_kls." | id_ptk: ".$id_ptk." | ";
                echo "Pertemuan terdeteksi: ".count(array_filter($pertemuanJournals))." | ";
                echo "ID Jurnal: ".implode(", ",$pertemuanJournals)." | ";
                echo "Attendance rows: ".count($attendance);
                echo "</div>\n";
            }
            $presentase = array();
            if ($jumlahAllPertemuan>0){
                foreach($presentCount as $nimx=>$cntx){
                    $presentase[$nimx] = number_format((($cntx/$jumlahAllPertemuan)*100),2)." &nbsp";
                }
            }
            $class="A";
            for($t=1;$t<=16;$t++){
                $class++;
                $idj = isset($pertemuanJournals[$t]) ? $pertemuanJournals[$t] : '';
                if($idj!=''){
                    echo "<td style='padding:5px;'><center>";
                    echo "<button type='button' class='btn-check-all' onclick=\"checkAll('{$class}', true); return false;\" title='Pilih semua mahasiswa untuk pertemuan ini'>✓</button>";
                    echo "<button type='button' class='btn-uncheck-all' onclick=\"checkAll('{$class}', false); return false;\" title='Hapus semua mahasiswa untuk pertemuan ini'>✗</button>";
                    echo "</center></td>";
                }else{
                    echo "<td></td>";
                }
            }
            ?>
        </tr>
    </thead>
    <tbody>
    <?php
    $sql=mysqli_query($connection,"SELECT viewNilai.*,wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM viewNilai 
        RIGHT JOIN wsia_mahasiswa_pt ON viewNilai.xid_reg_pd=wsia_mahasiswa_pt.xid_reg_pd
        LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
        WHERE viewNilai.vid_kls='".str_replace("_yz_","-",$_GET['id_kelas'])."' ORDER BY wsia_mahasiswa_pt.nipd ASC
        ");
    $no=0;
    if($sql) while($data=mysqli_fetch_array($sql)){
        $no++;
    ?>
        <tr style='background-color:white;'>
            <td><?php echo"<font style='font-size:12px'><center>$no</center></font>"?></td>
            <td><?php echo "<font style='font-size:12px'><center>$data[nipd]</center></font>"?></td>
            <td><?php echo "<font style='font-size:12px; padding-left:2px'>$data[nm_pd]</font>"?></td>
            <?php 
            $class="A";
            for($i=1;$i<=16;$i++){
                $class++;
                $idj = isset($pertemuanJournals[$i]) ? $pertemuanJournals[$i] : '';
            ?>
                <td><center>
                <?php 
                if($idj!=''){
                    $studentKey = trim((string)$data['nipd']);
                    $checked = '';
                    // Debug: Log the values being checked
                    if($debug_enabled && $i==1) {
                        echo "<!-- DEBUG ROW: idj=$idj, studentKey=$studentKey, attendance[$idj] exists: ".(isset($attendance[$idj]) ? "yes" : "no")." -->\n";
                        if(isset($attendance[$idj])) {
                            echo "<!-- attendance[$idj] keys: ".implode(", ", array_keys($attendance[$idj]))." -->\n";
                        }
                    }
                    // Try multiple normalized forms to match stored attendance
                    if(isset($attendance[$idj]) && !empty($attendance[$idj])){
                        if($attendance[$idj][$studentKey] == '1'){
                            $checked = 'checked';
                        } 
                        // else {
                        //     // Try int-cast version
                        //     $studentKeyInt = (string)intval($studentKey);
                        //     if($studentKeyInt !== $studentKey && isset($attendance[$idj][$studentKeyInt])){
                        //         $checked = 'checked';
                        //     }
                        // }
                    }
                ?>
                    <input type="checkbox" class="<?php echo $class; ?>" name="<?php echo $i."-".$studentKey; ?>" value="on" <?php echo $checked; ?> title="Mahasiswa: <?php echo htmlspecialchars($studentKey); ?>" />
                <?php } ?>
                </center></td>
            <?php 
            }
            // calculate percentage using present counts
            $studentKey = trim((string)$data['nipd']);
            $cnt = 0;
            if($studentKey !== '' && isset($presentCount[$studentKey])) $cnt = $presentCount[$studentKey];
            else {
                // try int-cast version
                $studentKeyInt = (string)intval($studentKey);
                if(isset($presentCount[$studentKeyInt])) $cnt = $presentCount[$studentKeyInt];
            }
            $pct = ($jumlahAllPertemuan) ? number_format((($cnt/$jumlahAllPertemuan)*100),2)." &nbsp" : ""; ?>
            <td style="text-align:right;"><font style="font-size:12px;"><?php echo $pct; ?></font></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br>
<input type="submit" class="btn btn-primary" value="Simpan Presensi" name="simpan_presensi" style="margin-top:10px;">
</form>
