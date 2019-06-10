使用map可以隔离每个开发人员各自的开发环境
1 nginx的配置文件添加如下指令

map $http_env $dir{
    'zhuang' 'zhuang';
    default 'root';
}

2 请求的时候在请求头附上各自的来源，可以用一些工具

思路是一样的，也可以基于ip区分


