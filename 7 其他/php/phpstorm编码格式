官方文档：https://www.jetbrains.com/help/phpstorm/encoding.html


能确认编码格式的：
	1 文件内指定了编码格式。例如：html文件在meta中指定了charset
	2 文件包含BOM头
	3 可以根据文件内容推测编码格式

不能确认编码格式的，会应用设置的默认编码格式
	项目内文件：文件或目录的编码 > 项目编码 
	全局编码(貌似没用到。测试的时候，不在任何项目的工作空间直接打开文件，貌似phpstorm会创建临时项目，然后还是应用了项目默认编码。。)
	属性文件的编码格式，也不清楚什么用。。

Tip1：file-》default setting 是超全局默认配置，当新建项目的时候，会将该配置的值复制到当前项目的preferences下
Tip2：在更新编码格式后，原先被打开过的文件不会及时切换编码，需要重启phpstorm
