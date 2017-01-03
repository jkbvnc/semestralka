<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class CommentPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($commentId)
    {
        $comment = $this->database->table('comments')->get($commentId);
        if (!$comment) {
            $this->error('Odpověď nenalezena.');
        }
        $this->template->comment = $comment;
    }

    public function renderCreate($postId)
    {
        $this['commentForm']->setDefaults(['post_id' => $postId]);
    }

    public function createComponentCommentForm()
    {
        $form = new Form;
        $form->addTextArea('content', 'Odpověď:')->setRequired('Zadejte Vaši odpověď.');
        $form->addHidden('post_id');
        $form->addSubmit('send', 'Odpovědět');
        $form->onSuccess[] = [$this, 'commentFormSucceeded'];
        return $form;
    }

    public function commentFormSucceeded($form, $values)
    {
        $commentId = $this->getParameter('commentId');
        if ($commentId) {
            $post = $this->database->table('comments')->get($commentId);
            $post->update($values);
        } else {
            $post = $this->database->table('comments')->insert($values);
        }
        $this->flashMessage("Odpověď byla úspěšně publikována.", 'success');
        $this->redirect('Homepage:');
    }

    public function actionEdit($commentId)
    {
        $comment = $this->database->table('comments')->get($commentId);
        if (!$comment)
        {
            $this->error('Odpověď nenalezena.');
        }
        $this['commentForm']->setDefaults($comment->toArray());
    }
}