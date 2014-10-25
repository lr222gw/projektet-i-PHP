<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-10-25
 * Time: 21:31
 */
class YoutubePlayer{

    private $playlistUrl;
    private $autoplay;

    public function __construct($playlistUrl, $autoplayOn){
        $this->playlistUrl = $playlistUrl;
        $this->turnOnAutoPlay($autoplayOn);
    }

    public function changePlaylist($playlistUrl){

        $playlistUrl = html_entity_decode($playlistUrl);//Nödvändigt? tror inte det...
        $regex = '^(http(s?):\/\/)?(www\.)?youtu(be)?\.([a-z])+\/^';
        $clearnUrl = preg_split($regex,$playlistUrl);

        $this->playlistUrl = $clearnUrl[count($clearnUrl)-1];
    }
    public function turnOnAutoPlay($trueForOn){
        if($trueForOn){
            $this->autoplay = "&amp;autoplay=1";
        }else{
            $this->autoplay = "";
        }
    }

    public function getPlayListPlayer(){
        $playlistUrl =$this->playlistUrl;//width=\"250\" height=\"35\"
        $playlistSetting = "<iframe id='musicPlayer'  src=\"//www.youtube.com/embed/{$playlistUrl}$this->autoplay\" frameborder=\"0\"></iframe>";
        return $playlistSetting;
    }








}