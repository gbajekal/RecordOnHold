<?php

require_once "../common/functions.php";

$userName = getUserName();
//debugStr("User Name is = " . $userName);

?>


<!DOCTYPE html>

<html>
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title>Live input record and playback</title>
    <link rel="stylesheet" href="../css/flex.css">
	 
</head>
<body> 
<div class="container">

  <?php include "../common/header.php" ?>
  <?php include "../common/menu.php" ?>
<article>
   <h2>Please record your audio content</h2>
  <button onclick="startRecording(this);">record</button>
  <button onclick="stopRecording(this);" disabled>stop</button>

  <h2>Recordings</h2>
  <ul id="recordingslist"></ul>

  <h2>Log</h2>
  <pre id="log"></pre>

  <script>
  function __log(e, data) {
    log.innerHTML += "\n" + e + " " + (data || '');
  }

  var audio_context;
  var recorder;

  function startUserMedia(stream) {
    var input = audio_context.createMediaStreamSource(stream);
    __log('Media stream created.' );
	__log("input sample rate " +input.context.sampleRate);

    // Feedback!
    //input.connect(audio_context.destination);
    __log('Input connected to audio context destination.');

    recorder = new Recorder(input, {
                  numChannels: 1
                });
    __log('Recorder initialised.');
  }

  function startRecording(button) {
    recorder && recorder.record();
    button.disabled = true;
    button.nextElementSibling.disabled = false;
    __log('Recording...');
  }

  function stopRecording(button) {
    recorder && recorder.stop();
    button.disabled = true;
    button.previousElementSibling.disabled = false;
    __log('Stopped recording.');

    // create WAV download link using audio data blob
    createDownloadLink();

    recorder.clear();
  }

  function createDownloadLink() {
    recorder && recorder.exportWAV(function(blob){
	/*
      var url = URL.createObjectURL(blob);
      var li = document.createElement('li');
      var au = document.createElement('audio');
      var hf = document.createElement('a');

      au.controls = true;
      au.src = url;
      hf.href = url;
      hf.download = new Date().toISOString() + '.wav';
      hf.innerHTML = hf.download;
      li.appendChild(au);
      li.appendChild(hf);
      recordingslist.appendChild(li);
	  */
    } );
	
	
  }

  window.onload = function init() {
    try {
      // webkit shim
      window.AudioContext = window.AudioContext || window.webkitAudioContext;
      navigator.getUserMedia = ( navigator.getUserMedia ||
                       navigator.webkitGetUserMedia ||
                       navigator.mozGetUserMedia ||
                       navigator.msGetUserMedia);
      window.URL = window.URL || window.webkitURL;

      audio_context = new AudioContext;
      __log('Audio context set up.');
      __log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
    } catch (e) {
      alert('No web audio support in this browser!');
    }

    navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
      __log('No live audio input: ' + e);
    });
  };
  </script>

   <script src="js/jquery-1.11.0.min.js"></script>
  <script src="recordmp3.js"></script>
  
   </article>
 
 
  </div>
  <?php include "../common/footer.php" ?>
</body>
</html>