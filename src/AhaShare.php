<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;
use iWedmak\Helper\Mate;

class AhaShare implements TorrentSearchInterface 
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
            if($html->find('a[href*=magnet]', 0))
            {
                $torrent=Search::makeRes
                    (
                        'AhaShare', 
                        $url, 
                        $html->find( 'td.fmmain h1', 0)->plaintext, 
                        $html->find('a[href*=magnet]', 0)->attr['href'], 
                        $html->find('fieldset.search',1)->find('table tr', 5)->find('td', 1)->plaintext, 
                        $html->find('font[color=green]', 0)->plaintext, 
                        $html->find('font[color=red]', 0)->plaintext,
                        $html->find('fieldset.search',1)->find('table tr', 4)->find('td', 1)->plaintext
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
            foreach($html->find('table.ttable_headinner tr') as $tr)
            {
                if($tr->find('td a img[src=/images/icon_download.gif]', 0)) 
                {
                    $torrent=Search::makeRes
                    (
                        'AhaShare', 
                        'http://http://www.ahashare.com'.$tr->find('a', 1)->attr['href'], 
                        $tr->find('a', 1)->attr['title'], 
                        'http://http://www.ahashare.com'.$tr->find('td', 2)->find('a', 0)->attr['href'], 
                        $tr->find('td', 4)->plaintext, 
                        $tr->find('td font[color=green]', 0)->plaintext, 
                        $tr->find('td font[color=red]', 0)->plaintext
                    );
                    $result[]=$torrent;
                }
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}