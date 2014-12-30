<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show Google Calendar Events on your site
 * 
 * @author  	Douglas Burchard
 * @package		KcsaraTwo\Widgets
 */
class Widget_EventList extends Widgets
{


	/**
	 * The widget title
	 *
	 * @var array
	 */
	public $title = 'Event List';

	/**
	 * The translations for the widget description
	 *
	 * @var array
	 */
	public $description = array(
		'en' => 'Display a list of events from Google Calendar',
	);
	
	/**
	 * The author of the widget
	 *
	 * @var string
	 */
	public $author = 'Douglas Burchard';

	/**
	 * The author's website.
	 * 
	 * @var string 
	 */
	public $website = 'http://www.douglasburchard.com/';

	/**
	 * The version of the widget
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The fields for customizing the options of the widget.
	 *
	 * @var array 
	 */
	public $fields = array(
		array(
			'field' => 'google_api_key',
			'label' => 'Google API Key',
			'rules' => 'required'
		)
	);

	/**
	 * The main function of the widget.
	 *
	 * @param array $options The options for displaying Google Calendar events.
	 * @return array 
	 */
	public function run($options)
	{
		if (empty($options['google_api_key']))
		{
			return array('output' => 'No API key provided.');
		}

		$this->load->library('google_api');

		/*
		 * TODO: We need this to be a module rather than a widget, to allow a 
		 *    scheduled event, to call a password protected URL, to refresh the data.
		 */

		/*
		 * 2. Use `calendar.calendarList.list` to read the list of calendars. 
		 *    We only need the IDs, so limit returned list with `fields`=`items/id`.
		 * 
		 *    TODO: Move the `key`-string to the widget's/module's options.
		 * 
		 *    TODO: Use Google's client APIs to gather all active calendars.
		 */
		$client = new Google_Client();
		$client->setApplicationName("KCSARA_EventList");
		// $client->setDeveloperKey('key');
		// $service = new Google_CalendarService($client);

		$calendars = array('webmaster@kcsara.org');

		/* 
		 * 3. For each found calendar, use `calendar.events.list` to read the 
		 *    list of events. Use `orderBy`=`startTime` and `singleEvents`=`true`.
		 *    Set `timeMin` to today's date in the format `2016-01-01T1:00:00-08:00` 
		 *    to limit the returned list to future events. Set `timeMax` to one
		 *    year from today's date. Retrict the returned fields to `items(description,
		 *    end,etag,htmlLink,id,location,start,summary)`.
		 */
		$query = array(
			'maxResults'=>'3',
			'orderBy'=>'startTime',
			'singleEvents'=>'true',
			'timeMax'=>'2016-01-01T1:00:00-08:00',
			'timeMin'=>'2015-01-01T1:00:00-08:00',
			'fields'=>'items(description,end,etag,htmlLink,id,location,start,summary)',
			'key'=>$options['google_api_key'],
		);
		$results = '';
		foreach( $calendars as $cal ) {

			$uri = 'https://www.googleapis.com/calendar/v3/calendars/'.$cal.'/events';

			/*
			 * TODO: Convert json response to a PHP array, and merge with previously returned results.
			 */
			$results = readFile( $uri . '?' . http_build_query( $query ));
		}

		/* 
		 * 4. Store the events in a table, updating where `id` or `etag` are duplicates.
		 */
		return array( 'output' => $results );
	}

}