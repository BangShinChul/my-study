<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends CI_Controller{
    function __construct()
    {       
        parent::__construct();
        //중복되는 부분은 생성자에서 작업해주는것이 좋다.
        $this->load->database(); //database 라이브러리 로드
        $this->load->model('board_model'); 
        $this->load->helper(array('url','date')); #helper 로드

        $this->load->library('form_validation'); //validation 라이브러리 로드
    }

    public function index(){
    	$this->board_lists();
    }

    # 사이트의 헤더, 푸터가 자동으로 추가된다.
    public function _remap($method){
    	//헤더 include
    	$this->load->view('header');
    	
    	if(method_exists($this, $method)){
    		$this->{"{$method}"}();
    	}

    	//푸터 include
    	$this->load->view('footer');
    }

    # 게시글 목록 불러오기
    public function board_lists(){

    	# 게시글 검색기능 처리
    	//$this->output->enable_profiler(TRUE);

    	# 검색어 초기화
    	$search_word = ''; 
    	$page_url = '';
    	$uri_segment = 4;
        // localhost/index.php/board/board_lists/page/여기
        //          /         /    1/          2/   3/  4 

    	# 주소 중에서 search(검색어) 세그먼트가 있는지 검사하기 위해 주소를 배열로 반환
    	$uri_array = $this->segment_explode($this->uri->uri_string());

    	if(in_array('search', $uri_array)){
    		# 주소에 검색어가 있을 경우 처리
    		$search_word = urldecode($this->url_explode($uri_array, 'search'));
    		# 페이지네이션 용 주소
    		$page_url = '/search/' . $search_word;
    		$uri_segment = 6; # 검색시 페이지 번호가 위치한 세그먼트 자리
            // localhost/index.php/board/board_lists/search/검색어/page/여기
            //                    /    1/          2/     3/     4/   5/  6
            //이므로 페이지 번호가 위치한 세그먼트 자리는 6이다.
    	}

    	//////////////////////////////////////////////////////////
    	#페이징 처리
    	$this->load->library('pagination');

    	# 페이지 네이션 설정
    	$config['base_url'] = '/index.php/board/board_lists/'. $page_url .'/page/'; # 페이징 주소
    	$config['total_rows'] = $this->board_model->get_list('board', 'count', '', '', $search_word); # 게시물 전체 개수
    	$config['per_page'] = 5; # 한 페이지에 표시할 게시물 수
    	$config['uri_segment'] = $uri_segment; # 페이지 번호가 위치한 세그먼트

    	# 페이지 네이션 초기화 : 
        # 위의 config 세팅 값들로 페이지네이션의 초기세팅을 함
        # 페이지네이션 필수 세팅값 : base_url, total_rows, per_page, uri_segment
        /*
        base_url : 페이지네이션에 포함될 컨트롤러/함수 의 전체 url 입니다. 위의 예제에서,컨트롤러는 “board_lists” 이고 함수는 “page” 입니다. url을 다른 구조로 하고 싶다면 url 라우팅 변경(re-route your URI) 을 이용하실 수 있습니다.

        total_rows : 페이지네이션할 전체 레코드의 수를 나타냅니다. 
                     통상적으로 이숫자는 데이터베이스 쿼리에서 리턴되는 전체 열 수 입니다.
        
        per_page : 한페이지에 보여질 아이템(열)수 입니다. 
        */
    	$this->pagination->initialize($config);
    	
        # 페이지 링크를 생성하여 view에서 사용할 변수에 할당
        // create_links() : 링크를 만들어주는 함수
    	$data['pagination'] = $this->pagination->create_links();

    	# 게시물 목록을 불러오기 위한 offset, limit값 가져오기
    	$page = $this->uri->segment($uri_segment,1);

    	if($page > 1){ # 페이지 번호 세그먼트가 1보다 클때 (첫페이지가 아닐때)
    		$start = ($page / $config['per_page']) * $config['per_page'];
    	}else{
    		$start = 0; # 그 외는 첫페이지 이므로 $start를 0으로 세팅
    	}

    	$limit = $config['per_page'];

    	$data['list'] = $this->board_model->get_list('board', '', $start, $limit, $search_word);
        
        $this->load->view('board/board_page',$data);
    }


    # 검색기능 처리 : url중 키 값을 구분하여 값을 가져오는 함수
    /*
	@param Array $url : segment_explode 한 url 값
	@param String $key : 가져오려는 값의 key
	@param String $url[$k] : 리턴 값
    */
    function url_explode($url, $key){
        $cnt = count($url);
 
        for ($i = 0; $cnt > $i; $i++) {
            if ($url[$i] == $key) {
                $k = $i + 1;
                return $url[$k];
            }
        }
    }

     /**
     * HTTP의 URL을 "/"를 Delimiter로 사용하여 배열로 바꿔 리턴한다.
     *
      @param String 대상이 되는 문자열
      @return string[]
     */
    function segment_explode($seg) {
        // 세그먼트 앞 뒤 "/" 제거 후 uri를 배열로 반환
 
        $len = strlen($seg);
 
        if (substr($seg, 0, 1) == '/') {
            $seg = substr($seg, 1, $len);
        }
 
        $len = strlen($seg);
 
        if (substr($seg, -1) == '/') {
            $seg = substr($seg, 0, $len - 1);
        }
 
        $seg_exp = explode("/", $seg);
        return $seg_exp;
    }

    # 게시물 상세 보기
    function view(){

        $table = 'board';
        $board_id = $this->uri->segment(3);

		// 게시판 이름과 게시물 번호에 해당하는 게시물 가져오기
        $data['views'] = $this->board_model->get_view('board', $board_id);
        
        $data['comment_count'] = $this->board_model->get_comment_count($board_id);
        // 게시판 이름과 번호에 해당하는 댓글 리스트 가져오기
        $data['comment_list'] = $this->board_model->get_comment('board_comment', $board_id);

        // view 호출
        $this -> load -> view('board/board_view_page', $data);
    }

    # 게시물 쓰기 
    function board_write(){
        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

        $this->form_validation->set_rules('subject', '제목', 'required');
        $this->form_validation->set_rules('contents', '내용', 'required');

        if($this->form_validation->run() == FALSE){
            $this->load->view('/board/board_write_page');
        }else{
            if($_POST){
                # 글쓰기 POST 전송 시 

                $this->load->helper('alert');

                # 주소중에서 page 세그먼트가 있는지 검사하기 위해 주소를 배열로 반환
                $uri_array = $this->segment_explode($this->uri->uri_string());

                if(in_array('page', $uri_array)) {
                    $pages = urldecode($this->url_explode($uri_array, 'page'));
                }else{
                    $pages = 1;
                }

                if(!$this->input->post('subject', TRUE) AND !$this->input->post('contents', TRUE)) {
                    # 글내용이 없을 경우, 프로그램 단에서 한번 더 체크 
                    alert('비정상적인 접근입니다.', '/index.php/board/board_lists/page/'.$pages);
                    exit;
                }

                # var_dump($_POST);
                # DB에 넣을 값들을 배열로 만듦
                $write_data = array(
                    'subject' => $this->input->post('subject', TRUE),
                    'contents' => $this->input->post('contents', TRUE),
                    'user_id' => $this->session->userdata('user_id'),
                    'user_name' => $this->session->userdata('user_name'),
                    'table' => 'board'
                );

                $result = $this->board_model->insert_board($write_data);

                if($result){
                    alert("게시글이 입력되었습니다.",'/index.php/board/board_lists/page/'.$pages);
                    exit;
                }else{
                    alert("게시글을 업로드하는 중 오류가 발생했습니다. 다시 입력해주세요.",'/index.php/board/board_lists/page/'.$pages);
                    exit;
                }
            }else {
                # 쓰기 폼 view 호출
                $this->load->view('/board/board_write_page');
            }            
        }
    }


    # 게시물 수정
    function board_modify(){
        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

        if($_POST){
            $this->load->helper('alert'); # alert 헬퍼 소환

            # url을 배열로 변환하여 가져옴            
            $uri_array = $this->segment_explode($this->uri->uri_string());

            # $uri_array 중 page라는 값이 있을 경우
            if( in_array('page', $uri_array)) {
                $pages = urldecode($this->url_explode($uri_array,'page'));
            }else{
                $pages = 1;
            }

            # 만약 post타입으로 넘어온 값중에 subject 혹은 contents가 없을 경우
            if( !$this->input->post('subject', TRUE) AND !$this->input->post('contents', TRUE) ){
                alert('비정상적인 접근입니다.', '/index.php/board/board_lists/page/'.$pages);
                exit;
            }

            # modify_data를 받아서 배열에 쌓음
            /*
                table: DB 테이블명
                board_id: 게시글 id / url상에 나와있는 게시글 id 세그먼트 가져옴
                subject: 제목 / post타입으로 받은 파라미터 중 subject값 가져옴
                contents: 내용 / post타입으로 받은 파라미터 중 contents값 가져옴
            */
            $modify_data = array(
                'table' => 'board',
                'board_id' => $this->uri->segment(3),
                'subject' => $this->input->post('subject', TRUE),
                'contents' => $this->input->post('contents', TRUE)
            );

            # 위에서 세팅한 수정값 배열가지고 모델로 넘김
            $result = $this->board_model->modify_board($modify_data);

            if( $result ){
                alert('수정되었습니다.', '/index.php/board/view/'.$modify_data['board_id']);
                exit;
            }else{
                alert('에러가 발생했습니다. 다시 수정해주세요.', '/index.php/board/view/'.$modify_data['board_id']);
                exit;
            }

        }else{
            # POST로 넘어온 값이 없을 경우 modify 뷰를 보여줌
            $data['views'] = $this->board_model->get_view('board',$this->uri->segment(3));
            $this->load->view('board/board_modify_page', $data);
        }

    }

    # 게시물 삭제
    function board_delete(){
        $this->load->helper('alert'); # alert 헬퍼 소환

        $id = $this->uri->segment(3);
        $result = $this->board_model->delete_board($id);

        if( $result ){
            alert('게시글이 삭제 되었습니다.', '/index.php/board/board_lists/');
            exit;
        }else{
            alert('에러가 발생했습니다. 다시 시도해주세요.', '/index.php/board/view/'.$id);
            exit;
        }

    }


}