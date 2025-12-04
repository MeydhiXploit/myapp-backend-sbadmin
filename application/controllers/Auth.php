<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $this->form_validation->set_rules('email','Email','required|trim|valid_email');
        $this->form_validation->set_rules('password','Password','required|trim');
        if($this->form_validation->run() == FALSE){
            $data['title'] = 'Login page';
            $this->load->view('template/auth_header',$data);
            $this->load->view('auth/login');
            $this->load->view('template/auth_footer');
        }else{
            $this->_login();
        }
    }

    private function _login(){
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        $user = $this->db->get_where('user',['email' => $email])->row_array();
        
        //jika usernya ada
        if($user){
            //jika usernya active
            if($user['is_active'] == 1){
                //cek passwordnya
            }else{
                $this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">This Email has not been activated!</div>'); 
                redirect('auth');    
            }
        }else{
            $this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">Email is not registered</div>'); 
            redirect('auth');
        }
    }


    public function registration(){
        $this->form_validation->set_rules('name','Name','required|trim');
        $this->form_validation->set_rules('email','Email','required|trim|is_unique[user.email]',[
            'is_unique' => 'This email has already register!'
        ]);
        $this->form_validation->set_rules('password1','Password','required|trim|min_length[3]|matches[password2]',[
            'matches' => 'Password dont match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2','Password','required|trim|matches[password1]');
        if( $this->form_validation->run() == false){    
            $this->load->library('form_validation');
            $data['title'] = 'MedeV User Registration';
            $this->load->view('template/auth_header',$data);
            $this->load->view('auth/registration');
            $this->load->view('template/auth_footer');
        }else{
            $data = [
                'name' => htmlspecialchars($this->input->post('name',TRUE)),
                'email' => htmlspecialchars($this->input->post('email',TRUE)),
                'image' => 'default.jpg',
                'password' => md5($this->input->post('password')),
                'role_id' => 2,
                'is_active' => 1,
                'date_created' => time()
            ];
            $this->db->insert('user',$data);
            $this->session->set_flashdata('message','<div class="alert alert-success" role="alert">Congratulation! your account has been created.Please login</div>');
            redirect('auth');
        }
    } 
}