<?php

namespace Maiev\EC;

use GuzzleHttp\Client;

class ECManager
{
    protected $client;

    protected $baseURI = 'https://open.workec.com/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseURI
        ]);
    }

    /**
     * send curl request
     * @param $method
     * @param $path
     * @param $data
     * @return string
     */
    public function send($method, $path, $data = [])
    {
        try {
            $response = $this->client->request($method, $path, [
                'headers' => $this->gerateHeaders(),
                'json' => $data
            ]);
            return $response->getBody()->getContents();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * generate curl headers
     * @return array
     */
    public function gerateHeaders()
    {
        return [
            'authorization' => $this->accessToken(),
            'corp_id' => config('ec-manager.CorpID'),
            'cache-control' => 'no-cache'
        ];
    }

    /**
     * fetch access token
     * @return bool
     */
    public function accessToken()
    {
        $response = $this->client->post('auth/accesstoken', [
            'json' => [
                'appId' => config('ec-manager.AppID'),
                'appSecret' => config('ec-manager.AppSecret')
            ]
        ]);

        $result = json_decode($response->getBody()->getContents());
        if ($result->errCode == 200) {
            return $result->data->accessToken;
        }
        return false;
    }

    /**
     *  获取部门和员工信息
     */
    public function structure()
    {
        return $this->send('get', 'user/structure');
    }

    /**
     *  获取指定员工信息
     * @param String $account 用户账号(手机号码)
     * @param String $userId 用户ID
     * $userId和$account必须填写一个，如果都填，以$userId为准
     * @return string
     */
    public function findUserInfoById($account = '', $userId = '')
    {
        $data = array_filter([
            'userId' => $userId,
            'account' => $account
        ]);
        return $this->send('post', 'user/findUserInfoById', $data);
    }

    /**
     * 创建客户
     */
    public function addCustomer($data)
    {
        return $this->send('post', 'customer/addCustomer', $data);
    }

    /**
     * 批量创建客户
     */
    public function createCustomer($data)
    {
        return $this->send('post', 'customer/create', $data);
    }

    /**
     * 批量精确查询客户
     * @return string
     */
    public function getCustomer($data)
    {
        return $this->send('get', 'customer/get', $data);
    }

    /**
     *  根据条件分页查询客户
     */
    public function rangeQueryCustomer($data)
    {
        return $this->send('post', 'customer/rangeQueryCustomer', $data);
    }

    /**
     * 获取自定义字段信息
     * @param Int $type 按资料类型传对应值： 1 客户资料 2 公司资料
     * @return string
     */
    public function getCustomFieldMapping($type = 1)
    {
        $data = [
            'type' => $type
        ];
        return $this->send('post', 'customer/getCustomFieldMapping', $data);
    }

    /**
     * 获取员工客户库分组信息
     * @param int $userId 员工ID
     * @return string
     */
    public function getCustomerGroup($userId)
    {
        $data = [
            'userId' => $userId
        ];
        return $this->send('post', 'customer/getCustomerGroup', $data);
    }

    /**
     * 修改客户资料
     */
    public function updateCustomer($data)
    {
        return $this->send('post', 'customer/updateCustomer', $data);
    }

    /**
     * 获取客户来源信息
     */
    public function getChannelSource()
    {
        return $this->send('get', 'customer/getChannelSource');
    }

    /**
     * 变更客户跟进人
     */
    public function changeCrmFollowUser($data)
    {
        return $this->send('post', 'customer/changeCrmFollowUser', $data);
    }

    /**
     * 放弃客户
     */
    public function abandonCustomer($data)
    {
        return $this->send('post', 'customer/abandon', $data);
    }

    /**
     * 获取删除的客户
     * @param String $startTime 查询删除客户的开始时间,格式yyyy-MM-dd HH:mm:ss
     * @param String $endTime 查询删除客户的截止时间,格式yyyy-MM-dd HH:mm:ss, 与startTime最大间隔7天
     * @param String $lastId 根据此参数来进行翻页。上一次请求得到的最后一条记录中的id，初始值可为""
     * @return string
     */
    public function delcrms($startTime = '', $endTime = '', $lastId = '')
    {
        $data = [
            "startTime" => $startTime,
            "endTime" => $endTime,
            "lastId" => $lastId
        ];
        return $this->send('post', 'customer/delcrms', $data);
    }

    /**
     * 获取员工签到记录
     */
    public function getCrmVisitDetails($data)
    {
        return $this->send('post', 'customer/getCrmVisitDetails', $data);
    }

    /**
     * 创建标签分组
     * @param int $userId 操作人ID
     * @param String $name 标签分组名
     * @param String $color 分组颜色 默认值为 c1,取值范围[c1~c20]
     * @param int $type 分组类型 默认值为0 取值： 0 代表此分组的标签可以多选 1 代表此分组的标签只能单选
     * @return string
     */
    public function addLabelGroup($userId, $name, $color = 'c1', $type = 0)
    {
        $data = [
            'name' => $name,
            'type' => $type,
            'color' => $color,
            'userId' => $userId
        ];
        return $this->send('post', 'label/addLabelGroup', $data);
    }

    /**
     * 创建标签
     * @param String $name 标签名
     * @param String $groupValue 分组id或者分组名
     * @param int $userId 操作人ID
     * @return string
     */
    public function addLabel($name, $groupValue, $userId)
    {
        $data = [
            'name' => $name,
            '$groupValue' => $groupValue,
            'userId' => $userId
        ];
        return $this->send('post', 'label/addLabel', $data);
    }

    /**
     * 批量修改客户标签
     */
    public function updateLabel($data)
    {
        $data = [
            'list' => $data
        ];
        return $this->send('post', 'label/update', $data);
    }

    /**
     * 获取标签信息
     * @param String $groupValue 分组id或者分组名
     * @return string
     */
    public function getLabelInfo($groupValue = '')
    {
        $data = [
            'groupValue' => $groupValue
        ];
        return $this->send('post', 'label/getLabelInfo', $data);
    }

    /**
     * 批量添加跟进记录
     */
    public function saveUserTrajectory($data)
    {
        $data = [
            'list' => $data
        ];
        return $this->send('post', 'trajectory/saveUserTrajectory', $data);
    }

    /**
     * 导出跟进记录
     */
    public function findUserTrajectory($data)
    {
        return $this->send('post', 'trajectory/findUserTrajectory', $data);
    }

    /**
     * 导出历史跟进记录
     */
    public function findHistoryUserTrajectory($data)
    {
        return $this->send('post', 'trajectory/findHistoryUserTrajectory', $data);
    }

    /**
     * 导出电话记录
     */
    public function telRecord($data)
    {
        return $this->send('post', 'record/telRecord', $data);
    }

    /**
     * 导出历史电话记录
     */
    public function telRecordHistory($data)
    {
        return $this->send('post', 'record/telRecordHistory', $data);
    }

    /**
     * 导出短信记录
     */
    public function sendSms($data)
    {
        return $this->send('post', 'record/sendSms', $data);
    }

    /**
     * 导出历史短信记录
     */
    public function sendSmsHistory($data)
    {
        return $this->send('post', 'record/sendSmsHistory', $data);

    }

    /**
     * 添加电话记录
     */
    public function addTelRecord($data)
    {
        $data = [
            'list' => $data
        ];
        return $this->send('post', 'record/addTelRecord', $data);
    }

    /**
     * 查询客户轨迹
     */
    public function getTrajectory($data)
    {
        return $this->send('post', 'customer/getTrajectory', $data);
    }

    /**
     * 获取销售金额字段信息
     */
    public function getSalesFieldMapping()
    {
        return $this->send('get', 'sales/getSalesFieldMapping');
    }

    /**
     * 创建销售金额
     */
    public function addSales($data)
    {
        return $this->send('post', 'sales/addSales', $data);
    }

    /**
     * 修改销售金额
     */
    public function updateSales($data)
    {
        return $this->send('post', 'sales/updateSales', $data);
    }

    /**
     * 更新销售金额状态
     */
    public function updateStatus($data)
    {
        return $this->send('post', 'sales/updateStatus', $data);
    }

    /**
     * 查询销售金额列表
     * @param array $data
     */
    public function getSales($data)
    {
        return $this->send('post', 'sales/getSales', $data);
    }

    /**
     * 查询销售金额详情
     * @param int $saleId 销售金额的id,在创建销售金额时返回的id，或者查询列表得到id。
     * @return string
     */
    public function getSalesDetail($saleId)
    {
        $data = [
            'saleId' => $saleId
        ];
        return $this->send('post', 'sales/getSalesDetail', $data);
    }
}