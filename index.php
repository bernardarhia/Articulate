<?php

use Articulate\Articulate;
use Articulate\Database\Model;
use Articulate\Database\Schema;

include_once __DIR__ . "/vendor/autoload.php";

Articulate::connection("mysql/localhost/root/sms", function ($error) {
    if ($error) die("Opps something went wrong");
});

$user = Schema::create("test", function ($table) {
    $table->increment("id")->primaryKey();
    $table->string("name");
    $table->string("email");
    $table->string("password");
});

class Members extends Model
{
}
$user = Members::one()->join("students", ['members.id' => "students.id"])->where("id", 1)->getQuery();
print_r($user);