<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


class SignPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    protected function createComponentSignInForm()
    {
        $form = new Form();
        $form->addText('username', 'Uživatelské jméno:')->setRequired('Prosím, vyplňte uživatelské jméno.');
        $form->addPassword('password', 'Heslo:')->setRequired('Prosím, vyplňte heslo.');
        $form->addSubmit('send', 'Přihlásit');
        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }

    public function signInFormSucceeded($form, $values)
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:default');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.');
        $this->redirect('Homepage:');
    }
}