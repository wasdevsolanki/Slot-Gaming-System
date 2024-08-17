@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
@push('post_css')
<style>
    #videoElement {
        width: 100%;
        max-width: 100%;
    }
    #canvas {
        display: none;
    }
    #captureBtn {
        cursor: pointer;
        position: absolute;
        z-index: 1;
        top: 77%;
        left: 80%;
        padding: 15px 20px;
        border-radius: 10px;
    }
    #captureBtn i {
        font-size: 30px;
    }
    .admin-section {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
    }
    .admin-section .camera {
        max-width: 47%;
    }
    .admin-section .view {
        max-width: 37%;
    }
</style>
@endpush

<div id="playerNotFoundModal" class="modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Player</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>This Player is not found in system.</p>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-sm bg-secondary text-light" data-bs-dismiss="modal">Close</button>
          <a href="" class="btn btn-sm btn-primary">Try again</a>
      </div>
    </div>
  </div>
</div>


<div class="row admin-section mb-5">
    <div class="camera">
        <div class="card">

            <div class="card-body faceView" id="cameraView">
                <video autoplay="true" id="videoElement"></video>
                <canvas id="canvas"></canvas>
                <a href="#" id="captureBtn" class="btn btn-primary d-flex flex-column"><i class="las la-camera"></i> Capture</a>
            </div>
            <div class="card-body d-none" id="capturedImg">

            </div>

        </div>
    </div>
    <div class="view admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">
                Player Points
            </div>

            <div class="card-body d-flex justify-content-center align-items-center">

                <form action="{{ route('admin.point.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="player_id" value="" id="playerId">

                    <div class="row h-100" id="pointForm">

                        <div class="col-md-12 mb-5 d-flex flex-column">
                            <h4 class="fw-semibold mb-0">Assign Points</h4>
                            <span>Add up points to player via Face ID</span>
                        </div>

                        <div class="col-md-12">
                            <div class="input-group mb-3 border">
                                <div class="form-floating">
                                    <input type="text" class="form-control shadow-none border-0" name="player_name" id="inputName" autocomplete="off" readonly>
                                    <label for="inputName">Player Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control shadow-none" name="player_bonus" id="inputBonus" value="" readonly>
                                <label for="inputBonus">Bonus Value</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control shadow-none" name="amount" id="inputPoint" required>
                                <label for="inputPoint">Add up Points</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-primary w-100" id="submitPoint" disabled>Add Point</button>
                        </div>

                    </div>

                    <div class="d-flex justify-content-center d-none" id="pointLoader">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                </form>
            </div>
    
        </div>
    </div>
</div>
@push('post_script')
<script>
$(document).ready(function() {
    
    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const playerNotFoundModal = new bootstrap.Modal(document.getElementById('playerNotFoundModal'));

    let stream;

    // Load face-api.js models
    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri('/assets/vendor/face-api/models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('/assets/vendor/face-api/models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('/assets/vendor/face-api/models')
    ]).then(initCamera);

    // Access webcam
    async function initCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            handleSuccess(stream);
        } catch (e) {
            console.error('Error accessing webcam:', e);
        }
    }

    // Display webcam stream
    function handleSuccess(stream) {
        video.srcObject = stream;
    }

    // Stop webcam stream
    function stopCamera() {
        let tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }

    // Fetch labeled face descriptions from server
    async function getLabeledFaceDescriptions() {

        try {
            const response = await $.ajax({
                url: '/admin/player/ajaxplayers',
                type: 'GET',
                dataType: 'json'
            });

            const players = response.players;
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

                    if (detections) {
                        descriptions.push(detections.descriptor);
                    }

                    return new faceapi.LabeledFaceDescriptors(label, descriptions);
                })
            );
        } catch (error) {
            console.error(error);
            return [];
        }
    }

    captureBtn.addEventListener('click', async () => {

        let pointForm = document.getElementById('pointForm');
        let pointLoader = document.getElementById('pointLoader');

        pointForm.classList.add("d-none");
        pointLoader.classList.remove("d-none");

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        // Get image data from canvas
        const imageDataURL = canvas.toDataURL('image/png');

        // Display the captured image
        const imageElement = new Image();
        imageElement.src = imageDataURL;
        imageElement.style.width = "100%";

        let cameraView = document.getElementById('cameraView');
        let capturedImg = document.getElementById('capturedImg');


        cameraView.classList.add("d-none");
        capturedImg.classList.remove("d-none");
        capturedImg.innerHTML = '';
        capturedImg.appendChild(imageElement);

        // Stop the camera after capturing the image
        stopCamera();

        // Start face recognition
        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

        const detection = await faceapi
            .detectSingleFace(canvas)
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (detection) {

            const result = faceMatcher.findBestMatch(detection.descriptor);
            const ProfileMatcher = result.label;
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            if( ProfileMatcher != 'unknown') {
                $.ajax({
                    url: '/admin/player/ajax_profile_match',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: { profile: ProfileMatcher },
                    success: function(response) {

                        pointLoader.classList.add("d-none");
                        pointForm.classList.remove("d-none");
    
                        $('#inputName').val(response.name);
                        $('#inputBonus').val(response.bonus);
                        $('#playerId').val(response.id);
                        $('#submitPoint').prop("disabled", false);
                        
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                playerNotFoundModal.show();
            }


        } else {
            playerNotFoundModal.show();
        }

    });
});
</script>
@endpush
@endsection
