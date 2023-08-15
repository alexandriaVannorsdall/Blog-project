<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;
use function PHPUnit\Framework\throwException;

class ArticleModel extends Model
{
    /**
     * Table's name
     * @var string
     */
    protected $table = 'article';

    /**
     * These are allowed fields within the table that data can be added to.
     * Important for the create method in
     * the article controller.
     * @var string[]
     */
    protected $allowedFields = ['title', 'keyword', 'text'];

    /**
     * Gets the articles of keyword is false, else returns the first article with that keyword.
     *
     * @param bool $keyword
     * @return array|object|null
     */
    public function getArticle(bool $keyword = false)
    {
        if ($keyword === false) {
            return $this->findAll();
        }

        return $this->where(['Keyword' => $keyword])->first();
    }

    /**
     * Gets the article if we have the id.
     *
     * @throws Exception
     */
    public function getArticleName(): string
    {
        $id = $this->getById();

        if ($id === null) {
            throw new Exception('Id cannot be null when trying to find the article name.');
        }

        $model = model(ArticleModel::class);
        $article = $model->find($id);

        if ($article === null) {
            throw new Exception("Article not found with id: $id");
        }

        return $article['Title'];
    }

    /**
     * Finds an article by the id and deletes it if it exists.
     *
     * @param int $id
     * @return true
     * @throws Exception
     */
    public function delete($id = null, bool $purge = false): bool
    {
        $builder = $this->db->table("article");
        $builder->get();
        $builder->where("Id", $id);

        if ($id === null) {
            throw new Exception('Id cannot be null.');
        }
        $builder->delete();
         return true;
    }

    /**
     * Retrieves an article by its id.
     *
     * @throws Exception
     */
    public function getById($id = null)
    {
        $builder = $this->db->table('article');
        $builder->getWhere(array('Id' => $id));

        if ($id === null) {
            throw new Exception('Id cannot be null.');
        } else {
            return $id;
        }
    }
}