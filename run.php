<?php

//https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token

class curlGitApi{
	public $GIT_AUTHEN = 'need change'; //(Personal access tokens) end 30day,...or 60day or never
	public $GIT_URL = 'https://api.github.com/';
	public $GIT_VERSION = '2022-11-28';
	public $GIT_ACCEPT = 'application/vnd.github+json';
	public $CURL_USERAGENT = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0';
	public $GIT_LINKS = [
		'USER' => 'user',
		'USERS' => 'users',
		'REPOS' => 'repos',
		'GISTS' => 'gists',
	];
	public function getUrl($AT_LINK, $MORE_URL = '')
	{
		$curl = curl_init();
		$headers = [];
		$url = $this->GIT_URL. $this->GIT_LINKS[$AT_LINK]. $MORE_URL;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0");
		$headers = array(
			'Authorization: Bearer ' . $this->GIT_AUTHEN,
			'X-GitHub-Api-Version: '.$this->GIT_VERSION,
			'Accept: '.$this->GIT_ACCEPT,
		);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_HEADERFUNCTION,
		  function($curl, $header) use (&$headers)
		  {
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
			  return $len;

			$headers[strtolower(trim($header[0]))][] = trim($header[1]);
			
			return $len;
		  }
		);

		$response = curl_exec($curl);
		$error_msg = '';
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
		}
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);


		$arr = json_decode($response,true);
		$request=[
			'url' => $url,
			'headers' => $headers,
		];
		
		return [
			'httpCode'=>$httpCode,
			'result'=>$arr,
			'headers'=>$headers, 
			'request'=>$request,
			'error_msg'=>$error_msg
		];
	}
	public function patchUrl($AT_LINK, $MORE_URL = '',$DATA_IN=''){
		return $this->methodCustomUrl('PATCH',$AT_LINK, $MORE_URL,$DATA_IN);
	}
	public function postUrl($AT_LINK, $MORE_URL = '',$DATA_IN=''){
		return $this->methodCustomUrl('POST',$AT_LINK, $MORE_URL,$DATA_IN);
	}
	public function putUrl($AT_LINK, $MORE_URL = '',$DATA_IN=''){
		return $this->methodCustomUrl('PUT',$AT_LINK, $MORE_URL,$DATA_IN);
	}

	private function methodCustomUrl($METHOD, $AT_LINK, $MORE_URL = '',$DATA_IN=''){
		$curl = curl_init();
		$headers = [];
		$url = $this->GIT_URL. $this->GIT_LINKS[$AT_LINK]. $MORE_URL;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0");
		$headers = array(
			'Authorization: Bearer ' . $this->GIT_AUTHEN,
			'X-GitHub-Api-Version: '.$this->GIT_VERSION,
			'Accept: '.$this->GIT_ACCEPT,
		);
		//CURLOPT_TIMEOUT => 30,
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $METHOD);
		
		//$data = "{'description': 'Hello-World-chau'}";
		
		//$data = ['description' => 'Hello-World-chau 123'];
		$jsonSend= json_encode($DATA_IN);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonSend);

		curl_setopt($curl, CURLOPT_HEADERFUNCTION,
		  function($curl, $header) use (&$headers)
		  {
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
			  return $len;

			$headers[strtolower(trim($header[0]))][] = trim($header[1]);
			
			return $len;
		  }
		);

		$response = curl_exec($curl);
		$error_msg = '';
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
		}
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);


		$arr = json_decode($response,true);
		$request=[
			'url' => $url,
			'headers' => $headers,
		];
		
		return [
			'httpCode'=>$httpCode,
			'result'=>$arr,
			'headers'=>$headers, 
			'request'=>$request,
			'error_msg' => $error_msg,
			'jsonSend' => $jsonSend,
			
		];
	}
}

$git = new curlGitApi();
$data = $git->getUrl('USER');
//$data = $git->getUrl('USERS','/tranchausky/repos'); //list public

//https://docs.github.com/en/rest/repos/repos?apiVersion=2022-11-28#list-repositories-for-the-authenticated-user
//$data = $git->getUrl('USER','/repos?per_page=100'); //all repos have permission with user

//https://api.github.com/repos/tranchausky/html-page

//$dataIn = ['description' => 'Hello-World-chau 1235'];
//$data = $git->patchUrl('REPOS','/tranchausky/html-page',$dataIn); 

//$data = $git->getUrl('REPOS','/tranchausky/html-page/branches'); //list branch

//$data = $git->getUrl('REPOS','/tranchausky/html-page/commits'); //list commits

/*
$dataIn = [
	'description' => 'hi ze',
	'public' => true,
	'files' => [
		'README.md'=> [
			'content' => 'Hello World. Push From Code'
		]
	],
];
$data = $git->postUrl('GISTS','',$dataIn);
*/

//https://docs.github.com/en/rest/gists/gists?apiVersion=2022-11-28#list-public-gists
//$data = $git->getUrl('GISTS','/public?per_page=100'); //list git public

//save a blob (binary large object) for repository
//https://docs.github.com/en/rest/git/blobs?apiVersion=2022-11-28#about-git-blobs
/*
$dataIn = [
	'content' => 'Content of the blob 123',
	'encoding' => 'utf-8'
];
$data = $git->postUrl('REPOS','/tranchausky/html-page/git/blobs',$dataIn);
*/

/*
$data = $git->getUrl('REPOS','/tranchausky/html-page/git/blobs/df41ef43b0ffe7d125e210e7369529316b43d035');
echo base64_decode($data['result']['content']);
*/

/*
$message = 'chau push content';
$base64 = base64_encode($message);
$path = 'files/file1.txt';
*/
/*
$message = file_get_contents('a2.png');
$base64 = base64_encode($message);
$path = 'files/a2.png';
*/

/*
$message = file_get_contents('xin-dung-lang-im-Soobin.mp3');
$base64 = base64_encode($message);
$path = 'files/xin-dung-lang-im-Soobin.mp3';
*/


/*
$message = file_get_contents('xin-dung-lang-im-Soobin.mp3');
$base64 = base64_encode($message);
$path = 'files/xin-dung-lang-im-Soobin.mp3';
*/

/*
$message = file_get_contents('Tin-vao-chinh-minh-louise-l-hay.pdf');
$base64 = base64_encode($message);
$path = 'files/Tin-vao-chinh-minh-louise-l-hay.pdf';


$dataIn = [
	'message' => 'my commit message pdf',
	'committer' => [
		'name' => 'tranchausky',
		'email' => 'tranchausky@gmail.com'
	],
	'content' => $base64
];
//https://docs.github.com/en/rest/repos/contents?apiVersion=2022-11-28#create-or-update-file-contents
$data = $git->putUrl('REPOS','/tranchausky/html-page/contents/'.$path,$dataIn);
*/

//https://docs.github.com/en/rest/repos/contents?apiVersion=2022-11-28#get-repository-content (1-100MB)
/*
$path = 'files/file1.txt';
$data = $git->getUrl('REPOS','/tranchausky/html-page/contents/'.$path);
echo base64_decode($data['result']['content']);
*/

//get list file in folder
/*
$path = 'files';
$data = $git->getUrl('REPOS','/tranchausky/html-page/contents/'.$path);
var_dump($data['result']['0']);
*/

//echo '<pre>';
//print_r($data);
var_dump($data);
//var_dump($heaer);
