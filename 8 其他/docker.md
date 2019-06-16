==========================================================================
镜像
默认 TAG 为latest

* 下载镜像
docker pull IMAGE[:TAG]

* 查看镜像
docker images

* 设置镜像标签
docker tag IMAGE[:TAG] NEW_IMAGE[:TAG]

* 查看镜像的详细信息
docker inspect IMAGE

* 搜索镜像，默认搜索官方库中的镜像
docker search IMAEGE -s 100 --no-trunc

* 删除镜像, 如果IMAGE指向镜像ID，会删除镜像本身和所有tag; 当容器存在时，无法删除
docker rmi IMAGE [IMAGE ..]

* 创建镜像
docker commit -m 'MESSAGE' -a 'AUTHOR' CONTAINER IMAGE

* 上传镜像
docker push IMAGE[:TAG]

==========================================================================
容器 镜像的运行实例

* 创建容器，创建后处于停止状态
docker create IMAGE

* 启动／停止／重启容器
docker start/stop/restart CONTAINER

* 等价于：先create 再 start; -t 伪终端 -i 标准输入 --rm 容器终止后会立即删除，与 -d 互斥
docker run -it --rm IMAGE COMMAND
docker run -it centos /bin/bash 
当用户推出容器，该容器也就处于停止状态!!!
可以通过 -d 参数使容器处于后台运行 ???

* 查看容器的输出
docker logs CONTAINER

* 唤起容器 ???
docker attach CONTAINER
docker exec -it CONTAINER COMMAND

* 删除容器
docker rm CONTAINER

* 将容器导出到文件
docker export CONTAINER > FILE

* 将文件恢复成镜像 ??? 和 docker load 的区别
cat FILE | docker import - IMAGE

* 查看所有容器
docker ps -a

* 删除容器
docker rm CONTAINER

* 挂载本地目录到容器
docker run -it -v HOST_DIR:VHOST_DIR:rw/ro IMAGE

* 共享数据卷
docker run -it -v /test --name share_db centos
其他容器要共享/test 就可以
docker run -it --volumes-from share_db --name other centos

* 端口映射
docker run -d -P centos // 端口随机分配 
docker run -d -p 5001:5000 centos // 将主机5001映射到容器5000端口

* 查看端口
docker ps -l 
docker port CONTAINER

* 容器间的互联
docker run -d --name db centos
docker run -d -P --name web --link db:db centos //--link name:alias









































