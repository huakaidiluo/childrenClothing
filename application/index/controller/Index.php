<?php
namespace app\index\controller;

class Index
{
    //接入API的验证
    public function index()
    {

        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
        //获取参数 signature nonce token timestamp
        $nonce = $_GET['nonce'];
        $token = 'imooc';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];

        //形成数组，然后按字典序
        $arr = array();
        $arr = array($nonce,$timestamp,$token);
        sort($arr);

        //拼接成字符串，sha1加密，然后与signature比较
        $str = sha1(implode($arr));
        if($str == $signature && $echostr){
            //第一次接入weixin api接口的时候,完成验证
            echo $echostr;
            exit;
        }else{
            //第二次或第n次
            $this->responseMsg();
        }
    }

    //接收事件推送并回复
    public function responseMsg()
    {
        //1.获取到微信推送过来post数据(XML格式)
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr); //将xml标签转化为对象
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注subscribe事件
            if (strtolower($postObj->Event == 'subscribe')) {
                //回复用户消息
                $toUser = $postObj->FromUserName;    //公众号id
                $fromUser = $postObj->ToUserName;     //openid
                $time = time();
                $msgType = 'text';
                $content = '欢迎关注我的公众账号';
                $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
            }
        }
        //消息回复－回复纯文本-关键字回复
        if (strtolower($postObj->MsgType) == 'text') {
            if (trim($postObj->Content) == 'imooc') {
                $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                $toUser = $postObj->FromUserName;    //公众号id
                $fromUser = $postObj->ToUserName;     //openid
                $time = time();
                $msgType = 'text';
                $content = "<a href='http://www.baidu.com'>百度</a>";  //可以回复链接
                echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content); //参数的顺序按照模板的顺序

            }
        }

        //消息回复－单图文消息－关键字的回复
        if (strtolower($postObj->MsgType) == 'text') {
            switch (trim($postObj->Content)) {
                case '男童':
                    break;
                case '女童':
                    break;
                default:
                    break;
            }
            $toUser = $postObj->FromUserName;    //公众号id
            $fromUser = $postObj->ToUserName;     //openid
            $time = time();
            $arr = array(
                array(
                    'title' => '新款女童装-1',
                    'description' => '这是一个测试数据',
                    'picUrl' => 'http://www.baidu.com', //可以访问到的图片地址
                    'url' => 'http://www.jd.com'
                ),
                array(
                    'title' => '新款女童装-2',
                    'description' => '这是一个测试数据',
                    'picUrl' => 'http://www.baidu.com', //可以访问到的图片地址
                    'url' => 'http://www.jd.com'
                ),
                array(
                    'title' => '新款女童装-3',
                    'description' => '这是一个测试数据',
                    'picUrl' => 'http://www.baidu.com', //可以访问到的图片地址
                    'url' => 'http://www.jd.com'
                )
            );

            $template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <ArticleCount>" . count($arr) . "</ArticleCount>
                    <Articles>";

            foreach ($arr as $k => $v) {
                $template .= "<item>
            <Title><![CDATA[" . $v['title'] . "]]></Title>
            <Description><![CDATA[" . $v['description'] . "]]></Description>
            <PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
            <Url><![CDATA[" . $v['url'] . "]]></Url>
            </item>";
            }
            $template .= '</Articles>
                </xml>';

            echo sprintf($template, $toUser, $fromUser, $time, $msgType);
            //注意：进行多图文发送时，子图文的个数不能超过10个
        }
    }// function end

    //curl的使用
    function http_curl()
    {
        //获取imooc
        //1.初始化curl
        $ch = curl_init();
        $url = 'http://www.baidu.com';  //要采集的网站地址
        //2.设置curl的参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        var_dump($output);
    }

    //获取access_token
    function getWxAccessToken()
    {
        //1.请求地址
        $grant_type = '';
        $appid = '';
        $secret = '';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        //2.初始化
        $ch = curl_init();
        //3.设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //3.调用接口
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        var_dump(json_decode($output,true));
    }

    //获取服务器ip地址--主要做安全检测
    function getWxServerIp()
    {
        $token = '';    //access_token
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$token;
        //2.初始化
        $ch = curl_init();
        //3.设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //3.调用接口
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        var_dump(json_decode($output,true));

    }
} //class end