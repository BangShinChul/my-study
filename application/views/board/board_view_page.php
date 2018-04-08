<script type="text/javascript" src="/static/js/httpRequest.js"></script>
<script>
$(document).ready(function(){
    var csrf_tk_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_tk_hash = $('#'+csrf_tk_name).val().toString();
    $('#comment_submit').click(function(){
        $.ajax({
            url: '/index.php/board_comment/ajax_test',
            type: 'POST',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': csrf_tk_hash,
                'comment_contents': encodeURIComponent($('#input01').val()),
                'board_id': '<?php echo $this->uri->segment(3); ?>'
            },
            dataType: 'html',
            error: function(){
                alert('댓글 입력에 실패했습니다. 다시 시도해주세요.');
            },
            complete: function(xhr, textStatus, data){
                if(textStatus == 'success'){
                    if(xhr.responseText == 1000){
                        alert('댓글 내용을 입력하세요.');
                    }else if(xhr.responseText == 2000){
                        alert('댓글을 다시 업력해주세요.');
                    }else if(xhr.responseText == 9000){
                        alert('댓글을 입력하시려면 로그인이 필요합니다.');
                    }else{
                        //$('#comment_area').html(xhr.responseText);
                        alert("댓글을 입력했습니다.");
                        $("#comment_area").load('/index.php/board_comment/ajax_comment_reload?board_id=<?php echo $this->uri->segment(3);?>');
                        $('#input01').val('');
                    }
                }
            }
        });
    });     
});    
   
function getXMLHttpRequest() {
    if (window.ActivXObject) {
        try {
            return new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try {
                return ActiveXObject("Microsoft.XMLHTTP");
            } catch(e1) {
                return null;
            }
        }
    } else if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else {
        return null;
    }
}
     
var httpRequest = null;
     
function sendRequest(url, params, callback, method) {
    httpRequest = getXMLHttpRequest();
    var httpMethod = method ? method : 'GET';
     
    if (httpMethod != 'GET' && httpMethod != 'POST') {
        httpMethod = 'GET';
    }
     
    var httpParams = (params == null || params == '') ? null : params;
    var httpUrl = url;
     
    if (httpMethod == 'GET' && httpParams != null) {
        httpUrl = httpUrl + "?" + httpParams;
    }
     
    httpRequest.open(httpMethod, httpUrl, true);
    httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    httpRequest.onreadystatechange = callback;
    httpRequest.send(httpMethod == 'POST' ? httpParams : null);
}
    
function board_search_enter(form) {
    var keycode = window.event.keyCode;
    if (keycode == 13)
        $("#search_btn").click();
}
    
function comment_add() {
    var csrf_token = getCookie('csrf_cookie_name');
    var name = "comment_contents=" + encodeURIComponent(document.com_add.comment_contents.value) + "&csrf_test_name=" + csrf_token + "&table=<?php echo $this->uri->segment(3); ?>&board_id=<?php echo $this->uri->segment(5); ?>";
    sendRequest("/bbs/ajax_board/ajax_comment_add", name, add_action, "POST");
}
    
function add_action() {
    if ( httpRequest.readyState == 4) {
        if ( httpRequest.status == 200) {
            if ( httpRequest.responseText == 1000) {
                alert("댓글의 내용을 입력하세요.");
            } else if ( httpRequest.responseText == 2000) {
                alert("다시 입력하세요.");
            } else if ( httpRequest.responseText == 9000) {
                alert("로그인하여야 합니다.");
            } else {
                var contents = document.getElementById("comment_area");
                contents.innerHTML = httpRequest.responseText;
                    
                var textareas = document.getElementById("input01");
                textareas.value = '';
            }
        }
    }
}
    
function getCookie(name) {
    var nameOfCookie = name + "=";
    var x = 0;
        
    while ( x <= document.cookie.length) {
        var y = (x + nameOfCookie.length);
            
        if (document.cookie.substring(x, y) == nameOfCookie) {
            if (( endOfCookie = document.cookie.indexOf(";", y)) == -1) {
                endOfCookie = document.cookie.length;
            }                
            return unescape(document.cookie.substring(y, endOfCookie));
        }
            
        x = document.cookie.indexOf(" ", x) + 1;
            
        if ( x == 0){
            break;    
        } 
    }
}

