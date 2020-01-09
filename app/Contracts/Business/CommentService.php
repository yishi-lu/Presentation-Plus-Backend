<?php

namespace App\Contracts\Business;

/**
 * Comment Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2019/12/28
 */

interface CommentService
{
    //fetch all comments of a post
    public function fetchPostComments($paging_info=20);

    //fetch all comments of a user
    public function fetchUserComments($user, $paging_info=20);

    //create a comment
    public function createComment($request);

    //edit a given comment
    public function editComment($request);

    //delete a comment
    public function deleteComment($request);

    //like a comment
    public function likeComment($id);

}