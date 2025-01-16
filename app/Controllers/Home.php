<?php

namespace App\Controllers;
use App\Models\UserModel;
use App\Config\Database;
ini_set('max_execution_time', 3600);

class Home extends BaseController
{
    public function __construct() {
        helper(['url']);
        $this->user = new UserModel();
    }

    public function index()
    {
        // $search = $this->request->getVar('search');

        // $username = $this->request->getVar('username');
        // $age = $this->request->getVar('age');
        // $email = $this->request->getVar('email');

        // $data['all_users'] = $this->user->orderBy('username', 'ASC')->findAll();

        // if($search) {
        //     $data['users'] = $this->user->like('username', "$search%", 'after')->orderBy('username', 'ASC')->paginate(5,'group');
            
        // }
        // else if($username || $age || $email) {
        //     $query = $this->user;
        //     // orwhere joins query with the name,age,email
        //     // where username = 'Anupam' OR age = 50 OR email = 'anupam@gmail.com'
        //     if($username) {
        //         $query = $query->where('username', $username);
        //     }
        //     if($age) {
        //         $query = $query->orWhere('age', $age);
        //     }
        //     if($email) {
        //         $query = $query->orWhere('email', $email);
        //     }
        //     $data['users'] = $query->paginate(5,'group');
        // } 
        // else {
        //     $data['users'] = $data['all_users'];
        //     $data['users'] = $this->user->paginate(5,'group');
        // }
        // echo view('/inc/header');
        // $this->user->paginate(5,'group');
        // $data['pager'] = $this->user->pager;
        // echo view('home', $data);
        // echo view('/inc/footer');

        $search = $this->request->getVar('search');
        $username = $this->request->getVar('username');
        $age = $this->request->getVar('age');
        $email = $this->request->getVar('email');

        $data['all_users'] = $this->user->orderBy('username', 'ASC')->findAll();
        $query = $this->user;
        if ($search) {
            $query->like('username', "$search%", 'after');
        }
        if ($username) {
            $query->where('username', $username);
        }
        // orwhere joins query with the name,age,email
        // where username = 'Anupam' OR age = 50 OR email = 'anupam@gmail.com'
        if ($age) {
            $query->orWhere('age', $age);
        }
        if ($email) {
            $query->orWhere('email', $email);
        }
        $data['users'] = $query->orderBy('username', 'ASC')->paginate(8, 'group');
        $data['pager'] = $query->pager;
        echo view('/inc/header');
        echo view('home', $data);
        echo view('/inc/footer');
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

        return redirect()->to(base_url("/home"));
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

        return redirect()->to(base_url("/home"));
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

    public function logout() {
        $session = session();

        $session->destroy();

        return redirect()->to(base_url("/"));
    }

    // public function filterUser() {
    //     $username = $this->request->getVar('username');
    //     $age = $this->request->getVar('age');
    //     $email = $this->request->getvar('email');

    //     if($username || $age || $email) {
    //         $a['users'] = $this->user->where('username',$username)->paginate(5,'group');
    //         $x['users'] = $this->user->where('age',$age)->paginate(5,'group');
    //         $y['users'] = $this->user->where('email',$email)->paginate(5,'group');

    //         $data['users'] = array_merge($a['users'],$x['users'],$y['users']);

    //         $newArray = [];

    //         for ($i = 0; $i < count($data['users']); $i++) {
    //             $item = $data['users'][$i];
    //             $id = $item['id']; // 'id' is the unique identifier
    //             // Only add the user if the id does not already exist in the unique array
    //             if (!array_key_exists($id, $newArray)) {
    //                 $newArray[$id] = [
    //                     'id' => $item['id'],
    //                     'username' => $item['username'],
    //                     'age' => $item['age'],
    //                     'email' => $item['email']
    //                 ];
    //             }
    //         }

    //         $data['users'] = $newArray;
    //         echo view('/inc/header');
    //         $data['pager'] = $this->user->pager;
    //         echo view('home',$data);
    //         echo view('/inc/footer');
    //     } else {
    //         echo view('/inc/header');
    //         $data['users'] = $this->user->orderby('id','ASC')->paginate(5,'group');
    //         $data['pager'] = $this->user->pager;
    //         echo view('home',$data);
    //         echo view('/inc/footer');
    //     }

        
        
    //     // print_r($data['users']);
    //     // echo "<br/>";
    //     // print_r($x['users']);
    //     // echo "<br />";
    //     // print_r($y['users']);

    //     // $a = array_merge($data['users'],$x['users']);
    //     // print_r($a);
        
    //     // $data['users'] = array_merge($a['users'],$x['users'],$y['users']);

    //     // print_r($data['users']);
    //     // $unique = array_unique($merge);
    //     // echo $unique;
        
    //     // $user = $data['users'][0]['username'];
    //     // $ag = $x['users'][0]['age'];
    //     // $em = $y['users'][0]['email'];
    //     // // echo $user;
    //     // echo $ag;
    //     // echo $em;

    //     // $newArray = [];

    //     // for($i=0; $i<count($data['users']); $i++) {
    //     //     // foreach($data['users'][$i] as $key => $value) {
    //     //     //     array_push($newArray[$key] = $value);
    //     //     // }
    //     //     foreach ($data['users'][$i] as $item) {
    //     //         $id = $item['id'];
    //     //         $username = $item['username'];
    //     //         $age = $item['age'];
    //     //         $email = $item['email'];
    //     //         // Only add the key-value pair if the key does not already exist
    //     //         if (!array_key_exists($key, $uniqueArray)) {
    //     //             $uniqueArray[$key] = $value;
    //     //         }
    //     //     }
    //     // }
    //     // for ($i = 0; $i < count($data['users']); $i++) {
    //     //     $item = $data['users'][$i];
    //     //     $id = $item['id']; // 'id' is the unique identifier
    //     //     // Only add the user if the id does not already exist in the unique array
    //     //     if (!array_key_exists($id, $newArray)) {
    //     //         $newArray[$id] = [
    //     //             'id' => $item['id'],
    //     //             'username' => $item['username'],
    //     //             'age' => $item['age'],
    //     //             'email' => $item['email']
    //     //         ];
    //     //     }
    //     // }
    //     // print_r($newArray);
    //     // echo "<br>";

    //     // $data['users'] = $newArray;
    //     // echo view('/inc/header');
    //     // $data['pager'] = $this->user->pager;
    //     // echo view('home',$data);
    //     // echo view('/inc/footer');
        
    //     // echo 1;
    //     // return redirect()->to(base_url("/home"));

    // }

    public function download() {
        $filename = 'users_data' . date('Ymd') . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        // get data 
        $usersData = $this->user->findAll();

        // file creation 
        $file = fopen('php://output', 'w');

        $header = array("ID", "Username","Age", "Email");

        fputcsv($file, $header);

        foreach ($usersData as $key => $line) {
            fputcsv($file, $line);
        }

        fclose($file);
    }

    public function upload() {

        $file = $this->request->getFile('uploadfile');

        // Check if there was an upload error
        if ($file->getError() > 0) {
            return redirect()->back()->with('error', 'Upload failed: ' . $file->getErrorString());
        }

        // Validate file type
        $ext = $file->getClientExtension();
        if ($ext !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV files are allowed');
        }
        // Create uploads directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Move uploaded file
        $newName = $file->getRandomName();
        if (!$file->move($uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Failed to move uploaded file');
        }

        $filepath = $uploadPath . $newName;

        $mongodata = []; // creating empty array to insert data into MongoDB

        // Process CSV file
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            $userModel = new UserModel();
            $db = \Config\Database::connect();
            $db->transStart(); // Start transaction

            $firstRow = true;
            $successCount = 0;
            $errorCount = 0;

            while (($filedata = fgetcsv($handle, 10000, ",")) !== FALSE) {
                // Skip header row
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                // Ensure we have all required fields
                if (count($filedata) >= 3) { // Adjust based on your CSV structure
                    $data = [
                        'username' => trim($filedata[0]), // Assuming first column is username
                        'age'      => trim($filedata[1]), // Assuming second column is age
                        'email'    => trim($filedata[2])  // Assuming third column is email
                    ];

                    // Basic validation
                    if (!empty($data['email']) && !empty($data['username']) && !empty($data['age'])) {
                      
                            // Check for existing user
                            $existingUser = $userModel->where('email', $data['email'])->first();

                            if ($existingUser) {
                                $userModel->update($existingUser['id'], $data);
                            } else {
                                $userModel->insert($data);
                                $id = $userModel->insertID();
                                $mongodata[$successCount] = [
                                    '_id' => $id,
                                    'username' => $data['username'],
                                    'age' => $data['age'],
                                    'email' => $data['email']
                                ];
                                $successCount++;   
                            }
                    } else {
                        $errdata[] = $data;
                        $errorCount++;
                    }
                }
            }

            fclose($handle);
            // unlink($filepath); // Delete the temporary file
            
            // print_r($errdata);
            // echo "<br />";
            // echo $errorCount;

            $db->transComplete(); // Complete transaction

            if ($db->transStatus() === FALSE) {
                return redirect()->to(base_url('/home'))
                    ->with('error', 'Transaction failed. Some records may not have been imported.');
            }

            $message = "";

            if($successCount >= 0 && $errorCount >= 0){
                $message = "Imported $successCount records. $errorCount records failed to import.";
            }

            $url = 'http://localhost:3000/users/insertMany';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mongodata));

            curl_exec($ch);

            curl_close($ch);

            if (!empty($errdata)) {
                $invalidFilePath = WRITEPATH . 'uploads/' . time() . '.csv';
                $output = fopen($invalidFilePath, 'w');
                $headers = array("username","age", "email");
                fputcsv($output, $headers);
                foreach ($errdata as $invalidRow) {
                    fputcsv($output, $invalidRow);
                }
                fclose($output);
                $this->response->download($invalidFilePath, null)->setFileName('invalid_entries.csv');
            }
            return redirect()->to(base_url('/home'))->with('success', $message);
        }

        return redirect()->to(base_url('/home'))->with('error', 'Could not open file for reading');
        
    }
    
}   
