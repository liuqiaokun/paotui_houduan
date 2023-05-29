<?php

//decode by http://www.yunlu99.com/
namespace app;

class Request extends \think\Request
{
	protected $filter = ["html_in"];
}