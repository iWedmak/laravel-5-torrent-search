<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl;

class Search 
{

    public $c;
    
    public function __construct()
    {
        $this->c=new \Curl\Curl();
        $this->c->setopt(CURLOPT_ENCODING, 'utf-8');
        $this->c->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $this->c->setopt(CURLOPT_RETURNTRANSFER, true);
        
    }
    
    public static function url($url_template, $str, $page=0)
    {
        $string=urlencode(str_replace( array( '\'', '"', ',', "'", "!" , ';', '<', '>', ')', '('), '', $str));
        if($page>=1)
        {
            $search_string = str_replace("{searchString}", $string, $url_template);
            $search_string = str_replace("{page}", $page, $search_string);
        }
        else
        {
            $search_string = str_replace("{searchString}", $string, $url_template);
        }
        return $search_string;
    }
    
    public static function makeRes($source, $link, $title, $magnet=false, $size=false, $seeds=false, $leachs=false, $lang=false)
    {
        $array['source']=$source;
        $array['link']=trim($link);
        $array['title']=trim($title);
        $array['magnet']=trim($magnet);
        $array['size']=trim($size);
        $array['seeds']=trim($seeds);
        $array['leachs']=trim($leachs);
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
    
}
?>