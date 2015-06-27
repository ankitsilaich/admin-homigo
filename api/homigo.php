<?php

require_once 'NotORM.php';

$connection = new PDO('mysql:dbname=homig7y7_main;host=localhost', 'homig7y7_main', 'homigo10450');

$db = new NotORM($connection);

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
session_start();

$authenticate = function($app)
{
    return function() use ($app)
    {
        if (!isset($_SESSION['user'])) {
            
            
            $app->redirect('/login');
        }
    };
};
$app->post("/auth/process/admin", function() use ($app, $db)
{
    $array    = (array) json_decode($app->request()->getBody());
    $email    = $array['email'];
    $password = $array['password'];
    $person   = $db->admin()->where('email', $email)->where('password', $password);
    $count    = count($person);
    
    if ($count == 1) {
        
        $_SESSION['user'] = $email;
        $data             = array(
            'login_success' => "true",
            'login_attempt_by' => $email,
            'message' => "Successfull sigin"
            
        );
        
    } else {
        $data = array(
            'login_success' => "false",
            'login_attempt_by' => $email,
            'message' => "please provide correct details"
            
        );
        
        
        
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
    
    
    
});
$app->get('/auth/process/admin', function() use ($app)
{
    
    if (isset($_SESSION['user'])) {
        $data = $_SESSION['user'];
    } else {
        $data = false;
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});

$app->get("/auth/logout/admin", function() use ($app)
{
    unset($_SESSION['user']);
    
    
});

//Get Method to get the data from database
function days($givendate)
{
    $now       = time(); // or your date as well
    $your_date = strtotime($givendate);
    $datediff  = $your_date - $now;
    return floor($datediff / (60 * 60 * 24));
    
}
;

$app->get('/houses(/:id)', function($id = null) use ($app, $db)
{
    
    if ($id == null) {
        $data  = array();
        $count = 0;
        foreach ($db->houses() as $houses) {
            
            $houses_tenants = array();
            $houses_deposits = array();
         
            foreach ($houses->houses_tenants() as $p) {
                $count++;
                $houses_tenants[] = array(
                    'id' => $p->tenants['id'],
                    'name' => $p->tenants["name"],
                    'phone' => $p->tenants["phone"]
                );
            }
             foreach ($houses->houses_deposits() as $p) {
                
                $houses_deposits[] = array(
                    'id' => $p->deposits['id'],
                    'rent' => $p->deposits["rent"],
                    'date' => $p->deposits["date"],
                     'status' => $p->deposits["status"]
                );
            }
            
            $data[] = array(
                'house_id' => $houses['id'],
                'house_name' => $houses['name'],
                'house_address' => $houses['address'],
                'house_entry_date' => $houses['entry_date'],
                'house_fixed_date' => $houses['due_date'],
                'house_rent' => $houses['rent'],
                'house_no_of_rooms' => $houses['totalrooms'],
                'house_totaldeposit' => $houses['totaldeposit'],
                'house_totaldepositleft' => $houses['depositleft'],
                'house_dthbill' => $houses['dthbill'],
                'house_dthbilldate' => $houses['dthbilldate'],
                'house_dthbilldays' => days($houses['dthbilldate']),
                'house_powerbill' => $houses['powerbill'],
                'house_powerbilldate' => $houses['powerbilldate'],
                'house_powerbilldays' => days($houses['powerbilldate']),
                'house_wifibill' => $houses['wifibill'],
                'house_wifibilldate' => $houses['wifibilldate'],
                'house_electricitybilldate' => $houses['electricitybilldate'],
                'house_wifibilldays' => days($houses['wifibilldate']),
                'house_owner' => array(
                    'id' => $houses->owners['id'],
                    'name' => $houses->owners['owner_name'],
                    'address' => $houses->owners['owner_address'],
                    'phone' => $houses->owners['owner_phone'],
                    'email' => $houses->owners['owner_email']
                    
                ),
                'tenants' => $houses_tenants,
                'deposits' => $houses_deposits,
                
                
            );
        }
    } else {
        
        $data = null;
        
        if ($houses = $db->houses()->where('id', $id)->fetch()) {
          
            $houses_tenants = array();
            $houses_deposits = array();
            foreach ($houses->houses_tenants() as $p) {
                
                $houses_tenants[] = array(
                    'id' => $p->tenants['id'],
                    'name' => $p->tenants["name"],
                    'phone' => $p->tenants["phone"]
                );
            }
            foreach ($houses->houses_deposits() as $p) {
                
                $houses_deposits[] = array(
                    'id' => $p->deposits['id'],
                    'rent' => $p->deposits["rent"],
                    'date' => $p->deposits["date"],
                     'status' => $p->deposits["status"]
                );
            }

            $data = array(
                'house_id' => $houses['id'],
                'house_name' => $houses['name'],
                'house_address' => $houses['address'],
                'house_fixed_date' => $houses['due_date'],
                'house_entry_date' => $houses['entry_date'],
                'house_rent' => $houses['rent'],
                'house_no_of_rooms' => $houses['totalrooms'],
                'house_totaldeposit' => $houses['totaldeposit'],
                'house_totaldepositleft' => $houses['depositleft'],
                'house_dthbill' => $houses['dthbill'],
                'house_dthbilldate' => $houses['dthbilldate'],
                'house_dthbilldays' => days($houses['dthbilldate']),
                'house_powerbill' => $houses['powerbill'],
                'house_powerbilldate' => $houses['powerbilldate'],
                 'house_electricitybilldate' => $houses['electricitybilldate'],
                'house_powerbilldays' => days($houses['powerbilldate']),
                'house_wifibill' => $houses['wifibill'],
                'house_wifibilldate' => $houses['wifibilldate'],
                'house_wifibilldays' => days($houses['wifibilldate']),
                'house_owner' => array(
                    'id' => $houses->owners['id'],
                    'name' => $houses->owners['owner_name'],
                    'address' => $houses->owners['owner_address'],
                    'phone' => $houses->owners['owner_phone'],
                    'email' => $houses->owners['owner_email']
                ),
                'tenants' => $houses_tenants,
                'deposits' => $houses_deposits,
               
            );
        }
    }
    $houses = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($houses);
});

$app->get('/tenants(/:id)', function($id = null) use ($app, $db)
{
     $tenants_deposits = array();
    if ($id == null) {
        $data  = array();
        $count = 0;
       
         
        foreach ($db->tenants() as $tenants) {
           
            
            
            $days   = days($tenants['entry_date']);
            //  $dthdays = days($houses['house_rent_due_date']);
            //  foreach ($houses->houses_tenants() as $p) {
            //      $count++;
            //         $houses_tenants[] = array('name'=>$p->tenants["name"],
            //          'phone'=>$p->tenants["phone"]);
            // }
            $data[] = array(
                'id' => $tenants['id'],
                'address' => $tenants['address'],
                'name' => $tenants['name'],
                'phone' => $tenants['phone'],
                'company' => $tenants['company'],
                'rent' => $tenants['rent'],
                'totaldeposit' => $tenants['totaldeposit'],
                'depositleft' => $tenants['depositleft'],
                'entry_date' => $tenants['entry_date'],
                'is_verified' => $tenants['isVerified'],
                'deposits' => $tenants_deposits,
                'is_Paid' => $tenants['isPaid']
                
                // 'house_totaldeposit' => $houses['house_total_deposit'],
                // 'house_totaldepositleft' => $houses['house_deposit_left'],
                // 'house_dthbill' => $houses['house_dth_bill_amount'],
                // 'house_dthbilldays' => days($houses['house_dth_bill_date']),
                // 'house_owner' => array('name' =>   $houses->owners['owner_name'],
                //                             'address' => $houses->owners['owner_address']
                //                              ),
                // 'tenants' => $houses_tenants,
                // 'days' => $days
                
            );
        }
    } else {
        
        $data = null;
        $houses_tenants=array();
       
        if ($tenants = $db->tenants()->where('id', $id)->fetch()) {
            $days = days($tenants['entry_date']);
            foreach ($tenants->tenants_deposits() as $p) {
                
                $tenants_deposits[] = array(
                    'id' => $p->deposits['id'],
                    'rent' => $p->deposits["rent"],
                    'date' => $p->deposits["date"],
                    'status' => $p->deposits["status"]
                );
            }
            foreach ($tenants->houses_tenants() as $p) {
                
                $houses_tenants[] = array(
                    'id' => $p->houses['id'],
                     'name' => $p->houses['name'],

                    
                );
            }
             
             
           
            $data = array(
                'id' => $tenants['id'],
                'address' => $tenants['address'],
                'name' => $tenants['name'],
                'phone' => $tenants['phone'],
                'company' => $tenants['company'],
                'rent' => $tenants['rent'],
                'totaldeposit' => $tenants['totaldeposit'],
                'depositleft' => $tenants['depositleft'],
                'entry_date' => $tenants['entry_date'],
                'is_verified' => $tenants['isVerified'],
                'rentfirst' => $tenants['rentfirst'],
                'deposits' => $tenants_deposits,
                 'currenthouse' => $houses_tenants,
                 'is_Paid' => $tenants['isPaid']
                
                
            );
        }
    }
    $tenants = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($tenants);
});
$app->get('/owners(/:id)', function($id = null) use ($app, $db)
{
     
    if ($id == null) {
        $data  = array();
        
       
         
        foreach ($db->owners() as $t) {
           
            
            
            
           
            $data[] = array(
                'id' => $t['id'],
                'address' => $t['owner_address'],
                'name' => $t['owner_name'],
                'phone' => $t['owner_phone'],
                'email' => $t['owner_email']
              
                
               
                
            );
        }
    } else {
        
        $data = null;
        
        if ($t = $db->owners()->where('id', $id)->fetch()) {
            
            
            $data[] = array(
                'id' => $t['id'],
                'address' => $t['owner_address'],
                'name' => $t['owner_name'],
                'phone' => $t['owner_phone'],
                'email' => $t['owner_email']
              
                
               
                
            );
        }
    }
    $owners = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($owners);
});
$app->get('/tenants/search/:id', function($id = null) use ($app, $db)
{
    
    $data  = array();
    $count = 0;
     
    foreach ($db->tenants()->where("NOT id", $db->houses_tenants()->select('tenants_id'))->where("name LIKE ?", "%" . $id . "%") as $tenants) {
        
        
        $days   = days($tenants['entry_date']);
        //  $dthdays = days($houses['house_rent_due_date']);
        //  foreach ($houses->houses_tenants() as $p) {
        //      $count++;
        //         $houses_tenants[] = array('name'=>$p->tenants["name"],
        //          'phone'=>$p->tenants["phone"]);
        // }
        $data[] = array(
            'id' => $tenants['id'],
            'address' => $tenants['address'],
            'name' => $tenants['name'],
            'phone' => $tenants['phone'],
            'company' => $tenants['company'],
            'rent' => $tenants['rent'],
            'totaldeposit' => $tenants['totaldeposit'],
            'depositleft' => $tenants['depositleft'],
            'entry_date' => $tenants['entry_date'],
            'rent_date' => $days
            
            
        );
    }
    
    $tenants = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($tenants);
});
$app->get('/owners/search/:id', function($id = null) use ($app, $db)
{
    
    $data = array();
    
    foreach ($db->owners()->where("owner_name LIKE ?", "%" . $id . "%") as $owners) {
        
        
        $data[] = array(
            'id' => $owners['id'],
            'address' => $owners['owner_address'],
            'name' => $owners['owner_name'],
            'phone' => $owners['owner_phone'],
            'email' => $owners['owner_email']
            
            
            
        );
    }
    
    $tenants = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($tenants);
});

//Post method to insert data into database

$app->post('/houses', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->houses()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    
});
$app->post('/houses_tenants', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->houses_tenants()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data);
    
});
$app->post('/houses_deposits', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->houses_deposits()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data);
    
});
$app->post('/tenants_deposits', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->tenants_deposits()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data);
    
});
$app->post('/owners', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->owners()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    
});
$app->post('/deposits', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->deposits()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    
});
$app->post('/tenants', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->tenants()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    //echo json_encode($array);
    
});


