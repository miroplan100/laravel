<?php 

namespace App\Lib\SypexGeo;

use URL;

/***************************************************************************\
| Sypex Geo                  version 2.2.3                                  |
| (c)2006-2014 zapimir       zapimir@zapimir.net       http://sypex.net/    |
| (c)2006-2014 BINOVATOR     info@sypex.net                                 |
|---------------------------------------------------------------------------|
|     created: 2006.10.17 18:33              modified: 2014.06.20 18:57     |
|---------------------------------------------------------------------------|
| Sypex Geo is released under the terms of the BSD license                  |
|   http://sypex.net/bsd_license.txt                                        |
\***************************************************************************/

define ('SXGEO_FILE', 0);
define ('SXGEO_MEMORY', 1);
define ('SXGEO_BATCH',  2);

class SxGeo {

	protected $fh;
	protected $ip1c;
	protected $info;
	protected $range;
	protected $db_begin;
	protected $b_idx_str;
	protected $m_idx_str;
	protected $b_idx_arr;
	protected $m_idx_arr;
	protected $m_idx_len;
	protected $db_items;
	protected $country_size;
	protected $db;
	protected $regions_db;
	protected $cities_db;

	public $id2iso = array(
		'', 'AP', 'EU', 'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'CW', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU',
		'AW', 'AZ', 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BM', 'BN', 'BO', 'BR', 'BS',
		'BT', 'BV', 'BW', 'BY', 'BZ', 'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN',
		'CO', 'CR', 'CU', 'CV', 'CX', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE', 'EG',
		'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'SX', 'GA', 'GB', 'GD', 'GE', 'GF',
		'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY', 'HK', 'HM', 'HN',
		'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT', 'JM', 'JO', 'JP', 'KE',
		'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR',
		'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP',
		'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE', 'NF', 'NG', 'NI',
		'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM', 'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN',
		'PR', 'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RU', 'RW', 'SA', 'SB', 'SC', 'SD', 'SE', 'SG',
		'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'ST', 'SV', 'SY', 'SZ', 'TC', 'TD', 'TF',
		'TG', 'TH', 'TJ', 'TK', 'TM', 'TN', 'TO', 'TL', 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'UM',
		'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU', 'WF', 'WS', 'YE', 'YT', 'RS', 'ZA',
		'ZM', 'ME', 'ZW', 'A1', 'XK', 'O1', 'AX', 'GG', 'IM', 'JE', 'BL', 'MF', 'BQ', 'SS'
	);

	public $batch_mode  = false;
	public $memory_mode = false;

	public function __construct($db_file = '../app/Lib/SypexGeo/SxGeoCityMax.dat', $type = SXGEO_FILE){
		
		//exit($_SERVER['DOCUMENT_ROOT']);

		$this->fh = fopen($db_file, 'rb');

		// Сначала убеждаемся, что есть файл базы данных
		$header = fread($this->fh, 40); // В версии 2.2 заголовок увеличился на 8 байт
		if(substr($header, 0, 3) != 'SxG') die("Can't open {$db_file}\n");
		$info = unpack('Cver/Ntime/Ctype/Ccharset/Cb_idx_len/nm_idx_len/nrange/Ndb_items/Cid_len/nmax_region/nmax_city/Nregion_size/Ncity_size/nmax_country/Ncountry_size/npack_size', substr($header, 3));
		if($info['b_idx_len'] * $info['m_idx_len'] * $info['range'] * $info['db_items'] * $info['time'] * $info['id_len'] == 0) die("Wrong file format {$db_file}\n");
		$this->range       = $info['range'];
		$this->b_idx_len   = $info['b_idx_len'];
		$this->m_idx_len   = $info['m_idx_len'];
		$this->db_items    = $info['db_items'];
		$this->id_len      = $info['id_len'];
		$this->block_len   = 3 + $this->id_len;
		$this->max_region  = $info['max_region'];
		$this->max_city    = $info['max_city'];
		$this->max_country = $info['max_country'];
		$this->country_size= $info['country_size'];
		$this->batch_mode  = $type & SXGEO_BATCH;
		$this->memory_mode = $type & SXGEO_MEMORY;
		$this->pack        = $info['pack_size'] ? explode("\0", fread($this->fh, $info['pack_size'])) : '';
		$this->b_idx_str   = fread($this->fh, $info['b_idx_len'] * 4);
		$this->m_idx_str   = fread($this->fh, $info['m_idx_len'] * 4);

		$this->db_begin = ftell($this->fh);
		if ($this->batch_mode) {
			$this->b_idx_arr = array_values(unpack("N*", $this->b_idx_str)); // Быстрее в 5 раз, чем с циклом
			unset ($this->b_idx_str);
			$this->m_idx_arr = str_split($this->m_idx_str, 4); // Быстрее в 5 раз чем с циклом
			unset ($this->m_idx_str);
		}
		if ($this->memory_mode) {
			$this->db  = fread($this->fh, $this->db_items * $this->block_len);
			$this->regions_db = $info['region_size'] > 0 ? fread($this->fh, $info['region_size']) : '';
			$this->cities_db  = $info['city_size'] > 0 ? fread($this->fh, $info['city_size']) : '';
		}
		$this->info = $info;
		$this->info['regions_begin'] = $this->db_begin + $this->db_items * $this->block_len;
		$this->info['cities_begin']  = $this->info['regions_begin'] + $info['region_size'];
	}

