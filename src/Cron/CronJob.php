<?php
namespace App\Cron;


use \App\Models\Item;
use \App\Models\UserItem;
use \App\Models\ReportedItem;
use Slim\Container;

/**
*
*/
class CronJob
{
    protected $container;

	public function __construct(Container $container)
	{
		return $this->container = $container;
	}

	public function __get($property)
	{
		return $this->container->{$property};
	}

    public function running()
    {
        $this->unreportedItem();
        $this->itemReappear();
    }

    //User unreported item
    public function unreportedItem()
    {
        $items = new Item($this->db);
        $users = new \App\Models\Users\UserModel($this->db);
        $unreported = new \App\Models\UnreportedItem($this->db);

        $user  = $users->getAllUser()->fetchAll();
        foreach ($user as $value) {
            $userItems = $items->userUnreported($value['id']);
            if ($userItems) {
                foreach ($userItems as  $val) {
                    $data = [
                        'item_id' => $val['id'],
                        'user_id' => $value['id'],
                        'date'    => $val['end_date'],
                    ];
                    $unreported->create($data);
                }
            }
        }
    }

    //Item Recurring
    public function itemReappear()
    {
        $item = new Item($this->db);
        $reportedItem = new ReportedItem($this->db);

        $expired = $item->expired();
        $data['status'] = 0;
        // var_dump($expired);die;

        foreach ($expired as $val) {
            switch ($val['recurrent']) {
                case "harian":
                $data['end_date'] = date('Y-m-d', strtotime($val['end_date']. '+1 day'));
                break;
                case "mingguan":
                $data['end_date'] = date('Y-m-d', strtotime($val['end_date']. '+1 week'));
                break;
                case "bulanan":
                $data['end_date'] = date('Y-m-d', strtotime($val['end_date']. '+1 month'));
                break;
                case "tahunan":
                $data['end_date'] = date('Y-m-d', strtotime($val['end_date']. '+1 year'));
                break;
                default:
                $data['end_date'] = '';
            }
            $item->updateData($data, $val['id']);
            $reported = $reportedItem->find('item_id', $val['id']);

            if ($reported) {
                $reportedItem->hardDelete($reported['id']);
            }
        }
    }

}

?>
