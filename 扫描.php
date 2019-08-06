<?php
error_reporting(E_ERROR);
ini_set('max_execution_time',80000);
ini_set('memory_limit','512M');
header("content-Type: text/html; charset=gb2312");
?>
<html>
<head>
<title>PHP沙琪玛 v2.0</title>
<style>
body {
	font-size:12px;
	font-family: "Courier New",verdana,arial,sans-serif;
}
table.codeview {
	font-family: "Courier New",verdana,arial,sans-serif;
	font-size:12px;
}
table.gridtable {
	font-family: "Courier New",verdana,arial,sans-serif;
	font-size:12px;
	color:#333333;
	border-width: 1px;
	border-color: #666666;
	border-collapse: collapse;
}
table.gridtable th {
	border-width: 1px;
	padding: 4px;
	border-style: solid;
	border-color: #666666;
}
table.gridtable td {
	border-width: 1px;
	padding: 4px;
	white-space:nowrap;
	border-style: solid;
	border-color: #666666;
}
</style>
<script type="text/javascript">
function gradeChange(value){
	if(value=='scode'){
		document.getElementById("tcode").disabled=false;
	} else {
		document.getElementById("tcode").value="";
		document.getElementById("tcode").disabled=true;
	}
}
</script>
</head>
<?php

$danger='array_map|eval|eval_r|assert|passthru|exec|system|shell_exec|popen|proc_open|socket_listen|socket_connect|stream_socket_server|base64_decode|base64_encode|str_rot13|gzinflate|gzuncompress|curl_init|copy|chmod|move_uploaded_file|file_get_contents|file_put_contents|fopen|fputs|fwrite|preg_replace|create_function|call_user_func';

$matches = array(
'/function\_exists\s*\(\s*[\'|\"](popen|exec|proc\_open|system|passthru)+[\'|\"]\s*\)/i',
'/(exec|shell\_exec|system|passthru)+\s*\(\s*\$\_(\w+)\[(.*)\]\s*\)/i',
'/((udp|tcp)\:\/\/(.*)\;)+/i',
'/preg\_replace\s*\((.*)\/e(.*)\,\s*\$\_(.*)\,(.*)\)/i',
'/preg\_replace\s*\((.*)\(base64\_decode\(\$/i',
'/(eval|assert|include|require|include\_once|require\_once)+\s*\(\s*(base64\_decode|str\_rot13|gz(\w+)|file\_(\w+)\_contents|(.*)php\:\/\/input)+/i',
'/(eval|assert|include|require|include\_once|require\_once|array\_map|array\_walk)+\s*\(\s*\$\_(GET|POST|REQUEST|COOKIE|SERVER|SESSION)+\[(.*)\]\s*\)/i',
'/eval\s*\(\s*\(\s*\$\$(\w+)/i',
'/\W(include|require|include\_once|require\_once)+\s*\/\*(.|\n)*?\*\/\s*\(?\s*\"?\$[_|\[|\]|\"\'\w]+\"?\s*\)?/i',
'/\W(include|require|include\_once|require\_once)+\s*\(?\s*[\'|\"](.+?)\.(jpg|gif|ico|bmp|png|txt|zip|rar|htm|html|css|js|db|dat)+[\'|\"]\s*\)?/i',
'/\$\_(\w+)(.*)(eval|assert|include|require|include\_once|require\_once)+\s*\(\s*\$(\w+)\s*\)/i',
'/\(\s*\$\_FILES\[(.*)\]\[(.*)\]\s*\,\s*\$\_(GET|POST|REQUEST|FILES)+\[(.*)\]\[(.*)\]\s*\)/i',
'/(fopen|fwrite|fputs|file\_put\_contents)+\s*\((.*)\$\_(GET|POST|REQUEST|COOKIE|SERVER)+\[(.*)\](.*)\)/i',
'/echo\s*curl\_exec\s*\(\s*\$(\w+)\s*\)/i',
'/new com\s*\(\s*[\'|\"]shell(.*)[\'|\"]\s*\)/i',
'/\$(.*)\s*\((.*)\/e(.*)\,\s*\$\_(.*)\,(.*)\)/i',
'/\$\_\=(.*)\$\_/i',
'/\$\_(GET|POST|REQUEST|COOKIE|SERVER)+\[(.*)\]\(\s*\$(.*)\)/i',
'/\$(\w|\[|\]|\'|"|\$)+\s*\(\s*\$\_(GET|POST|REQUEST|COOKIE|SERVER)+\[(.*)\]\s*\)/i',
'/\$(\w+)\s*\(\s*\$\{(.*)\}/i',
'/\$(\w+)\s*\(\s*chr\(\d+\)/i',
'/chr\(\d+\)\.chr\(\d+\)/',
'/[\'|\"]\/\[\w+]\/e[\'|\"]/',
'/(\$_GET(\[)[^]|.]+]|\$_POST(\[)[^]|.]+])[ \r\n\t]{0,}\(/',
'/\$\w+[ \t]*\((\$_GET(\[)[^]|.]+]|\$_POST(\[)[^]|.]+])\)/',
'/\$\w+[ \t]*=[ \t]*str_replace\(\"(\w+)\",\"\",\"\1*s\1*t\1*r\1*_\1*r\1*e\1*p\1*l\1*a\1*c\1*e\"\);/',
'/\$\w+( |\t|\r|\n)*=( |\t|\r|\n)*([\"\']\w+[\"\']( |\t|\r|\n)*\.( |\t|\r|\n)*){1,}[\"\']\w+[\"\']( |\t|\r|\n)*;/',
'/PHP神盾/i',
'/array\_map\s*\(\s*\".*?\"\s*,\s*\(array\)\$\_(GET|POST|REQUEST)+\[[\'\"]?.+?[\'\"]?\]\s*\)/',
'/(echo|print|print\_r|die|=)+\s*(\()?`\$?.+`(\))?/i',
'/\$\{\'\S+\'\.\$\S+\}\[\'\S+\'\]\(\$\{\'\S+\'\.\$\S+\}\[\'\S+\'\]\)/'
);

