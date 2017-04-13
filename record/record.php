<?php

require_once "../common/functions.php";

//$userName = getUserName();
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
   <table>
       <tr><td>
   <p><audio controls id="player" /></p> 
   </td></tr>
   
   </table>
   
 <!-- Rounded switch -->
 <table>
     <tr>   
         <td>    
<div class="onoffswitch">
    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" onchange= handleSwitch(this);>
    <label class="onoffswitch-label" for="myonoffswitch">
        <span class="onoffswitch-inner"></span>
        <span class="onoffswitch-switch"></span>
    </label>
  </div>
         </td>          
             
            
     
 
         <td> 
  <!-- <button id="start" title="Record" onclick="startRecording(this);"><img src="../common/icons/record button.png"></button>
  <button id="stop" title="Stop Recording" onclick="stopRecording(this);" disabled><img src="../common/icons/stop button.png"></button> -->
  <button id="reset" title = "Refresh" onclick="resetRecording(this);" disabled><img src="../common/icons/reset button.png"></button>
  <button id="save" title ="Save" onclick="saveRecording(this);" disabled><img src="../common/icons/save button.png"></button>
         </td>
 </tr>
 </table>
 
  <!--  <canvas class="visualizer" width="640" height="100"></canvas> -->
          
            <h2>Log</h2>
            <pre><span id="log" class="log"></span></pre>
         
              <h2>Saved Recordings</h2>
  
                <table>
                    <tr><td><ul id="recordingslist"></ul></td></tr>
                    <tr><td><button id="checkOut" title="Checkout" onclick="checkOut(this);" disabled>Secure Checkout</button></td></tr>
                </table>
          
      
  

  <script>
  function __log(e, data) {
    log.innerHTML += "\n" + e + " " + (data || '');
  }

 function __clearLog()
 {
     log.innerHTML = '';
 }

  const MAX_TIME_OUT = 1000 * 60; // 1 min timeout
  const MAX_RECORDINGS = 5; // Maximum Recordings allowed prior to checkout
  var audio_context;
  var recorder;
  var resetButton;
  var saveButton;
  var checkOutButton;
  var timeout;
  var analyser;
  var canvas;
  var canvasCtx;
  var gainNode;
  var biquadFilter;
  var convolver;
  var distortion;
  
  //***************************
  // This function handles the
  // toggle switch to start or
  // stop recording
  //****************************
  
 function handleSwitch(switchPosition)
 {
     if(switchPosition.checked)
         startRecording(switchPosition);
     else
         stopRecording(switchPosition);
     
 }