	protected function search_idx($ipn, $min, $max){
		if($this->batch_mode){
			while($max - $min > 8){
				$offset = ($min + $max) >> 1;
				if ($ipn > $this->m_idx_arr[$offset]) $min = $offset;
				else $max = $offset;
			}
			while ($ipn > $this->m_idx_arr[$min] && $min++ < $max){};
		}
		else {
			while($max - $min > 8){
				$offset = ($min + $max) >> 1;
				if ($ipn > substr($this->m_idx_str, $offset*4, 4)) $min = $offset;
				else $max = $offset;
			}
			while ($ipn > substr($this->m_idx_str, $min*4, 4) && $min++ < $max){};
		}
		return  $min;
	}

	protected function search_db($str, $ipn, $min, $max){
		if($max - $min > 1) {
			$ipn = substr($ipn, 1);
			while($max - $min > 8){
				$offset = ($min + $max) >> 1;
				if ($ipn > substr($str, $offset * $this->block_len, 3)) $min = $offset;
				else $max = $offset;
			}
			while ($ipn >= substr($str, $min * $this->block_len, 3) && ++$min < $max){};
		}
		else {
			$min++;
		}
		return hexdec(bin2hex(substr($str, $min * $this->block_len - $this->id_len, $this->id_len)));
	}

	public function get_num($ip){
		$ip1n = (int)$ip; // Первый байт
		if($ip1n == 0 || $ip1n == 10 || $ip1n == 127 || $ip1n >= $this->b_idx_len || false === ($ipn = ip2long($ip))) return false;
		$ipn = pack('N', $ipn);
		$this->ip1c = chr($ip1n);
		// Находим блок данных в индексе первых байт
		if ($this->batch_mode){
			$blocks = array('min' => $this->b_idx_arr[$ip1n-1], 'max' => $this->b_idx_arr[$ip1n]);
		}
		else {
			$blocks = unpack("Nmin/Nmax", substr($this->b_idx_str, ($ip1n - 1) * 4, 8));
		}
		if ($blocks['max'] - $blocks['min'] > $this->range){
			// Ищем блок в основном индексе
			$part = $this->search_idx($ipn, floor($blocks['min'] / $this->range), floor($blocks['max'] / $this->range)-1);
			// Нашли номер блока в котором нужно искать IP, теперь находим нужный блок в БД
			$min = $part > 0 ? $part * $this->range : 0;
			$max = $part > $this->m_idx_len ? $this->db_items : ($part+1) * $this->range;
			// Нужно проверить чтобы блок не выходил за пределы блока первого байта
			if($min < $blocks['min']) $min = $blocks['min'];
			if($max > $blocks['max']) $max = $blocks['max'];
		}
		else {
			$min = $blocks['min'];
			$max = $blocks['max'];
		}
		$len = $max - $min;
		// Находим нужный диапазон в БД
		if ($this->memory_mode) {
			return $this->search_db($this->db, $ipn, $min, $max);
		}
		else {
			fseek($this->fh, $this->db_begin + $min * $this->block_len);
			return $this->search_db(fread($this->fh, $len * $this->block_len), $ipn, 0, $len);
		}
	}

