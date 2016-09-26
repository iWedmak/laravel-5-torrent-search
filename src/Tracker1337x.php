<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

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
            if($html->find('div.top-row strong', 0) && $html->find('div.top-row strong', 0)->plaintext!='Error')
            {
                $size=false;
                foreach($html->find('div.category-detail li') as $li)
                {
                    if($li->find('strong', 0) && $li->find('strong', 0)->plaintext=='Total size')
                    {
                        $size=$li->find('span', 0)->plaintext;
                    }
                }
                $torrent=Search::makeRes
                    (
                        'Tracker1337x', 
                        $url, 
                        $html->find('div.top-row strong', 0)->plaintext, 
                        $html->find('a[href*=magnet]', 0)->attr['href'], 
                        $size, 
                        $html->find('span.green', 0)->plaintext, 
                        $html->find('span.red', 0)->plaintext
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
            foreach($html->find('ul.clearfix li') as $tr)
            {
                if($tr->find('a[href*=torrent]', 0))
                {
                    //pre($tr->find('div.uploader a, div.user a, div.vip a', 0)->plaintext);//for future reference in case of span filter
                    $torrent=Search::makeRes
                    (
                        'Tracker1337x', 
                        'http://1337x.to'.$tr->find('a[href*=torrent]', 0)->attr['href'], 
                        $tr->find('a[href*=torrent]', 0)->plaintext, 
                        false, 
                        $tr->find('div.coll-4 span', 0)->plaintext, 
                        $tr->find('span.green', 0)->plaintext, 
                        $tr->find('span.red', 0)->plaintext
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