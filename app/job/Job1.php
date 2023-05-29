<?php

//decode by http://www.yunlu99.com/
namespace app\job;

class Job1
{
	public function fire(\think\queue\Job $job, $data)
	{
		$isJobDone = $this->doTask($data);
		if ($isJobDone) {
			$job->delete();
		} else {
			if ($job->attempts() > 3) {
				$job->delete();
			}
			$job->release(2);
		}
	}
	public function doTask($data)
	{
		$map1 = [["wxapp_id", "=", $data["data"]["msg"]["wxapp_id"]], ["u_id", "=", $data["data"]["msg"]["suserinfo"]], ["s_id", "=", $data["data"]["msg"]["userinfo"]["u_id"]]];
		$map2 = [["wxapp_id", "=", $data["data"]["msg"]["wxapp_id"]], ["u_id", "=", $data["data"]["msg"]["userinfo"]["u_id"]], ["s_id", "=", $data["data"]["msg"]["suserinfo"]]];
		$findData = \app\api\model\ZhChat::whereOr([$map1, $map2])->find();
		if ($findData) {
			if ($findData["status"] == 0) {
				\app\api\model\ZhChat::where(["id" => $findData["id"], "wxapp_id" => $findData["wxapp_id"]])->update(["status" => 1]);
			}
			$chatId = $findData["id"];
		} else {
			$params = ["wxapp_id" => $data["data"]["msg"]["wxapp_id"], "u_id" => $data["data"]["msg"]["userinfo"]["u_id"], "s_id" => $data["data"]["msg"]["suserinfo"], "create_time" => date("Y-m-d H:i:s", time()), "status" => 1];
			$chatId = \app\api\model\ZhChat::insertGetId($params);
		}
		if ($chatId) {
			if ($data["data"]["msg"]["type"] == "text") {
				$arr = ["wxapp_id" => $data["data"]["msg"]["wxapp_id"], "chat_id" => $chatId, "type" => $data["data"]["msg"]["type"], "content" => $data["data"]["msg"]["content"]["text"], "add_time" => date("Y-m-d H:i:s", time()), "u_id" => $data["data"]["msg"]["userinfo"]["u_id"], "record_time" => $data["data"]["msg"]["time"]];
			} else {
				$arr = ["wxapp_id" => $data["data"]["msg"]["wxapp_id"], "chat_id" => $chatId, "type" => $data["data"]["msg"]["type"], "content" => json_encode($data["data"]["msg"]["content"]), "add_time" => date("Y-m-d H:i:s", time()), "u_id" => $data["data"]["msg"]["userinfo"]["u_id"], "record_time" => $data["data"]["msg"]["time"]];
			}
			$res = \app\api\model\ZhChatLog::insertGetId($arr);
			if ($res) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function failed($data)
	{
	}
}