<?php
/**
 * Walrus Framework
 * File maintained by: Thomas Bentkowski (Harper)
 * Created: 11:35 29/01/14
 */

namespace engine\models;

use R;

class User
{
    public function test()
    {
        $test = R::dispense('test');
        var_dump($test);
        $test->name = 'COUCOU';
        $id = R::store($test);
        var_dump($id);
    }
}
