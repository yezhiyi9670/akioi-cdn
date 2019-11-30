<?php

header("Content-Type: text/plain");

$src_file = './SakuraFonts/source.css';
$dst_file = './SakuraFonts/index.css';
$store_dir = './SakuraFonts/static/';
$store_prefix = 'static/';

global $log;
$log = './log.txt';

file_put_contents($log,'');

function printlog($str) {
	global $log;
	file_put_contents($log,$str,FILE_APPEND);
}

set_time_limit(3600);

$mat = [];

$str = file_get_contents($src_file);

printlog("Google Fonts 本地克隆器 v0.1\n");
printlog("-------------------------------\n");

printlog('加载数据...'."\n");

preg_match_all('/url\(https\:\/\/fonts\.gstatic\.com\/s\/(\w+)\/(\w+)\/(.+)\.(\w+)\)/',$str,$mat);
$str = preg_replace('/url\(https\:\/\/fonts\.gstatic\.com\/s\/(\w+)\/(\w+)\/(.+)\.(\w+)\)/','url(./'.$store_prefix.'$1.$2.$3.$4)',$str);

printlog('数据加载完成。共有 '.count($mat[0]).' 个文件。'."\n");

// var_dump($mat);

printlog('创建存放目录...'."\n");

if(!file_exists($store_dir)) {
	mkdir($store_dir);
}

printlog('导出数据...'."\n");

file_put_contents($dst_file,$str);

printlog("开始下载字体文件。\n");

foreach($mat[0] as $i => $item) {
	flush();
	$filename = $store_dir.$mat[1][$i].'.'.$mat[2][$i].'.'.$mat[3][$i].'.'.$mat[4][$i];
	$url = 'https://wmsdf.cf/ext/google-fonts-proxy/static/?s/'.$mat[1][$i].'/'.$mat[2][$i].'/'.$mat[3][$i].'.'.$mat[4][$i];
	printlog($i." ".$url." ... ");
	
	if(file_exists($filename)) {
		printlog('忽略。');
	} else {
		$data = file_get_contents($url);
		if(strlen($data)) {
			printlog('成功。');

			file_put_contents($filename,$data);
		}
		else printlog('错误。');

		if(connection_aborted()) {
			printlog('操作被打断，终止。');
		}
	}

	printlog("\n");
	flush();
}

printlog("完成。\n");
