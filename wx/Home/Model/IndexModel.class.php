<?php
namespace Home\Model;
class IndexModel{
	//图文回复
	public function responseNews($postObj,$arr)
{
	                $toUser=$postObj->FromUserName;
              		$fromUser=$postObj->ToUserName;
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

}
	//文本回复
	public function responseText($postObj,$content)
	{
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

        $info=sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
        echo $info;               
	}
	//关注事件回复
	Public function responseSubscribe($postObj,$content)
	{
		$toUser=$postObj->FromUserName;
                       $fromUser=$postObj->ToUserName;
                       $time=time();
                       $msgType='text';
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
?>