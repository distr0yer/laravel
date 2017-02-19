<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Redirect;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getIndex(){
      return view('index');
    }

    public function getMath(){
      $nums = collect([13, 24, 91, 120, 41, 76, 91, 46, 71, 101, 259, 12, 41, 28, 73, 33, 58]);

      //sorting for median
      $sorted = $nums->sort();
      $length =  $sorted->count();
      $mean = $sorted->avg();
      $median = $sorted->slice(floor(($sorted->count() / 2)), 1)->values()[0];

      $map = [];
      $stdDevis = collect();

      foreach($sorted->toArray() as $value){
        // counting the number of occurences to ascertain the mode
        // and finding the standard deviation for each value ln: 46
        if(array_key_exists($value, $map)){
          $map[$value] = $map[$value] + 1;
        }
        else{
          $map[$value] = 1;
        }

        $stdDevis->push(pow(($value - $mean), 2));
      };

      $deviation = pow(($stdDevis->sum() / $length), 0.5);

      $max = 0;

      // Evaluating which number has the most occurences
      foreach($map as $key => $value){
        if($value > $max){
          $max = $value;
        }
      }

      // Querying array to account for multiple modes
      $mode = array_keys($map, $max);

      $math = [
        'mean' => $mean,
        'median' => $median,
        'mode' => $mode,
        'standardDeviation' => $deviation
      ];

      return $math;

    }

    public function getRepos(Request $request){
      $client = new Client();
      $reqRepos = $client->get('https://api.github.com/user/repos?access_token=' . $request->session()->get('access_token'));
      // getting a 404 here $reqIssues = $client->get('https://api.github.com/issues?access_token=' . $request->session()->get('access_token'));
      $repoObjects = json_decode($reqRepos->getBody()->getContents());
      // 404 error above $issueObjects = json_encode($reqIssues->getBody()->getContents());
      $repos = collect();
      foreach($repoObjects as $repo){
        $repos->push($repo->full_name);
      }

      //get issues

      return $repos;
    }

    public function getCallback(Request $request){

      $client = new Client();
      // Sending client information to github for access token_get
      // A redirection to this route is performed after authorizing the application.
      $req = $client->post('https://github.com/login/oauth/access_token', [ 'json' => [
        'client_id' => $_ENV['GIT_CLIENT_ID'],
        'client_secret' => $_ENV['GIT_CLIENT_SECRET'],
        'code' => $request->code
      ]]);

      parse_str($req->getBody()->getContents(), $query);
      $request->session()->put('access_token', $query['access_token']);
      return Redirect::route('repos');

    }

    public function getGit(){
      return view('git')->with('client_id', $_ENV['GIT_CLIENT_ID']);
    }

    public function getLoops(){
      $objects = [];
      for($i = 0; $i < 1000000; $i++){
        $objects[$i] = "";
      }

      $time = microtime(true);
      for($i = 0; $i < 1000000; $i++){
        echo "";
      }
      $time_for = microtime(true) - $time;

      $time = microtime(true);
      foreach($objects as $object){
        echo $object;
      }
      $time_foreach = microtime(true) - $time;

      $time = microtime(true);
      while($i < 1000000){
        echo "";
        $i++;
      }
      $time_while = microtime(true) - $time;

      return "time for for loop: " . $time_for . " <br/>time for foreach: ". $time_foreach ."<br/>time for while loop: " . $time_while;
    }

}
