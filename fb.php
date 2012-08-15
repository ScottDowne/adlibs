<!doctype html> <?php if ($_SERVER["SERVER_NAME"] == 'localhost') { $FB_APP_ID = '461888813829980'; } if ($_SERVER["SERVER_NAME"] == 'ocupopdev.com') { $FB_APP_ID = '331797950244138'; } ?>
<html lang="en">
<head>
<meta charset="utf-8">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Facebook Test</title>
<style>
body
{
  font-family: 'Helvetica Neue';
  font-size: 16px;
  width: 900px;
}

h1
{
  margin: 0;
}

th
{
  text-align: left;
  padding-right: 2em;
}

.fb-login-button
{
  position: absolute !important;
  top: 15px;
  right: 15px;
}

.container
{
  background: #eee;
  width: 800px;
  height: 450px;
  overflow: hidden;
  position: relative;
}

#start strong
{
  cursor: pointer;
  display: block;
  font-size: 36px;
  font-weight: bold;
  padding: 2em;
}

/* *** *** *** *** *** *** *** *** *** */

.choice
{
  position: absolute;
  left: 800px;
  top: 0;
  -webkit-transition: all .2s ease-in-out;
     -moz-transition: all .2s ease-in-out;
       -o-transition: all .2s ease-in-out;
          transition: all .2s ease-in-out;
}

.choice.complete
{
  left: -800px;
}

.choice.current
{
  left: 0;
}

.choice
{
  padding: 0 25px;
  width: 750px;
}

.choice .answer,
.choice .chosen,
.choice .education
{
  display: none;
}

.choice .question
{
  background: #ccc;
}

.choice .choices
{
  background: #ccc;
  height: 350px;
}

.choice .choices ul
{
  margin: 0;
  padding: 0;
}

.choice .answer
{
  background: #ccc;
}

.choice .chosen
{
  background: #ccc;
}

.choice .education
{
  background: #ccc;
}

/* *** *** *** *** *** *** *** *** *** */

#info,
#photos
{
  margin: 2em 0;
}

#photos ul
{
  list-style: none;
  margin: 0;
  padding: 0;
}

#photos ul li
{
  float: left;
  width: 130px;
  height: 130px;
  padding: 10px;
}

#photos img
{
  cursor: pointer !important;
  opacity: .85;
  -webkit-transition: all .2s ease-in-out;
  -moz-transition: all .2s ease-in-out;
  -o-transition: all .2s ease-in-out;
  transition: all .2s ease-in-out;
}

#photos img.unselected
{
  opacity: .5;
}

#photos img:hover,
#photos img.selected
{
  opacity: 1;
}

.clear
{
  clear: both;
}
</style>
</head>
<body>

<div id="fb-root"></div>
<script>
// Initialize Facebook SDK.
window.fbAsyncInit = function() {
  FB.init({
    appId      : '<?php echo $FB_APP_ID; ?>', // App ID
    channelUrl : 'channel.php',               // Channel File
    status     : true,                        // Check login status.
    cookie     : true,                        // Enable cookies to allow the server to access the session.
    xfbml      : true                         // Parse XFBML.
  });

  FB.Event.subscribe('auth.authResponseChange', checkFacebookLoginStatus);

  // Check login.
  function checkFacebookLoginStatus(response) {
    if (response.status === 'connected')
    {
      // User is logged in to Facebook and has authenticated our app.
      var uid = response.authResponse.userID;
      var accessToken = response.authResponse.accessToken;

      // Hide button.
      $('.fb-login-button').hide();

      // Say hello.
      FB.api('/me', function(response) {
        $('h1 strong').html(', ' + response.first_name + '!');
      });
    }
    else if (response.status === 'not_authorized')
    {
      $('.fb-login-button').show();
    }
    else
    {
      $('.fb-login-button').show();
    }
  }
}

