<?php
//header('Content-type: application/json');
//header('Content-type: text/json');
function data_get($url,$post_data,$method) {
    $ch = curl_init();
    //curl初始化
    switch ($method) {
        case 'GET':
            //GET模式
            break;

        case 'POST':
            curl_setopt($ch, CURLOPT_POST, 1);
            //设置curl需要POST请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            //设置curl相关要POST的数据
            break;
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    //设置curl的url地址
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    //以下为转码，防中文乱码
    if (! mb_check_encoding($output, 'utf-8')) {
        $output = mb_convert_encoding($output,'UTF-8',['ASCII','UTF-8','GB2312','GBK']);
    }
    curl_close($ch);

    return $output;
}



/**
 * 解析url中参数信息，返回参数数组
 */
function convertUrlQuery($query)
{
   $queryParts = explode('&', $query);

   $params = array();
   foreach ($queryParts as $param) {
      $item = explode('=', $param);
      $params[$item[0]] = $item[1];
   }

   return $params;
}

function token_get($roadline)
{
	
    //检查是否存在token缓存
    $file_path = "token.txt";
    if(file_exists($file_path)){
    	$token_time = filemtime($file_path);
    	if((strtotime("now") - $token_time) < 50){
			return $token = file_get_contents($file_path);
    	}
    }
    
    $roadline = urlencode($roadline);

    $domain = 'http://zh.84000.com.cn:18803';
    $url ="{$domain}/bimp-base/plat-form!goJSON3.action?callback=".'jQuery11111005512304357686748_'.mt_rand(1000000000000,9999999999999)."&service=baseService&method=doRules5&param=rule%E2%96%B3true%E2%96%BDruleFile%E2%96%B3app.group.LineSimulation%E2%96%BDlineName%E2%96%B3111%E2%96%BD";
    $output = data_get($url,'','GET');
    $output = substr($output,-178);
    $output = substr($output, 0, -1);
    $output = json_decode($output,true);
    $url = convertUrlQuery($output[0]['url']);
    $token  = $url['http://mbst.shdzyb.com:36115/index.htm?aoma-token'];
    $token_save = fopen("token.txt", "w") or die("Unable to open file!");
	fwrite($token_save, $token);
    return $url['http://mbst.shdzyb.com:36115/index.htm?aoma-token'];
}

function info($roadline,$method)
{
   $ch = curl_init(); 
   $token = token_get($roadline);
   
   $domain = "203.156.246.118:36115";
   switch ($method){
        case 'departscreen':
           $url = "http://{$domain}/Ajax/Handler.ashx?Method=departscreen&startstation=all&aoma-token={$token}&roadline=$roadline";
           break;
        case  'gpsdata':
            $url = "http://{$domain}/interface/Handler.ashx?action=getgpsdata&aoma-token={$token}&roadline=$roadline";
            break;
        case 'station';
            $url = "http://{$domain}/Ajax/Handler.ashx?Method=station&aoma-token={$token}&roadline=$roadline";
            break;
   }
   if($method == 'station')
   {
        $file_path = "line/{$roadline}.txt";
        if(file_exists($file_path)){
            echo file_get_contents($file_path);
            return 0;
        }
   }
    $headers = array();
    $headers[] = 'Host: mbst.shdzyb.com:36115';
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 9; MI 8 SE Build/PKQ1.181121.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.108 Mobile Safari/537.36 x5app/1.1.2';
    
    $headers[] = 'Accept: text/plain, */*; q=0.01';
    
    $headers[] = 'Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2';
    //$headers[] = 'Accept-Encoding: gzip, deflate';
    $headers[] = 'X-Requested-With: XMLHttpRequest';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
    $headers[] = 'Connection: keep-alive';
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_URL, $url); 
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
   $output = curl_exec($ch); 
   //curl完成 处理数据并输出
   
    if(! mb_check_encoding($output, 'utf-8')) {
        $output = mb_convert_encoding($output,'UTF-8',['ASCII','UTF-8','GB2312','GBK']);
    }  //防止中文乱码，转换编码
    if($method == 'station')
    {
        $file_path = "line/{$roadline}.txt";
        file_put_contents($file_path,$output);
    }
    
        echo $output;  

    curl_close($ch);

}

//date_default_timezone_set(‘Asia/Shanghai');
ini_set('date.timezone','Asia/Shanghai');
    $roadline = $_GET['roadline'];
    $method   = $_GET['Method'];

logResult($ip,$roadline,$method);

    if ($roadline == '1250') $roadline = '1250%u8DEF%uFF0871%u8DEF%u652F%u7EBF1%uFF09';
    if ($roadline == '1251') $roadline = '1251%u8DEF%uFF0871%u8DEF%u652F%u7EBF2%uFF09';
    switch ($method) {
        case 'departscreen':
            info($roadline,'departscreen');
            break;
        
        case 'gpsdata':
            info($roadline,'gpsdata');
            break;
        case 'station':
            info($roadline,'station');
            break;
    }