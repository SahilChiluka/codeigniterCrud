<?php

namespace App\Controllers;
use App\Models\UserModel;

class Home extends BaseController
{
    public function __construct() {
        helper(['url']);
        $this->user = new UserModel(); 
    }

    public function index()
    {
        if($this->request->getVar('search')) {
            echo view('/inc/header');
            $search = $this->request->getVar('search');
            $data['users'] = $this->user->like('username',"$search%", 'after')->paginate(5,'group');
            $data['users'] = $this->user->like('age',"$search%", 'after')->paginate(5,'group');
            $data['users'] = $this->user->like('email',"$search%", 'after')->paginate(5,'group');
            $data['pager'] = $this->user->pager;
            echo view('home',$data);
            echo view('/inc/footer');
        } else {
            echo view('/inc/header');
            $data['users'] = $this->user->orderby('id','ASC')->paginate(5,'group');
            $data['pager'] = $this->user->pager;
            echo view('home',$data);
            echo view('/inc/footer');
        }
    }

    public function saveUser() {
        $username = $this->request->getVar('username');
        $age = $this->request->getVar('age');
        $email = $this->request->getvar('email');

        $sqldata = $this->user->save([
            "username" => $username,
            "age" => $age,
            "email" => $email,
        ]);

        // insert data in mongo
        $url = 'http://localhost:3000/users/insert/';

        $id = $this->user->insertID();

        $mongoData = [
            '_id' => $id,
            'username' => $username,
            'age' => $age,
            'email' => $email
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mongoData));

        $response = curl_exec($ch);

        curl_close($ch);

        session()->setFlashdata("success", "Data inserted successfully");

        return redirect()->to(base_url("/"));
    }

    public function getSingleUser($id) {
        $data = $this->user->where('id',$id)->first();
        echo json_encode($data);
    }

    public function updateUser() {
        $id = $this->request->getVar('updateId');
        $username = $this->request->getVar('username');
        $age = $this->request->getVar('age');
        $email = $this->request->getvar('email');

        $data['username'] = $username;
        $data['age'] = $age;
        $data['email'] = $email;

        $this->user->update($id,$data);

        $url = 'http://localhost:3000/users/update/'.$id;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        curl_close($ch);

        return redirect()->to(base_url("/"));
    }

    public function deleteUser() {

        $id = $this->request->getVar('id');
        $this->user->delete($id);
        echo 1;

        $url = 'http://localhost:3000/users/delete/'.$id;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        // redirect('redirect/index');
        // return redirect()->to(base_url("/")); 
        //exit;
    }

    public function deleteMultiUser() {
        $ids = $this->request->getVar('ids');

        for($id=0; $id<count($ids); $id++) {
            $this->user->delete($ids[$id]);
        }
        echo 1;

        $newids = [
            'ids'=> $ids
        ];

        $url = 'http://localhost:3000/users/deleteMany';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newids));

        $response = curl_exec($ch);

        curl_close($ch);

        // echo "multi user deleted";
    }
}
