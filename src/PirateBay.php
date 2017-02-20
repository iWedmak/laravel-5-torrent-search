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
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            $size=@$html->find('dl.col1 dd', 2)->plaintext;
            if(isset($size) && !empty($size))
            {
                $size=preg_replace ( '/\((.*?)\)/i' , '', $size);
                foreach($html->find('dl.col2 dt') as $key=>$dt)
                {
                    if($dt->plaintext=='Seeders:')
                    {
                        $seeds=$html->find('dl.col2 dd', $key)->plaintext;
                    }
                    elseif($dt->plaintext=='Leechers:')
                    {
                        $leechs=$html->find('dl.col2 dd', $key)->plaintext;
                    }
                    elseif($dt->plaintext=='Uploaded:')
                    {
                        $publish=$html->find('dl.col2 dd', $key)->plaintext;
                    }
                    elseif($dt->plaintext=='By:')
                    {
                        if(Search::badWords($html->find('dl.col2 dd', $key)->plaintext))
                        {
                            return false;
                        }
                    }
                }
                //pre($resp);
                $torrent=Search::makeRes
                    (
                        'PirateBay', 
                        $url, 
                        $html->find('div#title', 0)->plaintext, 
                        $html->find('a[href*=magnet]', 0)->attr['href'], 
                        $size, 
                        @$seeds, 
                        @$leechs,
                        false,
                        @$html->find('div.nfo pre', 0)->plaintext,
                        @$publish
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
            foreach($html->find('#searchResult tr') as $tr)
            {
                if($tr->find('td', 1))
                {
                    
                    //* for one column mode
                    if(($tr->find('td a[title*=Browse]', 0) && !Search::badWords($tr->find('td a[title*=Browse]', 0)->plaintext)) || !$tr->find('td a[title*=Browse]', 0))
                    {
                        //preg_match('/Size (.*?)\,/i', $tr->find('i', 0)->plaintext, $extra);
                        $torrent=Search::makeRes
                            (
                                'PirateBay', 
                                'https://'.$url_parsed['host'].$tr->find('a[href*=torrent]', 0)->attr['href'], 
                                $tr->find('a[href*=torrent]', 0)->plaintext, 
                                $tr->find('a[href*=magnet]', 0)->attr['href'], 
                                $tr->find('td', 4)->plaintext, 
                                $tr->find('td', 5)->plaintext, 
                                $tr->find('td', 6)->plaintext
                            );
                        $result[]=$torrent;
                    }
                    //*/
                    
                    /* for two colums mode //old
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
                }
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}