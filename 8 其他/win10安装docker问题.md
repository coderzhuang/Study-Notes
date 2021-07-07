
安装过程：
	使用阿里云镜像下载 http://mirrors.aliyun.com/docker-toolbox/windows/docker-toolbox/DockerToolbox-18.03.0-ce.exe
	在安装过程中会检测boot2docker.iso版本
	可以在start.sh中指定iso：
		"${DOCKER_MACHINE}" create -d virtualbox  --virtualbox-boot2docker-url=boot2docker.iso $PROXY_ENV "${VM}"
	还需要修改docker镜像
		docker-machine ssh default // 先进入虚拟机，default 是默认的虚拟机名称
		sudo vi /var/lib/boot2docker/profile
		编辑这个文件，添加镜像源 --registry-mirror https://registry.docker-cn.com http://hub-mirror.c.163.com

	Toolbox 默认只能访问 C:\Users 需要设置虚拟机配置 https://www.jianshu.com/p/b18122eaddc3

	This computer doesn't have VT-X/AMD-v enabled. Enabling it in the BIOS is mandatory
		可能是检测问题，进bios查看，虚拟支持是开启的
		docker-machine create default --virtualbox-boot2docker-url=boot2docker.iso  --virtualbox-no-vtx-check

	共享文件夹出不来
		更新vbox到版本6



问题描述：
	docker-machine ssh default 进入docker建立的虚拟机
	可以ping的通百度，但是ping不通宿主机，以及宿主机同网段主机
	宿主机ip：172.17.108.166
	宿主机同网ip：172.17.18.185
	VM有3张网卡：
		docker0 172.17.0.1
		eth0 10.0.2.15 (NAT)
		eth1 192.168.99.100（host-only）
		route:
			Destination     Gateway         Genmask         Flags Metric Ref    Use Iface
			default         10.0.2.2        0.0.0.0         UG    1      0        0 eth0
			10.0.2.0        *               255.255.255.0   U     0      0        0 eth0
			127.0.0.1       *               255.255.255.255 UH    0      0        0 lo
			172.17.0.0      *               255.255.0.0     U     0      0        0 docker0
			192.168.99.0    *               255.255.255.0   U     0      0        0 eth1
		iptables -t nat -S
			-P PREROUTING ACCEPT
			-P INPUT ACCEPT
			-P OUTPUT ACCEPT
			-P POSTROUTING ACCEPT
			-N DOCKER
			-A PREROUTING -m addrtype --dst-type LOCAL -j DOCKER
			-A OUTPUT ! -d 127.0.0.0/8 -m addrtype --dst-type LOCAL -j DOCKER
			-A POSTROUTING -s 172.17.0.0/16 ! -o docker0 -j MASQUERADE
			-A DOCKER -i docker0 -j RETURN

问题解决
	怀疑是宿主和docker0 网段相同，所以会在docker0网段内寻找172.17.18.185，所以失败
	修改docker0的网段为 172.19.0.1/16 就能ping通宿主机核其他机器了
	不太清楚是不是这样的？？？
{
  "registry-mirrors":["http://hub-mirror.c.163.com"],
  "bip":"172.17.10.1/24"
}
