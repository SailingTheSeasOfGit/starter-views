<?php
/**
 * Created by PhpStorm.
 * User: Spencer
 * Date: 11/3/2016
 * Time: 5:03 PM
 */
class Crud extends Application {
    public function index()	{
        $role = $this->session->userdata('userrole');
        $message = "You are not a user";
        if ($role == 'user')
            $message = "You are a user not an admin";
        elseif($role == 'admin'){
            $message = "You are an admin and better than a user";
        }
        $this->data['content'] = $message;
        $this->render('template');
    }
}