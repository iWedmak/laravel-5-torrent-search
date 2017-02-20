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
            $img='';
            if($html->find('a.pirobox', 0))
            {
                $img='<img src="'.$html->find('a.pirobox', 0)->attr['href'].'">';
            }
            $description=$html->find('table.episode_columns_holder', 0)->next_sibling()->find('td[valign=top]',1)->plaintext;
            preg_match('/<b>Filesize:<\/b> (?<size>.*?)<br\/>/iu', $resp, $size);
            //pre($size);
            preg_match('/<b>Released:<\/b>(?<date>.*?)<br\/>/iu', $resp, $publish);
            //pre($publish);
            $torrent=Search::makeRes
                (
                    'Eztv', 
                    $url, 
                    $html->find('h1', 0)->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    $size['size'], 
                    $html->find('span.stat_red', 0)->plaintext, 
                    $html->find('span.stat_green', 0)->plaintext,
                    'ENG',
                    $description.' '.$img,
                    $publish['date']
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
            $url_parsed = parse_url($url);
            foreach($html->find('table.forum_header_border tr.forum_header_border') as $tr)
            {
                if($tr->find('a[href*=magnet]', 0))
                {
                    $torrent=Search::makeRes
                    (
                        'Eztv', 
                        'https://'.$url_parsed['host'].$tr->find('td.forum_thread_post', 1)->find('a', 0)->attr['href'], 
                        $tr->find('a.epinfo', 0)->plaintext, 
                        $tr->find('a[href*=magnet]', 0)->attr['href'], 
                        $tr->find('td', 3)->plaintext,
                        $tr->find('td', 5)->plaintext,
                        false,
                        'ENG',
                        false
                    );
                    $result[]=$torrent;
                }
            }
            return $result;
        }
        return Search::makeError($client);
    }
    
}