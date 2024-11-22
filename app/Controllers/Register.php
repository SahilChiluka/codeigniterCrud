<?php 
namespace App\Controllers;

use App\Models\RegisterModel;

class Register extends BaseController {

    public function __construct() {
        helper(['url']);
        $this->register = new RegisterModel();
    }

    public function index() {
        echo view('register');
    }

    public function toLogin() {

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $confirmpassword = $this->request->getVar('confirmpassword');

        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        $email_exist = $this->register->where('email',$email)->findAll();

        if($email_exist) {
            session()->setFlashdata("error", "Email already exist's");
            return redirect()->to(base_url("/register"));
        }
        if($password == $confirmpassword) {
            $register = $this->register->save([
                'email' => $email,
                'password' => $hash_password
            ]);
            return redirect()->to(base_url("/"));
        } else {
            session()->setFlashdata("error", "Password and Confirm Password does not match");
            return redirect()->to(base_url("/register"));
        }
    
    }
}
?>