@extends('staff.layouts.master')
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
        padding: 15px 20px 15px 20px;
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
<div class="row admin-section mb-5">
    <div class="camera">
        <div class="card ">
            <div class="card-body faceView">
                <video autoplay="true" id="videoElement"></video>
                <canvas id="canvas"></canvas>
                <a href="#" id="captureBtn" class="btn btn-primary d-flex flex-column"><i class="las la-camera"></i> Capture</a>
            </div>
        </div>
    </div>
    <div class="view admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">Profile</div>
            <div class="card-body d-flex justify-content-center align-items-center" id="ProfileUpload">
                <span class="display-6 text-secondary">Profile Preview</span>
            </div>
            <div class="card-footer">
                <form action="{{ route('staff.player.face.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$pid}}">
                    <input type="file" name="profile" class="form-control d-none" value="" id="ProfileImg">
                    <button type="submit" class="btn btn-primary w-100 d-none" id="RegisterBtn">Save Profile</button>
                </form>
            </div>    
        </div>
    </div>
</div>

@push('post_script')
<script>
     const video = document.getElementById('videoElement');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('captureBtn');

            const constraints = {
                video: true
            };

            // Access webcam
            async function initCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    handleSuccess(stream);
                } catch (e) {
                    console.error('Error accessing webcam:', e);
                }
            }

            // Display webcam stream
            function handleSuccess(stream) {
                video.srcObject = stream;
            }

            // Capture image from canvas
            captureBtn.addEventListener('click', function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                // Get image data from canvas
                const imageDataURL = canvas.toDataURL('image/png');

                // Display the captured image
                const imageElement = new Image();
                imageElement.src = imageDataURL;
                imageElement.style.width = "100%";
                imageElement.style.height = "auto";

                let UploadContainer = document.getElementById('ProfileUpload');
                let RegisterBtn = document.getElementById('RegisterBtn');
                let ProfileImg = document.getElementById('ProfileImg');
                    
                // Convert the image data URL to a blob
                fetch(imageDataURL)
                    .then(res => res.blob())
                    .then(blob => {
                        // Create a File object from the blob
                        const file = new File([blob], 'profile-image.png', { type: 'image/png' });
                        // Create a new FileList containing the File object
                        const fileList = new DataTransfer();
                        fileList.items.add(file);
                        // Assign the FileList to the input field's files property
                        ProfileImg.files = fileList.files;
                    });



                while (UploadContainer.firstChild) {
                    UploadContainer.removeChild(UploadContainer.firstChild);
                }

                UploadContainer.appendChild(imageElement);
                RegisterBtn.classList.remove('d-none');

            });

            // Initialize webcam on page load
            initCamera();
</script>
@endpush
@endsection