</div>
<div id='footer' class='clear'>
	<div class='col-2'>
		<div id='trademark'>{{ Config::get('settings.copyright') }}</div>
	</div>
	<div class='col-2'>
		<ul class='nav'>
			<li><a href='https://twitter.com/Quotinator'><i class='fa fa-twitter'></i></a></li>
			<li><a href='https://plus.google.com/109145709302907172859' rel='publisher'><i class='fa fa-google-plus'></i></a></li>
			<li><a href='{{ URL::route('users') }}'>Users</a></li>
			<li><a href='https://github.com/Quotinator/Quotinator/issues'>Bugs</a></li>
			<li><a href='{{ URL::route('about') }}'>About</a></li>
			<li><a class='button' href='{{ URL::route('help') }}'>Help&nbsp;<span class="fa fa-book"></span>&nbsp;</a></li>
		</ul>
	</div>
	<div class='clear full-width'>
		<a target="_BLANK" href="https://www.digitalocean.com/?refcode=7426671a37b0">
			<img class='DO' alt='Proudly Hosted by Digital Ocean' src='{{ asset('img/DO_Proudly_Hosted_Badge_White-0f0151a4.png') }}' />
		</a>
	</div>
</div>
</div>
</body>
</html>
