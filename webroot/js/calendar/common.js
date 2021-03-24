jQuery(document).ready(function () {

    $('body').on('click', 'button.fc-prev-button', function() {
        //do something
        showCalendarStaff();
    });

    $('body').on('click', 'button.fc-next-button', function() {
        //do something
        showCalendarStaff();
    });

    $(".fc-today-button").click(function() {
        //do something
        showCalendarStaff();
    });

    $("#close-map").click(function() {
        //do something
        $("#eventModal").modal('show')
        $("#eventMapModal").modal('hide')
    });

    $('body').on('click', '#MAP', function(e) {
        e.preventDefault()
        //do something
        MapModule.initGoogleMap(map_events)
        MapModule.addLocationsToMap(map_events)
        $("#eventModal").modal('hide')
        $("#eventMapModal").modal()
    });

    $('body').on('click', '#event-edit', function(e) {
        e.preventDefault()
        enabledReport()
        $('#event-save').show()
        $('#event-edit').hide()

        $('.delete-image, .fileinput-button').show()
    });

    $('body').on('click', '#event-save', function(e) {
        e.preventDefault()
        $('#event-save').hide()
        $('#event-loading').show()
        insertReport()
    });

    $(document).on("click", "#Picture", function (e) {
        e.preventDefault()
        let img_in = $(this).attr('data-checkIn');
        let img_out = $(this).attr('data-checkOut');
        if (img_in) {
            img_in = '<img src="'+__baseUrl+img_in+'" width="150px">'
        }
        if (img_out) {
            img_out = '<img src="'+__baseUrl+img_out+'" width="150px">'
        }
        $("#checkIn").html( img_in  );
        $("#checkOut").html(  img_out );

        $('#faceModeClose').hide()
        $('#faceModeBack').show()
        $('#eventModal').modal('hide')

        $('#faceModal').modal()
    });

    $(document).on("click", "#faceModeBack", function () {
        $('#faceModal').modal('hide')
        $('#eventModal').modal()
    });

    $(document).on("click", "#close-report", function () {
        $('#eventModal').modal('hide')
    });

    $('#TypeReport').on('change', function(){
        if($('#TypeCode').val() == $(this).val()){
            $('#flagEdit').val('ena')
            resetFieldReport($('#timecard_id').val(),$('#report_id').val(), $(this).val())
        } else {
            $('#flagEdit').val('ena')
            resetFieldReport(null,null, $(this).val())
        }
    })
})

var MapModule = (function() {
    var mapElement = document.getElementById("MAPCONTENT");
    var mapInstance = null;

    var initGoogleMap = function() {
        mapInstance = new google.maps.Map(mapElement, {
            zoom: 13,
            //center: new google.maps.LatLng(15.967674, 108.020437),
            center: new google.maps.LatLng(map_events[0].lat, map_events[0].long),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
    }

    var addLocationsToMap = function(map_events) {
        console.log(map_events)
        // Create markers.
        for ( i = 0; i < map_events.length; i++) {
            new google.maps.Marker({
                position: new google.maps.LatLng(map_events[i].lat, map_events[i].long),
                map: mapInstance,
                title: map_events[i].CustomerName
            });
        }
    }

    return {
        initGoogleMap: initGoogleMap,
        addLocationsToMap: addLocationsToMap
    }
})()

function disbledReport(){
    $('#textType').show()
    $('#TypeReport').hide()
    $('#ContentReport').attr('readonly', true)
    $('#NoteReport').attr('readonly', true)
    $('.checkbox-report').prop('disabled', true)
}

function enabledReport(){
    $('#textType').hide()
    $('#TypeReport').show()
    $('#ContentReport').attr('readonly', false)
    $('#NoteReport').attr('readonly', false)
    $('.checkbox-report').prop('disabled', false)
}

function insertReport(){
    const form = $(".ajax-form-report");
    ajax_form(form);

}

function ajax_form(form){
    const form_data = new FormData(form.get(0));

    const button = form.find("button[id=event-save]");
    button.attr("disabled", true);

    // case update
    $arr_uploaded = []
    $('.image-uploaded').each(function(){
        $arr_uploaded.push($(this).attr('data-id-uploaded'))
    })
    form_data.append('imagesUploaded', $arr_uploaded)

    if(files.length < 1){
        form_data.append('files', 'null');
    } else {
        files.forEach(file => {
            /* here just put file as file[] so now in submitting it will send all files */
            form_data.append('files[]', file);
        });
    }


    var haveCheck = ($('.report-check').css('display') == "none") ? 0 : 1

    var arrChecked = []
    $('input[name="Check"]:checked').each(function(){
        arrChecked.push($(this).val())
    })

    form_data.append('typeSubmit', 'updated')
    form_data.append('typeReport', $('#TypeReport').val())
    form_data.append('customerID', '')
    form_data.append('ID', $('#report_id').val())
    form_data.append('TimeCardID', $('#timecard_id').val())
    form_data.append('content', $('#ContentReport').val())
    form_data.append('haveCheck', haveCheck)
    form_data.append('valuesChecked', arrChecked)
    form_data.append('note', $('#NoteReport').val())

    $.ajax({
        headers:{'X-CSRF-TOKEN': __csrfToken},
        url: __baseUrl + 'mypage/insertReport',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        dataType: "json"
    })
        .done(function(res){
            if(res.success == 1){
                $('#eventModal').modal('hide')
                swal({
                    title: 'Edited successfully!',
                    icon: 'success'
                })
                .then((reload) => {
                    if (reload) {
                        location.reload(true)
                    }
                })
                // setTimeout(function(){
                //     swal.close()
                // },1500)

                // if(res.uploaded && res.uploaded == 1){
                //     files = []
                // }
            }
            // $('#event-save').attr('disabled', false)
            // $('#event-loading').hide()
            // $('#event-edit').show()
        })
}