//Put method to update the data into database

$app->put('/houses/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = $app->request()->put();
        $info = array(
            "house_rent_amount" => $post['house_rent_amount']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
});
$app->put('/updatehouses/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
       $post = (array) json_decode($app->request()->getBody());
        $info = array(
             
                'name' => $post['name'],
                'address' => $post['address'],
               
                'entry_date' => $post['entrydate'],
                'rent' => $post['rent'],
                'totalrooms' => $post['totalrooms'],
                'totaldeposit' => $post['totaldeposit'],
                'depositleft' => $post['depositleft'],
                'dthbill' => $post['dthbill'],
                'dthbilldate' => $post['dthdate'],
               
                'powerbill' => $post['powerbill'],
                'powerbilldate' => $post['powerdate'],
                
               
                'wifibill' => $post['wifibill'],
                'wifibilldate' => $post['wifidate'],
                 'electricitybilldate' => $post['electricitydate']
                
                
                
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
  
});
$app->put('/updateowners/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->owners()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
       $post = (array) json_decode($app->request()->getBody());
        $info = array(
             
                'owner_name' => $post['name'],
                'owner_address' => $post['address'],
               
                'owner_phone' => $post['phone'],
                'owner_email' => $post['email']
               
                
                
                
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
  
});
$app->put('/updatetenants/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->tenants()->where('id', $id);
    
    
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
       $post = (array) json_decode($app->request()->getBody());
        $info = array(
             
                 
                'address' => $post['address'],
                'name' => $post['name'],
                'phone' => $post['phone'],
                'company' => $post['company'],
                'rent' => $post['rent'],
                'rentfirst' => $post['rentfirst'],
                'totaldeposit' => $post['totaldeposit'],
                'depositleft' => $post['depositleft'],
                'entry_date' => $post['entry_date']
                
                
                
                
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
  
});
$app->put('/tenants/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->tenants()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "entry_date" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($body)
    ;
});
$app->put('/firstmonthrent/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->tenants()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "isPaid" => 1
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($body)
    ;
});
$app->put('/electricitybillpay/:id', function($id = null) use ($app, $db)
{
    $houses = $db->houses()->where('id', $id);
    
    
   
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "electricitybilldate" => $post['newdate']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
     $app->response()->header('Content-Type', 'application/json');
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($body)
    ;
});
$app->put('/tenantverify/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->tenants()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "isVerified" => 1
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($body)
    ;
});
$app->put('/houses/rent/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "entry_date" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/deposits/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->deposits()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "status" => '1'
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/tenants/deposits/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->deposits()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "status" => '1'
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/electricity/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "powerbilldate" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/wifi/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "wifibilldate" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/owner/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "owners_id" => $post['owners_id']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/dth/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "dthbilldate" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});

