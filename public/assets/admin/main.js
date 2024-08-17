$(document).ready(function() {

  // Player Detection start
  $('#RequestFacePoint').change(function() {
    if(this.checked) {
        // If checkbox is checked, show input field
        $('#pointsInputField').html('<input type="text" class="form-control" name="request_point" placeholder="Enter Points">').show();
    } else {
        // If checkbox is unchecked, hide input field
        $('#pointsInputField').hide().html('');
    }
  });

  
  $('.playerFaceClose').on('click', function() {
      const video = document.getElementById("video");
      const stream = video.srcObject;
      const tracks = stream.getTracks();
    
      tracks.forEach(track => track.stop());
      video.srcObject = null;
  });
  
  $('.playerFaceModel').on('click', function () {
    const video = document.getElementById("video");
    Promise.all([
      faceapi.nets.ssdMobilenetv1.loadFromUri("/assets/vendor/face-api/models"),
      faceapi.nets.faceRecognitionNet.loadFromUri("/assets/vendor/face-api/models"),
      faceapi.nets.faceLandmark68Net.loadFromUri("/assets/vendor/face-api/models"),
    ]).then(startWebcam);
  
    function startWebcam() {
      navigator.mediaDevices
        .getUserMedia({
          video: true,
          audio: false,
        })
        .then((stream) => {
          video.srcObject = stream;
        })
        .catch((error) => {
          console.error(error);
        });
    }
  
    function getLabeledFaceDescriptions() {
      return new Promise((resolve, reject) => {
  
        $.ajax({
            url: '/admin/player/ajaxplayers',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                resolve(response.players);
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
  
      })
      .then(async (players) => {
        return Promise.all(
            players.map(async (player) => {
  
            const label = player.profile;
            const roomId = player.room_id;
            const descriptions = [];
  
              const img = await faceapi.fetchImage(`/upload/${roomId}/profile/${label}`);
              const detections = await faceapi
                .detectSingleFace(img)
                .withFaceLandmarks()
                .withFaceDescriptor();
              descriptions.push(detections.descriptor);
              
            return new faceapi.LabeledFaceDescriptors(label, descriptions);
          })
        );
      })
      .catch((error) => {
        console.error(error);
        return [];
      });
    }  
  
    video.addEventListener("play", async () => {
      const labeledFaceDescriptors = await getLabeledFaceDescriptions();
      const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
  
      const canvas = faceapi.createCanvasFromMedia(video);
      document.body.append(canvas);
  
      const displaySize = { width: video.width, height: video.height };
      faceapi.matchDimensions(canvas, displaySize);
  
        const detection = await faceapi
          .detectSingleFace(video)
          .withFaceLandmarks()
          .withFaceDescriptor();
  
          if (detection) {

            const result = faceMatcher.findBestMatch(detection.descriptor);
            const ProfileMatcher = result.label;
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
  
            $.ajax({
                url: '/admin/player/ajax_profile_match',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: { profile: ProfileMatcher },
                success: function(response) {

                  $('#FaceInputName').val(response.name);
                  $('#FaceInputBonus').val(response.bonus);
                  $('#playerInput').val(response.id);

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
            
          }
          
          
    });
  
  });
  // Player Detection start


    var userList = $('.userList');

    // Hide the list initially
    userList.hide();

    // Click event handler for list items
    userList.on('click', 'li', function() {
        var userName = $(this).text();
        var userId = $(this).data('id');

        $('.selectReference').val(userName);
        $('#refId').val(userId);
        userList.hide();
    });

    // Click event handler for the document
    $(document).on('click', function(event) {
        var target = $(event.target);

        // Check if the clicked element is not within the list-item-group or the input.selectReference
        if (!target.closest('.list-item-group').length && !target.hasClass('selectReference')) {
            userList.hide();
        }
    });

    $('.selectReference').on('input', function() {
        var reference = $(this).val();
        
        if (reference.length > 0) {
            $.ajax({
                url: '/admin/player/ajaxref',
                method: 'GET',
                data: { reference: reference },
                success: function(response) {
                  userList.empty();
                    response.data.forEach(function(user) {
                        userList.append('<li class="list-group-item" data-id="'+ user.id +'">' + user.name + '</li>');
                    });
                    userList.show();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        } else {
            userList.empty(); 
            userList.hide();
        }
    });



var playerList = $('.playerList');

// Search Player
  $('.searchPlayer').on('input', function() {
    var reference = $(this).val();
    
    if (reference.length > 0) {
        $.ajax({
            url: '/admin/player/search',
            method: 'GET',
            data: { reference: reference },
            success: function(response) {
              playerList.empty();

                response.data.forEach(function(player) {
                  playerList.append('<li class="list-group-item" data-id="'+ player.id +'">' + player.name + '</li>');
                });
                playerList.show();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    } else {
      playerList.empty(); 
      playerList.hide();
    }
  });

});
