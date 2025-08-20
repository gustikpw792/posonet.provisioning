<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Pengeluaran extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged_in()) {
			redirect('login?_rdr=' . urlencode(current_url()));
		}
		$this->load->model('pengeluaran_model', 'pengeluaran');
		$this->load->helper(array('MY_ribuan', 'MY_bulan'));
	}

	public function index()
	{
		set_status_header(401);
	}

	public function ajax_list()
	{
		$list = $this->pengeluaran->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $br) {
			$no++;
			$row = array();
			$row[] = $br->id_pengeluaran;
			$row[] = "<span class='font-bold'>$br->tgl_pengeluaran</span>";
			$row[] = "<span class='font-bold'>$br->nama_pengeluaran</span>";
			$row[] = 'Rp.' . ribuan($br->jumlah);
			$row[] = $br->keterangan;
			//add html for action
			$row[] = "<div class=\"btn-group\">
                            <button data-toggle=\"dropdown\" class=\"btn btn-default btn-xs dropdown-toggle\" aria-expanded=\"false\">Action <span class=\"caret\"></span></button>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"javascript:void(0)\" onclick=\"views('$br->id_pengeluaran')\"><i class=\"glyphicon glyphicon-eye-open\"></i> Lihat Detail</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"edits('$br->id_pengeluaran')\"><i class=\"glyphicon glyphicon-pencil\"></i> Edit</a></li>
                                <li><a href=\"javascript:void(0)\" onclick=\"deletes('$br->id_pengeluaran')\"><i class=\"glyphicon glyphicon-trash\"></i> Hapus</a></li>
                            </ul>
                        </div>";
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->pengeluaran->count_all(),
			"recordsFiltered" => $this->pengeluaran->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function save_pengeluaran()
	{
		$this->_validate();
		$data = array(
			'tgl_pengeluaran' => $this->input->post('tgl_pengeluaran'),
			'nama_pengeluaran' => $this->input->post('nama_pengeluaran'),
			'jumlah' => $this->input->post('jumlah'),
			'keterangan' => $this->input->post('keterangan'),
		);
		$insert = $this->pengeluaran->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function update_pengeluaran()
	{
		$this->_validate();
		$data = array(
			'tgl_pengeluaran' => $this->input->post('tgl_pengeluaran'),
			'nama_pengeluaran' => $this->input->post('nama_pengeluaran'),
			'jumlah' => $this->input->post('jumlah'),
			'keterangan' => $this->input->post('keterangan'),
		);
		$this->pengeluaran->update(array('id_pengeluaran' => $this->input->post('id_pengeluaran')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_pengeluaran($id_pengeluaran)
	{
		$this->pengeluaran->delete_by_id($id_pengeluaran);
		echo json_encode(array("status" => TRUE));
	}

	public function get_edit($id_pengeluaran = FALSE)
	{
		$data = $this->pengeluaran->get_by_id($id_pengeluaran);
		echo json_encode($data);
	}

	public function autocompletes()
	{
		$result = $this->db->query("SELECT nama_pengeluaran AS name FROM pengeluaran ORDER BY nama_pengeluaran ASC")->result();
		foreach ($result as $s) {
			$data[] = $s->name;
		}
		echo json_encode($data);
	}

	public function getSummary($bulan = null)
	{
		$bulan = ($bulan == null) ? date('Y-m') : $bulan;
		$last = date('Y-m-d', strtotime("+1 Months"));
		$tahunIni = date('Y');
		$tahunLalu = date('Y') - 1;
		$thisMonth = $this->db->query("SELECT SUM(jumlah) AS total FROM pengeluaran WHERE tgl_pengeluaran LIKE '$bulan%'")->row();
		$lastMonth = $this->db->query("SELECT SUM(jumlah) AS total FROM pengeluaran WHERE tgl_pengeluaran LIKE '$last%'")->row();
		$thisYear = $this->db->query("SELECT SUM(jumlah) AS total FROM pengeluaran WHERE YEAR(tgl_pengeluaran) = '$tahunIni'")->row();
		$lastYear = $this->db->query("SELECT SUM(jumlah) AS total FROM pengeluaran WHERE YEAR(tgl_pengeluaran) = '$tahunLalu'")->row();
		echo json_encode([
			'thisMonth' => $thisMonth->total == null ? 0 : ribuan($thisMonth->total),
			'lastMonth' => $lastMonth->total == null ? 0 : ribuan($lastMonth->total),
			'thisYear' => $thisYear->total == null ? 0 : ribuan($thisYear->total),
			'lastYear' => $lastYear->total == null ? 0 : ribuan($lastYear->total),
		]);
	}

	public function monthly()
	{
		$query = $this->db->query("SELECT SUBSTR(tgl_pengeluaran,1,7) AS tahun_bln, SUM(jumlah) AS total FROM pengeluaran GROUP BY SUBSTR(tgl_pengeluaran,1,7)")->result();
		$data = array();
		foreach ($query as $r) {
			$row = array();
			$row[] = $r->tahun_bln;
			$row[] = ribuan($r->total);
			$data[] = $row;
		}
		echo json_encode(['data' => $data]);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		//id tidak divalidasi karena auto_increment
		if ($this->input->post('nama_pengeluaran') == '') {
			$data['inputerror'][] = 'nama_pengeluaran';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('tgl_pengeluaran') == '') {
			$data['inputerror'][] = 'tgl_pengeluaran';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('jumlah') == '') {
			$data['inputerror'][] = 'jumlah';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($this->input->post('keterangan') == '') {
			$data['inputerror'][] = 'keterangan';
			$data['error_string'][] = 'Enter this field!';
			$data['status'] = FALSE;
		}
		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
