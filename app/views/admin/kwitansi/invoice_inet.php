<?php
/*
  PRINTING REQUIRMENT
  GOOGLE CHROME : 
    - PAPER SIZE = LEGAL 8.5"x14" 22x36cm
    - SCALE = CUSTOM 100
*/
class PDF extends FPDF
{
  var $angle = 0;

  public function Terbilang($satuan)
  {
    $huruf = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
    if ($satuan < 12) {
      return ' ' . $huruf[$satuan];
    } elseif ($satuan < 20) {
      return ' ' . $huruf[$satuan - 10] . 'Belas ';
    } elseif ($satuan < 100) {
      return '' . $huruf[$satuan / 10] . ' Puluh ' . $huruf[$satuan % 10];
    } elseif ($satuan < 200) {
      return ' Seratus ' . $this->Terbilang($satuan - 100);
    } elseif ($satuan < 1000) {
      return $this->Terbilang($satuan / 100) . ' Ratus ' . $this->Terbilang($satuan % 100) . ' ';
    } elseif ($satuan < 2000) {
      return ' Seribu' . $this->Terbilang($satuan - 1000);
    } elseif ($satuan < 1000000) {
      return $this->Terbilang($satuan / 1000) . ' Ribu' . $this->Terbilang($satuan % 1000);
    } elseif ($satuan < 1000000000) {
      return $this->Terbilang($satuan / 1000000) . 'Juta ' . $this->Terbilang($satuan % 1000000);
    } elseif ($satuan <= 1000000000) {
      echo 'Maaf, tidak dapat diproses karena jumlah uang terlalu besar';
    }
  }


  function Rotate($angle, $x = -1, $y = -1)
  {
    if ($x == -1)
      $x = $this->x;
    if ($y == -1)
      $y = $this->y;
    if ($this->angle != 0)
      $this->_out('Q');
    $this->angle = $angle;
    if ($angle != 0) {
      $angle *= M_PI / 180;
      $c = cos($angle);
      $s = sin($angle);
      $cx = $x * $this->k;
      $cy = ($this->h - $y) * $this->k;
      $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
    }
  }

  function _endpage()
  {
    if ($this->angle != 0) {
      $this->angle = 0;
      $this->_out('Q');
    }
    parent::_endpage();
  }

  function Watermark($x, $y, $txt, $angle)
  {
    //Put the watermark
    $this->SetFont('Arial', 'B', 35);
    $this->SetTextColor(255, 192, 203);
    $this->RotatedText($x, $y, $txt, $angle);
    $this->SetTextColor(0, 0, 0);
  }

  function RotatedText($x, $y, $txt, $angle)
  {
    //Text rotated around its origin
    $this->Rotate($angle, $x, $y);
    $this->Text($x, $y, $txt);
    $this->Rotate(0);
  }

  function NomorPelanggan($no_pelanggan, $x, $y)
  {
    $this->SetFont('Arial', 'B', 20);
    $this->SetTextColor(0, 0, 0);

    $this->Rotate(0, $x, $y);
    $this->Text($x, $y, $no_pelanggan);
    $this->Rotate(0);
  }

  function Penerima($x, $y)
  {
    $this->SetFont('Arial', '', 8);
    $this->SetTextColor(0, 0, 0);

    $this->Rotate(0, $x, $y);
    $this->Text($x, $y, '   Penerima');
    $this->Rotate(0);
  }

  function Underscore($x, $y, $text=false)
  {
    $this->SetFont('Arial', '', 8);
    $this->SetTextColor(0, 0, 0);
    $this->Rotate(0, $x, $y);
    $this->Text($x, $y + 5,  ($text ? ' ' . $text : '') . '____________');
    $this->Rotate(0);
  }

  function Keterangan($x, $y, $keterangan)
  {
    $this->SetFont('Arial', 'B', 10);
    $this->SetTextColor(0, 0, 0);
    $this->Rotate(0, $x, $y);
    $this->Text($x, $y + 5, $keterangan);
    $this->Rotate(0);
    $this->SetTextColor(0, 0, 0);
  }


