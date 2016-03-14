<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

class RARBG implements TorrentSearchInterface 
{

    public static function page($url, $cache=5, $client=false)
    {
        sleep(1);
        //$cache=5;
        if(!$client)
        {
            $client=new Parser;
        }
        $client->setProxy();
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            pre($resp);
            preg_match('/Seeders : (.*?) , Leechers : (.*?) = /i', $html->plaintext, $extra);
            pre($url);
            pre($html->find('h1', 0));
            $torrent=Search::makeRes
                (
                    'RARBG', 
                    $url, 
                    $html->find('h1', 0)->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    false, 
                    @$extra[1], 
                    @$extra[2]
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
        $client->setProxy();
        if($resp=$client->get($url, $cache, 'file'))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            pre($resp);
            $result=[];
            foreach($html->find('table.lista2t tr.lista2') as $tr)
            {
                $torrent=Search::makeRes
                (
                    'RARBG', 
                    'https://rarbg.to'.$tr->find('td', 1)->find('a', 0)->attr['href'], 
                    $tr->find('td', 1)->find('a', 0)->plaintext, 
                    false, 
                    $tr->find('td', 3)->plaintext, 
                    $tr->find('td', 4)->plaintext, 
                    $tr->find('td', 5)->plaintext
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