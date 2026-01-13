<?php

include_once "../koneksi.php";


function lihat_kehadiran_checked($nim, $id_jurnal, $id_ptk)
{
	global $connection;
	$sql = mysqli_query($connection, "SELECT presensi_rekap.*,presensi_jurnal_perkuliahan.* FROM presensi_rekap 
	LEFT JOIN presensi_jurnal_perkuliahan ON presensi_rekap.id_jurnal=presensi_jurnal_perkuliahan.id_jurnal
	WHERE
	presensi_rekap.id_jurnal='" . $id_jurnal . "'
	AND presensi_rekap.nim='" . $nim . "'
	AND presensi_rekap.id_ptk='" . str_replace("_yz_", "-", $id_ptk) . "'
 ");

	$jumlah = mysqli_num_rows($sql);
	if ($jumlah == 0) {
		return "<font color='red'><b>X</b></font>";
	} else {
		return "<b>&radic;</b>";
	}
}

function view_kelas($id_kls, $kolom)
{
	global $connection;
	$sql = mysqli_query($connection, "SELECT*FROM viewKelasKuliah WHERE xid_kls='" . str_replace("_yz_", "-", $id_kls) . "' AND id_ptk='" . $_COOKIE['simpreskul_id_ptk'] . "'");
	$data = mysqli_fetch_array($sql);

	if ($kolom == 'nm_matkul') {

		return $data['nm_mk'];

	} else if ($kolom == 'prodi-kelas') {
		$sqlCekGabungan = mysqli_query($connection, "SELECT * FROM presensi_kelas_gabungan WHERE xid_kls='" . str_replace("_yz_", "-", $id_kls) . "'");
		$jumlahCekGabungan = mysqli_num_rows($sqlCekGabungan);
		$dataCekGabungan = mysqli_fetch_array($sqlCekGabungan);
		if ($jumlahCekGabungan > 0) {

			$prodikelas = "";
			$sqlCekGabungan2 = mysqli_query($connection, "SELECT * FROM presensi_kelas_gabungan WHERE id_gabungan='" . $dataCekGabungan['id_gabungan'] . "'");
			while ($dataCekGabungan2 = mysqli_fetch_array($sqlCekGabungan2)) {
				$sqlKelasGabungan = mysqli_query($connection, "SELECT*FROM viewKelasKuliah WHERE xid_kls='" . $dataCekGabungan2['xid_kls'] . "'");
				$dataKelasGabungan = mysqli_fetch_array($sqlKelasGabungan);
				$prodikelas .= $dataKelasGabungan['nm_lemb'] . " / " . $dataKelasGabungan['nm_kls'] . "<br>";

			}

			return $prodikelas;

		} else {
			return $data['nm_lemb'] . " / " . $data['nm_kls'];
		}

	} else if ($kolom == 'prodi') {
		$dataProdi = explode("-", $data['progdi']);
		$prodi = $dataProdi[0];
		if ($prodi == 'A') {
			$prodi = "Teknologi Otomotif";
		} else if ($prodi == 'B') {
			$prodi = "Sistem Informasi";
		} else if ($prodi == 'C') {
			$prodi = "Komunikasi Massa";
		} else if ($prodi == 'D') {
			$prodi = "Perhotelan";
		} else if ($prodi == 'E') {
			$prodi = "Farmasi";
		} else if ($prodi == 'F') {
			$prodi = "Manajemen Informasi Kesehatan";
		}
		return $prodi;
	} else if ($kolom == 'kd_prodi') {
		$dataProdi = explode("-", $data['progdi']);
		$prodi = $dataProdi[0];
		return $prodi;
	} else if ($kolom == 'nama_dosen') {

		$sqlDosen = mysqli_query($connection, "SELECT nm_ptk FROM wsia_dosen WHERE wsia_dosen.xid_ptk='" . $data['id_ptk'] . "'");
		$dataDosen = mysqli_fetch_array($sqlDosen);
		return $dataDosen['nm_ptk'];

	} else if ($kolom == 'tahun_akademik') {

		$tahunAkademik = substr($data['id_smt'], 0, 4);
		$tahunAkademik .= "-" . ($tahunAkademik + 1);
		if (substr($data['id_smt'], 4, 1) == '1') {
			$tahunAkademik .= " Ganjil";
		} else if (substr($data['id_smt'], 4, 1) == '2') {
			$tahunAkademik .= " Genap";
		}

		return $tahunAkademik;


	} else if ($kolom == 'nik') {
		return $data['dosen'];
	} else if ($kolom == 'smt') {
		$sqlMataKuliah = mysqli_query($connection, "SELECT smt FROM wsia_mata_kuliah_kurikulum WHERE id_mk='" . $id_kls . "'");
		$dataMataKuliah = mysqli_fetch_array($sqlMataKuliah);
		return $dataMataKuliah['smt'];
		;
	} else {
		return $data[$kolom];
	}
}



function presentase_presensi($nim, $id_kls, $id_ptk)
{
	global $connection;
	$sqlAllPertemuan = mysqli_query($connection, "SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE " . cek_gabungan($id_kls) . "
	AND id_ptk='" . str_replace("_yz_", "-", $id_ptk) . "'");
	$jumlahAllPertemuan = mysqli_num_rows($sqlAllPertemuan);


	$sql = mysqli_query($connection, "SELECT presensi_rekap.id_absensi 
		FROM presensi_rekap 
		LEFT JOIN presensi_jurnal_perkuliahan ON presensi_rekap.id_jurnal=presensi_jurnal_perkuliahan.id_jurnal
		WHERE 
		" . cek_gabungan($id_kls) . "
		AND presensi_rekap.id_ptk='" . str_replace("_yz_", "-", $id_ptk) . "'
		AND presensi_rekap.nim='" . $nim . "'
		");

	$jumlah = mysqli_num_rows($sql);

	if ($jumlahAllPertemuan != 0) {
		$presentaseKehadiran = number_format((($jumlah / $jumlahAllPertemuan) * 100), 2);
		$data = explode(".", $presentaseKehadiran);


		if (substr($data[1], 0, 2) == '00') {
			$desimal = "<font color='white'>_</font>";
		} else {
			$desimal = "." . $data[1] . "<font color='white'>_</font>";
		}

		return $data[0] . $desimal;
	} else {
		return "";
	}
}


function cek_gabungan($id_kelas)
{
	global $connection;
	$sqlCekKelasGabungan = mysqli_query($connection, "SELECT id_gabungan FROM presensi_kelas_gabungan WHERE xid_kls='" . str_replace("_yz_", "-", $id_kelas) . "'");
	$dataKelasGabungan = mysqli_fetch_array($sqlCekKelasGabungan);
	$kondisiId_kls = "";
	$y = 1;
	if (!$dataKelasGabungan)
		return "presensi_jurnal_perkuliahan.xid_kls=" . str_replace("_yz_", "-", $id_kelas);
	$sqlCekKelasGabungan2 = mysqli_query($connection, "SELECT xid_kls FROM presensi_kelas_gabungan WHERE id_gabungan='" . $dataKelasGabungan['id_gabungan'] . "'");
	while ($dataCekKelasGabungan2 = mysqli_fetch_array($sqlCekKelasGabungan2)) {
		if ($y == 1) {
			$kondisiId_kls = "presensi_jurnal_perkuliahan.xid_kls=" . $dataCekKelasGabungan2['xid_kls'];
			$y++;
		} else {
			$kondisiId_kls .= " OR presensi_jurnal_perkuliahan.xid_kls=" . $dataCekKelasGabungan2['xid_kls'];
			$y++;
		}
	}

	if ($kondisiId_kls == '') {
		$kondisiId_kls = "presensi_jurnal_perkuliahan.xid_kls=" . str_replace("_yz_", "-", $id_kelas);
	} else {
		$kondisiId_kls = "(" . $kondisiId_kls . ")";
	}

	return ($kondisiId_kls);
}


function hitung_jumlah_pertemuan($id_kls, $id_ptk)
{
	global $connection;
	$sqlAllPertemuan = mysqli_query($connection, "SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE " . cek_gabungan($id_kls) . "
	AND id_ptk='" . str_replace("_yz_", "-", $id_ptk) . "'");
	return mysqli_num_rows($sqlAllPertemuan);
}


function lihat_kehadiran($nim, $matkul, $thn_akademik, $pertemuan_ke)
{
	$sql = mysql_query("SELECT presensi_rekap.*,presensi_jurnal.* FROM presensi_rekap 
	LEFT JOIN presensi_jurnal ON presensi_rekap.id_jurnal=presensi_jurnal.id_jurnal
	WHERE
	presensi_jurnal.matkul='" . $matkul . "'
	AND presensi_jurnal.thn_akademik='" . $thn_akademik . "'
	AND presensi_jurnal.pertemuan_ke='" . $pertemuan_ke . "'
	AND presensi_rekap.nim='" . $nim . "'
 ");

	$jumlah = mysql_num_rows($sql);
	if ($jumlah == 0) {
		return "<font color='red'><b>X</b></font>";
	} else {
		//return "<b>&#8730</b>";
		return "<b>&radic;</b>";
	}
}







function nama_dosen($nik)
{
	$sql = mysql_query("SELECT nama FROM simpeg_pegawai WHERE nik='" . $nik . "'");
	$data = mysql_fetch_array($sql);
	return $data['nama'];
}

function mata_kuliah($kd_matkul, $kolom)
{
	$sql = mysql_query("SELECT $kolom FROM siakad_matkul WHERE kd_matkul='" . $kd_matkul . "'");
	$data = mysql_fetch_array($sql);
	return $data[$kolom];
}

function konversi_bulan($bulan)
{
	if ($bulan == '01') {
		return "Januari";
	} else if ($bulan == '02') {
		return "Februari";
	} else if ($bulan == '03') {
		return "Maret";
	} else if ($bulan == '04') {
		return "April";
	} else if ($bulan == '05') {
		return "Mei";
	} else if ($bulan == '06') {
		return "Juni";
	} else if ($bulan == '07') {
		return "Juli";
	} else if ($bulan == '08') {
		return "Agustus";
	} else if ($bulan == '09') {
		return "September";
	} else if ($bulan == '10') {
		return "Oktober";
	} else if ($bulan == '11') {
		return "Nopember";
	} else if ($bulan == '12') {
		return "Desember";
	}
}

function tahun_angkatan($tahun_akademik, $semester, $prodi, $kelas)
{
	$tahun = explode('-', $tahun_akademik);
	$tahun_belakang = $tahun[1];


	if (($semester == 1) OR ($semester == 2)) {
		$tahun_ke = 1;
	} else if (($semester == 3) OR ($semester == 4)) {
		$tahun_ke = 2;
	} else if (($semester == 5) OR ($semester == 6)) {
		$tahun_ke = 3;
	} else if (($semester == 7) OR ($semester == 8)) {
		$tahun_ke = 4;
	}

	$angkatan = $tahun_belakang - $tahun_ke;
	$sqlNamaPA = mysql_query("SELECT simpeg_pegawai.nama FROM siakad_kelas LEFT JOIN simpeg_pegawai ON siakad_kelas.pa=simpeg_pegawai.nik
	WHERE siakad_kelas.angkatan='" . $angkatan . "' 
	AND siakad_kelas.progdi='" . $prodi . "'
	AND siakad_kelas.kelas='" . $kelas . "'");

	$dataNamaPA = mysql_fetch_array($sqlNamaPA);

	return $dataNamaPA['nama'];



}


function view_jadwal($id_jadwal, $kolom)
{
	;
	$sql = mysql_query("SELECT*FROM siakad_jadwal WHERE id_jadwal='" . $id_jadwal . "'");
	$data = mysql_fetch_array($sql);
	if ($kolom == 'nm_matkul') {
		$sqlMatkul = mysql_query("SELECT nm_matkul FROM siakad_matkul WHERE kd_matkul='" . $data['matkul'] . "'");
		$dataMatkul = mysql_fetch_array($sqlMatkul);
		return $dataMatkul[$kolom];
	} else if ($kolom == 'sks') {
		$sqlMatkul = mysql_query("SELECT sks FROM siakad_matkul WHERE kd_matkul='" . $data['matkul'] . "'");
		$dataMatkul = mysql_fetch_array($sqlMatkul);
		return $dataMatkul[$kolom];
	} else if ($kolom == 'kelas') {
		$dataProdi = explode("-", $data['progdi']);
		return $dataProdi[1];
	} else if ($kolom == 'prodi') {
		$dataProdi = explode("-", $data['progdi']);
		$prodi = $dataProdi[0];
		if ($prodi == 'A') {
			$prodi = "Mesin Otomotif";
		} else if ($prodi == 'B') {
			$prodi = "Manajemen Informatika";
		} else if ($prodi == 'C') {
			$prodi = "Komunikasi Massa";
		} else if ($prodi == 'D') {
			$prodi = "Perhotelan";
		} else if ($prodi == 'E') {
			$prodi = "Farmasi";
		} else if ($prodi == 'F') {
			$prodi = "Manajemen Informasi Kesehatan";
		}
		return $prodi;
	} else if ($kolom == 'kd_prodi') {
		$dataProdi = explode("-", $data['progdi']);
		$prodi = $dataProdi[0];
		return $prodi;
	} else if ($kolom == 'nama_dosen') {
		$sqlDosen = mysql_query("SELECT*FROM simpeg_pegawai WHERE nik='" . $data['dosen'] . "'");
		$dataDosen = mysql_fetch_array($sqlDosen);
		return $dataDosen['nama'];
		;
	} else {
		return $data[$kolom];
	}
}

function konversi_hari($hari)
{

	if ($hari == 'Mon') {
		return "Senin";
	} else if ($hari == 'Tue') {
		return "Selasa";
	} else if ($hari == 'Wed') {
		return "Rabu";
	} else if ($hari == 'Thu') {
		return "Kamis";
	} else if ($hari == 'Fri') {
		return "Jumat";
	} else if ($hari == 'Sat') {
		return "Sabtu";
	} else if ($hari == 'Sun') {
		return "Minggu";
	}


}


function konversi_prodi($prodi)
{
	if ($prodi == 'A') {
		$prodi = "Mesin Otomotif";
	} else if ($prodi == 'B') {
		$prodi = "Manajemen Informatika";
	} else if ($prodi == 'C') {
		$prodi = "Komunikasi Massa";
	} else if ($prodi == 'D') {
		$prodi = "Perhotelan";
	} else if ($prodi == 'E') {
		$prodi = "Farmasi";
	} else if ($prodi == 'F') {
		$prodi = "Manajemen Informasi Kesehatan";
	}

	return $prodi;


}

function konversi_prodi_singkatan($prodi)
{
	if ($prodi == 'A') {
		$prodi = "MO";
	} else if ($prodi == 'B') {
		$prodi = "MI";
	} else if ($prodi == 'C') {
		$prodi = "KM";
	} else if ($prodi == 'D') {
		$prodi = "HT";
	} else if ($prodi == 'E') {
		$prodi = "Far";
	} else if ($prodi == 'F') {
		$prodi = "MIK";
	}

	return $prodi;


}

function jumlah_mahasiswa($id_jurnal)
{
	global $connection;
	$sql = mysqli_query($connection, "SELECT id_absensi FROM presensi_rekap WHERE id_jurnal='" . $id_jurnal . "' ");
	$jumlah = mysqli_num_rows($sql);

	return $jumlah;
}

function singkatan_matakuliah($kode_matkul)
{
	$sql = mysql_query("SELECT nm_matkul,nm_matkul_singkatan FROM presensi_singkatan_matkul WHERE kd_matkul='" . $kode_matkul . "' ");
	$data = mysql_fetch_array($sql);
	if ($data['nm_matkul_singkatan'] == '') {
		return $data['nm_matkul'];
	} else {
		return $data['nm_matkul_singkatan'];
	}
}

function lihat_honor($nik, $kelasAB, $gabungan)
{
	$sql = mysql_query("SELECT*FROM presensi_honor_mengajar WHERE nik='" . $nik . "'");
	$data = mysql_fetch_array($sql);

	if ($data['metode'] == "pertemuan") {
		return $data['pagi'];
	} else if ($data['metode'] == "SKS") {
		if (($kelasAB == "1") && ($gabungan == "1")) {
			$jumlah = $data['pagi'];
		} else if (($kelasAB == "1") && ($gabungan >= "2")) {
			$jumlah = $data['gabungan_pagi'];
		} else if (($kelasAB == "2") && ($gabungan == "1")) {
			$jumlah = $data['petang'];
		} else if (($kelasAB == "2") && ($gabungan >= "2")) {
			$jumlah = $data['gabungan_petang'];
		}
		return $jumlah;
	}
	//pagi	petang	gabungan_pagi	gabungan_petang	transport	metode
}


function hitung_honor($nik, $kelasAB, $gabungan, $barisExcel, $pilihanKelas, $matkul)
{

	if ($kelasAB == $pilihanKelas) {

		if ($kelasAB == "1") {
			$sks = "F" . $barisExcel;
			$total_hari = "O" . $barisExcel;
		} else {
			$total_hari = "X" . $barisExcel;
		}

		$sql = mysql_query("SELECT*FROM presensi_honor_mengajar WHERE nik='" . $nik . "'");
		$data = mysql_fetch_array($sql);

		if ($data['metode'] == "pertemuan") {
			return "=" . $data['pagi'] . "*" . $total_hari;
		} else if ($data['metode'] == "SKS") {
			if ($kelasAB == "1") {
				return "=" . $sks . "*" . $total_hari . "*" . lihat_honor($nik, $kelasAB, $gabungan);
			} else {
				return "=" . $total_hari . "*" . lihat_honor($nik, $kelasAB, $gabungan);
			}
			//Jika data gaji belum diisi
		} else if ($data['metode'] == "SKS-far") {

			$sqlCekTeoriPraktikum = mysql_query("SELECT*FROM presensi_singkatan_matkul WHERE kd_matkul='" . $matkul . "'");
			$dataCekTeoriPraktikum = mysql_fetch_array($sqlCekTeoriPraktikum);

			$sqlHonor = mysql_query("SELECT teori,praktik FROM presensi_honor_mengajar WHERE nik='" . $nik . "'");
			$dataHonor = mysql_fetch_array($sqlHonor);
			if ($dataCekTeoriPraktikum['praktikum'] == "0") {
				return "=F" . $barisExcel . "*" . $total_hari . "*" . $dataHonor['teori'];
			} else if ($dataCekTeoriPraktikum['praktikum'] == "1") {
				return "=F" . $barisExcel . "*" . $total_hari . "*" . $dataHonor['praktik'];
			}

		}

	} else {
		return "";
	}
}