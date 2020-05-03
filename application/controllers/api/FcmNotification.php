<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class FcmNotification extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('Fcm_model', 'fcmModel');
        $this->load->model('Produk_model', 'produk');
    }

    public function create_post() {
        // get req body
        $token = $this->post('fcm_token');

        // response error params not found
        if (!$token) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response success
        if ($this->fcmModel->save(['fcm_token' => $token]) > 0) {
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function sendnotification_post() {
        $produk = $this->produk->getNotifikasi();
        
        // response error get data null
        if (!$produk) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $deviceToken = $this->fcmModel->get();

        // response error get data null
        if (!$deviceToken) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Device token not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $token = $deviceToken[0]['fcm_token'];
        $message = "Hi, disini ada produk yang mau habis...";

        $this->load->library('fcm');
        $this->fcm->setTitle('Reminder');
        $this->fcm->setMessage($message);
        $this->fcm->setIsBackground(false);
        $payload = array('notification' => '');
        $this->fcm->setPayload($payload);
        $this->fcm->setImage('');
        $json = $this->fcm->getPush();
        $this->fcm->send($token, $json);
    }

}