function initializeAnalyser(input)
  {
      try
      {
          __log("Initializing Analyzer");
            analyser = audio_context.createAnalyser();
            input.connect(analyser);
           
            distortion = audio_context.createWaveShaper();
            analyser.connect(distortion);
            canvas = document.querySelector('.visualizer');
            canvasCtx = canvas.getContext("2d");
            gainNode = audio_context.createGain();
            biquadFilter = audio_context.createBiquadFilter();
            convolver = audio_context.createConvolver();
            distortion.connect(biquadFilter);
            biquadFilter.connect(convolver);
            convolver.connect(gainNode);
            gainNode.connect(audio_context.destination);
            visualize();
            voiceChange();

        __log("Analyzer Ready");
      }
      catch(e)
      {
          __log("Problems in initializing Analyzer");
      }
      
  }
  
  
  function voiceChange()
  {
      
    biquadFilter.gain.value = 0;
    biquadFilter.type = "lowshelf";
    biquadFilter.frequency.value = 1000;
    biquadFilter.gain.value = 25;
  }
  
  function startUserMedia(stream) {
    //var myAudio = document.querySelector("audio");
    //var input = audio_context.createMediaElementSource(myAudio);
    
    var input = audio_context.createMediaStreamSource(stream);
    
    
    
    __log('Media stream created.' );
	__log("input sample rate " +input.context.sampleRate);

    // Feedback!
    //input.connect(audio_context.destination);
    __log('Input connected to audio context destination.');

    recorder = new Recorder(input, {
                  numChannels: 1
                });
  //  initializeAnalyser(input);
    __log('Recorder initialised.');
    
   
  }

  function startRecording(button) {
    __clearLog();  
  
  // gainNode.connect(audio_context.destination);
   
   
      
    recorder && recorder.record();
    //button.disabled = true;
    //button.nextElementSibling.disabled = false;
    __log('Recording in Progress...');
    
    //*****************************
    // GRB:Clear any previos timers
    // and set a timer
    //******************************
    clearTimeout(timeout);
    timeout = setTimeout(
     function()
     {
        var stopButton = document.getElementById("myonoffswitch");
        stopRecording(stopButton);
        alert("Recording limit of 5 sec reached. Recording stopped!");
     } ,
     MAX_TIME_OUT
    );
    
    //*******************************
    // enable reset and save buttons
    //******************************
    resetButton = document.getElementById("reset");
    resetButton.disabled = false;
    saveButton = document.getElementById("save");
    saveButton.disabled = false;
   
  }

  function stopRecording(button) {
      
      
    recorder && recorder.stop();
    button.checked = false;
    //button.disabled = true;
    //button.previousElementSibling.disabled = false;
    __log('Stopped recording.');

    // create WAV download link using audio data blob
    createDownloadLink();
    //playRecording();
    recorder.clear();
    clearTimeout(timeout);
   // gainNode.disconnect();
  
   
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
        
        //clear any past cookies
        //
        document.cookie = '';
   
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
      
      
      
      
      document.cookie = "recordings=''";
    } catch (e) {
      alert('No web audio support in this browser!');
    }

    navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
      __log('No live audio input: ' + e);
    });
  };
  
  
  
  
  //**********************************************
  // GRB: play - Handler for Play Recording Button
  //**********************************************
  function playRecording()
  {
     recorder && recorder.exportWAV(function(blob){
      var player = document.getElementById("player");
      var url = URL.createObjectURL(blob);
      alert("Player URL:" + url);
      player.src = url;
    }
    );
      
  }
  
  //**********************************************
  // GRB: resetRecording - Reset Recording
  //**********************************************
  function resetRecording(button)
  {
   __clearLog();
   __log("Resetting ....");
   recorder && recorder.clear();
    __log("Ready to record!");
   var player = document.getElementById("player");
   player.src = '';
   button.disabled = true;
   
   
   
  }
  
  //**********************************************
  // GRB: resetRecording - Reset Recording
  //**********************************************
  function saveRecording(button)
  {
      
    var player = document.getElementById("player");
    if( player && player.src)
    {
        __log("Saving to Server");
            saveRecording.count = ++saveRecording.count || 1 // f.count is undefined at first
            //******************************
            // Check if maximum permitted 
            // recordings reached
            //********************************
                if( saveRecording.count > MAX_RECORDINGS)
                {
                    alert("Maximum permitted recordings reached. Please checkout items before adding new recordings!!!");
                    return;
                }
                var recUrl = player.src;
                var audioFileName = 'audio_recording_' + new Date().getTime() + '.mp3';
                var li = document.createElement('li');
                var hf = document.createElement('a');
                hf.href = recUrl;
                hf.download = audioFileName;
                hf.innerHTML = hf.download;
                li.appendChild(hf);
                
                //*************************
                // save to Server here.
                // But how do we know the 
                // directory as the user
                // has not yet registered????
                // Save to a temp directory??
                //*************************
									
                    $.ajax({
                            type: 'POST',
                            url: 'upload.php',
                            data:{content: recUrl, recId: audioFileName}

                    }).done(function(data) {
                            
                          var cookieStr = getCookie('recordings');
                        // __log("Cookie="+ document.cookie);
                            
                    });
											


                recordingslist.appendChild(li);
                button.disable = true;
                var checkOutButton = document.getElementById("checkOut");
                checkOutButton.disabled = false;
                saveButton = document.getElementById("save");
                saveButton.disabled = true;
               
                __log("Saved the recording " + audioFileName );
                __log("Cookie = " + cookieStr);
                
    }
    
				
    
      
  }
  
  
  
  //**********************************************
  // GRB: saveRecording - Handler for SaveRecording Button
  //**********************************************
  function checkOut(button)
  {
      // Sanity chek to see if at least one item
      // is selected
      
      __log("Checking out saved recordings ..");
       window.location.href = '../register/register.php';
  }
  
  function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
};

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
};

function visualize()
{
   __log("In visualize to create waveform...");
 var WIDTH = canvas.width;
 var HEIGHT = canvas.height;

   analyser.fftSize = 2048;
   var bufferLength = analyser.fftSize;
   console.log(bufferLength);
   var dataArray = new Uint8Array(bufferLength);
    canvasCtx.clearRect(0, 0, WIDTH, HEIGHT);

    function draw() {

      drawVisual = requestAnimationFrame(draw);

      analyser.getByteTimeDomainData(dataArray);

      canvasCtx.fillStyle = 'rgb(200, 200, 200)';
      canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

      canvasCtx.lineWidth = 2;
      canvasCtx.strokeStyle = 'rgb(0, 0, 0)';

      canvasCtx.beginPath();

      var sliceWidth = WIDTH * 1.0 / bufferLength;
      var x = 0;

      for(var i = 0; i < bufferLength; i++) {
   
        var v = dataArray[i] / 128.0;
        var y = v * HEIGHT/2;

        if(i === 0) {
          canvasCtx.moveTo(x, y);
        } else {
          canvasCtx.lineTo(x, y);
        }

        x += sliceWidth;
      }

      canvasCtx.lineTo(canvas.width, canvas.height/2);
      canvasCtx.stroke();
    };

    draw();

 __log("Exiting visualize");
    
}
  </script>

   <script src="js/jquery-1.11.0.min.js"></script>
  <script src="recordmp3.js"></script>
  
   </article>
 
 
  </div>
  <?php include "../common/footer.php" ?>
</body>
</html>