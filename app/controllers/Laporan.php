<?php defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use \PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;

class Laporan extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('laporan_model', 'lap');
		$this->load->helper('MY_bulan');
	}

	public function index()
	{
		set_status_header(401);
	}

	/*
		Export LAPORAN TAGIHAN BY WILAYAH $ TAHUN
		.Xlsx format
	*/
	public function export_tagihan_by($id_wilayah, $tahun, $format)
	{
		// BLOK WARNA HIJAU JIKA LUNAS
		$styleArrayGreen = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFABFFA0',
				],
			],
		];
		// BLOK WARNA MERAH JIKA DIPUTIHKAN
		$styleArrayRed = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFE24040',
				],
			],
		];
		// BORDER THIN WARNA ABU-ABU
		$styleArrayThinGrey = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => 'FF676767'],
				],
			],
		];

		// Get Template from file
		$template_path = BASEPATH . '../assets/report/template/excel/';
		$spreadsheet = IOFactory::load($template_path . 'laporan_tagihan_tahunan.xlsx');
		// format info file
		$ff = $format == '.xlsx' ? 'Office 2019 XLSX Document' : 'Office 2007 XLS Document';
		// Setting spreadsheet metadata
		$spreadsheet->getProperties()
			->setCreator("POSO Net App")
			->setTitle("LAPORAN PENAGIHAN")
			->setSubject("$ff")
			->setDescription("File ini dibuat otomatis oleh aplikasi POSO Net App.")
			->setKeywords("PhpSpreadsheet")
			->setCategory("LAPORAN");
		$worksheet = $spreadsheet->getActiveSheet();
		// Set PAGE MARGIN
		$worksheet->getPageMargins()->setTop(0.5);
		$worksheet->getPageMargins()->setRight(0);
		$worksheet->getPageMargins()->setLeft(1);
		$worksheet->getPageMargins()->setBottom(0);
		// Get profile perusahaan
		$qa = $this->db->query("SELECT * FROM profil_perusahaan WHERE id_profil = (SELECT MAX(id_profil) FROM profil_perusahaan)")->row();
		// Get tarif dasar utk style merah jika tarif berbeda dari tarif dasar
		// $qd = $this->db->query("SELECT s.option_name, s.option_value AS tarif_dasar  FROM settings s WHERE s.option_name = 'tarif_dasar'")->row();
		// Get wilayah & kolektor by id_wilayah
		$qw = $this->db->query("SELECT wilayah, kode_wilayah FROM wilayah WHERE id_wilayah = $id_wilayah")->row();
		$qk = $this->db->query("SELECT nama_lengkap AS nama_kolektor FROM v_kolektor WHERE wilayah LIKE '%$id_wilayah%' ")->row();
		// Get target setoran by id_wilayah
		$qt = $this->db->query("SELECT SUM(t.tarif) AS target FROM v_pelanggan t WHERE id_wilayah = '$id_wilayah' ")->row();
		// Result pelanggan by id_wilayah
		$qp = $this->db->query("SELECT * FROM v_pelanggan WHERE id_wilayah = $id_wilayah ORDER BY no_pelanggan ASC")->result();

		$column = array('', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P');
		$startFill = 7;
		$x = 1;

		$worksheet->setCellValue("A2", $qa->slogan);
		$worksheet->setCellValue("Q2", $qa->alamat . ' Telp. ' . $qa->telp);
		$worksheet->setCellValue("A4", "Laporan Penagihan " . strtoupper($qw->wilayah));
		$worksheet->setCellValue("Q4", "UPDATE PER : " . date('d-m-Y') . " | DEBT : " . strtoupper($qk->nama_kolektor) . " | TARGET : Rp. " . number_format($qt->target));

		foreach ($qp as $plgn) {
			$worksheet->getStyle("A$startFill:Q$startFill")->applyFromArray($styleArrayThinGrey);
			$worksheet->getStyle("A$startFill:B$startFill")->getFont()->setSize(11);
			$worksheet->getStyle("C$startFill")->getFont()->setSize(10);
			$worksheet->getStyle("D$startFill")->getFont()->setSize(10);
			$worksheet->getStyle("Q$startFill")->getFont()->setSize(9);
			$worksheet->getStyle("A$startFill")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
			$worksheet->setCellValue("A$startFill", "$plgn->no_pelanggan");
			$worksheet->setCellValue("B$startFill", "$plgn->nama_pelanggan");
			$worksheet->setCellValue("C$startFill", "$plgn->nama_paket");
			$worksheet->setCellValue("Q$startFill", "$plgn->telp");

			$worksheet->getStyle("D$startFill")->getNumberFormat()->setFormatCode("#,##0");
			$worksheet->setCellValue("D$startFill", "$plgn->tarif");

			// Search bulan lunas by no_pelanggan
			$qlunas = $this->db->query("SELECT d.no_pelanggan, SUBSTR(d.bulan_penagihan,6,2) AS bln_lunas, d.`status`, d.keterangan, d.tarif, d.remark, d.metode_pembayaran, d.nama_penerima
				FROM v_detail_setoran d
				WHERE YEAR(d.bulan_penagihan) = '$tahun'
				AND d.no_pelanggan = '$plgn->no_pelanggan'
				ORDER BY SUBSTR(d.bulan_penagihan,6,2) ASC ")->result();

			$i = 0;
			foreach ($qlunas as $px) {
				$row2 = $column[(int) $px->bln_lunas] . $startFill;
				if ($px->status == 'Lunas') {
					$blokwarna = $styleArrayGreen;
				} elseif ($px->status == 'Diputihkan') {
					$blokwarna = $styleArrayRed;
				} else {
					$blokwarna = $styleArrayThinGrey;
				}
				$worksheet->getStyle("$row2")->applyFromArray($blokwarna);
				$worksheet->getStyle("$row2")->getFont()->setSize(7);
				$worksheet->setCellValue("$row2", "$x");

				$ket = ($px->remark == 0) ? $px->status . ' : ' . $px->keterangan : $px->keterangan;
				$penerima = ($px->metode_pembayaran != 'kolektor') ? "Metode Pembayaran : $px->metode_pembayaran \nPenerima : $px->nama_penerima" : "";
				$remarks = ($px->tarif != $px->remark) ? "Remark : $px->remark" : "";

				if ($px->keterangan != "" && $px->metode_pembayaran == 'kolektor' && $px->tarif == $px->remark) {
					$komen = $worksheet->getComment("$row2")->getText()->createTextRun("Keterangan : $ket $remarks");
					$worksheet->getStyle("$row2")->getAlignment()->setWrapText(true);
					$komen->getFont()->setSize(7);
				} elseif ($px->keterangan != "" && $px->metode_pembayaran != 'kolektor' && $px->tarif != $px->remark) {
					$komen = $worksheet->getComment("$row2")->getText()->createTextRun("Keterangan : $ket \n$remarks \n$penerima");
					$worksheet->getStyle("$row2")->getAlignment()->setWrapText(true);
					$komen->getFont()->setSize(7);
				} elseif ($px->keterangan != "" && $px->metode_pembayaran != 'kolektor') {
					$komen = $worksheet->getComment("$row2")->getText()->createTextRun("Keterangan : $ket \n$penerima");
					$worksheet->getStyle("$row2")->getAlignment()->setWrapText(true);
					$komen->getFont()->setSize(7);
				} elseif ($px->keterangan == "" && $px->metode_pembayaran != 'kolektor' && $px->tarif != $px->remark) {
					$komen = $worksheet->getComment("$row2")->getText()->createTextRun("$remarks \n$penerima");
					$worksheet->getStyle("$row2")->getAlignment()->setWrapText(true);
					$komen->getFont()->setSize(7);
				} elseif ($px->keterangan == "" && $px->metode_pembayaran == 'kolektor' && $px->tarif != $px->remark) {
					$komen = $worksheet->getComment("$row2")->getText()->createTextRun("$remarks");
					$worksheet->getStyle("$row2")->getAlignment()->setWrapText(true);
					$komen->getFont()->setSize(7);
				}
			}
			$startFill++;
			$x++;
		}
		$ketInfo1 = $startFill + 2;

		$worksheet->getStyle("E$ketInfo1")->applyFromArray($styleArrayThinGrey);
		$worksheet->getStyle("F$ketInfo1")->getFont()->setSize(9);
		$worksheet->setCellValue("F$ketInfo1", "BELUM BAYAR");

		$worksheet->getStyle("I$ketInfo1")->applyFromArray($styleArrayThinGrey);
		$worksheet->getStyle("I$ketInfo1")->applyFromArray($styleArrayRed);
		$worksheet->getStyle("J$ketInfo1")->getFont()->setSize(9);
		$worksheet->setCellValue("J$ketInfo1", "DIPUTIHKAN");

		$worksheet->getStyle("M$ketInfo1")->applyFromArray($styleArrayThinGrey);
		$worksheet->getStyle("M$ketInfo1")->applyFromArray($styleArrayGreen);
		$worksheet->getStyle("N$ketInfo1")->getFont()->setSize(9);
		$worksheet->setCellValue("N$ketInfo1", "LUNAS");

		$worksheet->setTitle(strtoupper($qw->wilayah));

		$file_name = "TAGIHAN#" . strtoupper($qw->wilayah) . "#" . strtoupper($qw->kode_wilayah) . "#" . date('d-m-Y') . ".$format";
		$ucFirstFormat = ucfirst($format);
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "$ucFirstFormat");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $file_name . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	public function exportplgn($id_wilayah, $format)
	{
		// BLOK WARNA HIJAU JIKA LUNAS
		$styleArrayGreen = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFABFFA0',
				],
			],
		];
		// BLOK WARNA MERAH JIKA DIPUTIHKAN
		$styleArrayRed = [
			'font' => [
				'bold' => false,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFE24040',
				],
			],
		];
		// BORDER THIN WARNA ABU-ABU
		$styleArrayThinGrey = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => 'FF676767'],
				],
			],
		];

		// Get Template from file
		$template_path = BASEPATH . '../assets/report/template/excel/';
		$spreadsheet = IOFactory::load($template_path . 'laporan_pelanggan.xlsx');
		// format info file
		$ff = $format == '.xlsx' ? 'Office 2016 XLSX Document' : 'Office 2007 XLS Document';
		// Setting spreadsheet metadata
		$spreadsheet->getProperties()
			->setCreator("POSO Net App")
			->setTitle("LAPORAN PELANGGAN")
			->setSubject("$ff")
			->setDescription("File ini dibuat otomatis oleh aplikasi POSO TV App.")
			->setKeywords("PhpSpreadsheet")
			->setCategory("LAPORAN");
		$worksheet = $spreadsheet->getActiveSheet();
		// Set PAGE MARGIN
		$worksheet->getPageMargins()->setTop(0.5);
		$worksheet->getPageMargins()->setRight(0);
		$worksheet->getPageMargins()->setLeft(1);
		$worksheet->getPageMargins()->setBottom(0);
		// Get profile perusahaan
		$qa = $this->db->query("SELECT * FROM profil_perusahaan WHERE id_profil = (SELECT MAX(id_profil) FROM profil_perusahaan)")->row();
		// Wilayah
		$qw = $this->db->query("SELECT wilayah, kode_wilayah FROM wilayah WHERE id_wilayah = $id_wilayah")->row();
		// Get target setoran by id_wilayah
		$qt = $this->db->query("SELECT SUM(t.tarif) AS target	FROM v_pelanggan t WHERE id_wilayah = '$id_wilayah' ")->row();
		// Result pelanggan by id_wilayah
		$qp = $this->db->query("SELECT no_pelanggan,nama_pelanggan,nama_paket,tarif,status,tgl_instalasi,expired,telp,serial_number,no_ktp,lokasi_map FROM v_pelanggan WHERE id_wilayah = $id_wilayah ORDER BY no_pelanggan ASC")->result();

		$startFill = 6;

		$worksheet->setCellValue("A2", $qa->slogan);
		$worksheet->setCellValue("J2", $qa->alamat . ' Telp. ' . $qa->telp);
		$worksheet->setCellValue("A4", "Laporan Pelanggan Internet " . strtoupper($qw->wilayah));
		$worksheet->setCellValue("J4", "UPDATE PER : " . date('d-m-Y') . " | TARGET : Rp. " . number_format($qt->target));

		foreach ($qp as $plgn) {
			$worksheet->getStyle("A$startFill:Q$startFill")->applyFromArray($styleArrayThinGrey);
			$worksheet->getStyle("A$startFill:B$startFill")->getFont()->setSize(11);
			$worksheet->getStyle("C$startFill:J$startFill")->getFont()->setSize(8);
			$worksheet->setCellValue("A$startFill", "$plgn->no_pelanggan");
			$worksheet->setCellValue("B$startFill", "$plgn->nama_pelanggan");
			$worksheet->setCellValue("C$startFill", "$plgn->nama_paket");
			$worksheet->getStyle("D$startFill")->getNumberFormat()->setFormatCode("[Black][>=5000]#,##0;[Red][==0]#,##0");
			$worksheet->setCellValue("D$startFill", "$plgn->tarif");
			if ($plgn->status == 'NONAKTIF') {
				$worksheet->getStyle("E$startFill")
					->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
			}
			$worksheet->setCellValue("E$startFill", "$plgn->status");
			$spreadsheet->getActiveSheet()->getStyle("F$startFill:G$startFill")
				->getNumberFormat()
				->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
			$worksheet->setCellValue("F$startFill", "$plgn->tgl_instalasi");
			$worksheet->setCellValue("G$startFill", "$plgn->expired");
			$worksheet->setCellValue("H$startFill", "$plgn->telp");
			$worksheet->setCellValue("I$startFill", "$plgn->serial_number");
			$worksheet->setCellValue("J$startFill", " $plgn->no_ktp");
			$worksheet->setCellValue("K$startFill", urldecode("$plgn->lokasi_map"));
			$startFill++;
		}


		$worksheet->setTitle(strtoupper($qw->wilayah));

		$file_name = "PELANGGAN#" . strtoupper($qw->wilayah) . "#" . strtoupper($qw->kode_wilayah) . "#" . date('d-m-Y') . ".$format";
		$ucFirstFormat = ucfirst($format);
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "$ucFirstFormat");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $file_name . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	public function exportplgnAll($format = 'xlsx')
	{
		// BORDER THIN WARNA ABU-ABU
		$styleArrayThinGrey = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => 'FF676767'],
				],
			],
		];

		// Get Template from file
		$template_path = BASEPATH . '../assets/report/template/excel/';
		$spreadsheet = IOFactory::load($template_path . 'laporan_pelanggan.xlsx');
		// format info file
		$ff = $format == '.xlsx' ? 'Office 2016 XLSX Document' : 'Office 2007 XLS Document';
		// Setting spreadsheet metadata
		$spreadsheet->getProperties()
			->setCreator("POSO Net App")
			->setTitle("LAPORAN SEMUA PELANGGAN")
			->setSubject("$ff")
			->setDescription("File ini dibuat otomatis oleh aplikasi POSO TV App.")
			->setKeywords("PhpSpreadsheet")
			->setCategory("LAPORAN");
		$worksheet = $spreadsheet->getActiveSheet();
		// Set PAGE MARGIN
		$worksheet->getPageMargins()->setTop(0.5);
		$worksheet->getPageMargins()->setRight(0);
		$worksheet->getPageMargins()->setLeft(1);
		$worksheet->getPageMargins()->setBottom(0);
		// Get profile perusahaan
		$qa = $this->db->query("SELECT * FROM profil_perusahaan WHERE id_profil = (SELECT MAX(id_profil) FROM profil_perusahaan)")->row();
		// Wilayah
		$qw = $this->db->query("SELECT id_wilayah, wilayah, kode_wilayah FROM wilayah ORDER BY id_wilayah ASC")->result();
		// Get target setoran by id_wilayah
		// $qt = $this->db->query("SELECT SUM(t.tarif) AS target	FROM v_pelanggan t WHERE id_wilayah = '$id_wilayah' ")->row();
		// Result pelanggan by id_wilayah
		// $qp = $this->db->query("SELECT * FROM v_pelanggan WHERE id_wilayah = $id_wilayah ORDER BY no_pelanggan ASC")->result();

		foreach ($qw as $x) {
			$clonedWorksheet = clone $worksheet;
			$clonedWorksheet->setTitle("$x->wilayah");
			$spreadsheet->addSheet($clonedWorksheet);
		}

		foreach ($qw as $w) {
			$worksheet = $spreadsheet->getSheetByName("$w->wilayah");

			$qt = $this->db->query("SELECT SUM(tarif) AS target FROM v_pelanggan WHERE id_wilayah = '$w->id_wilayah' ")->row();
			$qp = $this->db->query("SELECT * FROM v_pelanggan WHERE id_wilayah = $w->id_wilayah ORDER BY no_pelanggan ASC")->result();

			$worksheet->setCellValue("A2", $qa->slogan);
			$worksheet->setCellValue("J2", $qa->alamat . ' Telp. ' . $qa->telp);
			$worksheet->setCellValue("A4", "Laporan Pelanggan Internet " . strtoupper($w->wilayah));
			$worksheet->setCellValue("J4", "UPDATE PER : " . date('d-m-Y') . " | TARGET : Rp. " . number_format($qt->target));

			$startFill = 6;

			foreach ($qp as $plgn) {
				$worksheet->getStyle("A$startFill:J$startFill")->applyFromArray($styleArrayThinGrey);
				$worksheet->getStyle("A$startFill:B$startFill")->getFont()->setSize(11);
				$worksheet->getStyle("C$startFill:J$startFill")->getFont()->setSize(10);
				$worksheet->setCellValue("A$startFill", "$plgn->no_pelanggan");
				$worksheet->setCellValue("B$startFill", "$plgn->nama_pelanggan");
				$worksheet->setCellValue("C$startFill", "$plgn->nama_paket");
				$worksheet->getStyle("D$startFill")->getNumberFormat()->setFormatCode("[Black][>=5000]#,##0;[Red][==0]#,##0");
				$worksheet->setCellValue("D$startFill", "$plgn->tarif");
				if ($plgn->status == 'NONAKTIF') {
					$worksheet->getStyle("E$startFill")
						->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
				}
				$worksheet->setCellValue("E$startFill", "$plgn->status");
				$spreadsheet->getActiveSheet()->getStyle("F$startFill:G$startFill")
					->getNumberFormat()
					->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
				$worksheet->setCellValue("F$startFill", "$plgn->tgl_instalasi");
				$worksheet->setCellValue("G$startFill", "$plgn->expired");
				$worksheet->setCellValue("H$startFill", "$plgn->telp");
				$worksheet->setCellValue("I$startFill", "$plgn->serial_number");
				if (strlen($plgn->lokasi_map) > 5) {
					$maps = urldecode($plgn->lokasi_map);
					$worksheet->getCell("J$startFill")
						->setValue("Ada")
						->getHyperlink()
						->setUrl($maps)
						->setTooltip('Lihat Lokasi');
				} else {
					$worksheet->setCellValue("J$startFill", "Kosong");
				}
				$startFill++;
			}
		}

		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('TEMPLATE')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);

		$file_name = "INET#ALL PELANGGAN#" . date('d-m-Y') . ".$format";

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, ucfirst($format));
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $file_name . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
}
