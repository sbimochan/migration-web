<?php
namespace App\Nrna\Services;

use App\Nrna\Models\CountryTag;
use App\Nrna\Repositories\CountryTag\CountryTagRepositoryInterface;

/**
 * Class CountryTagService
 * @package App\Nrna\Services
 */
class CountryTagService
{
    /**
     * @var CountryTagRepositoryInterface
     */
    private $tag;

    /**
     * constructor
     * @param CountryTagRepositoryInterface $tag
     */
    public function __construct(CountryTagRepositoryInterface $tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param $data
     * @return CountryTag
     */
    public function save($data)
    {
        return $this->tag->save($data);
    }

    /**
     * @param  int $limit
     * @return \App\Nrna\Repositories\CountryTag\Collection
     */
    public function all($limit = 15)
    {
        return $this->tag->getAll($limit);
    }

    /**
     * @param $id
     * @return CountryTag
     */
    public function find($id)
    {
        try {
            return $this->tag->find($id);
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * @param $data
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->tag->update($data);
    }

    /**
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        return $this->tag->delete($id);
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->tag->getAll()->lists('title', 'id');
    }

    /**
     * creates tag id doesn't exists in database
     *
     * @param $tags
     * @return array param $tags
     */
    public function createOrGet($tags)
    {
        foreach ($tags as $index => $tag) {
            if (is_null($this->find($tag))) {
                $tagObj       = $this->save(['title' => $tag]);
                $tags[$index] = $tagObj->id;
            }
        }

        return $tags;
    }

    /**
     * @param $filter
     * @return array
     */
    public function latest($filter)
    {
        $tagArray = [];
        $tags     = $this->tag->latest($filter);
        foreach ($tags as $tag) {
            $tagArray[] = $this->buildTag($tag);
        }

        return $tagArray;
    }

    /**
     * @param CountryTag $tag
     * @return array
     */
    public function buildTag(CountryTag $tag)
    {
        return [
            'id'         => $tag->id,
            'title'      => $tag->name,
            'created_at' => $tag->created_at->timestamp,
            'updated_at' => $tag->updated_at->timestamp,
        ];
    }

    /**
     * gets deleted places
     * @param $filter
     * @return array
     */
    public function deleted($filter)
    {
        $posts = $this->tag->deleted($filter);

        return $posts;
    }

}
