<?php
namespace Home\Controller;
use Think\Controller;
use  Home\Model\IndexModel;
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
                       $content="获取天气，请回复城市名称\n获取资讯详情，请回复<tuwen1>";
						$indexModel=new IndexModel;
						$indexModel->responseSubscribe($postObj,$content);

              	 }
              }
            
            //当用户发送tuwen1关键字，回复一个单图文
                
              	if (strtolower($postObj->MsgType) == 'text' && trim(strtolower($postObj->Content) == 'tuwen1'))
              	{
              		$indexModel=new IndexModel;
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
              		$indexModel->responseNews($postObj,$arr);   
             }else{
                
                
                  // $ch=curl_init();
                  // $url="https://api.seniverse.com/v3/weather/now.json?key=faua8rceea8uoemu&location=".urlencode($postObj->Content)."&language=zh-Hans&unit=c";
                  // curl_setopt($ch, CURLOPT_URL, $url);
                  // curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

                  // $res=curl_exec($ch);
                  // curl_close();

                  // $arr=json_decode($res,true);

                  // $content=$arr['results'][0]['location']['name']."\r\n".'天气: '.$arr['results'][0]['now']['text']."\r\n".'温度: '.$arr['results'][0]['now']['temperature'];
                
                           
               
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
              		case '新浪':
              			$content="新浪微博";
              			break;		
              		default:
              			$content='欢迎关注琅琊阁blog';
              			break;
              	}
                           
           		//实例化模型
           		$indexModel=new IndexModel;
           		$indexModel->responseText($postObj,$content);
            }
               
    }

  	 function http_curl($url,$type='get',$res='json',$arr='')
  	{
  		//初始化url
  		$ch=curl_init();
  		//设置curl参数
  		curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		//判断post方式传输
  		if($type == 'post')
  		{
  			curl_setopt($ch, CURLOPT_POST, 1);
  			curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
  		}
  		//采集
  		$output=curl_exec($ch);

  		//关闭连接
  		curl_close($ch);

  		//转换为数组
        if($res == 'json')
        {
        	 if(curl_error($ch)){
                return curl_error($ch);
            }else{
                return json_decode($output,true);
            }
        }
    }
	
	//获取测试号token
	function  getToken(){
		$ch=curl_init();
		$appid="wx8789203a46051ae1";
		$appSecret="68d9fdc2cd3f6b3e583be54de724b37c";
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appSecret;
		$output=$this->http_curl($url);
		
		return $output['access_token'];
		//echo $output['access_token'];
	}

	//获取微信服务器ip地址
	function getIp()
	{
		$token=$this->getToken();
		$ch=curl_init();
		$url="https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=$token";
		$output=$this->http_curl($url);
		if(curl_errno($ch)){
			var_dump(curl_error($ch));
		}

		echo "<pre>";
		var_dump($output);	

	}

  //获取用户授权code
  public function getBaseInfo()  
    {  
      $appid="wx8789203a46051ae1";
      $redirect_uri=urlencode("http://djl52102.top/wx/Index/getUserOpenId");
      $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";  
      header('location:'.$url);
    } 

  public function getUserOpenId()
  {
      //获取到网页授权的access_token
    $appid="wx8789203a46051ae1";
    $appsecret="68d9fdc2cd3f6b3e583be54de724b37c";
    $code=$_GET['code'];
    $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
     //获取用户openid
    $res=$this->http_curl($url);
    var_dump($res);
  }

  //获取用户信息code
  public function getCode()  
    {  
      $appid="wx8789203a46051ae1";
      $redirect_uri=urlencode("http://djl52102.top/wx/Index/getUserInfo");
      $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";  
      header('location:'.$url);
    }

    //提取用户信息
    public function getUserInfo()
    {
       $appid="wx8789203a46051ae1";
       $appsecret="68d9fdc2cd3f6b3e583be54de724b37c";
       $code=$_GET['code'];
       $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
        //获取用户openid
       $res=$this->http_curl($url);
       //var_dump($res);
       $access_token=$res['access_token'];
       $openid=$res['openid'];

       //获取用户信息
       $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN"; 
       $result=$this->http_curl($url);
       //var_dump($result);
        $this->assign("result",$result);
        $this->display('index');
    }

    //生成临时二维码
     public function getQrCode()
    {
      header('content-type:text/html;charset=utf-8');
      $token=$this->getToken();
      
      $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
     
      $data=array(
      		'expire_seconds'=>604800,
      		'action_name'=>"QR_SCENE",
      		'action_info'=>array('scene'=>array('scene_id'=>2000))
      	);
      
      $data=json_encode($data);
      
      $res=$this->http_curl($url,'post','json',$data);

      $ticket=$res['ticket'];

       //生成二维码
       $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
       echo '临时二维码';
       echo "<image src='".$url."'/>";
    }

    //生成永久二维码
     public function getQrLimitCode()
    {
      header('content-type:text/html;charset=utf-8');	
      $token=$this->getToken();
      
      $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
     
      $data=array(
      		'action_name'=>"QR_LIMIT_SCENE",
      		'action_info'=>array('scene'=>array('scene_id'=>123))
      	);
      $data=json_encode($data);

      $res=$this->http_curl($url,'post','json',$data);

      $ticket=$res['ticket'];

       //生成二维码
       $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
       echo '永久二维码';
       //展示二维码
       echo "<image src='".$url."'/>";
    }

    //群发接口预览
    function sendMsg()
    {
    	header('content-type:text/html;charset=utf-8');
    	//获取token
    	$access_token=$this->getToken();
    	echo "<hr />";
    	$url="https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;
    	//传输数组
    	$data=array('touser'=>'of6qAv9zYCCp_B8POI8LTAvvghWM',
    				'text'=>array('content'=>"公众号测试"),
    				'msgtype'=>'text');
    	
    	$data=json_encode($data);
    	
    	//使用curl模拟发送post数据
    	 $res=$this->http_curl($url,'post','json',$data);
    	   echo "<hr />";
    	  var_dump($res);
    }

    //根据openid转发数据
    function sendMsgAll()
    {
    	//获取token
    	$access_token=$this->getToken();
    	echo "<hr />";
    	$url="https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".$access_token;
    	//传输数组
    	$data=array('touser'=>array('of6qAv-t8lzCoysUUpjHeGzgqw3w','of6qAv_u1hU4ruItSdmhQ0-H3RtU','of6qAvykb73uV2g4iD-W700KRXQ4','of6qAv1qZSZNvcb8VTiTVdj09vx4','of6qAv9CCyEK1M5SH6pji7o07FNs','of6qAv9zYCCp_B8POI8LTAvvghWM'),
    				'text'=>array('content'=>'欢迎来到春泥的周末时光'),
    				'msgtype'=>'text');
    	
    	$data=json_encode($data,JSON_UNESCAPED_UNICODE);
    	
    	//使用curl模拟发送post数据
    	 
    	$res=$this->http_curl($url,'post','json',$data);
    	  
    	  echo "<hr />";

    	  var_dump($res);
    }
    //返回access_token
    function getWxAccessToken()
    {
    	//access_token存在session/cookie中

    	if($_SESSION['access_token'] && $_SESSION['expire_time']>time())
    	{
    		return $_SESSION['access_token'];
			
    	}else
    	{
    		$appid="wx8789203a46051ae1";
		    $appSecret="68d9fdc2cd3f6b3e583be54de724b37c";
		    $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appSecret;
		    
			$res=$this->http_curl($url,'get','json');
		    
			$access_token=$res['access_token'];

		    //将access_token存储到session中
		    $_SESSION['access_token']=$access_token;
		    $_SESSION['expire_time']=time()+7000;

		    return $access_token;
			
			
    	}
    }
    //创建自定义菜单
    public function creatMenu()
    {
    	header('content-type:text/html;charset=utf-8');
		
    	$token=$this->getToken();

    	$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;
		
    	$data=array(
    		'button'=>array(
    			array(
    				'name'=>'今日歌曲',
    				'type'=>'click',
    				'key'=>'MUSIC',
    				),
    			array(
    				'name'=>'博客',
    				'type'=>'view',
					'url'=>'http://www.sina.cn/',
    				),
    			array(
    				'name'=>'知乎',
    				'type'=>'view',
    				'url'=>'http://www.zhihu.com/',
    				),
    			),
    		);

    	$data=json_encode($data,JSON_UNESCAPED_UNICODE);
		
		//var_dump($data);
    	
        $res=$this->http_curl($url,'post','json',$data);

        var_dump($res);
    }

    //发送模板消息
    public function sendTemplate()
    {
    	//获取token
    	$token=$this->getWxAccessToken();
    	$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
    	//模板消息
    	$data=array(
    				'touser'=>'of6qAv9zYCCp_B8POI8LTAvvghWM',
    				'template_id'=>'2Rt3tKzHpfwn0D8lFbp-ebIBfl4tuDg_hOULSOpaXTs',
    				'url'=>'http://www.sina.cn',
    				'data'=>array(
    						'user'=>array(
    								'value'=>'段尽力',
    								'color'=>'blue',
    							),
    						'money'=>array(
    								'value'=>'人民币500元',
    								'color'=>'blue',),
    						'date'=>array(
    								'value'=>'2017-9-19',
    								'color'=>'blue',
    							),		

    					),
    		);
    	$data=json_encode($data,JSON_UNESCAPED_UNICODE);

    	$res=$this->http_curl($url,'post','json',$data);

    	var_dump($res);

    }
      
}
?>