<?php

$text = 'Text to ddd';


$client = new TextToSpeechClient();

$input_text = (new SynthesisInput())
->setText("aaaayezzziiiiiii");

// note: the voice can also be specified by name
// names of voices can be retrieved with $client->listVoices()
$voice = (new VoiceSelectionParams())
->setLanguageCode('en-US')
->setSsmlGender(SsmlVoiceGender::FEMALE);

$audioConfig = (new AudioConfig())
->setAudioEncoding(AudioEncoding::MP3);

$response = $client->synthesizeSpeech($input_text, $voice, $audioConfig);
$audioContent = $response->getAudioContent();

file_put_contents('output.mp3', $audioContent);
print('Audio content written to "output.mp3"' . PHP_EOL);



$client->close();

?>