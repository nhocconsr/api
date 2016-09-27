<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;
use App\User;
use App\Models\Playlist;
use App\Models\Video;
use App\Models\Ycat;
use File;

class AdminController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
         return view('admin/home');
    }

    /**
     * get Playlist
     */
    public function getYcat($id = 0) {
        $cat = null;
        if ($id) {
            $cat = Ycat::find($id);
        }else{
            
        }
        return view('admin/videos/cat', ['cat' => $cat]);
    }

    /**
     * save cat
     */
    public function postYcat(Request $req) {
        if ($req->id) {
            $cat = Ycat::find($req->id);
        } else {
            $cat = new Ycat();
        }
        if ($req->title) {
            $cat->title = $req->title;
            $cat->save();
            return Redirect::to('/admin/playlists/1');
        }
        Input::flash();
        return Redirect::to('/admin/ycat/add');
    }

    /**
     * 
     * @param type $catId
     * @return type
     */
    public function getPlaylists($catId) {
        $cat = Ycat::find($catId);
//        $playlists = Playlist::where('cat_id', $catId)->get();
        return view('admin/videos/home', ['playlists' => $cat->playlists,'cat' => $cat]);
    }

    /**
     * get Videos from playlist
     */
    public function videos($id) {
        $playlist = Playlist::find($id);
        $videos = $playlist->videos;

        return view('admin/videos', ['videos' => $videos, 'playlist' => $playlist]);
    }

    /**
     * get Playlist
     */
    public function getPlaylist($id = 0) {
        $playlist = null;
        if ($id) {
            $playlist = Playlist::find($id);
        }
        return view('admin/videos/editPlaylist', ['playlist' => $playlist]);
    }

    /**
     * save playlist
     */
    public function postPlaylist(Request $req) {
         
        $yid = trim($req->get('yid'));
        $message = null;
        if (!$yid) {
            $message = "Please type a playlist id";
        } else {
            $this->_getPlaylist($req, $yid);
        }
        Input::flash();
        $url = '/admin/playlist/add';
        if ($req->get("id")) {
            $url = '/admin/playlist/edit/' . $req->get("id");
        }
        return Redirect::to($url)
                        ->with('error', 'Username/Password Wrong')
                        ->withInput()
                        ->withMessage($message);
    }

    /**
     * get playlist from youtube playlist id
     * @param type $plId
     */
    private function _getPlaylist($req, $plId = "PLPSfPyOOcp3R9ZPLNjZkWxRy-BWCfNMrn") {
        $f = new \App\library\myFunctions();
        $html = $f->file_get_html("https://www.youtube.com/playlist?list=" . $plId);
        $title = $html->find("#pl-header h1.pl-header-title", 0)->plaintext;
        $playlist = Playlist::where('yid', $plId)->first();
        if (!$playlist) {
            $playlist = new Playlist();
            $playlist->yid = $plId;
            
        }

        $playlist->title = trim($title);
        
            if($req->status){
                $playlist->status = 1;
            }else{
                $playlist->status = 0;
            }
        $playlist->save();
        $this->_getVideos($html, $playlist);
    }

    /**
     * get all videos of a playlist
     * @param type $html        html of playlist
     * @param type $playlist    current playlist
     */
    private function _getVideos($html, $playlist) {
        $playlist->item_count = 0;

        $table = $html->find("#pl-video-table", 0);
        $videos = $table->find("tr");
        foreach ($videos as $video_html) {
            $video = null;
            $id_text = "data-video-ids";
            $yid = $video_html->find(".addto-watch-queue-play-now", 0)->$id_text;
            if ($yid) {
                $video = Video::where("yid", $yid)->first();
            } else {
                continue;
            }
            if (!$video)
                $video = new Video();
            $video->yid = $yid;
            $video->title = trim($video_html->find("a.pl-video-title-link", 0)->innertext);
            try{
            $video->time = $video_html->find(".timestamp span", 0)->plaintext;
            }  catch (Exception  $error){
                continue;
            }
            $video->thumb_url = "http://i1.ytimg.com/vi/" . $yid . "/hqdefault.jpg";

            $video->save();
            if ($video->id) {
                if (!$playlist->thumb_url)
                    $playlist->thumb_url = $video->thumb_url;
                $Pl = $video->playlists()->find($playlist->id);
                if (!$Pl)
                    $video->playlists()->attach($playlist->id);
                $playlist->item_count ++;
            }
        }

        $playlist->save();
    }

    /**
     * get edit profile
     * @return type
     */
    public function getProfile() {
        $user = Auth::user();
        return view('admin/users/profile', ['user' => $user]);
    }

    /**
     * save playlist
     */
    public function postProfile(Request $req) {
        $user = Auth::user();
        if (\Illuminate\Support\Facades\Hash::check($req->password, $user->password)) {
            \Illuminate\Support\Facades\Session::flash('success', 'Profile saved successfully!');
            $user->password = bcrypt($req->new_password);
            $user->save();
        } else {
            \Illuminate\Support\Facades\Session::flash('error', 'Incorrect password!');
        }

        return Redirect::to('/admin/profile');
    }

}