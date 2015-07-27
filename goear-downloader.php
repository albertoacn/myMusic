<?php

$url = $argv[1];
mkdir('Songs', 0777);

	if(strpos($url,'playlist') !== false)
	{
		get_playlist($url);
	}
	else
	{
			if(strpos($url,'listen') !== false)
			{
				get_song($url);
			}
			else
			{
				echo "Input Parameter incorrect...";
			}
	}

	echo "Done!";

function get_song($song_url, $path = '/')
{
	$code_length = 7;

	$song_code = substr($song_url, strrpos($song_url,'/') - $code_length, $code_length);
	$song_name = substr($song_url, strrpos($song_url,'/') + 1);
	
	$ch = curl_init();
	# cURL configuration to get song
	curl_setopt ($ch, CURLOPT_HEADER, TRUE);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt ($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLINFO_HEADER_OUT, TRUE);
	curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt ($ch, CURLOPT_HTTPHEADER, array('accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'accept-language' => 'es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3'));
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt ($ch, CURLOPT_URL, 'http://goear.com/action/sound/get/' . $song_code);
	curl_setopt ($ch, CURLOPT_REFERER, 'http://www.goear.com/libs/swf/soundmanager2_flash9.swf');
	curl_setopt ($ch, CURLOPT_BUFFERSIZE, 1024);
	curl_setopt ($ch, CURLOPT_NOPROGRESS, FALSE);
	file_put_contents('Songs' . $path . $song_name . '.mp3', preg_replace('/^.*?(?:HTTP *?\/ *?\d+\.\d+ +?\d{3}.*?\r\n\r\n)+(.*)$/is', '\1', curl_exec ($ch)));
	curl_close ($ch);
}

function get_playlist($playlist_url)
{
	$code_length = 7;
	
	$playlist_code = substr($playlist_url, strrpos($playlist_url,'/') - $code_length, $code_length);
	$playlist_name = substr($playlist_url, strrpos($playlist_url,'/') + 1);
	
	$ch = curl_init();
	# cURL configuration to get XML playlist		
	curl_setopt ($ch, CURLOPT_HEADER, TRUE);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt ($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLINFO_HEADER_OUT, TRUE);
	curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt ($ch, CURLOPT_HTTPHEADER, array('accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'accept-language' => 'es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3'));
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt ($ch, CURLOPT_URL, 'http://goear.com/playerplaylist/' . $playlist_code);
	curl_setopt ($ch, CURLOPT_BUFFERSIZE, 1024);
	curl_setopt ($ch, CURLOPT_NOPROGRESS, FALSE);
	file_put_contents($playlist_code . '.xml', preg_replace('/^.*?(?:HTTP *?\/ *?\d+\.\d+ +?\d{3}.*?\r\n\r\n)+(.*)$/is', '\1', curl_exec($ch)));
	curl_close ($ch);
	
	mkdir('Songs/' . $playlist_name, 0777);
	
	$playlist_file = fopen($playlist_code . '.xml', "r") or die("Unable to open file!");

	# Read XML with playlist
	while(!feof($playlist_file)) {
		$line = fgets($playlist_file);
		$tracks = substr_count($line, 'target=');
		$offset = 0;
		while( $tracks > 0 )
		{
			$initpos = strpos($line, 'target="', $offset) + 8;
			get_song( substr( $line, $initpos, strpos($line, '"', $initpos + 1 ) - $initpos), '/' . $playlist_name . '/');
			$offset = $initpos + 1;
			$tracks = $tracks - 1;
		}
	}
	fclose($playlist_file);
	unlink($playlist_code . '.xml');
}

?>
