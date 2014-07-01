<?php

namespace app\engine\controllers;

use app\engine\models\Project;
use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

class ProjectController extends WalrusController
{

    public function index(){


        $this->setView('index');
    }

    public function create(){

        if(!empty($_POST)){

            $form = new WalrusForm('form_project');
            $check = $form->check();
            if($check === true){
                var_dump($check);
var_dump($form);
                $params = array(
                    "title" => $_POST['title'],
                    "description" => $_POST['description']
                );
/*
                $projects = new Project();
                $id = $projects->createProject($params);

                $this->reroute("ProjectController","show",array("id"=>$id));*/
            }
            else {
                $this->register("form",$form->render());

                $this->setView('create');
            }

        }
        else {

            $form = new WalrusForm("form_project");

            $this->register("form",$form->render());

            $this->setView('create');
        }

    }

}
