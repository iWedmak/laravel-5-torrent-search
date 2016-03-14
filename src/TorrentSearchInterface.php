<?php namespace iWedmak\TorrentSearch;

interface TorrentSearchInterface
{
    public static function page($url, $cache, $client);
    public static function search($url, $cache, $client);
}