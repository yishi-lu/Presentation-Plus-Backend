<?php

namespace App\Contracts\Business;

/**
 * Business Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2019/12/28
 */

interface BusinessService
{
    //fetch all posts in database, filter and order can be applied
    public function fetchAllPosts($filter=null, $order=null, $paging_info=2);

    //fetch all posts of a user, filter and order can be applied
    public function fetchUserPosts($user, $filter=null, $order=null, $paging_info=2);

    //fecth detail information of a post
    public function fetchOnePost($id);

    //create a post to a user
    public function createOnePost($request);

    //edit a given post
    public function editOnePost($request);

    //delete a given post
    public function deletePost($request);

    //collect a post by auth user
    public function collectPost($post_id);

    public function fetchCollectedPosts();
}