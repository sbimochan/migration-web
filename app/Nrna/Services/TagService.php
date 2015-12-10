<?php
namespace App\Nrna\Services;

use App\Nrna\Repositories\Tag\TagRepositoryInterface;

/**
 * Class TagService
 * @package App\Nrna\Services
 */
class TagService
{
    /**
     * @var TagRepositoryInterface
     */
    private $tag;

    /**
     * constructor
     * @param TagRepositoryInterface $tag
     */
    function __construct(TagRepositoryInterface $tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param $data
     * @return Tag
     */
    public function save($data)
    {
        return $this->tag->save($data);
    }
}