  function Kop($cust, $company, $terms, $rekening, $logo)
  {
    $a = 0;
    $i = 0;
    $y = $GLOBALS['marginY'];
    $geserx = 5; //44
    foreach ($cust as $plgn) {

      $namaLengkapTrim = (strlen($plgn['nama_pelanggan']) > 30) ? substr($plgn['nama_pelanggan'], 0, 19) . '-' : $plgn['nama_pelanggan'];
      $a++;
      $i++;
      $dx = 5 + $geserx + 10; //default x
      $gxl = 15 + $geserx + 4 + 8; // default gambar x kiri
      $gxr = 130 + $geserx + 46; // default gambar x kanan

      $xkanan = 50 + $geserx + 20;
      $xkanan1 = 105 + $geserx + 20;
      $xkanan2 = 155 + $geserx + 20;
      $xkiri = 55 + $geserx + 10; // 55
      $xkiri2 = 25 + $geserx + 10; // 25

      if ($a == 1) {
        $this->setY($GLOBALS['marginY']) + 1;
        $yg1 = $GLOBALS['marginY'] + 29 - 1;
        $yg2 = $GLOBALS['marginY'] + 25 - 12 - 1;
      }
      if ($a == 2) {
        $y = $GLOBALS['marginY'] + 83 - 0.5;
        $this->setY($y);
        $yg1 = $GLOBALS['marginY'] + 112 - 0.5 - 1;
        $yg2 = $GLOBALS['marginY'] + 108 - 12.5 - 1;
      }
      if ($a == 3) {
        $y = $GLOBALS['marginY'] + 165;
        $this->setY($y);
        $yg1 = $GLOBALS['marginY'] + 195 - 1 - 1;
        $yg2 = $GLOBALS['marginY'] + 191 - 13 - 1;
      }
      if ($a == 4) {
        $y = $GLOBALS['marginY'] + 249 - 1.5;
        $this->setY($y);
        $yg1 = $GLOBALS['marginY'] + 278 - 1.5 - 1;
        $yg2 = $GLOBALS['marginY'] + 273 - 12.5 - 1;
      }
      if ($a == 5) {
        $y = $GLOBALS['marginY'] + 118;
        $this->setY($y);
      }

      $qr = $plgn['url_gambar'];
      // $GLOBALS['namafile'] = $plgn['namafile'];

      $this->setX($dx);
      $this->Image($qr, 20, $yg1 + 1, 18, 18); // Tengah kiri
      $this->setX($dx);
      $this->Image($logo, $gxr-60, $yg2-12, 40, 8); // Pojok kanan atas
      $this->Image($qr, $gxr, $yg2, 18, 18); // Pojok kanan atas

      $this->NomorPelanggan($plgn['no_pelanggan'], $xkiri2, $yg1 - 22); // Kiri
      $this->NomorPelanggan($plgn['no_pelanggan'], $xkanan + 20, $yg2 - 6); // Kanan
      // $this->Watermark($xkanan2 - 95, $yg2 + 20, $txt = $plgn['bulan_penagihan'], 15);

      $this->Penerima(43, $yg1 + 5);
      $this->Underscore(43, $yg1 + 17);
      $this->Underscore(190, $yg1 + 35, $plgn['sort']);
      $this->Keterangan(76, $yg1 + 12, $plgn['keterangan']);

      $this->setFont('Arial', '', 10);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell(19, 5, "Nomor", 'LT', 0, 'L', 0);

      $this->setFont('Arial', 'B', 16);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx + 19);
      $this->cell(25, 6, '', 'LTR', 0, 'L', 0); //$plgn['no_pelanggan']

      $this->setFont('Arial', '', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan - 7);
      $this->cell(19, 4, "|", 0, 0, 'L', 0); // Garis robekan

      $this->setFont('Arial', '', 10);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      $this->cell(19, 5, "Nomor", 'LT', 0, 'L', 0); // Robekan Bagian Kanan

      $this->setFont('Arial', 'B', 16);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan + 19);
      $this->cell(25, 6, '', 'LTR', 0, 'L', 0); //$plgn['no_pelanggan']

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan + 30 + 2);
      $this->cell(110, 5, $company->nama_perusahaan . '       |', 0, 0, 'R', 0);

      $this->Ln();
      $this->setFont('Arial', '', 10);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell(20, 4, "Pelanggan", 'LB', 0, 'L', 0);
      $this->setX($xkanan);
      $this->cell(20, 4, "Pelanggan", 'LB', 0, 'L', 0); // Robekan Bagian Kanan

      $this->setFont('Arial', '', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx + 19);
      $this->cell(25, 4, '', 'LBR', 0, 'L', 0); // Robekan Bagian Kiri //$plgn['wilayah']
      $this->setX($xkanan + 20 - 1);
      $this->cell(25, 4, '', 'LBR', 0, 'L', 0); // Robekan Bagian Kanan //$plgn['wilayah']

      $this->setFont('Arial', '', 6);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan + 58);
      $this->cell($xkanan+2, 3, $company->slogan, 0, 1, 'R', 0); // Robekan Bagian Kanan
      $this->setX($xkanan2 - 30 + 13+2);
      $this->cell(45, 3, $company->alamat . ' Telp. ' . $company->telp, 0, 1, 'R', 0); // Robekan Bagian Kanan2

      $this->Ln(1);
      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 4, 'Nama', 0, 0, 'L', 0);

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkiri2 - 6);
      $this->cell($xkiri, 4, ': ' . $namaLengkapTrim, 0, 0, 'L', 0);

      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      $this->cell($xkiri, 4, 'Nama', 0, 0, 'L', 0);

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan1 - 20 - 18);
      $this->cell(45, 4, ': ' . $plgn['nama_pelanggan'], 0, 1, 'L', 0); // Robekan Bagian Kanan1

      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 4, "Wilayah", 0, 0, 'L', 0);

      // $will = ($plgn['alamat'] == '') ? $plgn['wilayah'] : $plgn['alamat'];

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkiri2 - 6);
      $this->cell($xkiri, 4, ": " . $plgn['wilayah'], 0, 0, 'L', 0);

      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      $this->cell(45, 4, "Wilayah", 0, 0, 'L', 0); // Robekan Bagian Kanan

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan1 - 20 - 18);
      $this->cell(45, 4, ": " . $plgn['wilayah'], 0, 1, 'L', 0); // Robekan Bagian Kanan1

      // $this->setFont('Arial','B',8);
      // $this->setFillColor(255,255,255);
      // $this->setX($xkanan2-30);  $this->cell(45,4,$plgn['tarif'],0,0,'L',0); // Robekan Bagian Kanan2

      // $this->Ln();
      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 4, 'Jumlah', 0, 0, 'L', 0);

      // $this->setX($xkanan2-30);  $this->cell(45,2,'CS/Teknisi',0,1,'L',0); // Robekan Bagian Kanan2

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx + 20 - 6);
      $this->cell($xkiri, 4, ": " . $plgn['tarif_rp'], 0, 0, 'L', 0);

      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      $this->cell(45, 4, 'Jumlah', 0, 0, 'L', 0); // Robekan Bagian Kanan

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan + 17);
      $this->cell(45, 4, ": " . $plgn['tarif_rp'], 0, 1, 'L', 0); // Robekan Bagian Kanan

      // if ($plgn['status'] == 'Putus Permanen' || $plgn['status'] == 'Putus Sementara') {
      //   $this->SetTextColor(0,0,0);
      // }

      $this->setFont('Arial', 'B', 8);
      $this->setFillColor(255, 255, 255);
      // $this->setX($xkanan2-30);  $this->cell(45,3,$company->telp_cs,0,0,'L',0); // Robekan Bagian Kanan

      // $this->Ln(4);
      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 4, 'Expired', 0, 0, 'L', 0);

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx + 20 - 6);
      $this->cell(35, 4, ': ' . $plgn['masa_aktif'], 0, 0, 'L', 1);

      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->SetTextColor(0, 0, 0);
      $this->setX($xkanan);
      $this->cell(45, 4, 'Masa Aktif Sampai', 0, 0, 'L', 0); // Robekan Bagian Kanan

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan + 30);
      $this->cell(35, 4, ': ' . $plgn['masa_aktif'], 0, 1, 'L', 0); // Robekan Bagian Kanan

      $this->Ln(2);
      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->SetTextColor(0, 0, 0);
      $this->setX($dx);
      $this->cell($xkiri, 4, "", 0, 0, 'L', 0);
      $this->setX($xkanan);
      $this->cell(45, 4, "Jumlah Terbilang ", 0, 0, 'L', 0); // Robekan Bagian Kanan

      $this->setFont('Arial', 'B', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan + 106);
      $this->cell($xkiri, 4, $plgn['kode_invoice'], 0, 1, 'L', 0);

      $this->setFont('Arial', 'BI', 10);
      $this->setFillColor(197, 217, 241);
      $this->SetTextColor(0, 0, 0);
      // $this->setX($xkanan);  $this->cell(45,4,$plgn['keterangan'],0,1,'L',0); // Robekan Bagian Kanan
      if (strlen($this->Terbilang($plgn['tarif'])) > 130) {
        $ketr = substr($this->Terbilang($plgn['tarif']), 0, 128) . '...';
      } else if (strlen($this->Terbilang($plgn['tarif'])) <= 4) {
        $ketr = 'Nol';
      } else {
        $ketr = $this->Terbilang($plgn['tarif']) . ' Rupiah';
      }
      // $ketr = (strlen($this->Terbilang($plgn['tarif'])) > 130) ? substr($this->Terbilang($plgn['tarif']), 0, 128) . '...' : (strlen($this->Terbilang($plgn['tarif'])) <= 4) ? 'Nol' : $this->Terbilang($plgn['tarif']) . ' Rupiah';
      $this->setX($xkanan);
      $this->MultiCell(130, 6, trim($ketr), 0, 'L', 1); // Robekan Bagian Kanan
      if (strlen($this->Terbilang($plgn['tarif'])) > 65) {
        $this->Ln(2);
      } else {
        $this->Ln(5);
      }

      $this->setFont('Arial', '', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      $this->cell($xkiri, 3, ' ', 0, 0, 'L', 0); // telp teknisi
      $this->setX($xkanan + 80);
      $this->cell($xkiri, 3, ' ', 0, 0, 'L', 0); // telp teknisi
      // $this->setX($xkanan+79+27);  $this->cell(45,3,'Kode Invoice',0,1,'L',0); // Robekan Bagian Kanan

      $this->Ln();
      $this->setFont('Arial', 'B', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 3, $plgn['kode_invoice'], 0, 0, 'L', 0);

      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      // $this->cell($xkiri, 3, '', 0, 0, 'L', 0);
      // $this->setX($xkanan + 80);
      $this->cell($xkanan + 80, 4, "Cara Pembayaran :", 0, 0, 'L', 0); // telp teknisi

      $this->setFont('Arial', '', 8);
      $this->setX($xkanan);
      $this->cell($xkanan + 58, 4, $plgn['wilayah'] . ',         ' . date('/m/Y'), 0, 1, 'R', 0); // Robekan Bagian Kanan

      // $this->setFont('Arial','B',7);
      // $this->setFillColor(255,255,255);
      // $this->setX($xkanan+79+27);  $this->cell(45,3,$plgn['kode_invoice'],0,1,'L',0); // Robekan Bagian Kanan

      // $this->Ln();

      $this->setFont('Arial', 'B', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkiri2 - 13);
      $this->cell($xkiri, 4, ': ' . $plgn['serial_number'], 0, 0, 'L', 0); // HP PELANGGAN

      $this->setFont('Arial', '', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 4, 'S/N', 0, 0, 'L', 0);

      $this->setFont('Arial', '', 9);
      $this->setX($xkanan);
      $this->cell($xkiri, 4, '- Pembayaran Iuran mulai tanggal 05 s/d 20 setiap bulan.', 0, 0, 'L', 0);

      $this->setFont('Arial', '', 8);
      $this->setX($xkanan + 57 + 30);
      $this->cell(45, 4, '', 0, 1, 'R', 0); // Robekan Bagian Kanan


      $this->setFont('Arial', '', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $tgl_instalasi = ($plgn['tgl_instalasi'] == '0000-00-00') ? '' : $plgn['tgl_instalasi'];
      $this->cell($xkiri, 4, 'Tanggal Instalasi : ' . $tgl_instalasi, 0, 0, 'L', 0);

      $this->setFont('Arial', 'B', 9);
      $this->setX($xkanan);
      // $this->cell($xkiri, 4, "- TRANSFER ke BRI : 0072-010-32185-504 a/n I GUSTI KETUT P. WIJAYA", 0, 1, 'L', 0);
      $this->cell($xkiri, 4, "- Transfer ke Rekening ".$rekening['nama_bank']." a/n ". $rekening['pemilik_rekening'], 0, 1, 'L', 0);

      // $this->setFont('Arial','',7);
      // $this->setFillColor(255,255,255);
      // $this->setX($xkanan+57+30);  $this->cell(45,3,' ',0,1,'R',0); // Robekan Bagian Kanan // telp teknisi
      $korY = $this->GetY();

      $this->setFont('Arial', '', 8);
      $this->setFillColor(255, 255, 255);
      $this->setX($dx);
      $this->cell($xkiri, 4, 'Iuran Pertama : ' . $plgn['expired'], 0, 1, 'L', 0);

      $this->setX($dx);
      $this->cell(20, 4, 'Lokasi Map : ', 0, 0, 'L', 0);

      $this->setFont('Arial', 'BIU', 10);
      if ($plgn['status_map'] == 'Ada') {
        $this->Write(4, $plgn['status_map'], urldecode($plgn['lokasi_map']));
      } else {
        $this->cell(24, 4, $plgn['status_map'], 0, 0, 'L', 0);
      }

      // $ketl = (strlen($plgn['keterangan']) > 80) ? substr($plgn['keterangan'],0,76).'...' : $plgn['keterangan'];
      // if ($plgn['keterangan'] !== '') {
      //   $this->setX($dx);  $this->MultiCell(43,3,$ketl,1,'L',0);
      // } else {
      //   $this->setX($dx);  $this->MultiCell(43,3,$ketl,0,'L',0);
      // }


      $this->setFont('Arial', 'B', 9);
      $this->setFillColor(255, 255, 255);
      $this->setY($korY);
      $this->setX($xkanan);
      // $this->cell(45, 4, '  Ganti 3 DIGIT ANGKA dibelakang jumlah pembayaran dengan nomor pelanggan,', 0, 1, 'L', 0); // Robekan Bagian Kanan1
      $this->cell(45, 4, '  No Rekening         : '. $rekening['no_rekening'], 0, 1, 'L', 0); // Robekan Bagian Kanan1

      // $this->setFont('Arial','B',7);
      // $this->setFillColor(255,255,255);
      // $this->setX($xkanan+57+30);  $this->cell(45,3,'',0,1,'R',0); // Robekan Bagian Kanan


      // $this->setFont('Arial', 'B', 10);
      // $this->setFillColor(197, 217, 241);
      // $this->setX($xkanan);
      // $this->cell(45, 4, 'Contoh : ' . $plgn['tarif_rp_trx'], 0, 1, 'L', 1); // Robekan Bagian Kanan1

      $this->setFont('Arial', 'B', 9);
      // $this->setFillColor(197, 217, 241);
      $this->setX($xkanan);
      $this->cell(45, 4, "  Jumlah Transfer   : " . $plgn['tarif_rp_trx'], 0, 0, 'L', 1); // Robekan Bagian Kanan1

      $this->setFont('Arial','I',8);
      // $this->setFillColor(255,255,255);
      $this->cell(45,4,"               *Jumlah transfer harus sesuai",0,1,'L',0); // Robekan Bagian Kanan


      $this->setFont('Arial', '', 9);
      $this->setFillColor(255, 255, 255);
      $this->setX($xkanan);
      $this->cell(45, 3, '                                      ', 0, 1, 'L', 0); // Robekan Bagian Kanan1

      // $this->setFont('Arial','B',7);
      // $this->setFillColor(255,255,255);
      // $this->setX($dx);  $this->cell($xkiri,2,$plgn['bulan_penagihan'],0,0,'L',0);
      // $this->setX($xkiri2);  $this->cell($xkiri,2,$plgn['kolektor'],0,0,'L',0);

      // $this->setFont('Arial','',8);
      // $this->setFillColor(255,255,255);
      // $this->setX($xkanan);  $this->cell(45,3,'* Sampaikan Keluhan Anda Melalui Telepon atau SMS, Sertakan NAMA Dan NOMOR PELANGGAN Anda.',0,1,'L',0); // Robekan Bagian Kanan1
      // $this->setX($xkanan);  $this->cell(45,3,'* Syarat Dan Ketentuan Berlaku.',0,1,'L',0); // Robekan Bagian Kanan1

      $this->Ln(4); //2
      if ($a != 4) {
        $this->setFont('Arial', '', 6);
        $this->setFillColor(255, 255, 255);
        $this->setX($dx - 20);
        $this->cell($xkiri, 3, '__________________________________________________________|____________________________________________________________________________________________________________________________', 0, 0, 'L', 0);
        $this->Ln(10);
      }

      if ($a == 4) {
        $this->AddPage();
        $a = 0;
        $y = $GLOBALS['marginY'];
      }
    }
  }
}


$GLOBALS['marginY'] = 5;

$pageSize = array(360, 220);
$pdf = new PDF('P', 'mm', $pageSize);
$pdf->setTopMargin($GLOBALS['marginY']);
$pdf->SetCreator('POSO TV App');
$pdf->SetAuthor('Rahtut Aza');
// $pdf->SetAutoPageBreak(true,3);
$pdf->AddPage();
$pdf->Kop($cust, $company, $terms, $rekening, $logo);
// $path = $GLOBALS['namafile'];
$path = $namafile;
if ($outputMode == 'FILE') {
  $pdf->Output('F', $path); // USE THIS FOR WRITE TO FILE
} else {
  $pdf->Output('', $path); // USE THIS FOR STREAM TO BROWSER
}
