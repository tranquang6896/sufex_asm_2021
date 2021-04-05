(function() {
    // The width and height of the captured photo. We will set the
    // width to the value defined here, but the height will be
    // calculated based on the aspect ratio of the input stream.

    var width = 0; // We will scale the photo width to this
    var height = 0; // This will be computed based on the input stream

    // |streaming| indicates whether or not we're currently streaming
    // video from the camera. Obviously, we start at false.

    var streaming = false;

    // The various HTML elements we need to configure or control. These
    // will be set by the startup() function.

    var videos = null;
    var canvas = null;
    var checkin = null;
    var checkout = null;

    function startup() {

        videos = document.getElementById('video');
        canvas = document.getElementById('canvas');

        turnOn(videos)

        checkin = document.getElementById('btnInsertCheckin');
        checkout = document.getElementById('btnInsertCheckout');

        checkin.addEventListener('click', function(ev) {
            if (streaming) {
                takepicture('insertCheckin');
                ev.preventDefault();
            }
        }, false);

        checkout.addEventListener('click', function(ev) {
            if (streaming) {
                // $timecardID = $('#timecardIDCheckout').val()
                takepicture('insertCheckout');
                ev.preventDefault();
            }
        }, false);

        window.addEventListener('focus', function(e) {
            turnOn(videos)
        })

        window.addEventListener('blur', function(e) {
            turnOff(videos)
        })
    }

    function turnOn(videos) {
        // camera image acquisition
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(stream => {
                // On success, the video element is set to a camera image and played back.
                videos.srcObject = stream;
                videos.play();
            }).catch(error => {
                // Outputs error log in case of failure.
                console.error('mediaDevice.getUserMedia() error:', error);
                alert('Permission denied. Please allow access your camera!')
                return;
            });

        videos.addEventListener('canplay', function(ev) {
            if (!streaming) {
                height = videos.videoHeight
                width = videos.videoWidth
                    // Firefox currently has a bug where the height can't be read from
                    // the video, so we will make assumptions if this happens.

                if (isNaN(height)) {
                    height = 500
                }

                streaming = true;
            }
        }, false);
    }

    function turnOff(videos) {
        videos.pause();
        videos.src = "";
        // videos.srcObject.getTracks()[0].stop();
        videos.srcObject.getTracks().forEach(function(track) {
            track.stop();
        });
        console.log("videos off");
    }

    // Capture a photo by fetching the current contents of the video
    // and drawing it into a canvas, then converting that to a PNG
    // format data URL. By drawing it on an offscreen canvas and then
    // drawing that to the screen, we can change its size and/or apply
    // other changes before drawing it.

    function takepicture(type) {
        var context = canvas.getContext('2d');

        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(videos, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');

            if (type == 'insertCheckin') {
                $('#titleSignModal').html("Check In")
                $('#CapturePhoto').attr("src", data)
                $("#textSignModal").html("Check In successfully!" + '<br>' + "Check-in Time" + '<br>' + moment().format('HH:mm:ss'))
                $('#TimeCheckin').html(moment().format('HH:mm:ss'))
                $('#TimeCheckout').html('')
                $('#signModal').modal()
                    // opacity window
                $('.modal-backdrop').addClass("opacity-window")
            } else {
                $('#titleSignModal').html("Check Out")
                $('#CapturePhoto').attr("src", data)
                $("#textSignModal").html("Check Out successfully!" + '<br>' + "Check-out Time" + '<br>' + moment().format('HH:mm:ss'))
                $('#TimeCheckout').html(moment().format('HH:mm:ss'))
                $('#signModal').modal()
                    // opacity window
                $('.modal-backdrop').addClass("opacity-window")
            }

            setTimeout(function() {
                $('#signModal').modal('hide')
            }, 3000)

            $.ajax({
                headers: { 'X-CSRF-TOKEN': csrfToken },
                type: 'POST',
                url: baseUrl + 'homepage/' + type,
                data: {
                    img: data,
                    coord: $("#currentCoord").val(),
                    // coord: "",
                    staffid: $('#staffID').val()
                },
                success: function(response) {
                    if (response.success == 1) {
                        // clear password
                        $('.show-password').html('<i class="fas fa-eye"></i>')
                        $('.show-password').data('type', 'show')
                        pass = $("input[name='Password']").val()
                        document.getElementsByName('Password')[0].type = 'password'
                        $("input[name='Password']").val('')
                            // end__clear
                            // add class checked-in
                        if (type == 'insertCheckin') {
                            $(".staff-avatar").each(function() {
                                if ($(this).attr('data-staffid') == $('#staffID').val()) {
                                    $(this).addClass("staff-checkedin")
                                }
                            })
                        }
                        // add class checked-out
                        else {
                            $(".staff-avatar").each(function() {
                                if ($(this).attr('data-staffid') == $('#staffID').val()) {
                                    $(this).addClass("staff-checkedout")
                                }
                            })
                        }
                    } else {
                        console.log(response)
                    }
                },
                error: function(res) {
                    console.log('error')
                    console.log(res)
                }
            })

        } else {
            console.log('error')
        }
    }

    // Set up our event listener to run the startup process
    // once loading is complete.
    window.addEventListener('load', startup, false);
})();