<?php

class Application_Form_Usuario extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombres');
        $nombre->setLabel('Nombres:');
        $nombre->setRequired();
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $apellido = new Zend_Form_Element_Text('apellidos');
        $apellido->setLabel('Apellidos:');
        $apellido->setRequired();
        $apellido->addFilter('StripTags');
        $this->addElement($apellido);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('E-mail:');
        $email->setRequired();
        $email->addFilter('StripTags');
        $email->addValidator(new Zend_Validate_EmailAddress());
        $this->addElement($email);
    }


}

