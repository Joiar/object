CREATE TABLE `blog_admin` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '用户名',
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL COMMENT 'password_hash加密',
  `userpic` varchar(255) NOT NULL COMMENT '用户头像',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1启用 2禁用',
  `reg_time` int(10) UNSIGNED NOT NULL COMMENT '注册时间',
  PRIMARY key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 记录后台管理员登陆信息
CREATE TABLE `blog_admin_info` (
  `id` int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT  NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `ipaddr` int(10) UNSIGNED NOT NULL COMMENT '用户登陆IP',
  `logintime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '用户登陆时间',
  `pass_wrong_time_status` tinyint(10) UNSIGNED NOT NULL COMMENT '登陆密码错误状态' COMMENT '0 正确 2错误'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `blog_admin` (`id`, `name`, `email`, `pass`, `reg_time`, `status`,`userpic`) VALUES
(1, 'cpphp', 'gzphper@163.com', '$2y$10$DkZOwoRAfBVxu9rflwJIGOUkRVgOp0XdtgzlnrhYHaZIU055vraNS',2130706433,1,'./uploads/admin.jpg');
