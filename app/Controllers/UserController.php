<?php

namespace App\Controllers;

use Delight\Auth\AuthError;
use Delight\Auth\InvalidEmailException;
use Delight\Auth\InvalidPasswordException;
use Delight\Auth\TooManyRequestsException;
use Delight\Auth\UserAlreadyExistsException;
use App\QueryBuilder;
class UserController {

    private $auth;
    private $querybuilder;

    public function __construct(QueryBuilder $queryBuilder, \Delight\Auth\Auth $auth) {
        $this->querybuilder = $queryBuilder;
        $this->auth = $auth;
    }

    public function register() {

        $email = $_POST['email'];

        $password = $_POST['password'];

        $name = $_POST['username'];

        $workplace = "";

        $telephone = "";

        $adress = "";

        $status = "Онлайн";

        $vk = "";

        $telegram = "";

        $instagram = "";

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $getOne = $this->querybuilder->selectMail("addUser", ["email"], $email);

        $getSecond = $this->querybuilder->selectMail("users", ["email"], $email);

        if ($getOne == true || $getSecond == true) {
            header("Location:/register");
            $_SESSION['exists'] = "Данный пользователь уже существует!";
            exit;
        } else {
            $this->querybuilder->insert("addUser", ["workplace" => $workplace, "telephone" => $telephone, "adress" => $adress,
                "email" => $email, "password" => $hashed_password, "status" => $status, "avatar" => "kot.jpg", "vk" => $vk, "telegram" => $telegram, "instagram" => $instagram, "name" => $name]);

                $userId = $this->auth->register($email, $password, $name);

            header("Location:/login");
        }
    }

    public function login() {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $getPerm = $this->querybuilder->getAll("addUser");

        foreach ($getPerm as $perm) {
            if($perm['email'] == $email && $perm['permissions'] == "Admin"){
                $_SESSION['Permission'] = "Admin";
            } elseif($perm['email'] == $email && $perm['permissions'] == "user") {
                $_SESSION['Permission'] = "user";
            }
        }

        try {

            $this->auth->login($email, $password);
            $_SESSION['login'] = $email;

            header("Location:/users");

        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            header("Location:/login");
            die('Wrong email address');

        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            header("Location:/login");
            die('Wrong password');

        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {

            die('Email not verified');

        }
        catch (\Delight\Auth\TooManyRequestsException $e) {

            die('Too many requests');

        }

    }

    public function logout() {
        $this->auth->destroySession();
        session_destroy();
        header('Location:/users');
    }

    public function addUser() {

        $email = $_POST['email'];

        $password = $_POST['password'];

        $name = $_POST['name'];

        $workplace = $_POST['workplace'];

        $telephone = $_POST['telephone'];

        $adress = $_POST['adress'];

        $status = $_POST['status'];

        $vk = $_POST['vk'];

        $telegram = $_POST['telegram'];

        $instagram = $_POST['instagram'];


        if(!empty($_FILES['file'])) {

            $file = $_FILES['file'];
            $filename = $file['name'];
            $pathFile = '../img/'.$filename;

            d($file);
            d($filename);
            d($pathFile);

            if(!move_uploaded_file($file['tmp_name'], $pathFile)) {
                echo 'Ошибка загрузки файла';
            }

        }


        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $getOne = $this->querybuilder->selectMail("addUser", ["email"], $email);

        $getSecond = $this->querybuilder->selectMail("users", ["email"], $email);


        if ($getOne == true || $getSecond == true) {
            header("Location:/users");
            $_SESSION['exstsUser'] = "Данный пользователь уже существует";
            exit;
        } else {
            $this->querybuilder->insert("addUser", ["workplace"=>$workplace, "telephone"=>$telephone, "adress"=>$adress,
                "email"=>$email, "password"=>$hashed_password, "status"=>$status, "avatar"=>$filename, "vk"=>$vk, "telegram"=>$telegram, "instagram"=>$instagram, "name"=>$name]);

            $userId = $this->auth->register($email, $password, $name);

            $_SESSION['successAdd'] = "Пользователь успешно добавлен!";
            header("Location:/users");
        }

        header("Location:/users");

    }

    public function addMedia() {

            $id = $_GET['id'];

            $file = $_FILES['file'];

            $name = $file['name'];

            $pathFile = __DIR__.'./img/'.$name;


            $this->querybuilder->update("addUser", ['avatar' => $name], $id);
            $this->querybuilder->loadImage();

            $_SESSION['successAdd'] = "Профиль обновлён!";

            header("Location:/users");
    }

    public function addStatus() {
        $id = $_GET['id'];

        $status = $_GET['status'];


        $this->querybuilder->update("addUser", ['status' => $status], $id);
        $this->querybuilder->loadImage();

        $_SESSION['successAdd'] = "Профиль обновлён!";

        header("Location:/users");
    }

    public function addSecurity() {
        $id = $_POST['id'];

        $email = $_POST['email'];

        $password = $_POST['password'];

        $confirmPassword = $_POST['confirmPassword'];

        $secondPass = password_hash($confirmPassword, PASSWORD_DEFAULT);


        if (password_verify($_POST['password'], $secondPass)) {


            $first =  $this->querybuilder->update('addUser',['email' => $email], $id);

            $second = $this->querybuilder->update('users',['email' => $email], $id);

            $_SESSION['successAdd'] = "Профиль успешно обновлён!";
            header("Location:/users");
        }
        header("Location:/users");
    }

    public function changeProfile() {
        $id = $_GET['id'];

        $name = $_GET['name'];

        $workplace = $_GET['workplace'];

        $telephone = $_GET['telephone'];

        $adress = $_GET['adress'];

        $this->querybuilder->update('addUser',['name' => $name, 'workplace' => $workplace, 'telephone' => $telephone, 'adress' => $adress], $id);

        $_SESSION['successAdd'] = "Профиль успешно обновлён!";
        header("Location:/users");
    }

    public function delete() {
        $id = $_GET['id'];

        $this->querybuilder->delete('addUser', $id);
        $this->querybuilder->delete('users', $id);
        header("Location:/users");
    }

}