$table_head = <<<END
<table class="gridtable">
<tr>
<td>文件路径</td>
<td>操作</td>
<td>创建时间</td>
<td>修改时间</td>
</tr>
END;

$table_head_2 = <<<END
<table class="gridtable">
<tr>
<td>文件路径</td>
<td>操作</td>
<td>特征代码</td>
<td>创建时间</td>
<td>修改时间</td>
</tr>
END;

$table_head_3 = <<<END
<table class="gridtable">
<tr>
<td>网站目录列表</td>
<td>过滤后缀</td>
<td>过滤时间</td>
<td>扫描类型</td>
<td>标记</td>
<td>操作</td>
</tr>
END;

$fileCount = 0;
$aSkippath = array();

function auto_read($file, $charset='GBK') {
	$list = array('GBK', 'UTF-8', 'ISO-8859-1');
	$str = file_get_contents($file);
	foreach ($list as $item) {
		$tmp = mb_convert_encoding($str, $item, $item);
		if (md5($tmp) == md5($str)) {
			return mb_convert_encoding($str, $charset, $item);
		}
	}
	return "";
}

function strdir($str) {
	return str_replace(array('\\','//','//'),array('/','/','/'),chop($str));
}

function toHtmlChars($sHtml)
{
    static $maEntities =
        array ('&' => '&amp;', '<' => '&lt;', '>' => '&gt;', '\'' => '&apos;', '"' => '&quot;', ' '=>'&nbsp;', "\r\n" => '<br>', "\n" => '<br>', "\t" => '&nbsp;&nbsp;&nbsp;&nbsp;');
    return strtr($sHtml, $maEntities);
}

function showMsg($msg, $act='') {
	if($act=='') $sact = '';
	if($act=='close') $sact = 'window.close();';
	if($act=='back') $sact = 'history.back(-1);';
	echo '<script language="javascript">alert("'.$msg.'");'.$sact.'</script>';
}

function scanshell($dir,$exs,$stime='',$listdir=false) {
	global $aSkippath,$fileCount;
	if(($handle = @opendir($dir)) == NULL) return false;
	$rootPath = strdir($_SERVER['DOCUMENT_ROOT']);
	while(false !== ($name = readdir($handle))) {
		if($name == '.' || $name == '..') continue;
		$path = $dir.$name;
		//过滤路径
        if(in_array($path.'/', $aSkippath)) continue;
		if(is_dir($path)) {
            if($listdir) {
                echo($path.'<br>');
                flush(); ob_flush();
			    if(is_readable($path)) scanshell($path.'/',$exs,$stime,true);
                continue;
            } else {
                if(is_readable($path)) scanshell($path.'/',$exs,$stime);
            }
		} else {
            if($listdir) continue;
			if(!preg_match($exs,$name)) continue;
			$createtime = date("Y-m-d H:i:s", filectime($path));
			$modtime = date("Y-m-d H:i:s", filemtime($path));
			if($stime!='') {
				if(!preg_match($stime,$createtime) && !preg_match($stime,$modtime)) continue;
			}
            $fileCount++;
			$fUrl = substr($path, strlen($rootPath), strlen($path)-strlen($rootPath));
			echo "<tr onmouseover=\"this.style.background='#87CEEB'\" onmouseout=\"this.style.background=''\"><td>{$path}</td><td><a href=\"?fp={$path}#1\" target=\"_blank\">代码</a>|<a href=\"{$fUrl}\" target=\"_blank\">访问</a></td><td>{$createtime}</td><td>{$modtime}</td></tr>\n";
			flush(); ob_flush();
		}
	}
	closedir($handle);
	return true;
}

