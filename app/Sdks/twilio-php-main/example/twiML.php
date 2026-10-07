<?php

require __DIR__.'/../vendor/autoload.php';

use Twilio\TwiML\Voice\Play;
use Twilio\TwiML\Voice\Say;
use Twilio\TwiML\VoiceResponse;

// TwiML Say and Play
$say = new Say('Hello World!', [
    'voice' => 'woman',
]);

$play = new Play('https://api.twilio.com/cowbell.mp3', [
    'loop' => 5,
]);

$twiml = new VoiceResponse;
$twiml->append($say);
$twiml->append($play);

echo "TwiML Say and Play: \n{$twiml->asXML()}\n";

// Gather, Redirect
$twimlResponse = new VoiceResponse;
$gather = $twimlResponse->gather();
$gather->setNumDigits(10);
$gather->say('Press 1');
$twimlResponse->redirect('https://example.com');
echo "TwiML Gather and Redirect: \n{$twimlResponse->asXML()}\n";

// Dial
$twimlResponse = new VoiceResponse;

// A valid phone number formatted with a '+' and a country code (e.g., +16175551212)
$callerID = '+XXXXXXXX';
$dial = $twimlResponse->dial('', [
    'callerId' => $callerID,
    'action' => 'https:///example.com',
    'hangupOnStar' => true,
]);

$dial->conference('My Room', ['beep' => 'true']);
echo "TwiML Dial: \n{$twimlResponse->asXML()}\n";
