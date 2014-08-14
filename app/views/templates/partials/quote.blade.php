<div class='quote'>
	<a name='{{ $quote->id }}'></a>
	<a href='{{ URL::route('user.profile', [$quote->user->username]) }}' title="View {{ $quote->user->username }}'s profile">
		<img class='avatar' src='{{ $quote->user->avatar }}' />
	</a>
	<span class='title'>
		<a href='{{ URL::route('quote', [$quote->id]) }}'>#{{ $quote->id }}</a>
		{{ $quote->title }}
		@if($quote->isFavored())
			<a href='?unfavor={{ $quote->id }}#{{ $quote->id }}' title='Remove Favorite'><i class='fa fa-star favorite'></i></a>
		@else
			<a href='?favor={{ $quote->id }}#{{ $quote->id }}' title='Make Favorite'><i class='fa fa-star-o favorite'></i></a>
		@endif
	</span>
	<br />
	<span class='poster'>Posted by <a href='{{ URL::route('user.profile', [$quote->user->username]) }}'>{{ $quote->user->username }}</a></span>
	<br />
	<em>{{ $quote->created_at }}</em>
	<div class='votes'>
		<a href='?upvote={{ $quote->id }}#{{ $quote->id }}' class='upvotes'><i class='fa fa-arrow-up'></i>{{ $quote->upVotes() }}</a>
		&nbsp;|&nbsp;
		<a href='?downvote={{ $quote->id }}#{{ $quote->id }}' class='downvotes'>{{ $quote->downVotes() }}<i class='fa fa-arrow-down'></i></a>
		@if($quote->didAuthVote())
		&nbsp;|&nbsp;
		<a href='?unvote={{ $quote->id }}#{{ $quote->id }}' class='unvote' title='Remove Vote'><i class='fa fa-eraser'></i></a>
		@endif
	</div>
	@if(Auth::check() && Auth::user()->can(['quote.approve', 'quote.deny']) && $quote->status == 0)
	<div class='moderate'>
		Moderator Tools
		&nbsp;|&nbsp;
		<a href='?approve={{ $quote->id }}#{{ $quote->id }}' class='Approve' title='approve'><i class='fa fa-thumbs-up'></i></a>
		&nbsp;|&nbsp;
		<a href='?deny={{ $quote->id }}#{{ $quote->id }}' class='deny' title='Deny'><i class='fa fa-thumbs-down'></i></a>
	</div>
	@endif
	<pre class='quotetext clear' onfocus='copyClipboard(this);'>{{ $quote->quote }}</pre>
</div>