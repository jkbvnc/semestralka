<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class PostPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($postId)
    {
        $post = $this->database->table('posts')->get($postId);
        if(!$post)
        {
            $this->error('Příspěvek nenalezen.');
        }
        $this->template->post = $post;
        $this->template->comments = $post->related('comment')->order('created_at');
    }

    public function renderCreate($categoryId)
    {
        $this['postForm']->setDefaults(['category_id' => $categoryId]);
    }

    public function createComponentPostForm()
    {
        $form = new Form;
        $form->addText('title', 'Název diskuze:')->setRequired('Zadejte název diskuze.');
        $form->addTextArea('content','Dotaz:')->setRequired('Zadejte dotaz.');
        $form->addHidden('category_id');
        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];
        return $form;
    }

    public function postFormSucceeded($form, $values)
    {
        $postId = $this->getParameter('postId');
        if($postId)
        {
            $post = $this->database->table('posts')->get($postId);
            $post->update($values);
        }
        else
        {
            $post = $this->database->table('posts')->insert($values);
        }
        $this->flashMessage("Příspěvek byl úspěšně publikován.", 'success');
        $this->redirect('Homepage:');
    }

    public function actionEdit($postId)
    {
        $post = $this->database->table('posts')->get($postId);
        if (!$post)
        {
            $this->error('Dotaz nebyl nalezen.');
        }
        $this['postForm']->setDefaults($post->toArray());
    }
}