function getFacebookData()
{
  // Basic information
  FB.api('/me', function(response) {

    // Copy basic user information into an object.
    var user_information = new Object();
    user_information.uid = response.id;
    user_information.name = response.name;
    user_information.first_name = response.first_name;
    user_information.last_name = response.last_name;
    user_information.birthday = response.birthday;

    // Omit the state name from the hometown string.
    user_information.hometown = response.hometown.name.substr(0, response.hometown.name.indexOf(','));

    // Get work information if it's available.
    // TODO: This should be an array to choose from, not just the first one we find.
    if (typeof(response.work) !== 'undefined') {
      if (typeof(response.work[0].position) !== 'undefined')
        user_information.work_position = response.work[0].position.name;

      if (typeof(response.work[0].employer) !== 'undefined')
        user_information.work_name = response.work[0].employer.name;

      if (typeof(response.work[0].start_date) !== 'undefined' && typeof(response.work[0].end_date) !== 'undefined')
        user_information.work_years = response.work[0].start_date.substr(0, 4) + ' to ' + response.work[0].end_date.substr(0, 4);
      else if (typeof(response.work[0].start_date) !== 'undefined')
        user_information.work_years = response.work[0].start_date.substr(0, 4);
    }

    // Get school information if it's available.
    // TODO: This should be an array to choose from, not just the first one we find.
    if (typeof(response.education) !== 'undefined') {
      user_information.school_name = response.education[0].school.name;
      user_information.school_year = response.education[0].year.name;
    } else {
    // If not, call it 'School of Hard Knocks' and add 18 years to their birthday.
      user_information.school_name = 'The School of Hard Knocks';
      user_information.school_year = parseInt(response.birthday.substr(6, 4)) + 18;
    }

    // Output all variables.
    var info_output = '<table>';
    for (property in user_information) {
      info_output += '<tr><th>' + property + '</th><td>' + user_information[property]+'</td></tr>';
    }
    info_output += '</table>';
    $('#info').html(info_output);

  });

  // Statuses
  FB.api('/me/posts', function(response) {
    // console.log(response);
  });

  // Likes
  FB.api('/me/likes', function(response) {
    // console.log(response);
  });

  // Achievements
  FB.api('/me/achievements', function(response) {
    // console.log(response);
  });

  // Photos
  FB.api('/me/photos', function(response) {
		if (response.data && response.data[0].images) {
			for (i = 0; i <= 25; i++) {
				if (response.data[i] && response.data[i].images[2]) {
				  console.log('Yay' + i);
					$('#photos ul').append( '<li><img src="' + response.data[i].images[6].source + '" id="' + response.data[i].id + '"></li>' );
				}
			}
		}
    // console.log(response);

		// Photo Chooser
    $('#photos img').click(function() {
      //
      if ($(this).hasClass('selected'))
      {
        $('#photos img').removeClass('selected');
        $('#photos img').removeClass('unselected');
      } else {
        $('#photos img').removeClass('selected');
        $('#photos img').addClass('unselected');
        $(this).removeClass('unselected').addClass('selected');
        getSelectedPhoto($(this).attr('id'));
      }
    });

    function getSelectedPhoto(photoID) {
      FB.api('http://graph.facebook.com/' + photoID, function(response) {
        if (response.images) {
          $('#selected_photo').html('<img src="' + response.images[1].source + '">');
        }
      });
    }
  });
}

// Load the SDK asynchronously.
(function(d){
  var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement('script'); js.id = id; js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js";
  ref.parentNode.insertBefore(js, ref);
}(document));
</script>

<script>
$(document).ready(function(){

  // Step through.

  function nextChoice(choice)
  {
    choice.addClass('complete').removeClass('current');
    choice.next('.choice').addClass('current');
  }

  function previousChoice(choice)
  {
    choice.removeClass('complete current');
    choice.prev('.choice').addClass('current');
  }

  $('.next').click(function() {
    choice = $(this).parent('.choice');
    nextChoice(choice);
  });

  $('.previous').click(function() {
    choice = $(this).parent('.choice');
    previousChoice(choice);
  });

});
</script>

<div class="fb-login-button" scope="user_about_me,
                                    user_activities,
                                    user_birthday,
                                    user_education_history,
                                    user_groups,
                                    user_hometown,
                                    user_interests,
                                    user_photos,
                                    user_likes,
                                    user_status,
                                    user_work_history">
  Login with Facebook
</div>

<h1>Hello<strong>&hellip;</strong></h1>

<div class="container">

  <div id="start" class="choice current">
    <strong class="next">START</strong>
  </div>

  <div id="choice1" class="choice">
    <h2 class="question">Pick a photo of your family!</h2>
    <div class="choices photos">
      <ul>
      </ul>
    </div>
    <h2 class="answer">Your family photo:</h2>
    <div class="chosen"></div>
    <div class="education">
      <p>Sepia-toned or black-and-white photos from the past can humanize a candidate&rsquo;s appeal.</p>
      <p><a href="http://www.youtube.com/watch?v=rPSJJwZUmik">Watch Gerald Ford&rsquo;s 1976 montage of sepia-toned photos.</a></p>
    </div>
  </div>

</div>

</body>
</html>

<script>
// Out of sight, out of mind.

// Albums
//   for (var i = 0; i < response.data.length; i++) {
//   var album = response.data[i];
//   if (album.name == 'Profile Pictures')
//   {
//     FB.api('/'+album.id+'/photos', function(photos){
//       if (photos && photos.data && photos.data.length){
//         for (var j=0; j<photos.data.length; j++){
//           var photo = photos.data[j];
//           // photo.picture contain the link to picture
//           var image = document.createElement('img');
//           image.src = photo.picture;
//           document.body.appendChild(img);
//         }
//       }
//     });
//
//     break;
//   }
// }
</script>