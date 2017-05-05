<html>
	<head>
		<title>Individual News Feed</title>
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			var feeds = {
                us: 'http://rss.cnn.com/rss/cnn_us.rss',
                world: 'http://rss.cnn.com/rss/cnn_world.rss',
                sports: 'http://www.espn.com/espn/rss/news',
                weather: 'http://www.rssweather.com/zipcode/14514/rss.php',
                tech: 'http://feeds.feedburner.com/crunchgear'
            }

			var feeds_selected = {
			  us: false,
			  world: false,
			  sports: false,
			  weather: false,
			  tech: false
			};
			var username;
			var password;
			var favorites = {owner: "", favorites: []};
			
			function getFeed(){
			    $.each(feeds, function(key,val) {		
					$.ajax({
						url: "loadRSS.php?rss_url=" + feeds[key] + "&feed=" + key,
						async: false,
						success: function (data) {
							$('#' + key).append(data);
							createFavButtons();
						}
					});
                });
				
			}
			
			function signup(){
				if (typeof(Storage) !== "undefined") {
					// Code for localStorage/sessionStorage.
					username = $('#username1')[0].value;
					password = $('#password1')[0].value;
					
					if(username !== "" & password !== ""){
						localStorage.setItem(username + "_name", username);
						localStorage.setItem(username + "_pwd", password);	
						$('#login').hide();
						$('#signup').hide();
						$('#reader').removeClass('hide');
						preparePage();
					}
					else{
						$('#error').html("Both fields are required");
					}
				}
				else {
					$('#error').html("Signup was invalid");
				}
			}
			
			function login(){
				if (typeof(Storage) !== "undefined") {
					username = $('#username2')[0].value;
					password = $('#password2')[0].value;
					var result1 = localStorage.getItem(username + "_name");
					var result2 = localStorage.getItem(username + "_pwd");	
					
					if(result1 !== null & result2 !== null){
						$('#login').hide();
						$('#signup').hide();
						$('#reader').removeClass('hide');
						preparePage();
						//createFavButtons();
					}
					else{
						$('#error').html("Login was invalid");
					}
				}
				else{
					$('#error').html("Login was invalid");
				}
			}
			
			function updateFav(){
				$('#fav_list ul').html("");
				$.each(favorites["favorites"] ,function(val){
 					$('#fav_list ul').append("<div class='row'>");
					$('#fav_list ul').append("<li class='col-md-10'>" + $('#' + favorites["favorites"][val])[0].innerHTML);
					$('#fav_list ul').append("<button class='btn-danger btn col-md-2' onclick=removeFavorite(" +  val +")>Remove</button></li>");
					$('#fav_list ul').append("</div>");		
				});
			}
			
			function addFavorite(link){
				favorites["favorites"][favorites["favorites"].length] = link.id;
				//Ensures that the favorite is only added once.
				function onlyUnique(value, index, self) { 
					return self.indexOf(value) === index;
				}
				favorites["favorites"] = favorites["favorites"].filter( onlyUnique );
				localStorage.setItem(username + "_favorites", JSON.stringify(favorites));
				updateFav();
			}
			
			function removeFavorite(link){
				favorites["favorites"].splice(link,1);
				localStorage.setItem(username + "_favorites", JSON.stringify(favorites));
				updateFav();
			}
			
			
			function createFavButtons(){
				var fav_btn = "<button ></button>";
				$.each($('.fav_box'),function(val){
					var id = this.parentNode.parentNode.id;
					fav_btn = "<button class='fav_btn btn btn-success' onclick=addFavorite(" + id + ")>Favorite</button>";
					this.innerHTML = fav_btn;
				});
			}
			
			function setCookie(cname,exdays) {
				var d = new Date();
				d.setTime(d.getTime() + (exdays*24*60*60*1000));
				var expires = "expires=" + d.toGMTString();
				var visited = new Date();
				//mm-dd-yyyy HH:mm:ss
				visited = visited.getMonth()+1 + "-" + visited.getDate() + "-" + visited.getFullYear() + " " + visited.getHours() + ":" + visited.getMinutes() + ":" + visited.getSeconds();
				document.cookie = cname + "=" + visited + ";" + expires + ";path=/";
			}

			function getCookie(cname) {
				var name = cname + "=";
				var ca = document.cookie.split(';');
				for(var i = 0; i < ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					if (c.indexOf(name) == 0) {
						return c.substring(name.length, c.length);
					}
				}
				return "";
			}
			
			function saveUser(){
				var userObject = { 'username': $('#username')[0].value, 'password': $('#password')[0].value };

				$.ajax({
				  type: 'POST',
				  url: "save.php",//url of receiver file on server
				  data: {data: JSON.stringify(userObject)}, //your data
				});
				
			}
			
			function preparePage(){
				var last_visited = getCookie(username + "_last");
				var message;
				if(last_visited == ""){
					message = "Welcome, this is your first time visiting this page.";
				}
				else{
					message = "Your last visit was on " + last_visited + ".";
				}
				$('#welcome').append(message);
				setCookie(username + "_last", 30);
				getFeed();
				
				if (typeof(Storage) !== "undefined") {
					var fav = localStorage.getItem(username + "_favorites");
					
					if(fav !== null & fav !== ""){
						favorites = JSON.parse(fav);
					}
					else{
						favorites["owner"] = username;
						
						localStorage.setItem(username + "_favorites", JSON.stringify(favorites));
					}
				}
				updateFav();
			}
			window.onload = function(){
			}
			
			$( document ).ready(function() {
			
				//Code that handles the hidding of feeds.
				$("#rss_select").click(function(e) {
					e.currentTarget.childNodes.forEach(function(val){
						if(val.checked == false && val.value != "all"){
							feeds_selected[val.value] = false;
							$("input[value='all']")[0].checked = false;
						}
						else if (val.checked == true && val.value != "all"){
							feeds_selected[val.value] = true;
						}
					});
					e.currentTarget.childNodes.forEach(function(val){
						if(val.type == "checkbox"){
							if(feeds_selected[val.value] == false){
								//$("#" + val.value).removeClass("show");
								//$("#" + val.value).addClass("hide");
								$("." + val.value).removeClass("show");
								$("." + val.value).addClass("hide");
							}
							else{
								//$("#" + val.value).removeClass("hide");
								//$("#" + val.value).addClass("show");
								$("." + val.value).removeClass("hide");
								$("." + val.value).addClass("show");
							}
						}
					});
				});
				$("input[value='all']").change(function(e) {
					if(e.currentTarget.checked == false){
						for(var i = 0; i <= 4; i++){
							$("input[type='checkbox']")[i].checked = true;
						}
						$(".feed").removeClass("hide");
						$(".item").removeClass("hide");
						$(".feed").addClass("show");
						$(".item").addClass("show");
					}
				});
			});	
		</script>
	</head>
	<body>
		<div class="container-fluid" id="login">
			<div class="row">
				<div class="col-md-12 col-sm-12 text-center">
					<h1>Individual News Feed</h1>
					<h3 id="error" class="text-warning"></h3>
				</div>
			</div>
			<div class="row">
				<form id="signup" class="col-md-6">
					<h3>Sign Up</h3>
					<div class="form-group">
						<label>Username: <input type="text" id="username1"></label>
					</div>
					<div class="form-group">
						<label>Password: <input type="password" id="password1"></label>
					</div>
					<button type="button" id="signup_btn" class="btn btn-default" onclick=signup()>Sign Up</button>
				</form>
				<form class="col-md-6">
					<h3>Login</h3>
					<div class="form-group">
						<label>Username: <input type="text" id="username2"></label>
					</div>
					<div class="form-group">
						<label>Password: <input type="password" id="password2"></label>
					</div>
					<button type="button" id="login_btn" class="btn btn-default" onclick=login()>Login</button>
				</form>
			</div>
		</div>

		<div id="reader" class="container-fluid hide">
			<div class="row">
				<div class="col-md-12 col-sm-12 text-center">
					<h1>Individual News Feed</h1>
					<h4 id="welcome"></h4>
				</div>
			</div>
			<div id="rss_select" class="row">
			<div class="col-md-12 col-sm-12 text-center">
					<input type="checkbox" class='checkbox-inline' value="all" checked><label>All</label>
					<input type="checkbox" class='checkbox-inline' value="us" checked><label>US News</label>
					<input type="checkbox" class='checkbox-inline' value="world" checked><label>World News</label>
					<input type="checkbox" class='checkbox-inline' value="sports" checked><label>Sports</label>
					<input type="checkbox" class='checkbox-inline' value="weather"checked><label>Weather</label>
					<input type="checkbox" class='checkbox-inline' value="tech" checked><label>Technology</label>
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div id="favorite" class="col-md-6">
					<h2>Favorites List</h2>
					<div id="fav_list"><ul></ul></div>
				</div>
				<div id="rssReader" class="col-md-6">
					<div id="us" class="us feed">
						<h2>US News</h2>
					</div> 
					<div id="world" class="world feed">
						<h2>World News</h2>	
					</div> 
					<div id="sports" class="sports feed">
						<h2>Sports</h2>
					</div> 
					<div id="weather" class="weather feed">
						<h2>Weather</h2>	
					</div> 
					<div id="tech" class="tech feed">
						<h2>Technology</h2>
					</div> 
				</div>
			</div>
		</div>
	</body>
</html>
