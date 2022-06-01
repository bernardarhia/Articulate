<?php

use App\Database\DB;

$results = DB::select("email", "password")->from("users")->where("id", "=", 1)->first();
print_r($results);
?>
<div>
    <a href='/student/add'>Add User</a>
</div>
<h1>Home page</h1>