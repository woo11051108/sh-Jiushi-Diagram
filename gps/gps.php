<?php
header('content-type:application/json');
/* 
   模块A：对上游API的数据获取
   实现：Curl
   相关参数：
   1.URL为请求的API接口地址
   2.post_data为POST的数组存放
*/

function data_get($url,$post_data,$method)
{
    $ch = curl_init();
    //curl初始化
    switch ($method){
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
    if(! mb_check_encoding($output, 'utf-8')) {
        $output = mb_convert_encoding($output,'UTF-8',['ASCII','UTF-8','GB2312','GBK']);
    }
    curl_close($ch);
    
    return $output;
}

function gps_data_get($roadline)
{
    $roadline = urlencode($roadline);
    $url = "http://103.56.60.48:51488/Handler/AjaxHandler.ashx?action=GetRangeVehicle&blc=10&maxlat=32.63475&maxlon=123.44238&minlat=29.71191&minlon=117.81738&line=$roadline&vehicle=&flag=0";
    $data = json_decode(data_get($url,'','GET'),true);
    $data = json_decode($data['ResultData']['data'],true);
    $i = 0; $res = array();
    foreach($data as $key=>$value)
    {
        $latlon = explode(',',$value['Strlatlon']);
        $res[$i]['lon'] = (float)$latlon[0];
        $res[$i]['lat'] = (float)$latlon[1];
        $res[$i]['zbh'] = $value['VehicleNo'];
        $res[$i]['vid'] = $value['VehicleId'];
        $res[$i]['upDown'] = $value['ToDir'];
        $i++;
    }
    
    return json_encode($res);
}

$roadline = $_GET['roadline'];
if(empty($roadline) || $roadline == 'null') exit();
$roadline=  strtr("$roadline","%","\\");
$rzw = '{"zw":'.'"'.$roadline.'"'."}";
$rzw = json_decode($rzw);
$roadline = $rzw -> zw ; 


$gps = gps_data_get($roadline);
print_r($gps);