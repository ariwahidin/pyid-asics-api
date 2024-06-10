<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Api extends CI_Controller
{

	private $key;

	public function __construct()
	{
		parent::__construct();
		$this->key = $this->config->item('jwt_secret_key');
		$this->load->database();
		$this->load->model('Order_model');
	}

	public function login()
	{
		$post = $this->input->post();
		if (isset($post['username']) && isset($post['password'])) {
			$username = $post['username'];
			$password = $post['password'];
			$where = array(
				'username' => $username,
				'pass' => $password
			);
			$this->db->select('username, name, group_id, group_city');
			$this->db->from('users');
			$this->db->where($where);
			$query = $this->db->get();

			if ($query->num_rows() > 0) {


				$data = array(
					'user' => $query->row(),
					'token' => $this->create_token($query->row())
				);


				$this->output
					->set_status_header(200)
					->set_output(json_encode(array('status' => 'success', 'message' => 'Login successfully', 'data' => $data)));
				return;
			} else {
				$this->output
					->set_status_header(200)
					->set_output(json_encode(array('status' => 'error', 'message' => 'Invalid credential')));
				return;
			}
		} else {
			$this->output
				->set_status_header(400)
				->set_output(json_encode(array('status' => 'error', 'message' => 'Invalid login format.')));
			return;
		}
	}

	public function create_token($user)
	{
		$issuedAt = time();
		$expirationTime = $issuedAt + 3600;  // Token valid selama 1 jam
		$payload = [
			'iat' => $issuedAt,
			'exp' => $expirationTime,
			'data' => [
				'username' => $user->username,
				'name' => $user->name,
				'group_id' => $user->group_id,
				'group_city' => $user->group_city,
			]
		];

		$jwt = JWT::encode($payload, $this->key, 'HS256');
		return $jwt;
	}

	public function verify_token()
	{
		// Mengambil token dari header Authorization
		$authHeader = $this->input->get_request_header('Authorization');

		if (!$authHeader) {
			$this->output
				->set_status_header(400)
				->set_output(json_encode(array('status' => 'error', 'message' => 'Invalid token format.')));
			return;
		}

		$jwt = $authHeader;

		try {
			$decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
			return (array) $decoded->data; // Mengembalikan data user dalam array
		} catch (Exception $e) {
			return null;
		}
	}

	public function get_orders()
	{
		$userData = $this->verify_token();

		if ($userData === null) {
			$this->output
				->set_status_header(401)
				->set_output(json_encode(array('status' => 'error', 'message' => 'Unauthorized')));
			return;
		}

		// Data dummy untuk order
		$dummyOrders = [
			['order_id' => 1, 'user_id' => 123, 'product' => 'Product A', 'quantity' => 2, 'price' => 50.00],
			['order_id' => 2, 'user_id' => 123, 'product' => 'Product B', 'quantity' => 1, 'price' => 30.00],
			['order_id' => 3, 'user_id' => 124, 'product' => 'Product C', 'quantity' => 3, 'price' => 20.00]
		];

		$this->output
			->set_status_header(200)
			->set_output(json_encode(array('status' => 'success', 'data' => $dummyOrders)));
	}

	public function get_do()
	{
		$userData = $this->verify_token();

		if ($userData === null) {
			$this->output
				->set_status_header(401)
				->set_output(json_encode(array('status' => 'error', 'message' => 'Unauthorized')));
			return;
		}

		$do =  $this->Order_model->get_do();
		$data = array(
			'total' => $do->num_rows(),
			'do' => $do->result()
		);

		$this->output
			->set_status_header(200)
			->set_output(json_encode(array('status' => 'success', 'data' => $data)));
	}
}
