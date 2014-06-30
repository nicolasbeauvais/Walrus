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

}
