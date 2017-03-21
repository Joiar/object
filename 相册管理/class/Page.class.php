<?php
	//分页类
	class Page{ 
		//最终计算   10，5 都是用户传入的实例化的时候传入每页显示条目数
		protected $num;		//每页显示条目数
		protected $current; //当前页
		protected $amount;	//总页码数 ceil(总条目数/每页数)
		protected $total;   //总条目数
		protected $offset;  //开始位置
		//为了不给外面赋值封装
		protected $limit;
	
		public function __construct($total,$num)
		{ 
			$this->total = $total;
			//计算总页码数
			$this->amount = ceil($total / $num);
			// echo $this->amount;
			
			// //检测出现负数
			$this->amount = max(1,$this->amount);
			
			$this->num = $num;
			//初始化当前页
			$this->init();
			// //判断 比1大 比总页码小 （写一个方法）
			// $current = $_GET['p'] ? $_GET['p'] : 1;
			//开始位置(放入属性)
			$this->offset = ($this->current-1) * $num;
			$this->limit = "{$this->offset},{$num}";
		}

		//但是外部要是用，只给一个取值的方法
		public function __get($key)
		{
			if($key == 'limit'){ 
				return $this->limit;
			}
		}
		//显示按钮
		public function show($size=0)
		{
			$pre = $next = $_GET;
			// 	echo "<pre>";
			// 		print_r($pre);
			// 	echo "</pre>";
			$pre['p'] -= 1;
			//判断范围
			$pre['p'] = max(1,$pre['p']);
			$next['p'] += 1;
			$next['p'] = min($this->amount,$next['p']);
			//神奇的函数http_build_query()
			// $tmp = http_build_query($pre);// echo $tmp;
			$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
			$pre = $url.'?'.http_build_query($pre);
			$next = $url.'?'.http_build_query($next);
			//搜索条件怎么写
			$str .= '<a href="'.$pre.'">上一页</a>';
			//数字按钮
			// $str .= $this->numlink($size,$url);
			$str .= '<a href="'.$next.'">下一页</a>';
			return $str;
		}

		/*=============辅助方法!================*/
		//获取当前页
		public function init()
		{ 
			$this->current = $_GET['p'] ? $_GET['p']:1;
			//判断 比1大 比总页码小 
			$this->current = max(1,$this->current);
			//要一个总页码数 (写一个属性)
			$this->current = min($this->amount,$this->current);
			// echo $this->current;
		}
	}