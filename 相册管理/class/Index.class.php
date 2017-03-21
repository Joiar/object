<?php 
	//单文件类
	class Index
	{ 
		//构造方法
		public function __construct()
		{ 

		}

		//显示成功信息的方法
		protected function success($msg,$url = '')
		{ 
			echo '<script>';
			echo 'alert("'.$msg.'")';
			echo '</script>';
			echo '<script>';
			if($url !== '') { 
				echo 'window.location.href="'.$url.'"';
			} else { 
				echo 'window.history.back()';
			}
			echo '</script>';
			exit;
		}
		
		//显示错误信息的方法
		protected function error($msg,$url = '')
		{ 
			echo '<script>';
			echo 'alert("'.$msg.'")';
			echo '</script>';
			echo '<script>';
			if($url !== '') { 
				echo 'window.location.href="'.$url.'"';
			} else { 
				echo 'window.history.back()';
			}
			echo '</script>';
			exit;
		}

		//判断是否登录.
		protected function issetSession()
		{ 
			if(!$_SESSION['adminInfo']) { 
				$this->error('非法访问','./index.php');exit;
			}
		}
		//显示验证码
		public function Verify()
		{
			//实例化验证码类
			$verify = new Verify();
			//显示验证码
			$verify->entry();
		} 

		//登录方法
		public function login()
		{ 
			//显示登录模板
			include './view/login.html';
		}

		//登录验证
		public function doLogin()
		{
			//获取用户信息
			$map['name'] = $_POST['name'];
			$map['pwd'] = md5($_POST['pwd']);

			//获取用户验证码
			$code  = $_POST['code'];

			//实例化验证码类
			$veriry = new Verify();
			//调用验证码验证方法
			if(!$veriry->check($code)) { 
				//调用错误信息显示方法
				$this->error('验证码错误');
			}

			//实例化登录类
			$login = new Login('user');

			$info = $login->check($map);

			if($info) { 
				$_SESSION['adminInfo'] = $info[0];
				$this->success('登录成功','./index.php?a=index');
			} else { 
				$this->error('账号或密码错误');
			}
		}

		//定义一个index方法
		public function index()
		{ 

			//判断是否合法登录
			$this->issetSession();
			include './view/index.html';

		}

		//退出登录方法
		public function outLogin()
		{ 
			unset($_SESSION['adminInfo']);
			$this->success('退出成功','./index.php');
			exit;
		}

		//创建相册方法
		public function createPic()
		{ 
			//判断是否合法登录
			$this->issetSession();

			//判断是添加还是显示
			if($_POST){
				$config = array(
					'savePath'   =>    './Public/Uploads/',
					);
				//实例化上传类
				$upload = new Upload($config);
				//调用上传方法
				$info = $upload->upload();
				//遍历获取图片保存路径
				foreach($info as $val) { 
					$Path = $val['savePath'].$val['saveName'];
				}
				//获取用户信息
				$map['userid'] = $_SESSION['adminInfo']['id'];
				$map['name'] = $_POST['name'];
				$map['image'] = $Path;
				$map['addtime'] = time();
				
				$photo = new Model('photo_name');
				$info = $photo->add($map);
				if($info) { 
					$this->success('新建相册成功');
				} else { 
					$this->error('新建相册失败');
				}
			} else { 
				$photo = new Model('photo_name');
				$map['userid'] = $_SESSION['adminInfo']['id'];
				$picList = $photo->where($map)->select();

				include './view/picList.html';
			}

		}

		//编辑相册
		public function editPic()
		{ 
			// $id = $_GET['id'];
			//判断是否合法登录
			$this->issetSession();

			if($_POST) {
				$map['id'] = $_POST['id'];
				$config = array(
					'savePath'   =>    './Public/Uploads/',
					);
				//实例化上传类
				$upload = new Upload($config);
				//调用上传方法
				$info = $upload->upload();
				//遍历获取图片保存路径
				foreach($info as $val) { 
					$Path = $val['savePath'].$val['saveName'];
				}

				$data['image'] = $Path;
				$data['addtime'] = time();
				$data['name'] = $_POST['name'];

				$edit = new Model('photo_name');
				$res = $edit->where($map)->update($data);
				if($res) {
					$this->success('相册修改成功','./index.php?a=createPic');
				} else { 
					$this->error('相册修改失败');
				}
			} else { 
				$id = $_GET['id'];
				$edit = new Model('photo_name');
				$res = $edit->where(array('id'=>$id))->select();
				include './view/editPic.html';
			}
		}

		//显示相片方法
		public function imageList()
		{ 
			//判断是否合法登录
			$this->issetSession();
			$map['picid'] = $_GET['picid'];
			$map['userid'] = $_SESSION['adminInfo']['id'];
			if($_POST){ 
				$config = array(
					'savePath'   =>    './Public/Uploads/',
					);
				//实例化上传类
				$upload = new Upload($config);
				//调用上传方法
				$info = $upload->upload();
				//遍历获取图片保存路径
				foreach($info as $val) { 
					$Path = $val['savePath'].$val['saveName'];
					//获取用户信息
					$map['picid'] = $_POST['picid'];
					$map['image'] = $Path;
					$map['addtime'] = time();
					//实例化Model类
					$image = new Model('photo_image');
					$info = $image->add($map);
				}
				if($info) { 
					//调用添加方法
					$this->success('添加成功');
				} else { 
					$this->error('添加失败');
				}
			} else { 
				$maps['picid'] = $_GET['picid'];
				$maps['userid'] = $_SESSION['adminInfo']['id'];
				$imageList = new Model('photo_image');
				$imageList = $imageList->where($maps)->select();
				// echo "<pre>";
				// 	var_dump($imageList);
				// echo "</pre>";
				include './view/imageList.html';
			}
		}

		//删除相片
		public function delImage()
		{ 
			//判断是否合法登录
			$this->issetSession();
			$id = $_GET['id'];
			$del = new Model('photo_image');
			//获取图片地址
			$imagePath = $del->find($id);
			$res = $del->where(array('id'=>$id))->del();
			if($res) { 
				unlink($imagePath['image']);
				$this->success('删除成功');
			} else { 
				$this->error('删除失败');
			}
		}
	}