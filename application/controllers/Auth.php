<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
	사용자 인증 컨트롤러
*/
class Auth extends CI_Controller{
    function __construct()
    {       
        parent::__construct();
        //중복되는 부분은 생성자에서 작업해주는것이 좋다.
        $this->load->database(); //database 라이브러리 로드
        $this->load->model('auth_model'); 
        
        $this->load->helper('form'); #helper 로드
        $this->load->library('form_validation'); # validation library 로드
        $this->load->helper('alert');
    }

    public function _remap($method){
    	$this->load->view('header');

    	if(method_exists($this, $method)){
    		$this->{"{$method}"}();
    	}
    	$this->load->view('footer');
    }

	public function index(){
		$this->get_login();
	}


	public function get_login(){
		if($_POST){
			$input_id = $this->input->post('id',TRUE);
			$input_password = $this->input->post('password',TRUE);
			
			// validation 조건 설정 (태그name, 이름(임의로지정), 조건)
			$this->form_validation->set_rules('id','ID','required');
			$this->form_validation->set_rules('password','Password','required');

			if($this->form_validation->run() == FALSE){//사용자가 입력한 정보를 유효성검사함
				//validation이 유효하지않은 경우
				$this->load->view('account/login_form_page'); 
				// validation과 함께 로그인페이지를 다시 보여줌
			}else{
				//validation이 유효한 경우
				//model을 통해 DB의 값 조회 및 체크 
				
				//체크해야할 조건들
				/*
				1. ID가 있는지, 
				2. ID가 있다면 입력한 PASSWORD가 일치하는지,
				*/

				$auth_data = array(
					'user_id' => $input_id,
					'password' => $input_password
				);

				$result = $this->auth_model->get_check($auth_data);

				if($result){ // 입력한 정보와 일치하는 계정이 있을 경우
					
					$result_password = $result->user_password;

					if($result_password == $input_password){ // 입력한 비밀번호화 조회한 비밀번호가 일치할 경우 
						$newdata = array(
							'user_id' => $result->user_id,
							'user_name' => $result->user_name,
							'user_email' => $result->user_email,
							'logged_in' => TRUE	
						);

						$this->session->set_userdata($newdata); // 로그인정보를 세션에 저장

						alert('로그인 되었습니다.', '/index.php/main');
						exit;
					}else{
						//비밀번호가 틀렸을 경우
						alert('비밀번호가 일치하지 않습니다. 다시 확인해주세요.', '/index.php/auth');
					}
				}else{ // 입력한 정보와 일치하는 계정이 없을 경우
					alert('입력하신 계정을 찾을 수 없습니다. 계정 정보를 다시 확인해주세요.', '/index.php/auth');
				}
			} // validation 끝
		}else{
			$this->load->view('account/login_form_page');
		} // POST로 넘어온 값이 없을 때
	}

	public function get_logout(){
		$this->session->sess_destroy();
		alert('로그아웃 되었습니다.','/index.php/main/');
		exit;
	}


}