<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

class PirateBay implements TorrentSearchInterface 
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
            $size=$html->find('dl.col1 dd', 2)->plaintext;
            $size=preg_replace ( '/\((.*?)\)/i' , '', $size);
            $torrent=Search::makeRes
                (
                    'PirateBay', 
                    $html->find('div#title a', 0)->attr['href'], 
                    $html->find('div#title', 0)->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    $size, 
                    $html->find('dl.col2 dd', 1)->plaintext, 
                    $html->find('dl.col2 dd', 2)->plaintext
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
        if($resp=$client->get($url, $cache, 'file'))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            
            $result=[];
            foreach($html->find('table#searchResult tr') as $tr)
            {
                if($tr->find('td', 1))
                {
                    //* for one column mode
                    preg_match('/size (.*?)\,/i', $tr->find('td font.detDesc', 0)->plaintext, $extra);
                    $torrent=Search::makeRes
                        (
                            'PirateBay', 
                            'https://thepiratebay.cr'.$tr->find('td div.detName a', 0)->attr['href'], 
                            $tr->find('td div.detName a', 0)->plaintext, 
                            $tr->find('td a[href*=magnet]', 0)->attr['href'], 
                            $extra[1], 
                            $tr->find('td', 2)->plaintext, 
                            $tr->find('td', 3)->plaintext
                        );
                    //*/
                    
                    /* for two colums mode
                    $torrent=Search::makeRes
                        (
                            'PirateBay', 
                            'https://thepiratebay.sh'.$tr->find('td', 1)->find('a', 0)->attr['href'],
                            $tr->find('td div.detName a', 0)->plaintext,
                            $tr->find('td', 3)->find('a', 0)->attr['href'],
                            $tr->find('td', 4)->plaintext,
                            $tr->find('td', 5)->plaintext,
                            $tr->find('td', 6)->plaintext
                        );
                    //*/
                    $result[]=$torrent;
                }
            }
            return $result;
        }
        else
        {
            return ['error_code'=>$client->c->error_code, 'error'=>$client->c->error, 'response_headers'=>$client->c->response_headers];
        }
    }
    
}
?>