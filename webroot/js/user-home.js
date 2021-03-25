// var staffActive = ""
// var passwordFilled = ""

$(document).ready(function() {
    // event click staff avatar
    $('.staff-avatar').on('click', function() {
        // clear input password
        $('.show-password').html('<i class="fas fa-eye"></i>')
        $('.show-password').data('type', 'show')
        pass = $("input[name='Password']").val()
        document.getElementsByName('Password')[0].type = 'password'
        $("input[name='Password']").val('')

        // toggle
        if ($(this).attr('data-active') == "false") { $(this).attr('data-active', 'true') } else { $(this).attr('data-active', 'false') }
        // refresh
        $('.staff-avatar').each(function() {
                if ($(this).hasClass("staff-active")) {
                    $(this).removeClass("staff-active")
                }
            })
            // active
        if ($(this).attr('data-active') == "true") {
            $(this).addClass("staff-active")
            var staffID = $(this).attr('data-staffid')
                // set staff
            $('#staffID').val(staffID)
            getPassword(staffID)
        } else {
            $('#staffID').val('')
            $('#pwStaff').val('')
        }
    })

    // show password
    $('.show-password').on('click', function() {
        showPassword($(this))
    })

    $('#btnSubmitCheckin').on('click', function(e) {
        e.preventDefault()
        $(this).attr('disabled', true)
        if (validateForm()) { validateCoordinate("checkin") } else $(this).attr('disabled', false)
    })
    $('#btnSubmitCheckout').on('click', function(e) {
        e.preventDefault()
        $(this).attr('disabled', true)
        if (validateForm()) { validateCoordinate("checkout") } else $(this).attr('disabled', false)
    })

    // Event click CAPTURE
    $('#CaptureCamera').on('click', function(e) {
        e.preventDefault()
        $('#lookCameraModal').modal('hide')
        if ($(this).data('type') == 'checkin') {
            $('#btnInsertCheckin').click()
        } else {
            $('#btnInsertCheckout').click()
        }
    })
})

/**
 * 
 * @param {input} element 
 */
function showPassword(element) {
    if (element.data('type') == 'show') {
        element.html('<i class="fas fa-eye-slash"></i>')
        element.data('type', 'hide')
        pass = $("input[name='Password']").val()
        document.getElementsByName('Password')[0].type = 'text'
        $("input[name='Password']").val(pass)
    } else {
        element.html('<i class="fas fa-eye"></i>')
        element.data('type', 'show')
        pass = $("input[name='Password']").val()
        document.getElementsByName('Password')[0].type = 'password'
        $("input[name='Password']").val(pass)
    }
}

function getPassword(staffID) {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: baseUrl + 'homepage/getPassword',
        type: 'POST',
        data: { staffID: staffID },
        success: function(res) {
            $('#pwStaff').val(res.password)
        }
    })
}

function validateForm() {
    // validate Staff
    var staffID = $('#staffID').val()
    if (staffID == "") {
        swal({
            title: '',
            text: "Please click your face image!",
            button: 'OK'
        })
        return false
    }
    // validate Password 
    var password = $('input[name="Password"]').val()
    if (password == "") {
        swal({
            title: '',
            text: "Please filled password!",
            button: 'OK'
        })
        return false
    } else {
        if (password != $('#pwStaff').val()) {
            swal({
                icon: 'error',
                title: '',
                text: "Password not correct. ",
                button: 'OK'
            })
            return false
        }
    }

    return true

    // return new Promise(function(resolve, reject) {
    //     initializeMap()
    //     var valid = true
    //     setTimeout(function() {
    //         var allow_location = ($('#currentCoord').val() != "none") ? true : false
    //         if (!allow_location) {
    //             swal({
    //                 title: '',
    //                 text: "Please enable location services on your device !",
    //                 button: 'OK'
    //             })
    //             valid = false
    //         }
    //     }, 500)
    //     resolve(valid)
    // })
}

function validateCoordinate(action) {
    initializeMap()
    setTimeout(function() {
        var allow_location = ($('#currentCoord').val() != "none") ? true : false
        if (!allow_location) {
            swal({
                    title: '',
                    text: "Please enable location services on your device !",
                    button: 'OK'
                })
                (action == "checkin") ? $('#btnSubmitCheckin').attr('disabled', false) : $('#btnSubmitCheckout').attr('disabled', false)
            return false
        } else {
            (action == "checkin") ? validateCheckin(): validateCheckout()
        }
    }, 500)
}

function validateCheckin() {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: baseUrl + 'homepage/validateCheckin',
        type: 'POST',
        data: { staffid: $('#staffID').val() },
        success: function(res) {
            $('#btnSubmitCheckin').attr('disabled', false)
            if (res.unvalid) {
                swal({
                    title: '',
                    text: res.unvalid,
                    button: 'OK'
                })
            } else {
                $('#CaptureCamera').data('type', 'checkin')
                $('#lookCameraModal').modal()
            }
        }
    })
}

function validateCheckout() {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: baseUrl + 'homepage/validateCheckout',
        type: 'POST',
        data: { staffid: $('#staffID').val() },
        success: function(res) {
            $('#btnSubmitCheckout').attr('disabled', false)
            if (res.unvalid) {
                swal({
                    title: '',
                    text: res.unvalid,
                    button: 'OK'
                })
            } else {
                $('#CaptureCamera').data('type', 'checkout')
                $('#lookCameraModal').modal()
            }
        }
    })
}

function initializeMap() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError, {
            timeout: 10000
        });
    } else {
        alert("Geolocation is not supported by this browser.")
    }

    function showPosition(position) {
        let lat = position.coords.latitude
        let lng = position.coords.longitude
        $('#currentCoord').val(lat + "," + lng)
    }

    function showError(error) {
        console.log(error)
    }
}