	protected function readData($seek, $max, $type){
		$raw = '';
		if($seek && $max) {
			if ($this->memory_mode) {
				$raw = substr($type == 1 ? $this->regions_db : $this->cities_db, $seek, $max);
			} else {
				fseek($this->fh, $this->info[$type == 1 ? 'regions_begin' : 'cities_begin'] + $seek);
				$raw = fread($this->fh, $max);
			}
		}
		return $this->unpack($this->pack[$type], $raw);
	}

	protected function parseCity($seek, $full = false){
		if(!$this->pack) return false;
		$only_country = false;
		if($seek < $this->country_size){
			$country = $this->readData($seek, $this->max_country, 0);
			$city = $this->unpack($this->pack[2]);
			$city['lat'] = $country['lat'];
			$city['lon'] = $country['lon'];
			$only_country = true;
		}
		else {
			$city = $this->readData($seek, $this->max_city, 2);
			$country = array('id' => $city['country_id'], 'iso' => $this->id2iso[$city['country_id']]);
			unset($city['country_id']);
		}
		if($full) {
			$region = $this->readData($city['region_seek'], $this->max_region, 1);
			if(!$only_country) $country = $this->readData($region['country_seek'], $this->max_country, 0);
			unset($city['region_seek']);
			unset($region['country_seek']);
			return array('city' => $city, 'region' => $region, 'country' => $country);
		}
		else {
			unset($city['region_seek']);
			return array('city' => $city, 'country' => array('id' => $country['id'], 'iso' => $country['iso']));
		}
	}

	protected function unpack($pack, $item = ''){
		$unpacked = array();
		$empty = empty($item);
		$pack = explode('/', $pack);
		$pos = 0;
		foreach($pack AS $p){
			list($type, $name) = explode(':', $p);
			$type0 = $type{0};
			if($empty) {
				$unpacked[$name] = $type0 == 'b' || $type0 == 'c' ? '' : 0;
				continue;
			}
			switch($type0){
				case 't':
				case 'T': $l = 1; break;
				case 's':
				case 'n':
				case 'S': $l = 2; break;
				case 'm':
				case 'M': $l = 3; break;
				case 'd': $l = 8; break;
				case 'c': $l = (int)substr($type, 1); break;
				case 'b': $l = strpos($item, "\0", $pos)-$pos; break;
				default: $l = 4;
			}
			$val = substr($item, $pos, $l);
			switch($type0){
				case 't': $v = unpack('c', $val); break;
				case 'T': $v = unpack('C', $val); break;
				case 's': $v = unpack('s', $val); break;
				case 'S': $v = unpack('S', $val); break;
				case 'm': $v = unpack('l', $val . (ord($val{2}) >> 7 ? "\xff" : "\0")); break;
				case 'M': $v = unpack('L', $val . "\0"); break;
				case 'i': $v = unpack('l', $val); break;
				case 'I': $v = unpack('L', $val); break;
				case 'f': $v = unpack('f', $val); break;
				case 'd': $v = unpack('d', $val); break;

				case 'n': $v = current(unpack('s', $val)) / pow(10, $type{1}); break;
				case 'N': $v = current(unpack('l', $val)) / pow(10, $type{1}); break;

				case 'c': $v = rtrim($val, ' '); break;
				case 'b': $v = $val; $l++; break;
			}
			$pos += $l;
			$unpacked[$name] = is_array($v) ? current($v) : $v;
		}
		return $unpacked;
	}

