<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl;

class Search 
{

    public static function url($url_template, $str, $page=0)
    {
        $string=urlencode(str_replace( array( '\'', '"', ',', "'", "!" , ';', '<', '>', ')', '('), '', $str));
        $search_string = str_replace("{searchString}", $string, $url_template);
        $search_string = str_replace("{page}", $page, $search_string);
        return $search_string;
    }
    
    public static function makeRes($source, $link, $title, $magnet=false, $size=false, $seeds=false, $leachs=false, $lang=false)
    {
        $array['source']=$source;
        $array['link']=trim($link);
        $array['title']=trim($title);
        $array['magnet']=trim($magnet);
        $array['size']=html_entity_decode(trim($size));
        
        $array['seeds']=(is_int($seeds/1))? (int)trim($seeds) : (boolean)$seeds;
        $array['leachs']=(is_int($leachs/1))? (int)trim($leachs) : (boolean)$leachs;
        $array['lang']=trim($lang);
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
            ];
        return  in_array($word, $array);
    }
    
}
?>