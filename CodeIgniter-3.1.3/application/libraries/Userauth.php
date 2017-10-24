<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userauth{

    private $login_page = "";
    private $logout_page = "";

    private $username;
    private $password;

    //$attempt = $_POST['username'];

    /**
    * Turn off notices so we can have session_start run twice
    */
    public function __construct()
    {

      error_reporting(E_ALL & ~E_NOTICE);
      $this->login_page = base_url() . "index.php?/Login";
      $this->logout_page = base_url() . "index.php?/Home";
    }

    /**
    * @return string
    * @desc Login handling
    */
    public function login($username,$password)
    {

      session_start();

      // User is already logged in if SESSION variables are good.
      if ($this->validSessionExists() == true)
      {
        $this->redirect($_SESSION['basepage']);
      }

      // First time users don't get an error message....
      if ($_SERVER['REQUEST_METHOD'] == 'GET') return;

      // Check login form for well formedness.....if bad, send error message
      if ($this->formHasValidCharacters($username, $password) == false)
      {
         return "Username/password fields cannot be blank!";
      }

      if ($this->accountFrozen()){
          return 'Account Frozen!';
      }

      // verify if form's data coresponds to database's data
      if ($this->userIsInDatabase() == false)
      {
        return 'Invalid username/password!';
      }
      else
      {

        // We're in!
        // Redirect authenticated users to the correct landing page
        // ex: admin goes to admin, members go to members
           $CI =& get_instance();
           $CI->db->select('accesslevel');
           $CI->db->from('userslab6');
           $CI->db->where('username', $this->username);
           $this->accesslevel = $CI->db->get()->row()->accesslevel;

            if ($this->accesslevel == 'admin'){
                $this->writeSession();
                $this->redirect($_SESSION['basepage'] . 'Admin');
            } else if($this->accesslevel == 'member'){
                $this->writeSession();
                $this->redirect($_SESSION['basepage'] . 'Members');
            }
       }
    }

    /**
    * @return void
    * @desc Validate if user is logged in
    */
    public function loggedin($page)
    {

        session_start();

      // Users who are not logged in are redirected out
        if ($this->validSessionExists() == false)
        {
            $this->redirect($this->login_page);
        }

        $CI =& get_instance();
        $acl = $CI->config->item('acl');

        if($acl[strtolower($page)][$_SESSION['accesslevel']] == false)
        {
            $this->redirect($_SESSION['basepage'] . "Members");
        }
        else
        {
          return true;
        }

    }

    /**
    * @return void
    * @desc The user will be logged out.
    */
    public function logout()
    {
      session_start();
      $_SESSION = array();
      session_destroy();
      header("Location: ".$this->logout_page);
    }

    /**
    * @return bool
    * @desc Verify if user has got a session and if the user's IP corresonds to the IP in the session.
    */
    public function validSessionExists()
    {
      session_start();
      if (!isset($_SESSION['username']))
      {
        return false;
      }
      else
      {
        return true;
      }
    }

    /**
    * @return void
    * @desc Verify if login form fields were filled out correctly
    */
    public function formHasValidCharacters($username, $password)
    {
      // check form values for strange characters and length (3-12 characters).
      // if both values have values at this point, then basic requirements met
      if ( (empty($username) == false) && (empty($password) == false) )
      {
        $this->username = $username;
        $this->password = $password;
        return true;
      }
      else
      {
        return false;
      }
    }

    /**
    * @return bool
    * @desc Verify username and password with MySQL database.
    */
    public function userIsInDatabase()
    {

      // Remember: you can get CodeIgniter instance from within a library with:
      $CI =& get_instance();
      // And then you can access database query method with:
      $query = $CI->db->query("SELECT * FROM userslab6 ORDER BY compid ASC");

      foreach ($query->result_array() as $row) {

        $_SESSION['rowdata'] = $row;
          if ($this->username == $row['username'] && $this->password == $row['password']){
              return true;
          } else {
             $row = $query->next_row();
             if ($row == $query->last_row()){
                 return false;
             }
          }
      }

      //Switch to this method - doesn't have problem with row ids
      // $CI =& get_instance();
      // $searchDbStatement = "SELECT * FROM userslab6 where useremail = ?";
      // //$query = $CI->db->query("SELECT * FROM userslab6 where 'useremail' = '" . $this->username . "' ");
      //
      // $query = $CI->db->query($searchDbStatement, array($this->username));
      // $row = $query->row();
      //
      //
      // $_SESSION['rowdata'] = $row;
      // //check if the row is empty, if the input matches the db and if the password is verified
      // if (isset($row) && $this->username == $row->useremail && password_verify($this->password, $row->userpassword))
      // {
      //   return true;
      // }
      // else
      // {
      //   return false;
      // }
  }

  public function accountFrozen(){
      $CI =& get_instance();
      $CI->db->select('frozen');
      $CI->db->from('userslab6');
      $CI->db->where('username', $this->username);
      $account = $CI->db->get()->row()->frozen;

      if($account == 'Y'){
          return true;
      } else {
          return false;
      }
  }

    /**
    * @return void
    * @param string $page
    * @desc Redirect the browser to the value in $page.
    */
    public function redirect($page)
    {
        header("Location: ".$page);
        exit();
    }

    /**
    * @return void
    * @desc Write username and other data into the session.
    */
    public function writeSession()
    {
        $_SESSION['username'] = $this->username;
        $_SESSION['accesslevel'] = $this->accesslevel;
        $_SESSION['basepage'] = base_url() . "index.php?/";

    }

    /**
    * @return string
    * @desc Username getter, not necessary
    */
    public function getUsername()
    {
        return $_SESSION['username'];
    }

}
