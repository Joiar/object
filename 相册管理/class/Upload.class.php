<?php
	/*
		1.配置上传信息
		2.检测上传目录 不存在创建
		3.检测上传文件是否合法
		4.上传前判断
		5.执行上传 同时生成新文件名
	*/
	//定义一个上传类
	class Upload{
		protected $config = array(
			'savePath'   => 	'./Public',   					//设置文件保存目录
			'saveName'   =>     '',								//设置文件保存文件名
			'maxSize'	 => 	0,								//设置文件上传的大小
			'exts' 		 =>  	array(),						//设置文件的类型
		);


		protected $error = '';									//存储错误信息
		protected $newName = '';								//存储上传好的文件名

		//构造方法 
		public function __construct($config = array()){ 
			//设置配置信息将用户定义的配置信息和系统定义的合并
			$this->config = array_merge($this->config,$config);

			//检测并设置文件保存目录
			$this->savePath($this->savePath);
		}	

		public function upload(){ 
			//遍历接收到的文件信息
			foreach($_FILES as $key => &$files) { 

				//使用循环进行多文件上传
				for($i = 0; $i < count($files['name']);$i++) { 
					//检测上传的文件是否合法
					if (!$this->check($files,$i)) return false;

					//进行上传前判断
					if(is_uploaded_file($files['tmp_name'][$i])) { 
						//执行上传
						if(move_uploaded_file($files['tmp_name'][$i],$this->savePath.$this->saveName($files,$i))) { 
								//将上传好后的信息放入新数组
								//获取file的键
								$arr = array_keys($files);
								for($j = 0; $j <count($arr); $j ++) { 
									$info[$i][$arr[$j]] = $files[$arr[$j]][$i];
									
								}
								$info[$i]['savePath'] = $this->savePath;
								$info[$i]['saveName'] = $this->newName;
								//将file数组返回
								// return $files;
						} else{ 
							$this->error = '上传失败，人品问题';
							return false;
						}
					} else { 
						$this->error = '非法上传';
						return false;
					}
				}
				//将info数组返回
				return $info;
			}
		} 

		/*=============辅助方法!================*/
		//魔术方法__get() 获取配置信息
		public function __get($key){ 
			return $this->config[$key];
		}

		//设置文件保存目录
		protected function savePath($savePath){ 
			//处理目录名称  
			// $savePath = iconv('utf-8', 'gb2312', $savePath);
			$this->savePath = rtrim($savePath,'/').'/';
			//检测目录是否存在
			if(!file_exists($this->savePath)) { 
				mkdir($this->savePath,0777,true);
			}
		}		

		//设置上传后的文件名
		protected function saveName($file,$i) { 
			//获取原文件的后缀
			$exts = pathinfo($file['name'][$i],PATHINFO_EXTENSION);
			//判断用户是否配置保存文件名，由于empty只可以判断变量，所以将文件名保存规则赋给变量用于判断。
			$saveName = $this->saveName;
			$saveName = empty($saveName) ? md5(rand(1,9)).uniqid() : $this->saveName.md5(rand(1,9999));
			//将最后生成的文件名保存
			$this->newName = $saveName.'.'.$exts;

			//返回最最后生成的文件名
			return $this->newName;
		}

		//检测上传文件是否合法方法
		protected function check($file,$i) { 
			if($file['error'][$i] > 0) { 
				switch($file['error'][$i]) { 
					case 1:
						$this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。';
						break;
					case 2:
						$this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。 ';
						break;
					case 3:
						$this->error = '文件只有部分被上传。';
						break;
					case 4:
						$this->error = '没有文件被上传。';
						break;
					case 6:
						$this->error = '找不到临时文件夹。';
						break;
					case 7:
						$this->error = '文件写入失败。';
						break;
					default:
						$this->error = '未知错误';
						break;
				}

				//错误返回 false
				return false;
			}

			//检测文件大小是否正确
			if($this->maxSize > 0) { 
				if($file['size'][$i] > $this->maxSize) { 
					$this->error = '文件大小超过限定值，限定值为：'.$this->maxSize;
					return false;
				}
			}

			//检测文件类型是否合法
			if (count($this->exts) > 0) { 
				//切割出上传文件的后缀
				$ext = explode('/',$file['type'][$i]);
				//检测文件类型
				if (!in_array($ext[1],$this->exts)) { 
					$this->error = '上传文件的类型不合法。允许的类型为：'.implode(',',$this->exts);
					return false;
				}
			}

			//如果全部正确，返回true
			return ture;
		}

		//获取上传的错误信息
		public function getError(){ 
			echo $this->error;
		}
	
	}