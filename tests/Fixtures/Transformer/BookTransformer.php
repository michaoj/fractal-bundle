<?php declare(strict_types=1);

namespace Somnambulist\Bundles\FractalBundle\Tests\Fixtures\Transformer;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use Somnambulist\Bundles\FractalBundle\Tests\Fixtures\Model\Book;
use Somnambulist\Bundles\FractalBundle\Tests\Fixtures\Services;

/**
 * Class BookTransformer
 *
 * @package    Somnambulist\Bundles\FractalBundle\Tests\Fixtures\Transformer
 * @subpackage Somnambulist\Bundles\FractalBundle\Tests\Fixtures\Transformer\BookTransformer
 */
class BookTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['author','comments'];

    public function transform(Book $book)
    {
        return [
            'id' => $book->id(),
            'name' => $book->name(),
            'added_at' => $book->addedAt()->format(\DateTime::ATOM),
        ];
    }

    public function includeComments(Book $book, ParamBag $params)
    {
        [$limit] = $params->get('limit');

        return $this->collection($book->comments($limit), Services::COMMENTS_TRANSFORMER);
    }

    public function includeAuthor(Book $book)
    {
        return $this->item($book->author(), Services::AUTHORS_TRANSFORMER);
    }
}