</script>

    
<article id="board_area">
    <table cellspacing="0" cellpadding="0" class="table table-striped">
        <thead>
            <tr>
                <th scope="col"><?php echo $views -> subject;?></th>
                <th scope="col">작성자: <?php echo $views -> user_name;?>(<?php echo $views -> user_id;?>)</th>
                <th scope="col">조회수: <?php echo $views -> hits;?></th>
                <th scope="col">등록일: <?php echo $views -> reg_date;?><?php if($views->board_status == '1') : ?>(수정됨)<?php endif; ?>

                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="4">
                    <?php echo $views -> contents;?>
                </th>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">
                    <a href="/index.php/board/board_lists" class="btn btn-primary">목록 </a>

                    <?php 
                        if( (@$this->session->userdata('logged_in') == TRUE && $this->session->userdata('user_id') == $views -> user_id) || (@$this->session->userdata('logged_in') == TRUE && $this->session->userdata('user_id') == 'admin') ) :
                    ?>
                    <a href="/index.php/board/board_modify/<?php echo $this -> uri -> segment(3);?>" class="btn btn-warning"> 수정 </a>
                    <a href="/index.php/board/board_delete/<?php echo $this -> uri -> segment(3);?>" class="btn btn-danger"> 삭제 </a>
                    <?php endif; ?>
                </th>
            </tr>
        </tfoot>
    </table>

    <?php 
    if(@$this->session->userdata('logged_in') == TRUE) :
    ?>
    <form class="form-horizontal" method="POST" action="" name="comment_add">
        <fieldset>
            <div class="control-group">
                <div class="controls">
                    <textarea class="input-xlarge" id="input01" name="comment_contents" rows="3" placeholder="댓글을 입력해주세요."></textarea><br>
                    <input type="button" id="comment_submit" class="btn btn-primary" value="작성"/>
                    <p class="help-block"></p>
                </div>
            </div>
        </fieldset>
    </form>
    <?php
    else :
    ?>
    댓글을 작성하시려면 <a href="/index.php/auth/get_login">로그인</a>이 필요합니다.
    <?php 
    endif;
    ?>

    <div id="comment_area">

    <input type="hidden" id="<?php echo $this->security->get_csrf_token_name(); ?>" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"/>

    <p>댓글 <?php echo $comment_count['comments']; ?></p>
    
    <?php
    if(empty($comment_list)){
    ?>
        <p>댓글이 없습니다. 첫번째 댓글을 작성해보세요!</p>
    <?php
    }elseif(isset($comment_list)){
    ?>
        <table cellpadding="0" cellspacing="0" class="table table-striped">
            <tbody>
                <?php 
                foreach ($comment_list as $lt) {
                ?>
                <tr>
                    <th scope="row"><?php echo $lt->user_id; ?></th>
                    <td id="comment_td"><?php echo $lt->comment_contents; ?></td>
                    <td>
                        <?php 
                        if( (@$this->session->userdata('logged_in') == TRUE && $this->session->userdata('user_id') == $lt->user_id) || (@$this->session->userdata('logged_in') == TRUE && $this->session->userdata('user_id') == 'admin') ) :
                        ?>
                        <a href="#" id="modify_comment" class="btn btn-warning" onclick="modifyCommentFunction('<?php echo $lt->comment_pk; ?>');"> 수정 </a>
                        <a href="/index.php/board_comment/ajax_comment_delete/<?php echo $this -> uri -> segment(3);?>/c_id/<?php echo $lt->comment_pk; ?>" class="btn btn-danger"> 삭제 </a>
                        <?php endif; ?>
                    <td>
                        <time datatime="<?php echo mdate('%Y-%M-%J',human_to_unix($lt->reg_date)); ?>">
                            <?php echo $lt->reg_date; ?>
                        </time>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    }
    ?>
    </div>
</article>
