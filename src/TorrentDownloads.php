<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

class TorrentDownloads implements TorrentSearchInterface 
{

    public static function page($url, $cache=5, $client=false)
    {
        sleep(1);
        //$cache=5;
        if(!$client)
        {
            $client=new Parser;
        }
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            
            $seed=false;
            $leeach=false;
            $size=false;
            foreach($html->find('div.grey_bar1') as $div)
            {
                switch($div->find('span', 0)->plaintext)
                {
                    case "Seeds: ":
                        list($trach, $seed)=explode(': ', $div->plaintext, 2);
                        break;
                    case "Leechers: ":
                        list($trach, $leeach)=explode(': ', $div->plaintext, 2);
                        break;
                    case "Total Size: ":
                        list($trach, $size)=explode(': ', $div->plaintext, 2);
                        break;
                }
            }
            $torrent=Search::makeRes
                (
                    'TorrentDownloads', 
                    $url, 
                    $html->find('h1.titl_1', 0)->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    $size, 
                    $seed, 
                    $leeach
                );
            return $torrent;
        }
        return Search::makeError($client);
    }
    
    public static function search($url, $cache=5, $client=false)
    {
        if(!$client)
        {
            $client=new Parser;
        }
        
        if($resp=$client->get($url, $cache, 'file'))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            $result=[];
            foreach($html->find('div.grey_bar3') as $tr)
            {
                if($tr->find('a[title*=torrent]', 0))
                {
                    $seed=false;
                    $leeach=false;
                    $size=false;
                    foreach($tr->find('span[!class]') as $key=>$span)
                    {
                        switch($key)
                        {
                            case 0:
                                $leeach=$span->plaintext;
                                break;
                            case 1:
                                $seed=$span->plaintext;
                                break;
                            case 2:
                                $size=$span->plaintext;
                                break;
                        }
                    }
                    $torrent=Search::makeRes
                    (
                        'TorrentDownloads', 
                        'https://www.torrentdownloads.me'.$tr->find('a[title*=torrent]', 0)->attr['href'], 
                        $tr->find('a[title*=torrent]', 0)->plaintext, 
                        false, 
                        $size, 
                        $seed, 
                        $leeach
                    );
                    $result[]=$torrent;
                }
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}