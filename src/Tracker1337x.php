<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;
use iWedmak\Helper\Mate;

class Tracker1337x implements TorrentSearchInterface 
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
                $size=false;
                $torrent=Search::makeRes
                    (
                        'Tracker1337x', 
                        $url, 
                        Mate::match('Download [*] Torrent', $html->find('title', 0)->plaintext), 
                        $html->find('a[href*=magnet]', 0)->attr['href'], 
                        $size, 
                        $html->find('span.seeds', 0)->plaintext, 
                        $html->find('span.leeches', 0)->plaintext
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
        
        if($resp=$client->get($url, $cache, 'file'))
        {
            //dd($resp);
            $html=new \Htmldom;
            $html->str_get_html($resp);
            $result=[];
            //pre($resp);
            //pre($html->find('.table-list tr'));
            foreach($html->find('table tr') as $tr)
            {
                //pre('some');
                if($tr->find('td.name a', 1)) 
                {
                    //pre($tr->find('div.uploader a, div.user a, div.vip a', 0)->plaintext);//for future reference in case of span filter
                    //pre($tr->find('td.name a', 1)->plaintext);
                    $torrent=Search::makeRes
                    (
                        'Tracker1337x', 
                        'http://1337x.to'.$tr->find('td.name a', 1)->attr['href'], 
                        $tr->find('td.name a', 1)->plaintext, 
                        false, 
                        $tr->find('td.size', 0)->plaintext, 
                        $tr->find('td.seeds', 0)->plaintext, 
                        $tr->find('td.leeches', 0)->plaintext
                    );
                    $result[]=$torrent;
                    //*/
                }
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}