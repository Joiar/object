<?php

	namespace loginDemo;

	class Login
	{

		protected $pdo;

		public function __construct()
		{
			//链接数据库
			$this->connectDB();
		}

		protected function connectDB()
		{
			$dsn = "mysql:host=localhost;dbname=demo;charset=utf8";
			$this->pdo = new \PDO($dsn, 'root', 'root');
		}

		//显示登录页
		public function loginPage()
		{
				include_once('./html/login.html');

		}

		//接受用户数据做登录
		public function handlerLogin()
		{

			$email = $_POST['email'];
			$pass = $_POST['pass'];

			//根据用户提交数据查询用户信息
			$sql = "select id,name,pass,reg_time from blog_admin where email = ?";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute([$email]);

			$userData = $stmt->fetch(\PDO::FETCH_ASSOC);

			//没有对应邮箱
			if ( empty($userData) ) {

				echo '登录失败1';

				echo '<meta http-equiv="refresh" content="2;url=./login.php">';
				exit;
			}

			//检查用户最近30分钟密码错误次数
			$res = $this->checkPassWrongTime($userData['id']);

			//错误次数超过限制次数
			if ( $res === false ) {
				echo '你刚刚输错很多次密码，为了保证账户安全，系统已经将您账号锁定30min';

				echo '<meta http-equiv="refresh" content="2;url=./login.php">';
				exit;
			}


			//判断密码是否正确
			$isRightPass = password_verify($pass, $userData['pass']);

			//登录成功
			if ( $isRightPass ) {

				echo '登录成功';
				exit;
			} else {

				//记录密码错误次数
				$this->recordPassWrongTime($userData['id']);

				echo '登录失败2';
				echo '<meta http-equiv="refresh" content="2;url=./login.php">';
				exit;
			}

		}

		//记录密码输出信息
		protected function recordPassWrongTime($uid)
		{

				//ip2long()函数可以将IP地址转换成数字
				$ip = ip2long( $_SERVER['REMOTE_ADDR'] );

				// echo '<pre>';
				// var_dump($_SERVER);exit;
				$time = date('Y-m-d H:i:s');
				$sql = "insert into blog_admin_info(uid,ipaddr,logintime,pass_wrong_time_status) values($uid,$ip,'{$time}',2)";


				$stmt = $this->pdo->prepare($sql);

				$stmt->execute();
		}

		/**
		 * 检查用户最近$min分钟密码错误次数
		 * $uid 用户ID
		 * $min  锁定时间
		 * $wTIme 错误次数
		 * @return 错误次数超过返回false,其他返回错误次数，提示用户
		 */
		protected function checkPassWrongTime($uid, $min=30, $wTime=3)
		{
				// return false;
				if ( empty($uid) ) {

					throw new \Exception("第一个参数不能为空");

				}

				$time = time();//9:00
				$prevTime = time() - $min*60;//8:30

				// echo '<pre>';
				//用户所在登录ip
				$ip = ip2long( $_SERVER['REMOTE_ADDR'] );
				// print_r($_SERVER);exit;

				//pass_wrong_time_status代表用户输出了密码
				$sql = "select * from blog_admin_info where uid={$uid} and pass_wrong_time_status=2 and UNIX_TIMESTAMP(logintime) between $prevTime and $time and ipaddr=$ip";


				$stmt = $this->pdo->prepare($sql);

				$stmt->execute();

				$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);


				//统计错误次数
				$wrongTime = count($data);

				//判断错误次数是否超过限制次数
				if ( $wrongTime > $wTime ) {

					return false;
				}

				return $wrongTime;



		}

		public function __call($methodName, $params)
		{

				echo '访问的页面不存在','<a href="./login.php">返回登录页</a>';
		}
	}

	$a = @$_GET['a']?$_GET['a']:'loginPage';
	// echo $a;
	$login = new Login();

	$login->$a();
