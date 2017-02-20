<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl;
use iWedmak\Helper\Mate;

class Search 
{

    public static function url($url_template, $str, $page=0)
    {
        $string=urlencode(str_replace( array( '\'', '"', ',', "'", "!" , ';', '<', '>', ')', '(', '.'), '', $str));
        $search_string = str_replace("{searchString}", $string, $url_template);
        $search_string = str_replace("{page}", $page, $search_string);
        return $search_string;
    }
    
    public static function makeRes($source, $url, $title, $magnet=false, $size=false, $seeds=false, $leachs=false, $lang=false, $desc=false, $publish=false)
    {
        $array['source']=$source;
        $array['url']=trim($url);
        $array['title']=trim($title);
        $array['magnet']=trim($magnet);
        $array['size']=Mate::sizer(html_entity_decode(trim($size)));
        $array['seeds']=(is_bool($seeds))? (boolean)$seeds : (int)trim($seeds);
        $array['leachs']=(is_bool($leachs))? (boolean)$leachs : (int)trim($leachs);
        $array['lang']=trim($lang);
        $array['description']=trim($desc);
        $array['published_at']=date('Y-m-d H:i:s',strtotime(trim($publish)));
        return $array;
    }
    
    public static function makeError($client)
    {
        $array=[
                'error_code'=>$client->c->error_code, 
                'error'=>$client->c->error, 
                'response_headers'=>$client->c->response_headers,
            ];
        return $array;
    }
    
    public static function badWords($word)
    {
        $array=[
                'sedinam',
                'kenta223',
                'rokibg22',
                'pianogirl16',
                'love_mealmie',
            ];
        return  in_array($word, $array);
    }
    
}