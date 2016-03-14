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
            //pre($url);
            //pre($html->find('h1', 0));
            $torrent=Search::makeRes
                (
                    'KickassTorrents', 
                    $html->find('h1', 0)->find('a', 0)->attr['href'], 
                    $html->find('h1', 0)->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    $html->find('div.widgetSize strong', 0)->plaintext, 
                    $html->find('div.seedBlock strong', 0)->plaintext, 
                    $html->find('div.leechBlock strong', 0)->plaintext
                );
            return $torrent;
        }
        
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
            foreach($html->find('tr[id*=torrent_tv_torrents]') as $tr)
            {
                $torrent=Search::makeRes
                (
                    'KickassTorrents', 
                    'https://kat.cr'.$tr->find('a.cellMainLink', 0)->attr['href'], 
                    $tr->find('a.cellMainLink', 0)->plaintext, 
                    $tr->find('a[href*=magnet]', 0)->attr['href'], 
                    $tr->find('td', 1)->plaintext,
                    $tr->find('td.green', 0)->plaintext,
                    $tr->find('td.red', 0)->plaintext
                );
                $result[]=$torrent;
            }
            return $result;
        }
        else
        {
            return ['error_code'=>$client->c->error_code];
        }
    }
    
}
?>