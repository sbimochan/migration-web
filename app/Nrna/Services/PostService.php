<?php
namespace App\Nrna\Services;

use App\Nrna\Models\Post;
use App\Nrna\Repositories\Post\PostRepositoryInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\Logging\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\Filesystem;

/**
 * Class PostService
 * @package App\Nrna\Services
 */
class PostService
{
    /**
     * @var PostRepositoryInterface
     */
    private $post;

    /**
     * @var string
     */
    private $uploadPath;
    /**
     * @var TagService
     */
    private $tag;
    /**
     * @var DatabaseManager
     */
    private $database;
    /**
     * @var Filesystem
     */
    private $file;
    /**
     * @var YoutubeService
     */
    private $youtube;
    /**
     * @var AudioService
     */
    private $audio;
    /**
     * @var Log
     */
    private $logger;

    /**
     * constructor
     * @param PostRepositoryInterface $post
     * @param TagService              $tag
     * @param DatabaseManager         $database
     * @param Filesystem              $file
     * @param YoutubeService          $youtube
     * @param AudioService            $audio
     * @param Log                     $logger
     */
    public function __construct(
        PostRepositoryInterface $post,
        TagService $tag,
        DatabaseManager $database,
        Filesystem $file,
        YoutubeService $youtube,
        AudioService $audio,
        Log $logger
    ) {
        $this->uploadPath = public_path(Post::UPLOAD_PATH);
        $this->post       = $post;
        $this->tag        = $tag;
        $this->database   = $database;
        $this->file       = $file;
        $this->youtube    = $youtube;
        $this->audio      = $audio;
        $this->logger     = $logger;
    }

    /**
     * @param $formData
     * @return Post|bool
     */
    public function save($formData)
    {
        $tags = [];

        if (isset($formData['tag'])) {
            $tags = $this->tag->createOrGet($formData['tag']);
        }
        $this->database->beginTransaction();
        try {
            if ($formData['metadata']['type'] === 'audio') {
                $formData['metadata']['data']['audio']    = $this->upload($formData['metadata']['data']['audio']);
                $formData['metadata']['data']['duration'] = $this->audio->getDuration(
                    $this->getAudioFilePath($formData['metadata']['data']['audio'])
                );
            }
            if ($formData['metadata']['type'] === 'video') {
                $formData = $this->getVideoData($formData);
            }

            $post = $this->post->save($formData);
            if (!$post) {
                return false;
            }
            $this->updateRelations($formData, $post);
            $post->tags()->sync($tags);
            $this->database->commit();

            return $post;
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->database->rollback();
        }
        $this->database->rollback();

        return false;
    }

    /**
     * @param  int        $limit
     * @return Collection
     */
    public function all($limit = 15)
    {
        return $this->post->getAll($limit);
    }

    /**
     * @param $id
     * @return Post
     */
    public function find($id)
    {
        try {
            return $this->post->find($id);
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * @param $id
     * @param $formData
     * @return bool
     */
    public function update($id, $formData)
    {
        $tags = [];
        if (isset($formData['tag'])) {
            $tags = $this->tag->createOrGet($formData['tag']);
        }
        $this->database->beginTransaction();
        try {
            $post = $this->find($id);
            if ($formData['metadata']['type'] === 'audio') {
                $formData['metadata']['data']['audio']    = $post->audioName;
                $formData['metadata']['data']['duration'] = $post->metadata->data->duration;
            }
            if ($formData['metadata']['type'] === 'audio' && isset($formData['metadata']['data']['audio'])) {
                $formData['metadata']['data']['audio']    = $this->upload($formData['metadata']['data']['audio']);
                $formData['metadata']['data']['duration'] = $this->audio->getDuration(
                    $this->getAudioFilePath($formData['metadata']['data']['audio'])
                );
                $this->file->delete($post->audioPath);
            }

            if ($formData['metadata']['type'] === 'video') {
                $formData = $this->getVideoData($formData);
            }

            if (!$post->update($formData)) {
                return false;
            }
            $this->updateRelations($formData, $post);
            $post->tags()->sync($tags);
            $this->database->commit();

            return $post;
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->database->rollback();

            return false;
        }
        $this->database->rollback();

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $post = $this->find($id);
        if ($this->post->delete($id)) {
            $this->file->delete($post->audioPath);

            return true;
        }

        return false;
    }

    /**
     * @param  UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName    = $file->getClientOriginalName();
        $file_type   = $file->getClientOriginalExtension();
        $newFileName = sprintf("%s.%s", sha1($fileName.time()), $file_type);
        if ($file->move($this->uploadPath, $newFileName)) {
            return $newFileName;
        }

        return null;
    }

    /**
     * @param $filter
     * @return array
     */
    public function latest($filter)
    {
        $postArray = [];
        $posts     = $this->post->latest($filter);
        foreach ($posts as $post) {
            $postArray[] = $this->buildPost($post);
        }

        return $postArray;
    }

    /**
     * @param  Post  $post
     * @return array
     */
    public function buildPost(Post $post)
    {
        $postArray['id']           = $post->id;
        $postArray                 = array_merge($postArray, (array) $post->apiMetadata);
        $postArray['tags']         = $post->tags->lists('title')->toArray();
        $postArray['question_ids'] = $post->questions->lists('id')->toArray();
        $postArray['country_ids']  = $post->countries->lists('id')->toArray();
        $postArray['answer_ids']   = $post->answers->lists('id')->toArray();
        $postArray['created_at']   = $post->created_at->timestamp;
        $postArray['updated_at']   = $post->updated_at->timestamp;

        return $postArray;
    }

    /**
     * @param $formData
     * @param $post
     */
    protected function updateRelations($formData, $post)
    {
        if (isset($formData['country'])) {
            $post->countries()->sync($formData['country']);
        }
        if (isset($formData['question'])) {
            $questions = $this->getQuestionsData($formData['question']);
            $answers   = $this->getAnswerData($formData['question']);
            $post->questions()->sync($questions);
            $post->answers()->sync($answers);
        }
    }

    /**
     * @param $formData
     * @return mixed
     */
    protected function getVideoData($formData)
    {
        $videoInformation             = $this->youtube->getVideoInfo(
            $formData['metadata']['data']['media_url']
        );
        $formData['metadata']['data'] = array_merge($formData['metadata']['data'], $videoInformation);

        return $formData;
    }

    /**
     * @param $fileName
     * @return string
     */
    protected function getAudioFilePath($fileName)
    {
        return sprintf('%s/%s', $this->uploadPath, $fileName);
    }

    /**
     * @param $questions
     * @return array
     */
    protected function getQuestionsData($questions)
    {
        $questionArray = [];
        foreach ($questions as $question => $answer) {
            if (!is_array($answer)) {
                $questionArray [] = $answer;
            }
        }

        return $questionArray;
    }

    /**
     * @param $questions
     * @return array
     */
    protected function getAnswerData($questions)
    {
        $answerArray = [];
        foreach ($questions as $question => $answer) {
            if (is_array($answer)) {
                $answerArray [] = array_keys($answer['answer']);
            }
        }

        return array_flatten($answerArray);
    }

    /**
     * gets deleted posts
     * @param $filter
     * @return array
     */
    public function deleted($filter)
    {
        $posts = $this->post->deleted($filter);

        return $posts;
    }
}