//Delete method to delete the data into database
$app->delete('/tenants/:id', function($id) use ($app, $db)
{
    /*
     * Fetching Person for deleting
     */
    $person = $db->tenants()->where('id', $id);
     foreach ($db->tenants_deposits()->where('tenants_id', $id) as $p) {
      $deposit = $db->deposits()->where('id', $p['deposits_id']);
       if ($deposit->fetch()) {
        
        $datab = $deposit->delete();
    }
     $datas = $p->delete();
     }
    $data = null;
    if ($person->fetch()) {
        /*
         * Deleting Person
         */
        $data = $person->delete();
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});
$app->delete('/securitytenant/:id', function($id) use ($app, $db)
{
    /*
     * Fetching Person for deleting
     */
    $person = $db->deposits()->where('id', $id);
     foreach ($db->tenants_deposits()->where('deposits_id', $id) as $p) {
     
     $datas = $p->delete();
     }
    $data = null;
    if ($person->fetch()) {
        /*
         * Deleting Person
         */
        $data = $person->delete();
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});
$app->delete('/securityhouse/:id', function($id) use ($app, $db)
{
    /*
     * Fetching Person for deleting
     */
    $person = $db->deposits()->where('id', $id);
     foreach ($db->houses_deposits()->where('deposits_id', $id) as $p) {
     
     $datas = $p->delete();
     }
    $data = null;
    if ($person->fetch()) {
        /*
         * Deleting Person
         */
        $data = $person->delete();
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});
$app->delete('/deletehousetenant/:id', function($id) use ($app, $db)
{
    
    //$person = $db->deposits()->where('id', $id);
     foreach ($db->houses_tenants()->where('tenants_id', $id) as $p) {
     
     $datas = $p->delete();
     }
    //$data = null;
    //if ($person->fetch()) {
        /*
         * Deleting Person
         */
      //  $data = $person->delete();
    //}
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($datas);
});
$app->delete('/houses/:id', function($id) use ($app, $db)
{
    /*
     * Fetching Person for deleting
     */
    $person = $db->houses()->where('id', $id);
     foreach ($db->houses_deposits()->where('houses_id', $id) as $p) {
      $deposit = $db->deposits()->where('id', $p['deposits_id']);
       if ($deposit->fetch()) {
        
        $datab = $deposit->delete();
    }
     $datas = $p->delete();
     }
    $data = null;
    if ($person->fetch()) {
        /*
         * Deleting Person
         */
        $data = $person->delete();
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});




$app->run();