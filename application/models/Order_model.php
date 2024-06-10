<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // Load database jika belum diload
        $this->load->database();
    }

    public function get_orders_by_user_id($user_id)
    {
        // Contoh query untuk mengambil data order berdasarkan user_id
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('orders');
        return $query->result_array();
    }

    public function get_do()
    {
        $this->db->select('id, deliv_date, material, dlv_qty, delivery_no, sloc, ship_to_party, tgl_upload, status');
        $this->db->from('list_do_part');
        $this->db->order_by('id', 'desc');
        $this->db->limit('100');
        $query = $this->db->get();
        return $query;
    }
}
