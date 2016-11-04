<?php
/**
 * Created by PhpStorm.
 * User: Spencer
 * Date: 11/3/2016
 * Time: 4:56 PM
 */
class Toggle extends Application {
    public function index()	{
        $origin = $_SERVER['HTTP_REFERER'];
        $role = $this->session->userdata('userrole');
        if ($role == 'user') $role = 'admin';
        else $role = 'user';
        $this->session->set_userdata('userrole', $role);
        redirect($origin);
    }
}