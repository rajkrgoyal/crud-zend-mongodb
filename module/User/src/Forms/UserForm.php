<?php
namespace User\Forms;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Form\Element\Checkbox;

class UserForm{
    public $form;
    private $validator;
    private $errors;
    public function  __construct(){
        $this->form = new Form('user');
        $name = new Element('name');
        $name->setLabel('Name:');
        $name->setAttributes([
            'type' => 'text',
            'class'=>'form-control',
            'required' => 'required',
            'placeholder' => 'Enter Name'
        ]);
        $email = new Element('email');
        $email->setLabel('Email:');
        $email->setAttributes([
            'type' => 'email',
            'class'=>'form-control',
            'required' => 'required',
            'placeholder' => 'Enter Email'
        ]);

        $password = new Element('opwd');
        $password->setLabel('Password:');
        $password->setAttributes([
            'type' => 'password',
            'class'=>'form-control',
            'placeholder' => 'Enter Password'
        ]);
        $password->setAttribute("minlength",6);

        $checkBox = new Checkbox("pwd_checkbox");
        $checkBox->setAttributes([
            'type' => 'checkbox',
            'onclick' => 'toggleNewPass(this)',
            'value' => '1',
            'id' => 'pwd_checkbox'
        ]);
        $checkBox->removeAttribute('checked');

        $submit = new Element('submit');
        $submit->setValue('Submit');
        $submit->setAttributes([
            'type' => 'submit',
            'class' => 'btn btn-primary'
        ]);
        
        $this->form->add($name);
        $this->form->add($email);
        $this->form->add($password);
        $this->form->add($submit);
        $this->form->add($checkBox);

        $this->validator = array();
        $this->validator['name'] = new Validator\ValidatorChain();
        $this->validator['name']->attach(new Validator\NotEmpty());
        $this->validator['email'] = new Validator\ValidatorChain();
        $this->validator['email']->attach(new Validator\EmailAddress());
        $this->validator['pwd'] = new Validator\ValidatorChain();
        $this->validator['pwd']->attach(new Validator\StringLength(['min' => 6]));
        $this->errors = array();
    }

    public function setName($name){
        $this->form->get('name')->setAttribute('value',$name);
    }

    
    public function setEmail($email){
        $this->form->get('email')->setAttribute('value',$email);
    }

    public function isValid($formData){
        $valid = true;
        if(!$this->validator["name"]->isValid($formData['name'])){
            $this->errors = array_merge($this->errors,$this->validator["name"]->getMessages());
            $valid = false;
        }
        if(!$this->validator["email"]->isValid($formData['email'])){
            $this->errors = array_merge($this->errors,$this->validator["email"]->getMessages());
            $valid = false;
        }
        if(!empty($formData['pwd_checkbox'])){
            if(!$this->validator["pwd"]->isValid($formData['opwd'])){
                $e = $this->validator["pwd"]->getMessages();
                $e["stringLengthTooShort"] = "Password length should be greater than 6";
                $this->errors = array_merge($this->errors,$e);
                $valid = false;
            }
            if(isset($formData['npwd'])){
                if(!$this->validator["pwd"]->isValid($formData['npwd'])){
                    $e = $this->validator["pwd"]->getMessages();
                    $e["stringLengthTooShort"] = "Password length should be greater than 6";
                    $this->errors = array_merge($this->errors,$e);
                    $valid = false;
                }
            }
        }
        
        return $valid;

    }

    public function getErrors(){
        //var_dump($this->errors);
        return $this->errors;
    }

} 