function scanshell_full($dir,$exs,$matches,$stime='') {
	global $aSkippath;
	if(($handle = @opendir($dir)) == NULL) return false;
	while(false !== ($name = readdir($handle))) {
		if($name == '.' || $name == '..') continue;
		$path = $dir.$name;
		//过滤路径
		if(in_array($path.'/', $aSkippath)) continue;
		//过滤时间
		if(is_file($path)) {
			if($stime!='') {
				if(!preg_match($stime,$createtime) && !preg_match($stime,$modtime)) continue;
			}
			//扫描文件
			ScanFiles($name, $path, $exs, $matches);
		} else {
			if(is_readable($path)) scanshell_full($path.'/',$exs,$matches,$stime);
		}
	}
	closedir($handle);
	return true;
}

function ScanFiles($name, $path, $exs, $matches) {
	global $fileCount;
	$rootPath = strdir($_SERVER['DOCUMENT_ROOT']);
	$createtime = date("Y-m-d H:i:s", filectime($path));
	$modtime = date("Y-m-d H:i:s", filemtime($path));
	if(strpos($name,';') > -1 || strpos($name,'%00') > -1 || strpos($name,'/') > -1) {
		$fileCount++;
		$fUrl = substr($path, strlen($rootPath), strlen($path)-strlen($rootPath));
		echo "<tr onmouseover=\"this.style.background='#87CEEB'\" onmouseout=\"this.style.background=''\"><td>{$path}</td><td><a href=\"?fp={$path}#1\" target=\"_blank\">代码</a>|<a href=\"{$fUrl}\" target=\"_blank\">访问</a></td><td>解析漏洞木马</td><td>{$createtime}</td><td>{$modtime}</td></tr>\n";
		flush(); ob_flush();
	} else {
		if(!preg_match($exs,$name)) return;
		if(filesize($path) > 10000000) return;
		$fp = fopen($path,'r');
		$code = fread($fp,filesize($path));
		fclose($fp);
		if(empty($code)) return;
		foreach($matches as $matche) {
			$array = array();
			preg_match($matche,$code,$array);
			if(!$array) continue;
			if(strpos($array[0],"\x24\x74\x68\x69\x73\x2d\x3e")) continue;
			$fileCount++;
			$fUrl = substr($path, strlen($rootPath), strlen($path)-strlen($rootPath));
			echo "<tr onmouseover=\"this.style.background='#87CEEB'\" onmouseout=\"this.style.background=''\"><td>{$path}</td><td><a href=\"?fp={$path}#1\" target=\"_blank\">代码</a>|<a href=\"{$fUrl}\" target=\"_blank\">访问</a></td><td><input type=text style=\"width:200px;\" value=\"".htmlspecialchars($array[0])."\" onfocus=\"this.select();\" title=\"".htmlspecialchars($array[0])."\"></td><td>{$createtime}</td><td>{$modtime}</td></tr>\n";
			flush(); ob_flush(); break;
		}
		unset($code,$array);
	}
}

