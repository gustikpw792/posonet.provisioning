<?php defined('BASEPATH') or exit('No direct script access allowed');
class Master_setoran extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('master_setoran_model', 'master_setoran');
		$this->load->model('kolektor_model', 'kolektor');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ajax_list()
	{
		$list = $this->master_setoran->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			$bulanThn = bulan_tahun($br->tgl_setoran);
			$qk = $this->db->query("SELECT * FROM v_kolektor WHERE id_kolektor = $br->id_kolektor")->row();
			// Wilayah
			if ($qk->wilayah != null || $qk->wilayah != '') {
				$wilayah = json_decode($qk->wilayah);
				$arrLenght = count($wilayah);
				$strWilayah = '';
				for ($w = 0; $w < $arrLenght; $w++) {
					$q = $this->kolektor->getWilayah($wilayah[$w]);
					$strWilayah .= '<span class="badge badge-primary">' . $q->wilayah . '</span> ';
				}
			} else {
				$strWilayah = '';
			}
			$row[] = "<span class='font-bold'>$br->tgl_setoran</span>";
			$row[] = "<span class='font-bold'>$bulanThn</span>";
			$row[] = "<span class='font-bold'>$br->kolektor</span> $strWilayah";
			$row[] = $br->keterangan;
			// $row[] = ($br->total_setoran_remark == null || $br->total_setoran_remark == 0) ? '-' : 'Rp.' . ribuan($br->total_setoran_remark);
			//add html for action
			$row[] = "<a href=\"" . site_url('scan/' . $br->id_master_setoran) . "\" class=\"btn btn-outline btn-primary btn-xs\"><i class=\"glyphicon glyphicon-camera\"></i> Scan</a>
			<div class=\"btn-group\">
                            <button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"javascript:void(0)\" onclick=\"edits('$br->id_master_setoran')\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"deletes('$br->id_master_setoran')\"><i class=\"glyphicon glyphicon-trash\"></i> Hapus</a></li>
							</ul>
                        </div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->master_setoran->count_all(),
			"recordsFiltered" => $this->master_setoran->count_filtered(),
			"data" => $data
		);
		//output to json format
		echo json_encode($output);
	}

	public function save()
	{
		$this->_validate();
		$data = array(
			'tgl_setoran' => $this->input->post('tgl_setoran'),
			'id_kolektor' => $this->input->post('id_kolektor'),
			'keterangan' => $this->input->post('keterangan'),
		);
		$bln = substr($data['tgl_setoran'], 0, 7);
		$exist = $this->db->query("SELECT * FROM master_setoran WHERE id_kolektor = $data[id_kolektor] AND tgl_setoran LIKE '$bln%'")->num_rows();

		if ($exist > 0) {
			echo json_encode(array("status" => FALSE, "msg" => "Sudah ada setoran Kolektor dibulan yang sama!, Setiap Kolektor hanya boleh membuat 1 data master setoran perbulan."));
		} else {
			$insert = $this->master_setoran->save($data);
			echo json_encode(array("status" => TRUE));
		}
	}

	public function update()
	{
		$this->_validate();
		$data = array(
			'tgl_setoran' => $this->input->post('tgl_setoran'),
			'id_kolektor' => $this->input->post('id_kolektor'),
			'keterangan' => $this->input->post('keterangan'),
		);
		$this->master_setoran->update(array('id_master_setoran' => $this->input->post('id_master_setoran')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete($id)
	{
		// before delete master setoran data, scanned data will be restored to 'Belum Bayar' to temp_invoice
		$restoreData = $this->db->query("SELECT * FROM detail_setoran WHERE id_master_setoran = $id")->result();
		foreach ($restoreData as $d) {
			$this->db->query("UPDATE temp_invoice SET status='Belum Bayar', tgl_penyetoran=NULL WHERE kode_invoice = '$d->kode_invoice'");
		}
		$this->master_setoran->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

	public function get_edit($id_master_setoran = FALSE)
	{
		$data = $this->master_setoran->get_by_id($id_master_setoran);
		echo json_encode($data);
	}

	public function vget_edit($id_master_setoran = FALSE)
	{
		$data1 = $this->master_setoran->v_get_by_id($id_master_setoran);
		$data = array(
			'id_master_setoran' => $data1->id_master_setoran,
			'tgl_setoran' => $data1->tgl_setoran,
			'wilayah' => $data1->wilayah,
			'total_setoran' => $data1->total_setoran,
			'total_setoran_remark' => $data1->total_setoran_remark,
			'keterangan' => $data1->keterangan,
		);
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		//id tidak divalidasi karena auto_increment

		if ($this->input->post('tgl_setoran') == '') {
			$data['inputerror'][] = 'tgl_setoran';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('id_kolektor') == '') {
			$data['inputerror'][] = 'id_kolektor';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
