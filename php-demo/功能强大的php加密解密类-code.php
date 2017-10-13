<?php
class Ender{
  private $enkey;//加密解密用的密钥
  //构造参数是密钥
  public function __construct($key=''){
    if(!$key){
      $this->enkey=$key;
    }
  }
  //设置密钥
  public function set_key($key){
    $this->enkey=$key;
  }
  private function keyED($txt,$encrypt_key)
  {
    $encrypt_key = md5($encrypt_key);
    $ctr=0;
    $tmp = "";
    for ($i=0;$i<strlen($txt);$i++)
    {
      if ($ctr==strlen($encrypt_key)) $ctr=0;
      $tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1);
      $ctr++;
    }
    return $tmp;
  }
  //加密字符串
  public function encrypt($txt,$key='')
  {
    if(!$key){
      $key=$this->enkey;
    }
    srand((double)microtime()*1000000);
    $encrypt_key = md5(rand(0,32000));
    $ctr=0;
    $tmp = "";
    for ($i=0;$i<strlen($txt);$i++)
    {
      if ($ctr==strlen($encrypt_key)) $ctr=0;
      $tmp.= substr($encrypt_key,$ctr,1) .
        (substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1));
      $ctr++;
    }
    return base64_encode($this->keyED($tmp,$key));
  }
  //解密字符串
  public function decrypt($txt,$key='')
  {
    $txt=base64_decode($txt);
    if(!$key){
      $key=$this->enkey;
    }
    $txt = $this->keyED($txt,$key);
    $tmp = "";
    for ($i=0;$i<strlen($txt);$i++)
    {
      $md5 = substr($txt,$i,1);
      $i++;
      $tmp.= (substr($txt,$i,1) ^ $md5);
    }
    return $tmp;
  }

}

$key  = "#$%^&";
$obj = new Ender();
echo $obj->encrypt('test123456',$key);
echo '<hr>';
echo $obj->decrypt('VSpaNQ0nBXAEZ141AmUNYw9qUjE=',$key);
echo '<hr>';




