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
                    elseif($dt->plaintext=='By:')
                    {
                        if(Search::badWords($html->find('dl.col2 dd', $key)->plaintext))
                        {
                            return false;
                        }
                    }
                }
                $torrent=Search::makeRes
                    (
                        'PirateBay', 
                        $html->find('div#title a', 0)->attr['href'], 
                        $html->find('div#title', 0)->plaintext, 
                        $html->find('a[href*=magnet]', 0)->attr['href'], 
                        $size, 
                        @$seeds, 
                        @$leechs
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
            //pre($url);
            //pre($resp);
            $html=new \Htmldom;
            $html->str_get_html($resp);
            
            $result=[];
            foreach($html->find('table#searchResult tr') as $tr)
            {
                if($tr->find('td', 1))
                {
                    //* for one column mode
                    if(!Search::badWords($tr->find('font.detDesc', 0)->first_child ()->plaintext))
                    {
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
                        $result[]=$torrent;
                    }
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
                }
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}
?>