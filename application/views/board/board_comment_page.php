
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
</script>

<input type="hidden" id="<?php echo $this->security->get_csrf_token_name(); ?>" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"/>
<p>댓글 <?php echo count($comment_list); ?></p>
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
