<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 AJAX 댓글 처리 컨트롤러
*/
class Board_comment extends CI_Controller{
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
    
    }

    public function ajax_test(){
        if(@$this->session->userdata('logged_in') == TRUE){ // 로그인 된 상태일 경우
            $table = 'board_comment';
            $board_id = $this->input->post('board_id',TRUE);
            $comment_contents = $this->input->post('comment_contents',TRUE);

            $arr_for_get_last_pk = array(
                'table' => $table,
                'board_id' => $board_id
            );

            $comment_id = 0;

            $latest_comment_id = $this->board_model->get_latest_comment_id($arr_for_get_last_pk);

            if($latest_comment_id){
                $comment_id = (int)$latest_comment_id['comment_id'] + 1;
            }else{
                $comment_id = 1;
            }

            if(!empty($comment_contents)){
                $write_data = array(
                    'table' => $table,
                    'board_id' => $board_id,
                    'comment_id' => $comment_id,
                    'comment_pk' => $board_id.'_'.$comment_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'comment_contents' => $comment_contents
                );
            }else{
                echo "1000";
            }

            $result = $this->board_model->insert_comment($write_data);

            if($result){ // 댓글을 DB에 정상적으로 insert했다면,
                // 해당 board_id의 댓글들 데이터를 리턴
                $data['comment_list'] = $this->board_model->get_comment('board_comment', $board_id);
                echo json_encode(array("comment_list" => $data['comment_list']));
            }else{
                echo "2000";
            }       

        }else{ // 로그인이 안되어있는 상태일 경우
            echo "9000";
        }
    }

    public function ajax_comment_reload(){
        $board_id = $this->input->get('board_id',TRUE);

        $data['comment_list'] = $this->board_model->get_comment('board_comment', $board_id);
        $this -> load -> view('board/board_comment_page', $data);
    }

    public function ajax_comment_add(){
        if(@$this->session->userdata('logged_in') == TRUE){
            // 로그인 되어있을 시 
            $table = 'board_comment';
            $board_id = $this->input->post('board_id',TRUE);
            $comment_contents = $this->input->post('comment_contents',TRUE);
            

            $array_for_id = array(
                'table' => $table,
                'board_id' => $board_id
            );

            $comment_id = 0;

            $latest_comment_id = $this->board_model->get_latest_comment_id($array_for_id); 

            if($latest_comment_id){
                $comment_id = (int)$latest_comment_id['comment_id'] + 1;
            }else{
                $comment_id = 1;
            }
            
            if($comment_contents != ''){

                $write_data = array(
                    'table' => $table,
                    'board_id' => $board_id,
                    'comment_id' => $comment_id,
                    'comment_pk' => $board_id.'_'.$comment_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'comment_contents' => $comment_contents
                );

                $result = $this->board_model->insert_comment($write_data);

                if ($result) {

                    $sql = "SELECT * FROM ". $table ." WHERE board_id = '". $board_id . "' ORDER BY comment_id DESC";
                    $query = $this -> db -> query($sql);

                    $get_comments = $this->board_model->get_comment($write_data['table'],$write_data['board_id']);

                    $commnets_cnt = $this->board_model->get_comment_count($board_id);
                ?>
                <table cellspacing="0" cellpadding="0" class="table table-striped">
                    <tbody>
                    <?php
                    foreach ($get_comments -> result() as $lt) {
                    ?>
                        <tr>
                            <th scope="row">
                                <?php echo $lt -> user_id;?>
                            </th>
                            <td><?php echo $lt -> comment_contents;?></td>
                            <td>
                            <?php 
                            if( (@$this->session->userdata('logged_in') == TRUE && $this->session->userdata('user_id') == $lt->user_id) || (@$this->session->userdata('logged_in') == TRUE && $this->session->userdata('user_id') == 'admin') ) :
                            ?>
                            <a href="/index.php/board_comment/ajax_comment_modify/<?php echo $this -> uri -> segment(3);?>" class="btn btn-warning"> 수정 </a>
                            <a href="/index.php/board_comment/ajax_comment_delete/<?php echo $this -> uri -> segment(3);?>" class="btn btn-danger"> 삭제 </a>
                            <?php endif; ?>
                            <td>
                            <td>
                                <time datetime="<?php echo mdate("%Y-%M-%j", human_to_unix($lt->reg_date));?>">
                                    <?php echo $lt -> reg_date;?>
                                </time>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>

                <?php
                } else {
                    // 글 실패시
                    echo "2000";
                }
            } else {
                // 글 내용이 없을 경우
                echo "1000";
            }
        } else {
            // 로그인 필요 에러
            echo "9000";
        }
        
    }
}