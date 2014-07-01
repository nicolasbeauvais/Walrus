<?php

namespace app\engine\models;

use R;
use Walrus\core\WalrusModel;

class Project extends WalrusModel
{

    /**
     * Create a new project
     *
     * @param array $params
     * @return int|string
     */
    public function createProject($params){

        $project = R::dispense("project");

        foreach($params as $key => $value){
            $project[$key] = $value;
        }
        $project['created_at'] = R::isoDateTime();
        $project['updated_at'] = R::isoDateTime();

        $id = R::store($project);
        return $id;
    }

    /**
     * Update an existing project
     *
     * @param int $id
     * @param array $params
     * @return int
     */
    public function updateProject($id,$params){

        $project = R::load("project",$id);

        foreach($params as $key => $value){
            $project[$key] = $value;
        }
        $project['updated_at'] = R::isoDateTime();

        R::store($project);
        return $id;

    }

    /**
     * Delete an existing project
     *
     * @param int $id
     */
    public function deleteProject($id){

       $project = R::load("project",$id);
       R::trash($project);
    }

    /**
     * Find an existing project
     *
     * @param int $id
     * @return \RedBean_OODBBean
     */
    public function findProject($id){

        $project = R::load("project",$id);
        return $project;
    }

    /**
     * Returns all the projects
     *
     * @return array
     */
    public function getAllProjects(){

        $projects = R::findAll("project");
        return $projects;
    }

    public function getProjectsByUser($user){

        $projects = R::find("project",'user_id = :user',[':user' => $user]);
        return $projects;
    }

    public function getProjectsByCollaboration($user){

        $projects = array();
        $allProjects = R::find("project_user","user_id = :user",[':user' => $user]);
        foreach($allProjects as $project){
            $projects[] = R::load("project",$project->project_id);
        }
        return $projects;
    }

}
