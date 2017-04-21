<?php 

/**
 * PHP 非递归实现查询该目录下所有文件
 * @param unknown $dir
 * @return multitype:|multitype:string
 */
function scanfiles($dir) {
 if (! is_dir ( $dir ))
 return array ();
  
 // 兼容各操作系统
 $dir = rtrim ( str_replace ( '\\', '/', $dir ), '/' ) . '/';
  
 // 栈，默认值为传入的目录
 $dirs = array ( $dir );
  
 // 放置所有文件的容器
 $rt = array ();
  
 do {
 // 弹栈
 $dir = array_pop ( $dirs );
  
 // 扫描该目录
 $tmp = scandir ( $dir );
  
 foreach ( $tmp as $f ) {
  // 过滤. ..
  if ($f == '.' || $f == '..')
  continue;
   
  // 组合当前绝对路径
  $path = $dir . $f;
   
   
  // 如果是目录，压栈。
  if (is_dir ( $path )) {
  array_push ( $dirs, $path . '/' );
  } else if (is_file ( $path )) { // 如果是文件，放入容器中
  $rt [] = $path;
  }
 }
  
 } while ( $dirs ); // 直到栈中没有目录
  
 return $rt;
}

?>
附另一篇：不用递归遍历目录下的文件
如果要遍历某个目录下的所有文件（包括子目录），最首先想到的思路就是用递归：先处理当前目录，再处理当前目录下的子目录。不用递归可不可以呢？以前学数据结构的时候看到过，递归其实是利用堆栈来实现的，递归的特点就是不断的调用自身，最后一次的调用是最先执行完的，倒数第二次调用是其次执行完的，依次类推，最初的调用是最后执行完的。如果理解了递归的原理，其实就可以把所有用递归的实现转化为非递归的实现。

用非递归方式遍历某个目录下的所有文件，思路主要分三步：
1. 创建一个数组，将要遍历的这个目录放入；（其实就是创建了一个栈）
2. 循环处理这个数组，循环结束的条件是数组为空；
3. 每次循环，处理数组中的一个元素，并将元素删除，如果这个元素是目录，则将目录下所有的子元素加入数组；
按照这种思路写出的代码如下：
<?php 

/**
 * 遍历某个目录下的所有文件
 * @param string $dir
 */
function scanAll($dir)
{
  $list = array();
  $list[] = $dir;
 
  while (count($list) > 0)
  {
    //弹出数组最后一个元素
    $file = array_pop($list);
 
    //处理当前文件
    echo $file."\r\n";
 
    //如果是目录
    if (is_dir($file))
    {
      $children = scandir($file);
      foreach ($children as $child)
      {
        if ($child !== '.' && $child !== '..')
        {
          $list[] = $file.'/'.$child;
        }
      }
    }
  }
}

?>

这里我并没有认为递归有多大的缺点，事实上很多情况下，用递归来设计还是非常简洁可读的，至于效率问题，除非在递归深度特别大的时候，才会有影响。
以下是用递归的实现，作为对比：
<?php 

/**
 * 遍历某个目录下的所有文件(递归实现)
 * @param string $dir
 */
function scanAll2($dir)
{
  echo $dir."\r\n";
 
  if (is_dir($dir))
  {
    $children = scandir($dir);
    foreach ($children as $child)
    {
      if ($child !== '.' && $child !== '..')
      {
        scanAll2($dir.'/'.$child);
      }
    }
  }
}

?>
运行发现，两个函数的结果略有不同，主要表现在打印的顺序上。函数一运行结果的顺序是倒着的，是因为压栈的顺序正好和scandir出来的顺序相反了，可以将第21行改一下：
$children = array_reverse(scandir($file));