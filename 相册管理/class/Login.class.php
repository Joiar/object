<?php 

	//定义一个登录类
	class Login
	{ 
		//定义一个成员属性,用于存储表名
		protected $tabName;
		//写一个构造方法用于接收表名
		public function __construct($tabName)
		{ 
			$this->tabName = $tabName;
		}

		//登录验证方法
		public function check($data)
		{ 
			//实例化Model类
			$user = new Model($this->tabName);

			//调用Model类中的查询方法
			$info = $user->where($data)->select();
			// echo "<pre>";
			// 	var_dump($info);
			// echo "</pre>";
			// exit;
			//判断是否正确
			if($info) { 
				//正确返回数据
				return $info;
			} else { 
				//错误返回false;
				return false;
			}
		}
	}