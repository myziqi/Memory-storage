<?php

//alert提示 
function alert($msg){
	echo "<script>alert('$msg');</script>";
}

//把一些预定义的字符转换为 HTML 实体 
function d_htmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = d_htmlspecialchars($val);
		}
	} else {
		$string = str_replace('&', '&', $string);
		$string = str_replace('"', '"', $string);
		$string = str_replace(''', ''', $string);
		$string = str_replace('<', '<', $string);
		$string = str_replace('>', '>', $string);
		$string = preg_replace('/&(#\d;)/', '&\1', $string);
	}
	return $string;
}

//在预定义字符前加上反斜杠，包括 单引号、双引号、反斜杠、NULL，以保护数据库安全 
function d_addslashes($string, $force = 0) {
	if(!$GLOBALS['magic_quotes_gpc'] || $force) {
	if(is_array($string)) {
		foreach($string as $key => $val) $string[$key] = d_addslashes($val, $force);
	}
		else $string = addslashes($string);
	}
	return $string;
}

//生成随机字符串，包含大写、小写字母、数字 
function randstr($length) {
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	mt_srand((double) microtime() * 1000000);
	for($i = 0;$i < $length;$i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

//转换时间戳为常用的日期格式 
function trans_time($timestamp){
	if($timestamp < 1) echo '无效的Unix时间戳';
	else return date("Y-m-d H:i:s", $timestamp);
}

//获取IP 
function get_ip() {
	if ($_SERVER["HTTP_X_FORWARDED_FOR"])
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if ($_SERVER["HTTP_CLIENT_IP"])
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	else if ($_SERVER["REMOTE_ADDR"])
		$ip = $_SERVER["REMOTE_ADDR"];
	else if (getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else
		$ip = "Unknown";
	return $ip;
}

//计算时间差：默认返回类型为“分钟” 
//$old_time 只能是时间戳，$return_type 为 h 是小时，为 s 是秒 
function timelag($old_time, $return_type = 'm'){
	if($old_time < 1){
		echo '无效的Unix时间戳';
	}else{
		switch($return_type){
		case 'h':
			$type = 3600;
			break;
		case 'm':
			$type = 60;
			break;
		case 's':
			$type = 1;
			break;
		case '':
			$type = 60;
			break;
		}
		$dif = round( (time()-$old_time)/$type );
		return $dif;
	}
}

//获取当前页面的URL地址 
function url_this(){
	$url = "http://".$_SERVER ["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$return_url = "<a href='$url'>$url</a>";
	return $return_url;
}

//跳转函数 
function url_redirect($url, $delay = ''){
	if($delay == ''){
		echo "<script>window.location.href='$url'</script>";
	}else{
		echo "<meta http-equiv='refresh' content='$delay;URL=$url' />";
	}
}

//1、PHP加密解密PHP加密和解密函数可以用来加密一些有用的字符串存放在数据库里，并且通过可逆解密字符串，该函数使用了base64和MD5加密和解密。
function encryptDecrypt($key, $string, $decrypt) {
    if ($decrypt) {
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "12");
        return $decrypted;
    } else {
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
        return $encrypted;
    }
}
//使用方法如下：以下是将字符串“Helloweba欢迎您”分别加密和解密 
//加密： 
echo encryptDecrypt('password', 'Helloweba欢迎您', 0);
//解密： 
echo encryptDecrypt('password', 'z0JAx4qMwcF+db5TNbp/xwdUM84snRsXvvpXuaCa4Bk=', 1);

//2、PHP生成随机字符串当我们需要生成一个随机名字，临时密码等字符串时可以用到下面的函数：
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
//使用方法如下：
$filename = '我的文档.doc';
echo getExtension($filename);

//4、PHP获取文件大小并格式化以下使用的函数可以获取文件的大小，并且转换成便于阅读的KB，MB等格式。
function formatSize($size) {
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) {
        return('n/a');
    } else {
        return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
    }
}
//使用方法如下：
$thefile = filesize('test_file.mp3');
echo formatSize($thefile);

//5、PHP替换标签字符有时我们需要将字符串、模板标签替换成指定的内容，可以用到下面的函数：
function stringParser($string, $replacer) {
    $result = str_replace(array_keys($replacer), array_values($replacer), $string);
    return $result;
}

//使用方法如下：
$string = 'The {b}anchor text{/b} is the {b}actual word{/b} or words used {br}to describe the link {br}itself';
$replace_array = array('{b}' => '<b>', '{/b}' => '</b>', '{br}' => '<br />');
echo stringParser($string, $replace_array);

//6、PHP列出目录下的文件名如果你想列出目录下的所有文件，使用以下代码即可：
function listDirFiles($DirPath) {
    if ($dir = opendir($DirPath)) {
        while (($file = readdir($dir)) !== false) {
            if (!is_dir($DirPath . $file)) {
                echo "filename: $file<br />";
            }
        }
    }
}
//使用方法如下：
listDirFiles('home/some_folder/');