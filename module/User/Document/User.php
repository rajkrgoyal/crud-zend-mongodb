<?php

namespace Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
/**
 * @ODM\Document(collection="users") */
class User
{   
    /** @ODM\Id */
    private $id;
     /** @ODM\Field(type="string") */
    private $name;
    /** @ODM\Field(type="string") */
    private $email;
    /** @ODM\Field(type="string") */
    private $password;

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setPassword($password){
        $this->password = $password;
    }

}