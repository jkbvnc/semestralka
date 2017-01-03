<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class CategoryPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($categoryId)
    {
        $category = $this->database->table('categories')->get($categoryId);
        if(!$category)
        {
            $this->error('Kategorie neexistuje.');
        }
        $this->template->category = $category;
        $this->template->posts = $category->related('post')->order('created_at');
    }

    public function createComponentCategoryForm()
    {
        $form = new Form;
        $form->addText('title', 'Název kategorie:')->setRequired();
        $form->addTextArea('content','Popis kategorie:')->setRequired();
        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'categoryFormSucceeded'];
        return $form;
    }

    public function categoryFormSucceeded($form, $values)
    {
        $categoryId = $this->getParameter('categoryId');
        if($categoryId)
        {
            $category = $this->database->table('categories')->get($categoryId);
            $category->update($values);
        }
        else
        {
            $category = $this->database->table('categories')->insert($values);
        }
        $this->flashMessage("Kategorie byla úspěšně publikována.", 'success');
        $this->redirect('show',$category->id);
    }

    public function actionEdit($categoryId)
    {
        $category = $this->database->table('categories')->get($categoryId);
        if (!$category)
        {
            $this->error('Kategorie nebyla nalezena.');
        }
        $this['categoryForm']->setDefaults($category->toArray());
    }
}
