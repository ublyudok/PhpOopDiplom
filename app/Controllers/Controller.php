<?php

namespace App\Controllers;

use App\QueryBuilder;
use League\Plates\Engine;
Class Controller {

    private $templates;
    private $querybuilder;
    public function __construct(QueryBuilder $queryBuilder, Engine $engine) {
        $this->querybuilder = $queryBuilder;
        $this->templates = $engine;
    }

    public function users() {
        echo $this->templates->render('users');
    }

    public function register() {
        echo $this->templates->render('page_register');
    }

    public function createUser() {
        echo $this->templates->render('create_user');
    }

    public function edit() {
        echo $this->templates->render('edit');
    }

    public function media() {
        echo $this->templates->render('media');
    }

    public function status() {
        echo $this->templates->render('status');
    }

    public function security() {
        echo $this->templates->render('security');
    }

    public function  login() {
        echo $this->templates->render('page_login');
    }

    public function  profile() {
        echo $this->templates->render('page_profile');
    }

}