function listDIRs($dir,$exs,$matches) {
    global $table_head_2,$table_head_3;
	$fileItems = array();
    $scanExts = '<input type="text" name="exs" value=".php" style="width:80px;">';
    $scanTime = '<input type="text" name="ftime" value="" style="width:140px;">';
    $scanType = '<select name="stype" id="stype" onChange="gradeChange(this[selectedIndex].value)"><option value ="sshell">全面扫描</option><option value ="squick">快速扫描</option><option value ="stime">时间扫描</option><option value ="sdir">目录扫描</option><option value ="sonebyone">分别扫描</option></select>';
    echo("\r\n".$table_head_3);
    $pathHandler = opendir($dir);
	while(($file = readdir($pathHandler)) !== false) {
		$fpath = $dir . DIRECTORY_SEPARATOR . $file;
		if($file == '.' || $file == '..') {
			continue;
		} else if(is_dir($fpath)){
		   $filename=strdir($dir . DIRECTORY_SEPARATOR . $file);
           echo "<tr><form method=\"POST\" action=\"?act=scan&path={$filename}\" target=\"_blank\"><td>{$filename}</td><td>{$scanExts}</td><td>{$scanTime}</td><td>{$scanType}</td><td><a href=\"?act=scan&path={$filename}\" target=\"_blank\">默认</a></td><td><input type=\"submit\" value=\"扫描此目录\"></td></form></tr>";
		} else if(is_file($fpath)){
			$fileItems[$file] = $fpath;
		}
	}
	echo "</table><br>";
	echo("\n".$table_head_2);
	foreach($fileItems as $f=>$p){
		ScanFiles($f, strdir($p),$exs,$matches);
	}
	echo "</table>";
    return false;
}

function doScan($stype, $dir, $exs='.php', $fTime='', $skippath='') {
    global $matches,$danger,$aSkippath,$table_head,$table_head_2,$fileCount;
    $start_time=microtime(true);
	if($skippath!='') {
		$skippath = trim($skippath);
		if(strstr($skippath, '|')!==false) {
			$atmp = explode('|', $skippath);
			foreach($atmp as $p) {
				$aSkippath[] = strdir($p.'/');
			}
		} else {
			$aSkippath[] = strdir($skippath.'/');
		}
	}
	$dir = strdir($dir.'/');
	$exs = '/('.str_replace('.','\\.',$exs).')/i';
	$fTime = trim($fTime);
	if(!empty($fTime)) $fTime = '/'.$fTime.'/';
	$flag = false;
	if($stype=='stime') {
		echo("\n".$table_head);
		$flag = scanshell($dir,$exs,$fTime);
	} elseif($stype=='squick') {
		echo("\n".$table_head_2);
		$flag = scanshell_full($dir,$exs,$matches,$fTime);
	} elseif($stype=='sshell') {
		$matches[]="/($danger)[ \r\n\t]{0,}\(/";
		echo("\n".$table_head_2);
		$flag = scanshell_full($dir,$exs,$matches,$fTime);
	} elseif($stype=='sdir') {
        $flag = scanshell($dir,$exs,$fTime,true);
    } elseif($stype=='scode'){
		$scode = array();
		$scode[] = '/'.str_replace('/','\/',preg_quote(trim($_POST['tcode']))).'/';
		echo("\n".$table_head_2);
        $flag = scanshell_full($dir,$exs,$scode,$fTime);
    } elseif($stype=='sonebyone'){
        $flag = listDIRs($dir,$exs,$matches);
    }
    if($stype!='sdir') echo '</table>';
    echo '扫描完成！';
	$end_time=microtime(true);
	$total=$end_time-$start_time;
	if($stype!='sdir' && $stype!='sonebyone') {
        echo("<br>共发现可疑文件：{$fileCount} 个，耗时: ".round(round(($total) * 1000, 1)/1000, 1).' 秒');
    }
    showMsg("扫描完成！");
}

/*--------------------------------------------------------------------------------------------------------------------------------*/

if($_GET['fp']!='') {
	$fp = trim($_GET['fp']);
	if(is_file($fp)) {
		$file_content = auto_read($fp);
        $i = 1;
        foreach($matches as $matche) {
            $array = array();
            preg_match($matche,$file_content,$array);
            if(!$array) continue;
            $hlcode = $array[0];
            $file_content = str_replace($hlcode, '{#@SPAN1@#}'.$hlcode.'{#@SSPAN@#}', $file_content);
            $i++;
        }
        $file_content = preg_replace("/($danger)(\n| |\t|\r){0,}\(/", '{#@SPAN2@#}${1}({#@SSPAN@#}', $file_content);
        $file_content = toHtmlChars($file_content);
        $file_content = str_replace('{#@SPAN1@#}','<span style="background-color:red;font-size:20px;font-weight:bold;" id="1">',$file_content);
        $file_content = str_replace('{#@SPAN2@#}','<span style="background-color:yellow;font-size:20px;font-weight:bold;" id="1">',$file_content);
        $file_content = str_replace('{#@SSPAN@#}','</span>',$file_content);
		echo('<table class="codeview" border="1" cellpadding="0" cellspacing="0" style="table-layout:fixed;word-break:break-all;width:100%">');
        echo('<tr><td align="center"><a href="?act=del&fn='.$fp.'" onClick="return confirm(\'确认删除?\')"><b>删除</b></a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:window.close();"><b>关闭</b></a></td></tr>');
        echo('<tr><td>'.$file_content.'</td></tr>');
        echo('<tr><td align="center"><a href="?act=del&fn='.$fp.'" onClick="return confirm(\'确认删除?\')"><b>删除</b></a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:window.close();"><b>关闭</b></a></td></tr>');
		echo('</table>');
	} else {
		showMsg("文件不存在。", 'close');
	}
	exit;
}

