<?php 
namespace App\Controllers;
use App\Models\RegisterModel;

class Login extends BaseController {

    public function __construct() {
        helper(['url']);
        $this->register = new RegisterModel();
    }

    public function index() {
        echo view('login');
    }

    public function toHome() {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->register->where('email',$email)->find();

        if($user) {
            $enc_password = $user[0]['password'];
            $dec_password = password_verify($password, $enc_password);

            if($dec_password) {
                $session = session();
                $session->set('email', $email);
                return redirect()->to(base_url("/home"));
            } else {
                session()->setFlashdata("error", "Wrong Password");
                return redirect()->to(base_url("/"));
            }

        } else {
            session()->setFlashdata("error", "Email doesn't exist");
            return redirect()->to(base_url("/"));
        }
    } 
    

}
?>