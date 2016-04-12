<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

class Eztv implements TorrentSearchInterface 
{

    public static function page($url, $cache=5, $client=false)
    {
        if(!$client)
        {
            $client=new Parser;
        }
        if($resp=$client->get($url, $cache, 'file'))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            $torrent=Search::makeRes
                (
                    'Eztv', 
                    $url, 
                    $html->find('h1', 0)->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    false, 
                    $html->find('span.stat_red', 0)->plaintext, 
                    $html->find('span.stat_green', 0)->plaintext
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
            foreach($html->find('table.forum_header_border tr.forum_header_border') as $tr)
            {
                $torrent=Search::makeRes
                (
                    'Eztv', 
                    'https://eztv.ag'.$tr->find('td.forum_thread_post', 1)->find('a', 0)->attr['href'], 
                    $tr->find('a.epinfo', 0)->plaintext, 
                    $tr->find('a[href*=magnet]', 0)->attr['href'], 
                    $tr->find('td', 3)->plaintext
                );
                
                $result[]=$torrent;
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}
?>