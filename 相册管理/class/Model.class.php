<?php
	//操作数据库的类
	//  where 条件的书写(简单的)
	  
	//定义一个model类
	class Model{  
		//成员属性
		protected $link;     		//数据库连接资源
		protected $tabName;  		//用于存储表名
		protected $fields = '*';	//用于存储要查询的字段
		protected $limit;   	 	//用于条目查
		protected $allFields;    	//用于存储数据库字段
		protected $where;    		//用于存储查询条件
		//构造方法
		public function __construct($tabName){ 
			//连接数据库
			$this->getConnect();
			//设置表名
			$this->tabName = $tabName;
			//调用字段缓存
			$this->getFields();

		}
		//增加
		/*	
			需要的数据的格式
			$data['name'] = 'jack';
			$data['age'] = 18;
		*/
		public function add($data){
			$keys = join("`,`",array_keys($data));
			$vals = join("','",array_values($data));
			$sql = "insert into {$this->tabName}(`{$keys}`) values ('{$vals}')";
			return $this->execute($sql);
		}
		
		//查询一条数据
		public function find($id){ 
			$sql = "select {$this->fields} from {$this->tabName} where id = '{$id}'";
			$info = $this->query($sql);
			return $info[0];
		}
		
		//统计总条目数
		public function count(){ 
			$sql = "select count(*) as total from {$this->tabName} {$this->where}";
			$total = $this->query($sql);
			return $total[0]['total'];
		}

		//删除
		public function del(){
			$sql = "delete from {$this->tabName} {$this->where}";
			return $this->execute($sql);
		}
		
		//修改
		public function update($data = array()){

			//去除值为空的元素
			$data = array_filter($data);
			$list = '';
			foreach($data as $key=>$val) { 
				$list .= "`".$key."` = '".$val."',";
			}
			$list = rtrim($list,',');
			$sql = "update {$this->tabName} set {$list} {$this->where}";
			return $this->execute($sql);
		}
		
		//查询
		public function select(){
			// echo 123;
			$sql = "select {$this->fields} from {$this->tabName} {$this->where} {$this->limit}";
			// echo $sql;
			return $this->query($sql);
		}

		//where 条件
		/*
			$map['name'] = '张三';
			$map['age'] = array('gt大于',18)
			$map['age'] = array('lt小于',18)
			$map['name'] = array('link',%张%)

		*/
		public function where($arr){ 
			//通过参数拼接字符串
			if(is_array($arr) && !empty($arr)) {
			//判断是and 还是 or 
			if (isset($arr['_logic'])) {
            	$logic = $arr['_logic'];
            	//删除这个逻辑下标
            	unset($arr['_logic']);
	        } else {
	            $logic = 'and';
	        }
			$tmp = array(); 
				foreach($arr as $key=>$val){ 
					if(is_array($val)){
						$type = $val[0]; 
						switch($type){
							case 'lt':
								$tmp[] = "{$key} < '{$val[1]}'";
								break;
							case 'gt':
								$tmp[] = "{$key} > '{$val[1]}'";
								break;
							case 'like':
								$tmp[] = "{$key} like '%{$val[1]}%'";
								break;
							default:
								die('手贱用户');
								break;
						}
					} else { 
						//等于
						$tmp[] = "{$key} = '{$val}'";
					}
				}
				//先把数组打印出来看看
				$where = 'where '.join(" {$logic} ",$tmp);
				// echo $where;

				// 	echo "<pre>";
				// 		print_r($tmp);
				// 	echo "</pre>";
				//where
				$this->where = $where;
			}
			return $this;
		}
/*==================辅助方法===================*/
		//用于查询条目数
		public function limit($limit){ 
			$this->limit = 'limit '.$limit;
			return $this;
		}

		//用于分字段查询
		public  function field($arr){ 
			if (!is_array($arr)) return $this;

			//检查数据内容，删除没有用的字段
			$arr = $this->check($arr);

			//判断处理完的是不是空数组
			if (empty($arr)) return $this;
			$this->fields = join(',',$arr);
			return $this;
		}

		//用于过滤非法数据
		/*
			array('id','name','age')
		*/
		protected function check($arr){ 
			//遍历$arr将数据库中没有的字段删除
			foreach($arr as $key=>$val) { 
				if (!in_array($val,$this->allFields)) unset($arr[$key]);
			}
			// var_dump($arr);exit;
			return $arr;
		}

		//查询数据库字段
		protected function getFields(){ 
			$sql = "desc {$this->tabName}";
			$info = $this->query($sql);
			$fields = array();
			foreach($info as $val) { 
				$fields[] = $val['Field'];
			}
			//将字段信息存储起来
			$this->allFields = $fields;
		}

		//用户查询，返回二位数组
		protected function query($sql){ 
			// echo $sql.'<br>';
			$result = mysqli_query($this->link,$sql);
			if($result) { 
				//处理结果集
				while($row = mysqli_fetch_assoc($result)){
					$list[] = $row;
				}
				return $list;
			}
			die('SQL语句错误');
		}

		//用于执行，返回受影响行或者最后插入ID
		protected function execute($sql){ 
			$result = mysqli_query($this->link,$sql);
			if($result) { 
				return mysqli_insert_id($this->link) ? mysqli_insert_id($this->link) : mysqli_affected_rows($this->link);
			} 
			die('sql语句错误');
		}
		
		//数据库的连接操作
		protected function getConnect(){ 
			//连接数据库
			$this->link = mysqli_connect(HOST,USER,PASS) or die('数据库连接失败');
			mysqli_select_db($this->link,DB) or die ('数据库连接失败');
			mysqli_set_charset($this->link,'utf8');
		}
		
		//析构方法
		public  function __destruct(){ 
			//关闭数据库
			mysqli_close($this->link);
		}
	}