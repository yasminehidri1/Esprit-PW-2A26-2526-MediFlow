<?php
require 'config.php';
require 'Models/UserModel.php';
$db = config::getConnexion();
$model = new \Models\UserModel($db);
$result = $model->createUser(['nom'=>'test', 'prenom'=>'test', 'mail'=>'testf2@gmail.com', 'id_role'=>10, 'password'=>'test']);
var_dump($result);
