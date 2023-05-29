<?php

//decode by http://www.yunlu99.com/
declare (strict_types=1);
namespace app\command;

class Swoole extends \think\console\Command
{
	protected function configure()
	{
		$this->setName("swoole")->setDescription("swooleServer");
	}
	protected function execute(\think\console\Input $input, \think\console\Output $output)
	{
		$ws = new \Swoole\WebSocket\Server("0.0.0.0", intval(\think\facade\Env::get("database.port", 9502)));
		$ws->on("open", function ($ws, $request) {
			$ws->push($request->fd, json_encode(["msg" => "连接成功", "fid" => $request->fd]));
		});
		$ws->on("message", function ($ws, $request) {
			$data = json_decode($request->data, true);
			if ($data["type"] == "heartbeat") {
				$userList = cache("chat_user_" . $data["userUid"]);
				if ($userList != $request->fd) {
					cache("chat_user_" . $data["userUid"], $request->fd);
					$userFDData = \think\facade\Db::name("zh_fd")->where(["wxapp_id" => $data["wxapp_id"], "u_id" => $data["userUid"]])->find();
					if ($userFDData && $userFDData["fd"] != $request->fd) {
						\think\facade\Db::name("zh_fd")->where(["wxapp_id" => $data["wxapp_id"], "u_id" => $data["userUid"]])->update(["fd" => $request->fd]);
					} else {
						if (!$userFDData) {
							\think\facade\Db::name("zh_fd")->where(["wxapp_id" => $data["wxapp_id"], "u_id" => $data["userUid"]])->insert(["wxapp_id" => $data["wxapp_id"], "u_id" => $data["userUid"], "fd" => $request->fd, "create_time" => date("Y-m-d H:i:s", time())]);
						}
					}
				}
				$ws->push($request->fd, json_encode(["type" => $data["type"], "msg" => "心跳", "data" => $userList, "fid" => $request->fd]));
			} elseif ($data["type"] == "msg") {
				file_put_contents("job.txt", 1111);
				$fd = \think\facade\Db::name("zh_fd")->where(["wxapp_id" => $data["data"]["msg"]["wxapp_id"], "u_id" => $data["data"]["msg"]["suserinfo"]])->find();
				$sFD = $fd["fd"];
				if ($sFD && $ws->isEstablished($sFD)) {
					$ws->push($sFD, json_encode($data));
					$data["is_read"] = 1;
				} else {
					$data["is_read"] = 0;
					$sUserData = \app\api\model\WechatUser::where(["u_id" => $data["data"]["msg"]["suserinfo"], "wxapp_id" => $data["data"]["msg"]["wxapp_id"]])->find();
					$sender = \app\api\model\WechatUser::where(["u_id" => $data["data"]["msg"]["userinfo"]["u_id"], "wxapp_id" => $data["data"]["msg"]["wxapp_id"]])->find();
					file_put_contents("job1.txt", json_encode($data["data"]["msg"]));
					$wxSetting = \app\api\model\Setting::where("wxapp_id", $data["data"]["msg"]["wxapp_id"])->find();
					if ($sUserData && $wxSetting["template_notice"]) {
						\app\api\controller\MsgSubscribe::chat($data["data"]["msg"]["wxapp_id"], $sUserData, $sender);
					}
				}
				file_put_contents("job.txt", json_encode($data));
				$res = \think\facade\Queue::push("app\\job\\Job1", $data);
			}
		});
		$ws->on("close", function ($ws, $fd) {
			$userList = cache("chat_user_list") ?: [];
			$userList = $this->delByValue($userList, $fd);
			cache("chat_user_list", $userList);
		});
		cache("chat_user_list", []);
		$ws->start();
		$output->writeln("start swoole");
	}
	public function delByValue($arr, $value)
	{
		$keys = array_keys($arr, $value);
		if (!empty($keys)) {
			foreach ($keys as $key) {
				unset($arr[$key]);
			}
		}
		return $arr;
	}
}