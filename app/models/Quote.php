<?php

class Quote extends Eloquent {
	protected $fillable = [];
	/**
	 * Adding customs attributes to the object
	 * @var array
	 */
	protected $appends = array('has_favorites', 'total_favorites', 'has_comments', 'total_comments', 'is_favorite_for_current_user');

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rulesAdd = [
		'content' => 'required|min:50|max:300|unique:quotes,content',
	];

	public static $colors = [
		'#27ae60', '#16a085', '#d35400', '#e74c3c', '#8e44ad', '#F9690E', '#2c3e50', '#f1c40f', '#65C6BB', '#E08283'
	];

	public static function getRandomColors()
	{
		if (Session::has('quotesColors') AND !empty(Session::get('quotesColors')))
			return Session::get('quotesColors');
		else {
			$colors = self::$colors;
			shuffle($colors);

			Session::put('quotesColors', $colors);

			return $colors;
		}
	}

	public function textTweet()
	{
		$content = $this->content;
		$maxLength = 118;
		$twitterUsername = Lang::get('layout.twitterUsername');
		$maxLengthAddTwitterUsername = $maxLength - strlen($twitterUsername);

		if (strlen($content) > $maxLength)  {
			$content = substr($content, 0, $maxLength);
			$lastSpace = strrpos($content, " "); 

			// After the space, add ..
			$content = substr($content, 0, $lastSpace).'..';
		}
		elseif (strlen($content) <= $maxLengthAddTwitterUsername) {
			$content .= ' '.$twitterUsername;
		}
		
		return $content;
	}

	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function favorites()
	{
		return $this->hasMany('FavoriteQuote');
	}


	public function getTotalCommentsAttribute()
	{
		return $this->hasMany('Comment')->count();    
	}

	public function getHasFavoritesAttribute()
	{
		return ($this->total_favorites > 0);
	}

	public function getTotalFavoritesAttribute()
	{
		return $this->hasMany('FavoriteQuote')->count();    
	}

	public function getHasCommentsAttribute()
	{
		return ($this->total_comments > 0);
	}

	public function getIsFavoriteForCurrentUserAttribute()
	{
		if (Auth::check()) {
			$countFavorite = FavoriteQuote::where('quote_id', '=', $this->id)->where('user_id', '=', Auth::user()->id)->count();

			if ($countFavorite === 0)
				return false;
			else
				return true;
		}
		else
			return false;
	}

	public function scopeWaiting($query)
	{
		return $query->where('approved', '=', '0');
	}

	public function scopeRefused($query)
	{
		return $query->where('approved', '=', '-1');
	}

	public function scopePending($query)
	{
		return $query->where('approved', '=', '1');
	}

	public function scopePublished($query)
	{
		return $query->where('approved', '=', '2');
	}

	/**
	 * Lighten or darken a color from an hexadecimal code
	 * @author http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
	 * @param string $hex The color in hexadecimal
	 * @param int $steps Steps should be between -255 and 255. Negative = darker, positive = lighter
	 * @return string The computed hexadecimal color
	 */
	public static function adjustBrightness($hex, $steps)
	{
		$steps = max(-255, min(255, $steps));

    	// Format the hex color string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

    	// Get decimal values
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

    	// Adjust number of steps and keep it inside 0 to 255
		$r = max(0, min(255,$r + $steps));
		$g = max(0, min(255,$g + $steps));  
		$b = max(0, min(255,$b + $steps));

		$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

		return '#'.$r_hex.$g_hex.$b_hex;
	}

}