<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

class KickassTorrents implements TorrentSearchInterface 
{

    public static function page($url, $cache=5, $client=false)
    {
        if(!$client)
        {
            $client=new Parser;
        }
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            if($html->find('h1', 0))
            {
                $torrent=Search::makeRes
                    (
                        'KickassTorrents', 
                        $url, 
                        $html->find('h1', 0)->plaintext, 
                        $html->find('a[href*=magnet]', 0)->attr['href'], 
                        $html->find('div.widgetSize', 0)->plaintext, 
                        $html->find('div.seedBlock strong', 0)->plaintext, 
                        $html->find('div.leechBlock strong', 0)->plaintext,
                        false,
                        $html->find('#desc', 0)->plaintext,
                        $html->find('time.timeago', 0)->attr['datetime']
                    );
                return $torrent;
            }
        }
        return Search::makeError($client);
    }
    
    public static function search($url, $cache=5, $client=false)
    {
        if(!$client)
        {
            $client=new Parser;
        }
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            $result=[];
            $url_parsed = parse_url($url);
            foreach($html->find('tr.odd , tr.even') as $tr)
            {
                $torrent=Search::makeRes
                (
                    'KickassTorrents', 
                    'https://'.$url_parsed['host'].$tr->find('a.cellMainLink', 0)->attr['href'], 
                    $tr->find('a.cellMainLink', 0)->plaintext, 
                    $tr->find('a[href*=magnet]', 0)->attr['href'], 
                    $tr->find('td', 1)->plaintext,
                    $tr->find('td.green', 0)->plaintext,
                    $tr->find('td.red', 0)->plaintext,
                    false,
                    false,
                    $tr->find('td', 2)->attr['title']
                );
                $result[]=$torrent;
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}