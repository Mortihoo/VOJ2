Virtual Online Judge
===============

由于本学期PHP课程考试需要做一个PHP的网站，所以决定拿回之前写好的简易版VOJ，在原生PHP的基础上，使用thinkphp框架，并且使用think-queue实现消息队列。简单的实现了从hdoj上的一个DIY题库中（因为当初是为了举办小比赛，当时我们学校并没有OJ，却又要限制他们内网，又不想配PC^2，于是乎弄了这么一个东西）提交题目，返回结果。

笔记：
+ 使用了composer来安装thinkphp，包括后面的think-queue也是用composer来安装。
+ 使用redis扩展的时候，需要安装php7.0-dev,以及git 拉取 phpredis ，并且编译，在php.ini 加一个扩展。
+ 期间出现了一个问题，Call to undefined function think\helper\mb_substr()。后来找到了解决办法，需要apt-get install php7.0-mbstring.然后记得将php.ini中的xxxx.string.dll 改成 xxxxstring.so
+ 使用thinkphp的分页，不知道为什么引入的bootstrap.css版本太高反而不行，后来引入了3.11就成功了。

