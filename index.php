<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Artist Info</title>

    <!-- Bootstrap core JavaScript
   ================================================== -->
   <!-- Placed at the end of the document so the pages load faster -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
   <script src="js/bootstrap.js"></script>

    <!-- Bootstrap core CSS -->
     <link href="css/bootstrap.css" rel="stylesheet">


    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/accordion.js"></script>

  </head>

  <header>

            <nav class="navbar navbar-default">
        <div class="container">
          <div class="navbar-header">
            <a class="navbar-brand" href="index.php">Artist Social Media</a>
          </div>
        </div>
        </nav>

  </header>



  <body>

<div id="center-all">
<?php
session_start();
require_once __DIR__ . '/src/Facebook/autoload.php';



$fb = new Facebook\Facebook([
  'app_id' => '1635400263439302',
  'app_secret' => '1df84dc96f09f0e5077bac583ad7974e',
  'default_graph_version' => 'v2.7',
]);

$helper = $fb->getRedirectLoginHelper();

define('APP_URL', 'http://chart-challenge-tobisi.c9users.io/chartmetric/');



$permissions = ['user_posts', 'user_photos', 'user_videos', 'user_likes', 'public_profile']; // optional



    try {
              	if (isset($_SESSION['facebook_access_token'])) {
              		$accessToken = $_SESSION['facebook_access_token'];
              	} else {
                		$accessToken = $helper->getAccessToken();
              	}
          } catch(Facebook\Exceptions\FacebookResponseException $e) {
           	// When Graph returns an error
           	echo 'Graph returned an error: ' . $e->getMessage();
            	exit;
          } catch(Facebook\Exceptions\FacebookSDKException $e) {
           	// When validation fails or other local issues
          	echo 'Facebook SDK returned an error: ' . $e->getMessage();
            	exit;
           }
          if (isset($accessToken)) {
          	if (isset($_SESSION['facebook_access_token'])) {
          		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
          	} else {
          		// getting short-lived access token
          		$_SESSION['facebook_access_token'] = (string) $accessToken;
          	  	// OAuth 2.0 client handler
          		$oAuth2Client = $fb->getOAuth2Client();
          		// Exchanges a short-lived access token for a long-lived one
          		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
          		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
          		// setting default access token to be used in script
          		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
          	}
          	// redirect the user back to the same page if it has "code" GET variable
          	if (isset($_GET['code'])) {
          		header('Location: ./');
          	}
          	// validating user access token
          	try {
                	$user = $fb->get('/me');
                	$user = $user->getGraphNode()->asArray();
                	 } catch(Facebook\Exceptions\FacebookResponseException $e) {
                		// When Graph returns an error
                		  echo 'Graph returned an error: ' . $e->getMessage();
                		    session_destroy();
                		// if access token is invalid or expired you can simply redirect to login page using header() function
                		exit;
                	     } catch(Facebook\Exceptions\FacebookSDKException $e) {
                		       // When validation fails or other local issues
                		        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                		          exit;
                	}


          	// getting json data of Artist
          	$getPosts = $fb->get('182736663312?fields=name,posts.limit(10){message,likes.limit(0),comments.limit(5),reactions.limit(500),full_picture,link}');
          	$getPosts = $getPosts->getGraphNode()->asArray();

              // Artist Title
              $artist = $getPosts['name'];
             echo  "<div id='title-container'>
              <h1>$artist</h1><br> <br>
              </div>";


              // get Data from the Json
              for ($x = 0; $x <=9; $x++){


                // get artist image
                $image = $getPosts['posts'][$x]['full_picture'];
                $link = $getPosts['posts'][$x]['link'];
                echo "<a href='$link'><img src='$image' height='400px' width='480px'></a>" . '<br>' . '<br>';

                // get artist message
                // echo "<h2 class='title-post'>Post</h2>";
                $message = $getPosts['posts'][$x]['message'];
                echo "<div class='artist-message'> $message <br><br> </div>";



                    // get amount of likes
                    // $likes = $getPosts['posts'][$x]['likes'];
                    // $count_likes = count($likes);
                    // // echo "<h4>Likes:$count_likes</h4>" . '<br>' . '<br>';
                    // echo "$count_likes<img src='https://mir-s3-cdn-cf.behance.net/project_modules/disp/e4299734559659.56d57de04bda4.gif' width='50px'>" . '<br>';
                    //


                    // get amount of reactions
                    // echo "<h4>Reactions:</h4>";
                    $likes = 0;
                    $love = 0;
                    $angry = 0;
                    $wow = 0;
                    $sad = 0;
                    $haha = 0;
                    $reactions = $getPosts['posts'][$x]['reactions'];
                    $count_reactions = count($reactions);
                for($g = 0; $g <= ($count_reactions - 1 ); $g++){
                      // $reactions = $getPosts['posts'][$x]['reactions'][$g]['name'] . ': ';
                      // echo $reactions;
                            $reactions_type = $getPosts['posts'][$x]['reactions'][$g]['type'];

                            if ($reactions_type == 'LIKE'){
                              $likes++;
                            } elseif ($reactions_type == 'LOVE') {
                              $love++;
                            } elseif ($reactions_type == 'ANGRY'){
                              $angry++;
                            } elseif ($reactions_type == 'WOW'){
                              $wow++;
                            } elseif ($reactions_type == 'SAD'){
                              $sad++;
                            } elseif ($reactions_type == 'HAHA'){
                              $haha++;
                            }

                          }

                          //printing reaction counts
                          echo "$likes <img src='https://mir-s3-cdn-cf.behance.net/project_modules/disp/e4299734559659.56d57de04bda4.gif' width='50px'>";
                          echo "$love <img src='https://mir-s3-cdn-cf.behance.net/project_modules/disp/65ea2034559659.56d57de06cea2.gif'width='40px'> ";
                          echo "$sad <img src='http://cdn.makeuseof.com/wp-content/uploads/2015/06/21_emoji.png?b34c28'width='33px'> ";
                          echo "$wow <img src='http://createdigital.com/~/media/Blog/2016/Facebook-Reactions-Wow.ashx?la=en&hash=DEF46B75298DECB2DF35DD5F0DA2522B62C1329B'width='35px'> ";
                          echo "$angry <img src='https://mir-s3-cdn-cf.behance.net/project_modules/disp/e66e6e34559659.56d57de095aee.gif'width='44px'> ";
                          echo "$haha <img src='http://emojipedia-us.s3.amazonaws.com/cache/79/40/7940b441b930e991eb1137e18b310fac.png'width='33px'> ";

                          echo  '<br>' . '<br>';


                        // get user Comments
                        // echo "<h4>Comments:</h4>";
                        // for($i = 0; $i <=4; $i++){
                        //   $from = $getPosts['posts'][$x]['comments'][$i]['from']['name'];
                        //   $user_message = $getPosts['posts'][$x]['comments'][$i]['message'];
                        //     echo "<div class='user-message'><strong>$from</strong>: <i>$user_message</i></div>" . '<br>' . '<br>';
                        //
                        // //     // echo "<div class='user-message'>$user_message</div>" . '<br>' . '<br>';
                        // //
                        // }
                        //

                        //  COMMENTS JQUERY ACCORDION
                        $comments = $getPosts['posts'][$x]['comments'];
                        $comments_count = count($comments);
                        echo "<div id='container'>
                              <ul class='faq'>
                                <li class='q'><a>Display last $comments_count comments</a></li>
                                <li class='a'>";
                                for($i = 0; $i <=4; $i++){
                                  $from = $getPosts['posts'][$x]['comments'][$i]['from']['name'];
                                  $user_message = $getPosts['posts'][$x]['comments'][$i]['message'];
                                   echo "<div class='user-message'><strong>$from</strong>: <i>$user_message</i></div>" . '<br>' . '<br>';
                                  }
                        echo  "</li>
                              </ul>
                            </div>";



                        echo '<hr>';

                }


            	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
          } else {
          	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
            $loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
          	echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
          }


 ?>
    </div>



    </body>



      <footer>
        <p>Copyright &copy; 2016, All Rights Reserved</p>
      </footer>


</html>
