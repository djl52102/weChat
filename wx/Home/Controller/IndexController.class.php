<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
  public function index(){
  	//获取nonce,timestamp,token
  	$nonce=$_GET['nonce'];
  	$timestamp=$_GET['timestamp'];
  	$token='weiphp';
  	$signature=$_GET['signature'];
  	$echostr=$_GET['echostr'];

  	//将参数存储到数组中，并进行排序
  	
  	$array=array($timestamp,$nonce,$token);
  	sort($array);

  	//将参数拼接为字符串，并加密
  	$str=implode('', $array);
  	$str=sha1($str);

  	//判断参数
  	if ($str == $signature && $echostr){
  		echo $echostr;
  		exit;
  	}else{
  		$this->responseMsg();
  	}
  }
  //消息回复		
  public function responseMsg()
  {
	          /*
	          <xml>
              <ToUserName><![CDATA[toUser]]></ToUserName>
              <FromUserName><![CDATA[FromUser]]></FromUserName>
              <CreateTime>123456789</CreateTime>
              <MsgType><![CDATA[event]]></MsgType>
              <Event><![CDATA[subscribe]]></Event>
              </xml>
              */
              //获取微信推送的post数据,xml格式
              $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];
              //处理消息类型，并设置回复类型和内容
              $postObj=simplexml_load_string($postArr);
              //$postObj->ToUserName='';
              //$postObj->FromUserName='';
              //$postObj->CreateTime='';
              //$postObj->MsgType='';
              //$postObj->Event='';
              //判断该数据包是否是订阅事件推送
              if (strtolower($postObj->MsgType) == 'event'){
              	 if(strtolower($postObj->Event) == 'subscribe'){
                       $toUser=$postObj->FromUserName;
                       $fromUser=$postObj->ToUserName;
                       $time=time();
                       $msgType='text';
                       $content='问候语，请回复<hello>
                       			获取人名，请回复<dingding>
                       			获取图文咨询，请回复<tuwen1>';
                       $template="<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[%s]]></MsgType>
                       <Content><![CDATA[%s]]></Content>
                       </xml>";

                       $info=sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                       echo $info;

              	 }
              }
              

              //当用户发送tuwen1关键字，回复一个单图文
              
              	if (strtolower($postObj->MsgType) == 'text' && trim(strtolower($postObj->Content) == 'tuwen1'))
              	{
              		$toUser=$postObj->FromUserName;
              		$fromUser=$postObj->ToUserName;
              		//字图文不能超过10个
              		$arr=array(
              			array(
              					'title'=>'网易云音乐',
              					'description'=>'找寻喜欢同样音乐的朋友，来网易一起听吧！',
              					'picUrl'=>'http://p1.music.126.net/QWMV-Ru_6149AKe0mCBXKg==/1420569024374784.jpg?param=180y180',
              					'url'=>'http://music.163.com/'
              				),
              			array(
              					'title'=>'古典武侠',
              					'description'=>'一起欣赏金庸，古龙眼中的大侠',
              					'picUrl'=>'http://zuopinj.com/d/file/wx/jinyong/2015-02-06/8b9c15d8a9c52448738084e5abce5a2e.jpg',
              					'url'=>'http://jinyong.zuopinj.com/'
              				),
              			array(
              					'title'=>'知乎',
              					'description'=>'与世界分享你的知识，经验与见解',
              					'picUrl'=>'https://ss0.baidu.com/73x1bjeh1BF3odCf/it/u=1462875463,4072409776&fm=85&s=598B8B554EAD5E1B1734ADEA03006033',
              					'url'=>'https://www.zhihu.com/explore'
              				),
              			);
              		$template="<xml>
                              <ToUserName><![CDATA[%s]]></ToUserName>
                              <FromUserName><![CDATA[%s]]></FromUserName>
                             <CreateTime>%s</CreateTime>
                             <MsgType><![CDATA[%s]]></MsgType>
                             <ArticleCount>".count($arr)."</ArticleCount>
                             <Articles>";
		      foreach ($arr as $k => $v) 
		      {
			         $template.="<item>
                                 <Title><![CDATA[".$v['title']."]]></Title> 
                                 <Description><![CDATA[".$v['description']."]]></Description>
                                 <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
                                 <Url><![CDATA[".$v['url']."]]></Url>
                                 </item>
                                ";
		      };
   
               $template.="</Articles>
                           </xml>";
               echo sprintf($template,$toUser,$fromUser,time(),'news');      
             }else{
              			$template="<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[%s]]></MsgType>
                       <Content><![CDATA[%s]]></Content>
                       </xml>";
                       $toUser=$postObj->FromUserName;
                       $fromUser=$postObj->ToUserName;
                       $time=time();
                       $msgType='text';
                       $content='';
              	switch (trim($postObj->Content)) {
              		case 'hello':
              			$content='hello wechat';
              			break;
              		case 'dingding':
              			$content='南城喜欢你';
              			break;
              		case '百度':
              			$content="<a href='http://www.baidu.com'>百度</a>";
              			break;	
              		default:
              			$content='欢迎关注琅琊阁blog';
              			break;
              	}
                            
              	 $info=sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                     echo $info;
              }		
              		
         
    }

  	 function http_curl()
  	{
  		//初始化url
  		$ch=curl_init();
  		$url='http://www.baidu.com';
  		//设置curl参数
  		curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  		//采集
  		$output=curl_exec($ch);
  		//关闭连接
  		curl_close($ch);

  		var_dump($output);
    }
	
	//获取token
	function  getToken(){
		$ch=curl_init();
		$appid="wxc1dab505f8977d0a";
		$appSecret="480d71f1692d86a5180bc5066ad99523";
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appSecret";
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		
		$output=curl_exec($ch);
		
		curl_close();
		
		$output=json_decode($output,true);

		return $output['access_token'];
	}

	//获取微信服务器ip地址
	function getIp()
	{
		$token=$this->getToken();
		$ch=curl_init();
		$url="https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=$token";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		$output=curl_exec($ch);
		curl_close();

		var_dump($output);

	}
		
}
?>