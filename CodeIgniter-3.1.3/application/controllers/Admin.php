<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

  var $TPL;

  public function __construct()
  {
    parent::__construct();
    // Your own constructor code
  $this->load->library('form_validation', 'session');
  $this->load->helper(array('form', 'url'));
   $this->TPL['loggedin'] = $this->userauth->loggedin('Admin');


   $this->TPL['active'] = array('home' => false,
                                'members'=>false,
                                'admin' => true,
                                'login'=>false);
    $this->TPL['newentryInvalid'] = false;


    $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[15]is_unique[userslab6.username]');

    $this->form_validation->set_rules('password', 'Last Name', 'trim|required|min_length[5]|max_length[20]');

    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

  }

  public function index()
  {
    $query = $this->db-> query("SELECT * FROM userslab6 ORDER BY compid ASC;");
    $this->TPL['listing'] = $query->result_array();
    $this->template->show('admin', $this->TPL);
  }

  public function delete($id)
  {
    $query = $this->db->query("DELETE FROM userslab6 where compid = '$id';");
    $this->template->show('admin', $this->TPL);
  }

  public function freeze($id)
  {
    // $this->form_validation->set_rules('username', 'Username', 'trim|required|callback_checkUsername');
    // $this->form_validation->set_rules('password', 'Password', 'trim|required');
    // $this->form_validation->set_rules('accesslevel', 'Access Level', 'trim|required|callback_checkAccessLevel');

    $frozenStatus = $this->db->query("SELECT frozen FROM userslab6 where compid = '$id';");
    $changedFreezeStatus;
    if ($frozenStatus == 'Y')
    {
      $changedFreezeStatus = 'N';
    }
    else
    {
      $changedFreezeStatus = 'Y';
    }
    $query = $this->db->query("UPDATE userslab6 " .
                              "SET frozen = '$changedFreezeStatus' WHERE compid = '$id';");


    $this->template->show('admin', $this->TPL);
  }

  public function newentry()
  {
    if($this->form_validation->run() != FALSE)
    {
      $username = $this->input->post("username");
      $password = $this->input->post("password");
      $accesslevel = $this->input->post("accesslevel");
      $query = $this->db->query("INSERT INTO userslab6 VALUES (NULL, '$username', '$password', '$email', 'N');");

      redirect(current_url());
    }
    else
    {
      $this->TPL['newentryInvalid'] = true;
      $this->template->show('admin', $this->TPL);
    }
  }

}
