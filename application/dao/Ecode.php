<?php 
namespace dao;

class Ecode
{

	const ERROR = 401; //没有该方法
	const OK = 0; //成功
	const PARAMS_ERROR = 400; //参数错误
	const RET_ERROR = 402; //返回参数错误
	const SYSTEM_ERROR = 500; //系统错误
	const RET_REPEAT = -9999; //无效业务原样输出
	const RET_BACK = -9998; //黑名单
	const RET_NO_TOKEN = 999; //token失效
}