	public function get($ip){
		return $this->max_city ? $this->getCity($ip) : $this->getCountry($ip);
	}
	public function getCountry($ip){
		if($this->max_city) {
			$tmp = $this->parseCity($this->get_num($ip));
			return $tmp['country']['iso'];
		}
		else return $this->id2iso[$this->get_num($ip)];
	}
	public function getCountryId($ip){
		if($this->max_city) {
			$tmp = $this->parseCity($this->get_num($ip));
			return $tmp['country']['id'];
		}
		else return $this->get_num($ip);
	}
	public function getCity($ip){
		$seek = $this->get_num($ip);
		return $seek ? $this->parseCity($seek) : false;
	}
	public function getCityFull($ip){
		$seek = $this->get_num($ip);
		return $seek ? $this->parseCity($seek, 1) : false;
	}
	public function about(){
		$charset = array('utf-8', 'latin1', 'cp1251');
		$types   = array('n/a', 'SxGeo Country', 'SxGeo City RU', 'SxGeo City EN', 'SxGeo City', 'SxGeo City Max RU', 'SxGeo City Max EN', 'SxGeo City Max');
		return array(
			'Created' => date('Y.m.d', $this->info['time']),
			'Timestamp' => $this->info['time'],
			'Charset' => $charset[$this->info['charset']],
			'Type' => $types[$this->info['type']],
			'Byte Index' => $this->b_idx_len,
			'Main Index' => $this->m_idx_len,
			'Blocks In Index Item' => $this->range,
			'IP Blocks' => $this->db_items,
			'Block Size' => $this->block_len,
			'City' => array(
				'Max Length' => $this->max_city,
				'Total Size' => $this->info['city_size'],
			),
			'Region' => array(
				'Max Length' => $this->max_region,
				'Total Size' => $this->info['region_size'],
			),
			'Country' => array(
				'Max Length' => $this->max_country,
				'Total Size' => $this->info['country_size'],
			),
		);
	}


public function Country ($ip) {

$country = $this->getCountry($ip);

$countries = json_decode(@file_get_contents('../app/Lib/SypexGeo/Countries.json'),true); 

foreach ($countries as $k => $v) {

if ($country == key($countries[$k])) return current($countries[$k]);

}

}



public function getOS($userAgent) {

$list = [

// Mircrosoft Windows Operating Systems

'Windows 3.11' => '(Win16)',
'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
'Windows 98' => '(Windows 98)|(Win98)',
'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
'Windows Server 2003' => '(Windows NT 5.2)',
'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
'Windows 8.1' => '(Windows NT 6.3)',
'Windows 10' => '(Windows NT 6.4)|(Windows 10)',
'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
'Windows CE' => '(Windows CE)',
// UNIX Like Operating Systems
'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
'Mac OS X Cheetah' => '(Mac OS X 10.0)',
'Mac OS X Puma' => '(Mac OS X 10.1)',
'Mac OS X Jaguar' => '(Mac OS X 10.2)',
'Mac OS X Panther' => '(Mac OS X 10.3)',
'Mac OS X Tiger' => '(Mac OS X 10.4)',
'Mac OS X Leopard' => '(Mac OS X 10.5)',
'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
'Mac OS X Lion' => '(Mac OS X 10.7)',
'Mac OS X' => '(Mac OS X)',
'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
'Open BSD' => '(OpenBSD)',
'SunOS' => '(SunOS)',
'Solaris 11' => '(Solaris/11)|(Solaris11)',
'Solaris 10' => '((Solaris/10)|(Solaris10))',
'Solaris 9' => '((Solaris/9)|(Solaris9))',
'CentOS' => '(CentOS)',
'QNX' => '(QNX)',

// Kernels
'UNIX' => '(UNIX)',

// Linux Operating Systems
'Ubuntu 12.10' => '(Ubuntu/12.10)|(Ubuntu 12.10)',
'Ubuntu 12.04 LTS' => '(Ubuntu/12.04)|(Ubuntu 12.04)',
'Ubuntu 11.10' => '(Ubuntu/11.10)|(Ubuntu 11.10)',
'Ubuntu 11.04' => '(Ubuntu/11.04)|(Ubuntu 11.04)',
'Ubuntu 10.10' => '(Ubuntu/10.10)|(Ubuntu 10.10)',
'Ubuntu 10.04 LTS' => '(Ubuntu/10.04)|(Ubuntu 10.04)',
'Ubuntu 9.10' => '(Ubuntu/9.10)|(Ubuntu 9.10)',
'Ubuntu 9.04' => '(Ubuntu/9.04)|(Ubuntu 9.04)',
'Ubuntu 8.10' => '(Ubuntu/8.10)|(Ubuntu 8.10)',
'Ubuntu 8.04 LTS' => '(Ubuntu/8.04)|(Ubuntu 8.04)',
'Ubuntu 6.06 LTS' => '(Ubuntu/6.06)|(Ubuntu 6.06)',
'Red Hat Linux' => '(Red Hat)',
'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
'Fedora 17' => '(Fedora/17)|(Fedora 17)',
'Fedora 16' => '(Fedora/16)|(Fedora 16)',
'Fedora 15' => '(Fedora/15)|(Fedora 15)',
'Fedora 14' => '(Fedora/14)|(Fedora 14)',
'Chromium OS' => '(ChromiumOS)',
'Google Chrome OS' => '(ChromeOS)',

// Kernel
'Linux' => '(Linux)|(X11)',

// BSD Operating Systems
'OpenBSD' => '(OpenBSD)',
'FreeBSD' => '(FreeBSD)',
'NetBSD' => '(NetBSD)',

// Mobile Devices
'Android' => '(Android)',
'iPod' => '(iPod)',
'iPhone' => '(iPhone)',
'iPad' => '(iPad)',

//DEC Operating Systems
'OS/8' => '(OS/8)|(OS8)',
'Older DEC OS' => '(DEC)|(RSTS)|(RSTS/E)',
'WPS-8' => '(WPS-8)|(WPS8)',
// BeOS Like Operating Systems
'BeOS' => '(BeOS)|(BeOS r5)',
'BeIA' => '(BeIA)',
// OS/2 Operating Systems
'OS/2 2.0' => '(OS/220)|(OS/2 2.0)',
'OS/2' => '(OS/2)|(OS2)',
// Search engines
'Search engine or robot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves/Teoma)|(ia_archiver)'

];
 
foreach($list as $os => $pattern){

if(preg_match("/$pattern/i",$userAgent)) { 

return $os;

}

}

return false; 

}

public function browser ($userAgent) {

$list = ['Firefox','Chrome','Safari','Opera','MSIE'];

foreach ($list as $key => $value) {

$str = strstr($userAgent,$list[$key]);

if ($str) break;

}

return($str ? substr($str,0,strpos($str,'/')) : false);

}


public function setInfo () {

$rand = rand(0,mt_getrandmax());

$date = date('d-m-Y',time());

$temp = array([

'ip' => $_SERVER['REMOTE_ADDR'],

'cookie_id' => array_key_exists('xxx',$_COOKIE) ? $_COOKIE['xxx'] : $rand,

'date' => $date,

'browser' => $this->browser($_SERVER['HTTP_USER_AGENT']),

'os' => $this->getOS($_SERVER['HTTP_USER_AGENT']),

'country' => $this->Country($_SERVER['REMOTE_ADDR']),

'city' => $this->get($_SERVER['REMOTE_ADDR'])['city']['name_ru'],

'referer' => URL::previous()

]);

$redis = new \Redis();

if ($redis->connect('127.0.0.1')) {

if ($redis->get('red')) {

$get = json_decode($redis->get('red'),true);

foreach ($get as $key => $value) {

if ($get[$key]['ip'] == $_SERVER['REMOTE_ADDR'] && $get[$key]['date'] == $date) {

if (array_key_exists('xxx',$_COOKIE)) {

if ($_COOKIE['xxx'] != $get[$key]['cookie_id']) return false;

} else return false;

}

}

$redis->set('red',json_encode(array_merge($get,$temp)));

} else {$redis->set('red',json_encode($temp));}

if (!array_key_exists('xxx',$_COOKIE)) {

@setcookie('xxx',$rand,(time() + 24 * 60 * 60),'/',$_SERVER['HTTP_HOST'],false,true);

}

return $redis->close();

} else return false;

}

}