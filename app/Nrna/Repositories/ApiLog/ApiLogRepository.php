<?php namespace App\Nrna\Repositories\ApiLog;

use App\Nrna\Models\ApiLog;

class ApiLogRepository implements ApiLogRepositoryInterface
{
    /**
     * @var ApiLog
     */
    protected $apiLog;

    /**
     * ApiLogRepository constructor.
     * @param ApiLog $apiLog
     */
    public function __construct(ApiLog $apiLog)
    {
        $this->apiLog = $apiLog;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getList()
    {
        return $this->apiLog->orderBy('id','desc')->paginate(15);
    }

    /**
     * @param $logDetails
     * @return bool
     */
    public function saveLog($logDetails)
    {
        $logDetails['response'] = json_encode($logDetails['response']);

        return $this->apiLog->fill($logDetails)->save();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->apiLog->findOrFail($id);
    }
}