if($_GET['act']=='del') {
	$msg='';
	$fn=$_GET['fn'];
	if(is_file($fn)){
        if(unlink($fn)) {
            showMsg("已成功删除！", 'close');
        } else {
            showMsg("删除失败，可能是没有权限。", 'back');
        }
    } else {
        showMsg("文件不存在！", 'close');
    }
	exit;
}

if($_GET['act']=='scan' && is_dir($_GET['path'])) {
    if(!file_exists(trim($_GET['path']))){showMsg("扫描路径不存在！");exit;}
    if(isset($_POST['stype'])) {
        doScan(trim($_POST['stype']), trim($_GET['path']), trim($_POST['exs']), trim($_POST['ftime']));
    } else {
        doScan('sshell', trim($_GET['path']));
    }
    exit;
}

if($_GET['act']=='delSelf') {
    if(unlink(basename($_SERVER['SCRIPT_NAME']))){
        echo '已删除！';
    } else {
        echo '删除失败！';
    }
    exit;
}

/*--------------------------------------------------------------------------------------------------------------------------------*/

echo '<form method="POST">';
echo '<table class="gridtable"><tr><td>';
echo '扫描路径:<input type="text" name="dir" value="'.($_POST['dir'] ? strdir($_POST['dir'].'/') : strdir($_SERVER['DOCUMENT_ROOT'].'/')).'" style="width:400px;">&nbsp;';
echo '后缀:<input type="text" name="exs" value="'.($_POST['exs'] ? $_POST['exs'] : '.php').'" style="width:80px;">&nbsp;';
echo '时间:<input type="text" name="ftime" value="'.($_POST['ftime']).'" style="width:140px;">&nbsp;';
$sType = $_POST['stype'];
$selectType = '扫描类型:<select name="stype" id="stype" onChange="gradeChange(this[selectedIndex].value)"><option value ="sshell" '.($sType=='sshell'?'selected':'').'>全面扫描</option><option value ="squick" '.($sType=='squick'?'selected':'').'>快速扫描</option><option value ="stime" '.($sType=='stime'?'selected':'').'>时间扫描</option><option value ="sdir" '.($sType=='sdir'?'selected':'').'>目录扫描</option><option value ="sonebyone" '.($sType=='sonebyone'?'selected':'').'>分别扫描</option><option value ="scode" '.($sType=='scode'?'selected':'').'>特征扫描</option></select>&nbsp;';
echo $selectType;
echo '特征码:<input type="text" name="tcode" id="tcode" value="'.($_POST['tcode'] ? $_POST['tcode'] : '').'" style="width:235px;" '.($sType=='scode'?'':'disabled').'>&nbsp;';
echo '<a href="?act=delSelf" onClick="return confirm(\'确认要删除沙琪玛吗?\')">（删除本程序）</a></td></tr>';
echo '<tr><td>忽略目录:<input type="text" name="skippath" style="width:1120px;" value="'.strdir($_POST['skippath']).'">&nbsp;';
echo '<input type="submit" style="width:80px;" value="扫描"></td></tr>';
echo '</form></table><br>';

if($_SERVER['REQUEST_METHOD']=='POST') {
    $vScanType = trim($_POST['stype']);
    $vScanDIR = trim($_POST['dir']);
    $vScanExt = trim($_POST['exs']);
    $vScanTime = trim($_POST['ftime']);
    $vSkipDIR = trim($_POST['skippath']);
    if(!file_exists($vScanDIR)){showMsg("扫描路径不存在！");exit;}
    if(file_exists($vScanDIR)) {
        doScan($vScanType, $vScanDIR, $vScanExt, $vScanTime, $vSkipDIR);
    }
}
?>
</html>