<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation','encryption');
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->model('report_model');
        if(!$this->session->userdata('user_email')) {
            redirect('login');
        }
    }
    public function home(){
      $data['title'] = 'Home';
      $data['feed']=$this->report_model->userHome($showStatus="1");
      // $this->load->view('user_home/server_migration');
      // $data['feed']=$this->report_model->userHome();
      $this->load->view('userhome/header',$data);
      $this->load->view('userhome/home',$data);
      $this->load->view('userhome/footer');
    }
    public function submit()
    {
        $data['title'] = 'Submit a new opportunity';
        $this->load->view('userhome/header',$data);
        $this->load->view('userhome/new_post');
        $this->load->view('userhome/footer');
    }
    public function process()
    {
      require("./sendgrid/vendor/autoload.php");
      $data = "";
      $data=$this->security->xss_clean($data);
      $temp = $this->input->post('company')." at ".$this->input->post('title');
      $id_link = preg_replace('/\s+/', '-', $temp);
      $user_email=$_SESSION['user_email'];
      $this->db->where('user_email',$user_email);
      $query=$this->db->get('login_users');
      $row = $query->row();
      $temp = array(
          'lid' => $row->lid,
          'user_email' => $row->user_email,
          'user_company' => $row->user_company,
          'author_first_name' => $row->first_name,
          'author_last_name' => $row->last_name,
          'user_phone' =>$row->phone,
          'validated' => true
      );
      $this->session->set_userdata($temp);
      $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $code = substr(str_shuffle($set), 0, 12);
      // echo '<pre>'; print_r($this->session->all_userdata());
      $message = 	"
            <html>
            <head>
              <title>Verify Post</title>
            </head>
            <body>
              <h2>Verify Post</h2>
              <p>Post details:</p>
              <p>Title: ".$this->input->post('title')."</p>
              <p>Company: ".$this->input->post('company')."</p>
              <p>Last Date: ".$this->input->post('last-date')."</p>
              <p>Registration Link: ".$this->input->post('website')."</p>
              <p>Location: ".$this->input->post('location')."</p>
              <p>Email: ".$this->input->post('email')."</p>
              <p>More Details: ".$this->input->post('moreInfo')."</p>
              <p>Please click the link below to verify the information.</p>
              <h4><a href='".base_url()."Admin/verifyPost/".$code."/".$_SESSION['user_phone']."'>Verify</a></h4>
            </body>
            </html>
            ";
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("no-reply@ecetkmce.live", "Information Center ECETKMCE");
        $email->setSubject("Verify Post");
        $email->addTo("thameemk612@ieee.org");
        $email->addContent(
          "text/html", $message
        );
        $sendgrid = new \SendGrid('PUT_YOUR_API_KEY_HERE');

        try {
            $response = $sendgrid->send($email);
            $status = $response->statusCode();
            // print $response->statusCode() . "\n";
           // print_r($response->headers());
           // print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        if($status=='202'){
          $data = array(
            'title' => $this->input->post('title'),
            'place' => $this->input->post('company'),
            'last_date' => $this->input->post('last-date'),
            'reg_link' => $this->input->post('website'),
            'location' => $this->input->post('location'),
            'email' => $this->input->post('email'),
            'user_phone' => $_SESSION['user_phone'],
            'details' => $this->input->post('moreInfo'),
            'id_link' => $id_link,
            'user_email' => $_SESSION['user_email'],
            'company' => $_SESSION["user_company"],
            'author_first_name' => $_SESSION['author_first_name'],
            'author_last_name' => $_SESSION['author_last_name'],
            'code' => $code,
          );
          $this->report_model->form($data);
          $this->session->set_flashdata('msg', 'After verification it will show to website !');
          redirect('User/submit');
          // print $response->statusCode() . "\n";
          // echo "flag1";exit;
        }
        else{
          $this->session->set_flashdata('msgreq', 'Some error has been occured. Contact Web admin!');
          redirect('User/submit');
          // print $response->statusCode() . "\n";
          // echo "flag2";exit;
        }
    }

    public function profile()
    {
        $user_email=$_SESSION['user_email'];
        $data['user']=$this->report_model->userUploads($user_email);
          // foreach ($data['user'] as $row ) {
          //   $post_date =  substr($row['timeStamp'],0,10);
          //   echo $post_date;
          //   echo "<br>";
          // }
        // exit;
        $this->db->where('user_email',$user_email);
        $query=$this->db->get('login_users');
        $row = $query->row();
        $temp = array(
            'lid' => $row->lid,
            'user_email' => $row->user_email,
            'user_company' => $row->user_company,
            'author_first_name' => $row->first_name,
            'author_last_name' => $row->last_name,
            'phone'=>$row->phone,
            'img'=> $row->gravatar,
            'validated' => true
        );
        $this->session->set_userdata($temp);
        //count posts
        $this->db->where('user_email',$user_email);
        $temp2=$this->db->get('feedHome');
        $data['num_rows'] =  $temp2->num_rows();
        //session values
        $data['img'] = $_SESSION['img'];
        $data['user_company'] = $_SESSION['user_company'];
        $data['user_email'] = $_SESSION['user_email'];
        $data['phone'] = $_SESSION['phone'];

        $fullname =  $_SESSION['author_first_name']." ". $_SESSION['author_last_name'];  ;
        $data['title'] = $fullname;
        // $this->load->view('user_home/user_header',$data);
        // $this->load->view('user_home/profile',$data);
        // $this->load->view('user_home/user_footer');
        $this->load->view('userhome/header',$data);
        $this->load->view('userhome/profile',$data);
        $this->load->view('userhome/footer');

    }
    public function editprofile()
    {
        $data['title'] = 'Edit profile';
        $this->load->view('userhome/header',$data);
        $this->load->view('userhome/editprofile');
        $this->load->view('userhome/footer');
    }
}
