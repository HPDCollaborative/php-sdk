# HPD Collaborative PHP SDK

The PHP SDK is designed to allow for HPD Collaborative API Partners to easily integrate OAuth authentication used by our API into their PHP applications.

**YOU MUST BE AN EXISTING API PARTNER TO USE THE API**

Once you've received access to the API as a Partner, you can then begin integrating OAuth authentication and token access with this SDK, or you can roll your own using the SDK as a guide.

## Installation

The SDK uses composer to manage dependencies so you'll need to ensure you have a working knowledge of composer to install and integrate it into your application.

To install the SDK run:

```bash
composer require hpdc/sdk
```

This will download the SDK into your vendor directory and set up autoloading with the PSR-4 autoloading convention.

## Usage

Below is an example of integrating the Authentication SDK into a given controller. This example is a Laravel Controller, but integrating into the framework of your choice should be very similar.

The example is documented on each method, and keep in mind the SDK requires PHP > 7.0 so you may or may not be able to type hint classes automatically depending on your framework.

```php
<?php

namespace App\Http\Controllers;

use Hpdc\Authentication;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Authentication client.
     * 
     * @var \Hpdc\Authentication
     */
    protected $client;

    /**
     * Instantiate the controller.
     * 
     * @param \Hpdc\Authentication $auth
     */
    public function __construct(Authentication $auth)
    {
    	// Set your local client property.
    	$this->client = $auth;

    	/*
    	|--------------------------------------------------------------------------
    	| Set Required Configuration Variables
    	|--------------------------------------------------------------------------
    	|
    	| The Authentication SDK requires the API url, Client ID and Secret. You
    	| can set them all in one chainable function, using whatever method
    	| you've created to store them.
    	*/
    	$this->client->setUrl(config('api.url'))
                     ->setCallback(config('api.callback'))
    				 ->setClient(config('api.credentials.client'))
    				 ->setSecret(config('api.credentials.secret'));
    }

    /**
     * Front facing of controller.
     * 
     * @return response
     */
    public function index()
    {
    	return view('api');
    }

    /**
     * Forget the API session and redirect.
     * 
     * @return response
     */
    public function forget()
    {
    	// remove API token from session.
    	session()->forget('api-token');
	    session()->forget('api');

	    return redirect('/');
    }

    /**
     * Build the authorize query and redirect to API.
     * 
     * @return response
     */
    public function auth()
    {
    	$url = $this->client->make();

    	return redirect($url);
    }

    /**
     * Send a token request and store the credentials in your session,
     * then redirect the user back to a selected page.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return response
     */
    public function callback(Request $request)
    {
        // code variable returned from OAuth
        // capture this from your request object
        $code = $request->code;

        // retrieve your token
        $response = $this->client->send($code);

        // store the token in your session
        session(['api'       => $response]);
        session(['api-token' => $response['access_token']]);

        return redirect('/');
    }
}
```

If you have any questions or concerns please feel free to open an issue.
