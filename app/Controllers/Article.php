<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;
use ReflectionException;

class Article extends BaseController
{
    /**
     * @return string
     */
    public function index(): string
    {
        $model = model(ArticleModel::class);

        $data = [
            'article'  => $model->getArticle(),
            'Title' => 'Posts',
        ];

        return view('templates/header', $data)
            . view('article/index')
            . view('templates/footer');
    }

    /**
     * @param $keyword
     * @return string
     */
    public function view($keyword = null): string
    {
        $model = model(ArticleModel::class);

        $data['article'] = $model->getArticle($keyword);

        if (empty($data['article'])) {
            throw new PageNotFoundException('Cannot find the blog post item: ' . $keyword);
        }

        $data['Title'] = $data['article']['Title'];

        return view('templates/header', $data)
            . view('article/view')
            . view('templates/footer');

    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function create(): string
    {
        helper('form');

        // Checks whether the form is submitted.
        if (! $this->request->is('post')) {
            // The form is not submitted, so returns the form.
            return view('templates/header', ['title' => 'Create a new post'])
                . view('article/create')
                . view('templates/footer');
        }

        $post = $this->request->getPost(['title', 'text']);

        // Checks whether the submitted data passed the validation rules.
        if (! $this->validateData($post, [
            'title' => 'required|max_length[255]|min_length[3]',
            'text'  => 'required|max_length[5000]|min_length[10]',
        ])) {
            // The validation fails, so returns the form.
            return view('templates/header', ['title' => 'Create a new post'])
                . view('article/create')
                . view('templates/footer');
        }

        $model = model(ArticleModel::class);

        $model->save([
            'title' => $post['title'],
            'keyword'  => url_title($post['title'], '-', true),
            'text'  => $post['text'],
        ]);

        return view('templates/header', ['title' => 'Create a new post'])
            . view('article/success')
            . view('templates/footer');
    }

    /**
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function delete(int $id): string
    {
        $model = model(ArticleModel::class);
        $article = $model->find($id);

        if($model->delete($id)){
            $message = "Post deleted!";
        }
        else{
            $message = "Your post was not deleted.";
        }

        return view('templates/header')
            . view('article/view', ['message' => $message, 'article' => $article])
            . view('templates/footer');
    }

    /**
     * @param int|null $id
     * @return string
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(int $id): string
    {
        helper('form');

        $model = model(ArticleModel::class);

        // Fetch the specific article from the database based on the ID.
        $post = $model->find($id);

        // Check if the post exists.
        if ($post === null) {
            // Article not found, return back to the main site.
            return view('templates/header')
                . view('article/view')
                . view('templates/footer');
        }

        if ($this->request->getPostGet() === 'post') {
            $submittedData = $this->request->getPost(['title', 'text']);

            // Validate the submitted data.
            if (!$this->validateData($submittedData, [
                'title' => 'required|max_length[255]|min_length[3]',
                'text' => 'required|max_length[5000]|min_length[10]',
            ])) {
                // The validation fails, so return the form with the submitted data and the error messages.
                return view('templates/header')
                    . view('article/edit', ['post' => $submittedData, 'errors' => $this->validator->getErrors()])
                    . view('templates/footer');
            }

            // Update the article data with the submitted values.
            $model->update($id, [
                'title' => $submittedData['title'],
                'keyword' => url_title($submittedData['title'], '-', true),
                'text' => $submittedData['text'],
            ]);
        }

        return view('templates/header', ['title' => 'Edit a post'])
            . view('article/edit', ['post' => $post])
            . view('templates/footer');
    }
}