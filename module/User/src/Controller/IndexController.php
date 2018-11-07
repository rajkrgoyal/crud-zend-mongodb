<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;

use Config\Config;
use Document\User;
use MongoDB;
// require_once __DIR__.'/../../../../config/config.php';
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Forms\UserForm;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {

        $dm = (new Config())->getConnection();
        $users = $dm->createQueryBuilder('Document\User')
            ->hydrate(false)
            ->getQuery()
            ->execute();
        $contentView = new ViewModel(array("users" => $users));
        $contentView->setTemplate('user/index.phtml'); // path to phtml file under view folder
        return $contentView;
    }

    public function addAction()
    {
        $dm = (new Config())->getConnection();
        $request = $this->getRequest();
        $userForm = new UserForm();
        if ($request->isPost()) {
            $email = $request->getPost('email');
            $error = [];
            $name = $request->getPost('name');
            $tempUser = ["name" => $name, "email" => $email];
            if(!$userForm->isValid($request->getPost())){
              $error = array_merge($error,$userForm->getErrors());
            }
            $existing_user = $dm->createQueryBuilder('Document\User')->field('email')->equals($email)
                ->getQuery()->execute()->count();
            if ($existing_user > 0) {
                $error["email"] = "Email already Exists!";
            }
            if(!empty($error)){
              $userForm->setName($tempUser["name"]);
              $userForm->setEmail($tempUser["email"]);
              $contentView = new ViewModel(["error" => $error,"form"=>$userForm->form]);
              $contentView->setTemplate('user/create.phtml');
              return $contentView;
            }
            $password = password_hash($request->getPost('opwd'), PASSWORD_DEFAULT);
            $user = new User();
            $user->setId(new MongoDB\BSON\ObjectId());
            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($password);
            $dm->persist($user);
            $dm->flush(); //save user to db
            return $this->redirect()->toRoute('user');
        } else {
            
            $contentView = new ViewModel(array("form"=>$userForm->form));
            $contentView->setTemplate('user/create.phtml');
            // $id = $this->params()->fromRoute('id');
            return $contentView;
        }

    }

    public function editAction()
    {
        $dm = (new Config())->getConnection();
        $id = $this->params()->fromRoute('id');
        $user = $dm->createQueryBuilder('Document\User')->hydrate(false)->field('_id')->equals($id)
            ->getQuery()->execute();
        $user = iterator_to_array($user);
        $userForm = new UserForm();
        $userForm->setName($user[$id]["name"]);
        $userForm->setEmail($user[$id]["email"]);
        $contentView = new ViewModel(["user" => $user[$id],"form"=>$userForm->form]);
        $contentView->setTemplate('user/update.phtml');
        return $contentView;
    }

    public function deleteAction()
    {
        $dm = (new Config())->getConnection();
        $id = $this->params()->fromRoute('id');
        $dm->createQueryBuilder('Document\User')
            ->remove()
            ->field('_id')->equals($id)
            ->getQuery()
            ->execute();
        return $this->redirect()->toRoute('user');
    }

    public function updateAction()
    {
        $dm = (new Config())->getConnection();
        $id = $this->params()->fromRoute('id');
        $userForm = new UserForm();
        $request = $this->getRequest();
        $error = [];
        if(!$userForm->isValid($request->getPost())){
          $error = array_merge($error,$userForm->getErrors());
        }
        $user = $dm->createQueryBuilder('Document\User')->hydrate(false)->field('_id')->equals($id)
            ->getQuery()->execute();
        $user = iterator_to_array($user)[$id];
        $email = $request->getPost('email');
        $name = $request->getPost('name');
        $tempUser = ["_id" => $id, "name" => $name, "email" => $email];
        $password = $user["password"];
        if ($email != $user["email"]) {
            $existing_user = $dm->createQueryBuilder('Document\User')->field('email')->equals($email)
                ->getQuery()->execute()->count();
            if ($existing_user > 0) {
                $error["email"] = "Email already Exists!";
            }

        }
        $newdata;
        if (!empty($request->getPost('pwd_checkbox')) && empty($error)) {
            if (password_verify($request->getPost('opwd'), $user["password"])) {
                $password = password_hash($request->getPost('npwd'), PASSWORD_DEFAULT);
                $newData = array("email" => $email, "password" => $password, "name" => $name);
            } else {
                $error["password"] = "Old Password is Wrong!";
            }
        } else if (empty($error)) {
            $newData = array("email" => $email, "name" => $name);
        }
        if (empty($error)) {
            $dm->createQueryBuilder('Document\User')
                ->updateOne()
                ->field('name')->set($name)
                ->field('email')->set($email)
                ->field('password')->set($password)
                ->field('_id')->equals($id)
                ->getQuery()
                ->execute();
            return $this->redirect()->toRoute('user');
        } else {
            $userForm->setName($tempUser["name"]);
            $userForm->setEmail($tempUser["email"]);
            $contentView = new ViewModel(["error" => $error,"form"=>$userForm->form,"user"=>$tempUser]);
            $contentView->setTemplate('user/update.phtml');

            return $contentView;
        }

    }

}
