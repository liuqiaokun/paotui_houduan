<?php

//decode by http://www.yunlu99.com/
namespace app\api\model;

class ZhArticles extends \think\Model
{
	use \think\model\concern\SoftDelete;
	protected $deleteTime = "delete_time";
	protected $pk = "article_id";
	protected $name = "zh_articles";
}