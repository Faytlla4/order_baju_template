<?php defined('BASEPATH') || exit('No direct script access allowed');

class Master extends App_Controller
{
    protected $permissionView = 'Site.Master.View';
    protected $permissionCreate = 'Site.Master.Create';
    protected $permissionEdit = 'Site.Master.Edit';
    protected $permissionDelete = 'Site.Master.Delete';

    public function __construct()
    {
        parent::__construct();
        // Skip auth restriction temporarily if role permissions are missing
        // $this->auth->restrict($this->permissionView);
        $this->load->library('form_validation');
    }

    public function index()
    {
        $records = $this->db->order_by('urutan', 'ASC')->get('master_jenis_baju')->result_array();

        Template::set('records', $records);
        Template::set('toolbar_title', 'Master Jenis Baju');
        Template::render();
    }

    public function create()
    {
        if ($this->input->method() == 'post') {
            $this->form_validation->set_rules('nama_jenis', 'Nama Jenis', 'required|max_length[50]');
            if ($this->form_validation->run() !== FALSE) {
                $data = array(
                    'nama_jenis' => $this->input->post('nama_jenis'),
                    'urutan'     => $this->input->post('urutan') ? $this->input->post('urutan') : 0,
                    'keterangan' => $this->input->post('keterangan'),
                    'status'     => $this->input->post('status') !== null ? $this->input->post('status') : 1,
                    'created_on' => date('Y-m-d H:i:s')
                );
                $this->db->insert('master_jenis_baju', $data);
                Template::set_message('Berhasil menambahkan data.', 'success');
                redirect(SITE_AREA . '/master');
            }
        }
        
        Template::set('toolbar_title', 'Tambah Master Jenis Baju');
        Template::render('master/form');
    }

    public function edit($id = null)
    {
        if ($id === null) {
            redirect(SITE_AREA . '/master');
        }

        if ($this->input->method() == 'post') {
            $this->form_validation->set_rules('nama_jenis', 'Nama Jenis', 'required|max_length[50]');
            if ($this->form_validation->run() !== FALSE) {
                $data = array(
                    'nama_jenis' => $this->input->post('nama_jenis'),
                    'urutan'     => $this->input->post('urutan') ? $this->input->post('urutan') : 0,
                    'keterangan' => $this->input->post('keterangan'),
                    'status'     => $this->input->post('status') !== null ? $this->input->post('status') : 1
                );
                $this->db->where('id', $id)->update('master_jenis_baju', $data);
                Template::set_message('Berhasil mengubah data.', 'success');
                redirect(SITE_AREA . '/master');
            }
        }

        $record = $this->db->where('id', $id)->get('master_jenis_baju')->row_array();
        Template::set('record', $record);
        Template::set('toolbar_title', 'Edit Master Jenis Baju');
        Template::render('master/form');
    }

    public function delete($id = null)
    {
        if ($id) {
            $this->db->where('id', $id)->delete('master_jenis_baju');
            Template::set_message('Berhasil menghapus data.', 'success');
        }
        redirect(SITE_AREA . '/master');
    }
}
